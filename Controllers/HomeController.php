<?php
namespace Controllers;

use Models\Auth;

class HomeController
{
    private $auth;

    public function __construct()
    {
        $this->auth = Auth::getInstance();
    }

    /**
     * Affiche la page d'accueil
     *
     * @return void
     */
    public function index()
    {
        // Si l'utilisateur n'est pas connecté, rediriger vers la page de connexion
        if (!$this->auth->isLoggedIn()) {
            header('Location: index.php?page=login');
            exit;
        }

        // Définir le titre de la page
        $pageTitle = 'Accueil';

        // Récupérer les informations de l'utilisateur connecté
        $user = $this->auth->getUser();

        // Charger la vue de la page d'accueil
        require_once __DIR__ . '/../Views/home.php';
    }

    public function profile()
    {
        $auth = \Models\Auth::getInstance();

        if (!$auth->isLoggedIn()) {
            header('Location: index.php?page=login');
            exit;
        }

        $userData = $auth->getUser(); // tableau
        $userModel = new \Models\User();
        $user = $userModel->find($userData['id']); // ici on utilise ['id']

        if (!$user) {
            die("Erreur : utilisateur introuvable.");
        }

        $role = $user['role'];
        $pageTitle = 'Mon Profil';

        require_once __DIR__ . '/../Views/profile.php';
    }

}
