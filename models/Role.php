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
     * Met a jour un role existant
     * @param int $id ID du role
     * @param string $nom Nouveau nom du role
     * @param string $description Nouvelle description du role
     * @return bool Resultat de l'operation
     */
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
