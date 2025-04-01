<?php
namespace Controllers;

use Models\User;
use Models\Auth;

class AuthController 
{
    private $userModel;
    private $auth;
    
    public function __construct() 
    {
        $this->userModel = new User();
        $this->auth = Auth::getInstance();
    }
    
    /**
     * Affiche le formulaire de connexion
     * 
     * @return void
     */
    public function loginForm() 
    {
        // Si l'utilisateur est déjà connecté, rediriger vers la page d'accueil
        if ($this->auth->isLoggedIn()) {
            header('Location: index.php?page=home');
            exit;
        }
        
        // Charger la vue du formulaire de connexion
        require_once __DIR__ . '/../Views/Auth/login.php';
    }
    
    /**
     * Traite la soumission du formulaire de connexion
     * 
     * @return void
     */
    public function login() 
    {
        // Si l'utilisateur est déjà connecté, le rediriger vers l'accueil
        if ($this->auth->isLoggedIn()) {
            header('Location: index.php?page=home');
            exit;
        }
        
        // Vérifier la méthode de requête
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->loginForm();
            return;
        }
        
        // Récupérer les données
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Valider les données
        $errors = [];
        
        if (empty($email)) {
            $errors['email'] = 'L\'adresse email est requise';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'L\'adresse email n\'est pas valide';
        }
        
        if (empty($password)) {
            $errors['password'] = 'Le mot de passe est requis';
        }
        
        // S'il y a des erreurs, réafficher le formulaire avec les erreurs
        if (!empty($errors)) {
            $data = [
                'email' => $email,
                'errors' => $errors
            ];
            
            // Charger la vue du formulaire de connexion avec les erreurs
            require_once __DIR__ . '/../Views/Auth/login.php';
            return;
        }
        
        // Tenter l'authentification
        $user = $this->userModel->authenticate($email, $password);
        
        if (!$user) {
            $errors['auth'] = 'Identifiants incorrects';
            
            $data = [
                'email' => $email,
                'errors' => $errors
            ];
            
            // Charger la vue du formulaire de connexion avec les erreurs
            require_once __DIR__ . '/../Views/Auth/login.php';
            return;
        }
        
        // Connecter l'utilisateur
        $this->auth->login($user);
        
        // Ajouter un message flash pour confirmer la connexion
        \Utils\Flash::setFlash('success', 'Vous êtes maintenant connecté!');
        
        // Rediriger vers la page d'accueil
        header('Location: index.php?page=home');
        exit;
    }
    
    /**
     * Déconnecte l'utilisateur
     * 
     * @return void
     */
    public function logout() 
    {
        $this->auth->logout();
        
        // Rediriger vers la page de connexion
        header('Location: index.php?page=login');
        exit;
    }
    
    /**
     * Vérifie si l'utilisateur a accès à une fonctionnalité spécifique
     * 
     * @param string $permission Code de la permission à vérifier
     * @param bool $redirect Si true, redirige vers la page de connexion si non autorisé
     * @return bool
     */
    public function checkPermission($permission, $redirect = true) 
    {
        // Vérifier si l'utilisateur est connecté et a la session valide
        if (!$this->auth->validateSession()) {
            if ($redirect) {
                header('Location: index.php?page=login');
                exit;
            }
            return false;
        }
        
        // Vérifier si l'utilisateur a la permission requise
        if (!$this->auth->hasPermission($permission)) {
            if ($redirect) {
                header('Location: index.php?page=unauthorized');
                exit;
            }
            return false;
        }
        
        return true;
    }
    
    /**
     * Affiche la page d'accès non autorisé
     * 
     * @return void
     */
    public function unauthorized() 
    {
        // Charger la vue d'accès non autorisé
        require_once __DIR__ . '/../Views/Auth/unauthorized.php';
    }
}
