<?php
namespace Controllers;

use Models\User;
use Models\Auth;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


class UserController 
{
    private $userModel;
    private $auth;
    
    public function __construct() 
    {
        $this->userModel = new User();
        $this->auth = Auth::getInstance();
    }
    
    /**
     * Affiche le profil de l'utilisateur connecté
     */
    public function profile() 
    {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user']) || !isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
            header('Location: index.php?page=login');
            exit;
        }

        // Récupérer l'ID de l'utilisateur connecté
        $userId = $_SESSION['user']['id'];

        // Récupérer les informations de l'utilisateur depuis la base de données
        $user = $this->userModel->find($userId);

        if (!$user) {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'Utilisateur non trouvé.'
            ];
            header('Location: index.php');
            exit;
        }

        // Définir le titre de la page
        $pageTitle = 'Mon profil';

        // Charger la vue du profil
        require_once __DIR__ . '/../Views/User/profile.php';
    }
}
