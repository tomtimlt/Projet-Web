<?php
namespace Models;

class Auth 
{
    private static $instance = null;
    private $user = null;
    private $permissions = [];
    
    private function __construct() 
    {
        // Charger les permissions depuis la configuration
        $this->permissions = require_once __DIR__ . '/../Config/roles.php';
        
        // Démarrer la session si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            // Configuration sécurisée des cookies de session
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                ini_set('session.cookie_secure', 1);
            }
            
            session_start();
        }
        
        // Vérifier si l'utilisateur est déjà connecté
        if (isset($_SESSION['user'])) {
            $this->user = $_SESSION['user'];
        }
    }
    
    private function __clone() {}
    
    public static function getInstance() 
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Connecte un utilisateur et crée sa session
     * 
     * @param array $user Données de l'utilisateur
     * @return void
     */
    public function login($user) 
    {
        // Régénérer l'ID de session pour prévenir la fixation de session
        session_regenerate_id(true);
        
        $this->user = $user;
        $_SESSION['user'] = $user;
        $_SESSION['auth'] = true;
        
        // Stocker l'adresse IP et l'agent utilisateur pour la sécurité
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $_SESSION['last_activity'] = time();
    }
    
    /**
     * Déconnecte l'utilisateur actuel
     * 
     * @return void
     */
    public function logout() 
    {
        $this->user = null;
        
        // Détruire toutes les données de session
        $_SESSION = [];
        
        // Détruire le cookie de session
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        // Détruire la session
        session_destroy();
    }
    
    /**
     * Vérifie si l'utilisateur est connecté
     * 
     * @return bool
     */
    public function isLoggedIn() 
    {
        return $this->user !== null;
    }
    
    /**
     * Vérifie si l'utilisateur a un rôle spécifique
     * 
     * @param string|array $roles Le ou les rôles à vérifier
     * @return bool
     */
    public function hasRole($roles) 
    {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        
        return in_array($this->user['role'], $roles);
    }
    
    /**
     * Vérifie si l'utilisateur a une permission spécifique
     * 
     * @param string $permission Le code de la permission à vérifier
     * @return bool
     */
    public function hasPermission($permission) 
    {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        // Vérifier si la permission existe
        if (!isset($this->permissions['permissions'][$permission])) {
            return false;
        }
        
        // Vérifier si le rôle de l'utilisateur est autorisé pour cette permission
        return in_array($this->user['role'], $this->permissions['permissions'][$permission]['roles']);
    }
    
    /**
     * Obtient l'utilisateur actuellement connecté
     * 
     * @return array|null Les données de l'utilisateur ou null s'il n'est pas connecté
     */
    public function getUser() 
    {
        return $this->user;
    }
    
    /**
     * Obtient l'ID de l'utilisateur actuellement connecté
     * 
     * @return int|null L'ID de l'utilisateur ou null s'il n'est pas connecté
     */
    public function getUserId() 
    {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return $this->user['id'];
    }
    
    /**
     * Vérifie si la session est valide (pour prévenir les détournements de session)
     * 
     * @return bool
     */
    public function validateSession() 
    {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        // Vérifier l'IP et l'agent utilisateur
        if (
            $_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR'] ||
            $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']
        ) {
            // Possible détournement de session, déconnecter l'utilisateur
            $this->logout();
            return false;
        }
        
        // Vérifier l'inactivité (30 minutes)
        if (time() - $_SESSION['last_activity'] > 1800) {
            $this->logout();
            return false;
        }
        
        // Mettre à jour le timestamp de dernière activité
        $_SESSION['last_activity'] = time();
        
        return true;
    }
    
    /**
     * Obtient toutes les permissions pour un rôle spécifique
     * 
     * @param string $role Le rôle à vérifier
     * @return array Liste des permissions
     */
    public function getPermissionsForRole($role) 
    {
        $rolePermissions = [];
        
        foreach ($this->permissions['permissions'] as $code => $permission) {
            if (in_array($role, $permission['roles'])) {
                $rolePermissions[$code] = $permission;
            }
        }
        
        return $rolePermissions;
    }
    
    /**
     * Obtient toutes les permissions et rôles configurés
     * 
     * @return array Configuration des permissions et rôles
     */
    public function getPermissions() 
    {
        return $this->permissions;
    }
}
