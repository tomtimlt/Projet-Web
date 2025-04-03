<?php
/**
 * Controleur pour la gestion des roles et permissions
 */
class RoleController {
    private $role;
    private $user;
    
    /**
     * Constructeur
     */
    public function __construct() {
        // Charge le modele de roles
        require_once 'models/Role.php';
        $this->role = new Role();
        
        // Charge le modele d'utilisateurs
        require_once 'models/User.php';
        $database = new Database();
        $db = $database->getConnection();
        $this->user = new User($db);
    }
    
    /**
     * Verifie si l'utilisateur connecte a une permission specifique
     * @param string $permission Nom de la permission a verifier
     * @return bool True si l'utilisateur a la permission, false sinon
     */
    public function hasPermission($permission) {
        // Verifie si l'utilisateur est connecte
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        // Verifie la permission
        return $this->role->hasPermission($_SESSION['user_id'], $permission);
    }
    
    /**
     * Verifie si l'utilisateur a une permission specifique ou redirige
     * @param string $permission Nom de la permission a verifier
     * @param string $redirectUrl URL de redirection si la permission est refusee
     */
    public function checkPermissionOr403($permission) {
        if (!$this->hasPermission($permission)) {
            // Prepare le message d'erreur
            $_SESSION['error'] = "Vous n'avez pas les permissions necessaires pour acceder a cette ressource.";
            
            // Envoie une reponse 403 Forbidden
            header('HTTP/1.1 403 Forbidden');
            include('views/errors/403.php');
            exit();
        }
    }
    
    /**
     * Affiche la liste des roles
     */
    public function index() {
        // Verifie les permissions
        $this->checkPermissionOr403('manage_roles');
        
        // Recupere tous les roles
        $roles = $this->role->getAllRoles();
        
        // Affiche la vue
        include('views/roles/index.php');
    }
    
    /**
     * Affiche le formulaire de creation d'un role
     */
    public function create() {
        // Verifie les permissions
        $this->checkPermissionOr403('manage_roles');
        
        // Recupere toutes les permissions pour le formulaire
        $permissions = $this->role->getAllPermissions();
        
        // Affiche la vue
        include('views/roles/create.php');
    }
    
    /**
     * Traite la soumission du formulaire de creation de role
     */
    public function store() {
        // Verifie les permissions
        $this->checkPermissionOr403('manage_roles');
        
        // Validation
        if (!isset($_POST['nom']) || empty($_POST['nom'])) {
            $_SESSION['error'] = "Le nom du role est requis.";
            header('Location: index.php?controller=role&action=create');
            exit();
        }
        
        // Cree le role
        $roleId = $this->role->createRole($_POST['nom'], $_POST['description'] ?? '');
        
        // Ajoute les permissions selectionnees
        if ($roleId && isset($_POST['permissions']) && is_array($_POST['permissions'])) {
            foreach ($_POST['permissions'] as $permissionId) {
                $this->role->assignPermission($roleId, $permissionId);
            }
        }
        
        // Redirige avec un message de succes
        $_SESSION['success'] = "Le role a ete cree avec succes.";
        header('Location: index.php?controller=role&action=index');
        exit();
    }
    
    /**
     * Affiche le formulaire d'edition d'un role
     */
    public function edit() {
        // Verifie les permissions
        $this->checkPermissionOr403('manage_roles');
        
        // Verifie l'ID du role
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            $_SESSION['error'] = "ID de role non specifie.";
            header('Location: index.php?controller=role&action=index');
            exit();
        }
        
        // Recupere le role
        $role = $this->role->getRoleById($_GET['id']);
        if (!$role) {
            $_SESSION['error'] = "Role non trouve.";
            header('Location: index.php?controller=role&action=index');
            exit();
        }
        
        // Recupere toutes les permissions
        $permissions = $this->role->getAllPermissions();
        
        // Recupere les permissions actuelles du role
        $rolePermissions = $this->role->getRolePermissions($_GET['id']);
        $rolePermissionIds = array_column($rolePermissions, 'id');
        
        // Affiche la vue
        include('views/roles/edit.php');
    }
    
    /**
     * Traite la soumission du formulaire de mise a jour de role
     */
    public function update() {
        // Verifie les permissions
        $this->checkPermissionOr403('manage_roles');
        
        // Validation
        if (!isset($_POST['id']) || empty($_POST['id']) || !isset($_POST['nom']) || empty($_POST['nom'])) {
            $_SESSION['error'] = "Donnees de formulaire incompletes.";
            header('Location: index.php?controller=role&action=index');
            exit();
        }
        
        // Mise a jour du role
        $this->role->updateRole($_POST['id'], $_POST['nom'], $_POST['description'] ?? '');
        
        // Supprime toutes les permissions actuelles
        $this->role->deleteRolePermissions($_POST['id']);
        
        // Ajoute les permissions selectionnees
        if (isset($_POST['permissions']) && is_array($_POST['permissions'])) {
            foreach ($_POST['permissions'] as $permissionId) {
                $this->role->assignPermission($_POST['id'], $permissionId);
            }
        }
        
        // Redirige avec un message de succes
        $_SESSION['success'] = "Le role a ete mis a jour avec succes.";
        header('Location: index.php?controller=role&action=index');
        exit();
    }
    
    /**
     * Supprime un role
     */
    public function delete() {
        // Verifie les permissions
        $this->checkPermissionOr403('manage_roles');
        
        // Verifie l'ID du role
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            $_SESSION['error'] = "ID de role non specifie.";
            header('Location: index.php?controller=role&action=index');
            exit();
        }
        
        // Verifie si des utilisateurs utilisent ce role
        if ($this->role->roleHasUsers($_GET['id'])) {
            $_SESSION['error'] = "Ce role ne peut pas etre supprime car des utilisateurs y sont assignes.";
            header('Location: index.php?controller=role&action=index');
            exit();
        }
        
        // Supprime le role
        if ($this->role->deleteRole($_GET['id'])) {
            $_SESSION['success'] = "Le role a ete supprime avec succes.";
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la suppression du role.";
        }
        
        // Redirige
        header('Location: index.php?controller=role&action=index');
        exit();
    }
}
