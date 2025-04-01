<?php
/**
 * Classe Middleware - Fournit des fonctions de verification d'acces pour l'application
 */
class Middleware {
    private static $role;
    
    /**
     * Initialise le middleware avec une instance du modele Role
     */
    public static function init() {
        require_once 'models/Role.php';
        self::$role = new Role();
    }
    
    /**
     * Verifie si l'utilisateur est connecte
     * @return bool True si l'utilisateur est connecte, false sinon
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Verifie si l'utilisateur est connecte, sinon redirige vers la page de connexion
     */
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            $_SESSION['error'] = "Vous devez être connecté pour accéder à cette page.";
            header('Location: index.php?controller=auth&action=login');
            exit();
        }
    }
    
    /**
     * Verifie si l'utilisateur connecte a une permission specifique
     * @param string $permission Nom de la permission a verifier
     * @return bool True si l'utilisateur a la permission, false sinon
     */
    public static function hasPermission($permission) {
        if (!self::isLoggedIn()) {
            return false;
        }
        
        return self::$role->hasPermission($_SESSION['user_id'], $permission);
    }
    
    /**
     * Verifie si l'utilisateur a une permission specifique, sinon affiche une erreur 403
     * @param string $permission Nom de la permission a verifier
     */
    public static function requirePermission($permission) {
        if (!self::hasPermission($permission)) {
            $_SESSION['error'] = "Vous n'avez pas les permissions nécessaires pour accéder à cette ressource.";
            header('HTTP/1.1 403 Forbidden');
            include('views/errors/403.php');
            exit();
        }
    }
    
    /**
     * Verifie si l'utilisateur a l'un des roles specifies
     * @param array $roles Liste des roles autorises
     * @return bool True si l'utilisateur a l'un des roles, false sinon
     */
    public static function hasRole($roles) {
        if (!self::isLoggedIn()) {
            return false;
        }
        
        $userRole = self::$role->getUserRole($_SESSION['user_id']);
        if (!$userRole) {
            return false;
        }
        
        return in_array($userRole->nom, (array) $roles);
    }
    
    /**
     * Verifie si l'utilisateur a l'un des roles specifies, sinon affiche une erreur 403
     * @param array $roles Liste des roles autorises
     */
    public static function requireRole($roles) {
        if (!self::hasRole($roles)) {
            $_SESSION['error'] = "Votre rôle ne vous permet pas d'accéder à cette ressource.";
            header('HTTP/1.1 403 Forbidden');
            include('views/errors/403.php');
            exit();
        }
    }
    
    /**
     * Verifie si l'utilisateur est proprietaire d'une ressource (ex: son evaluation)
     * @param string $table Nom de la table
     * @param int $resourceId ID de la ressource
     * @return bool True si l'utilisateur est proprietaire, false sinon
     */
    public static function isOwner($table, $resourceId) {
        if (!self::isLoggedIn()) {
            return false;
        }
        
        require_once 'config/database.php';
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "SELECT COUNT(*) FROM $table WHERE id = :id AND user_id = :user_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $resourceId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Verifie si l'utilisateur est proprietaire d'une ressource ou a une permission specifique
     * @param string $table Nom de la table
     * @param int $resourceId ID de la ressource
     * @param string $permission Permission alternative
     * @return bool True si l'utilisateur est proprietaire ou a la permission, false sinon
     */
    public static function isOwnerOr($table, $resourceId, $permission) {
        return self::isOwner($table, $resourceId) || self::hasPermission($permission);
    }
    
    /**
     * Verifie si l'utilisateur est proprietaire ou a une permission, sinon affiche erreur 403
     * @param string $table Nom de la table
     * @param int $resourceId ID de la ressource
     * @param string $permission Permission alternative
     */
    public static function requireOwnerOr($table, $resourceId, $permission) {
        if (!self::isOwnerOr($table, $resourceId, $permission)) {
            $_SESSION['error'] = "Vous n'êtes pas autorisé à effectuer cette action.";
            header('HTTP/1.1 403 Forbidden');
            include('views/errors/403.php');
            exit();
        }
    }
}
