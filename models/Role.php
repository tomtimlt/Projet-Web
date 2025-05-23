<?php
require_once 'config/database.php';

/**
 * Classe Role - Gestion des roles utilisateurs
 * Permet de gerer les roles et permissions des utilisateurs
 */
class Role {
    private $conn;
    private $id;
    private $nom;
    private $permissions;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    /**
     * Recupere tous les roles disponibles
     * @return array Liste des roles
     */
    public function getAllRoles() {
        $query = "SELECT * FROM roles ORDER BY id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * Recupere un role par son ID
     * @param int $id Identifiant du role
     * @return object|false Donnees du role ou false si non trouve
     */
    public function getRoleById($id) {
        $query = "SELECT * FROM roles WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    
    /**
     * Verifie si un utilisateur a une permission specifique
     * @param int $userId ID de l'utilisateur
     * @param string $permission Nom de la permission
     * @return bool True si l'utilisateur a la permission, false sinon
     */
    public function hasPermission($userId, $permission) {
        // Recupere le role de l'utilisateur
        $query = "SELECT r.* FROM roles r 
                  INNER JOIN users u ON r.id = u.role_id 
                  WHERE u.id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        $role = $stmt->fetch(PDO::FETCH_OBJ);
        if (!$role) {
            return false;
        }
        
        // Recupere les permissions du role
        $query = "SELECT p.nom FROM permissions p 
                  INNER JOIN role_permissions rp ON p.id = rp.permission_id 
                  WHERE rp.role_id = :role_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':role_id', $role->id, PDO::PARAM_INT);
        $stmt->execute();
        
        $permissions = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Verifie si la permission demandee est dans la liste
        return in_array($permission, $permissions);
    }
    
    /**
     * Recupere toutes les permissions d'un role
     * @param int $roleId ID du role
     * @return array Liste des permissions
     */
    public function getRolePermissions($roleId) {
        $query = "SELECT p.* FROM permissions p 
                  INNER JOIN role_permissions rp ON p.id = rp.permission_id 
                  WHERE rp.role_id = :role_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':role_id', $roleId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * Cree un nouveau role
     * @param string $nom Nom du role
     * @param string $description Description du role
     * @return int|bool ID du role cree ou false en cas d'echec
     */
    public function createRole($nom, $description = '') {
        $query = "INSERT INTO roles (nom, description) VALUES (:nom, :description)";
        $stmt = $this->conn->prepare($query);
        
        // Securisation des données
        $nom = htmlspecialchars(strip_tags($nom));
        $description = htmlspecialchars(strip_tags($description));
        
        // Liaison des parametres
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Met a jour un role existant<?php include 'views/layout.php'; ?>
     
     <div class="container py-4">
         <nav aria-label="breadcrumb">
             <ol class="breadcrumb">
                 <li class="breadcrumb-item"><a href="index.php?page=users">Utilisateurs</a></li>
                 <li class="breadcrumb-item">
                     <a href="index.php?page=user&action=view&id=<?= $user['id'] ?>">
                         <?= htmlspecialchars($user['prenom'].' '.$user['nom']) ?>
                     </a>
                 </li>
                 <li class="breadcrumb-item active" aria-current="page">Modifier le profil</li>
             </ol>
         </nav>
     
         <div class="card shadow-sm">
             <div class="card-header">
                 <h2 class="h5 mb-0">
                     <i class="fas fa-edit me-2"></i>Modifier le profil utilisateur
                 </h2>
             </div>
             <div class="card-body">
                 <form action="index.php?page=user&action=editProfile&id=<?= $user['id'] ?>" method="post" novalidate>
                     <div class="row mb-3">
                         <div class="col-md-6">
                             <div class="mb-3">
                                 <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                                 <input type="text" class="form-control <?= isset($errors['nom']) ? 'is-invalid' : '' ?>" 
                                     id="nom" name="nom" value="<?= htmlspecialchars($_POST['nom'] ?? $user['nom']) ?>" required>
                                 <?php if (isset($errors['nom'])): ?>
                                     <div class="invalid-feedback"><?= $errors['nom'] ?></div>
                                 <?php endif; ?>
                             </div>
                         </div>
                         <div class="col-md-6">
                             <div class="mb-3">
                                 <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                                 <input type="text" class="form-control <?= isset($errors['prenom']) ? 'is-invalid' : '' ?>" 
                                     id="prenom" name="prenom" value="<?= htmlspecialchars($_POST['prenom'] ?? $user['prenom']) ?>" required>
                                 <?php if (isset($errors['prenom'])): ?>
                                     <div class="invalid-feedback"><?= $errors['prenom'] ?></div>
                                 <?php endif; ?>
                             </div>
                         </div>
                     </div>
                     
                     <div class="mb-3">
                         <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                         <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                             id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? $user['email']) ?>" required>
                         <?php if (isset($errors['email'])): ?>
                             <div class="invalid-feedback"><?= $errors['email'] ?></div>
                         <?php endif; ?>
                     </div>
                     
                     <div class="mb-3">
                         <label for="telephone" class="form-label">Téléphone</label>
                         <input type="tel" class="form-control" id="telephone" name="telephone" 
                             value="<?= htmlspecialchars($_POST['telephone'] ?? $user['telephone'] ?? '') ?>">
                     </div>
                     
                     <?php if ($user['role'] === 'etudiant'): ?>
                     <div class="row mb-3">
                         <div class="col-md-6">
                             <div class="mb-3">
                                 <label for="centre" class="form-label">Centre</label>
                                 <input type="text" class="form-control" id="centre" name="centre" 
                                     value="<?= htmlspecialchars($_POST['centre'] ?? $user['centre'] ?? '') ?>">
                             </div>
                         </div>
                         <div class="col-md-6">
                             <div class="mb-3">
                                 <label for="promotion" class="form-label">Promotion</label>
                                 <input type="text" class="form-control" id="promotion" name="promotion" 
                                     value="<?= htmlspecialchars($_POST['promotion'] ?? $user['promotion'] ?? '') ?>">
                             </div>
                         </div>
                     </div>
                     <?php endif; ?>
                     
                     <div class="d-flex justify-content-end">
                         <a href="index.php?page=user&action=view&id=<?= $user['id'] ?>" class="btn btn-outline-secondary me-2">Annuler</a>
                         <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                     </div>
                 </form>
             </div>
         </div>
     </div>
     * @param int $id ID du role
     * @param string $nom Nouveau nom du role
     * @param string $description Nouvelle description du role
     * @return bool Resultat de l'operation
     */<?php include 'views/layout.php'; ?>
     
     <div class="container py-4">
         <nav aria-label="breadcrumb">
             <ol class="breadcrumb">
                 <li class="breadcrumb-item"><a href="index.php?page=users">Utilisateurs</a></li>
                 <li class="breadcrumb-item">
                     <a href="index.php?page=user&action=view&id=<?= $user['id'] ?>">
                         <?= htmlspecialchars($user['prenom'].' '.$user['nom']) ?>
                     </a>
                 </li>
                 <li class="breadcrumb-item active" aria-current="page">Changer le mot de passe</li>
             </ol>
         </nav>
     
         <div class="card shadow-sm">
             <div class="card-header">
                 <h2 class="h5 mb-0">
                     <i class="fas fa-key me-2"></i>Changer le mot de passe
                 </h2>
             </div>
             <div class="card-body">
                 <form action="index.php?page=user&action=changePassword&id=<?= $user['id'] ?>" method="post" novalidate>
                     
                     <?php if ($user['id'] == $this->auth->getUserId()): ?>
                     <div class="mb-3">
                         <label for="current_password" class="form-label">Mot de passe actuel <span class="text-danger">*</span></label>
                         <input type="password" class="form-control <?= isset($errors['current_password']) ? 'is-invalid' : '' ?>" 
                             id="current_password" name="current_password" required>
                         <?php if (isset($errors['current_password'])): ?>
                             <div class="invalid-feedback"><?= $errors['current_password'] ?></div>
                         <?php endif; ?>
                     </div>
                     <?php endif; ?>
                     
                     <div class="mb-3">
                         <label for="new_password" class="form-label">Nouveau mot de passe <span class="text-danger">*</span></label>
                         <input type="password" class="form-control <?= isset($errors['new_password']) ? 'is-invalid' : '' ?>" 
                             id="new_password" name="new_password" required>
                         <?php if (isset($errors['new_password'])): ?>
                             <div class="invalid-feedback"><?= $errors['new_password'] ?></div>
                         <?php else: ?>
                             <div class="form-text">Le mot de passe doit contenir au moins 8 caractères.</div>
                         <?php endif; ?>
                     </div>
                     
                     <div class="mb-3">
                         <label for="confirm_password" class="form-label">Confirmer le nouveau mot de passe <span class="text-danger">*</span></label>
                         <input type="password" class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" 
                             id="confirm_password" name="confirm_password" required>
                         <?php if (isset($errors['confirm_password'])): ?>
                             <div class="invalid-feedback"><?= $errors['confirm_password'] ?></div>
                         <?php endif; ?>
                     </div>
                     
                     <div class="d-flex justify-content-end">
                         <a href="index.php?page=user&action=view&id=<?= $user['id'] ?>" class="btn btn-outline-secondary me-2">Annuler</a>
                         <button type="submit" class="btn btn-primary">Changer le mot de passe</button>
                     </div>
                 </form>
             </div>
         </div>
     </div>
    public function updateRole($id, $nom, $description = '') {
        $query = "UPDATE roles SET nom = :nom, description = :description WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        // Securisation des données
        $nom = htmlspecialchars(strip_tags($nom));
        $description = htmlspecialchars(strip_tags($description));
        $id = (int) $id;
        
        // Liaison des parametres
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Supprime un role
     * @param int $id ID du role
     * @return bool Resultat de l'operation
     */
    public function deleteRole($id) {
        // D'abord supprimer les permissions associées
        $this->deleteRolePermissions($id);
        
        // Ensuite supprimer le role
        $query = "DELETE FROM roles WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        // Securisation des données
        $id = (int) $id;
        
        // Liaison des parametres
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Attribue une permission a un role
     * @param int $roleId ID du role
     * @param int $permissionId ID de la permission
     * @return bool Resultat de l'operation
     */
    public function assignPermission($roleId, $permissionId) {
        $query = "INSERT INTO role_permissions (role_id, permission_id) 
                  VALUES (:role_id, :permission_id)";
        $stmt = $this->conn->prepare($query);
        
        // Liaison des parametres
        $stmt->bindParam(':role_id', $roleId, PDO::PARAM_INT);
        $stmt->bindParam(':permission_id', $permissionId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Recupere le role d'un utilisateur
     * @param int $userId ID de l'utilisateur
     * @return object|false Donnees du role ou false si non trouve
     */
    public function getUserRole($userId) {
        $query = "SELECT r.* FROM roles r 
                  INNER JOIN users u ON r.id = u.role_id 
                  WHERE u.id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    
    /**
     * Supprime toutes les permissions d'un role
     * @param int $roleId ID du role
     * @return bool Resultat de l'operation
     */
    public function deleteRolePermissions($roleId) {
        $query = "DELETE FROM role_permissions WHERE role_id = :role_id";
        $stmt = $this->conn->prepare($query);
        
        // Liaison des parametres
        $stmt->bindParam(':role_id', $roleId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Verifie si un role est utilise par au moins un utilisateur
     * @param int $roleId ID du role
     * @return bool True si le role est utilise, false sinon
     */
    public function roleHasUsers($roleId) {
        $query = "SELECT COUNT(*) as count FROM users WHERE role_id = :role_id";
        $stmt = $this->conn->prepare($query);
        
        // Liaison des parametres
        $stmt->bindParam(':role_id', $roleId, PDO::PARAM_INT);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['count'] > 0;
    }
    
    /**
     * Recupere toutes les permissions disponibles
     * @return array Liste des permissions
     */
    public function getAllPermissions() {
        $query = "SELECT * FROM permissions ORDER BY nom ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
