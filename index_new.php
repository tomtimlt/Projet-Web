<?php
/**
 * Point d'entree principal de l'application
 * Gere le routage des requetes vers les controleurs appropries
 */

// Demarrage de la session de facon securisee
session_start();

// Configuration des en-tetes de securite
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-inline'");

// Inclure les fichiers de configuration et les classes
require_once 'config/database.php';
require_once 'models/User.php';
require_once 'models/Entreprise.php';
require_once 'models/Evaluation.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/DashboardController.php';

// Instancier la base de donnees et les modeles
$database = new Database();
$db = $database->getConnection();
$userModel = new User($db);
$entrepriseModel = new Entreprise($db);
$evaluationModel = new Evaluation($db);

// Instancier les controleurs
$authController = new AuthController($userModel);
$dashboardController = new DashboardController($entrepriseModel);

// Gestion simple du routage
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'dashboard';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Routage des requetes vers les controleurs appropries
switch ($controller) {
    case 'auth':
        switch ($action) {
            case 'login':
                $authController->showLoginForm();
                break;
            case 'process_login':
                $authController->login();
                break;
            case 'logout':
                $authController->logout();
                break;
            case 'register':
                $authController->showRegisterForm();
                break;
            case 'process_register':
                $authController->register();
                break;
            default:
                $authController->showLoginForm();
                break;
        }
        break;
    
    case 'dashboard':
        if (isset($_SESSION['user_id'])) {
            $dashboardController->index();
        } else {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        break;
        
    case 'entreprise':
        require_once 'controllers/EntrepriseController.php';
        $entrepriseController = new EntrepriseController($entrepriseModel, $evaluationModel);
        
        switch ($action) {
            case 'index':
                $entrepriseController->index();
                break;
            case 'view':
                $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
                $entrepriseController->view($id);
                break;
            case 'create':
                $entrepriseController->create();
                break;
            case 'store':
                $entrepriseController->store();
                break;
            case 'edit':
                $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
                $entrepriseController->edit($id);
                break;
            case 'update':
                $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
                $entrepriseController->update($id);
                break;
            case 'delete':
                $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
                $entrepriseController->delete($id);
                break;
            case 'evaluate':
                $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
                $entrepriseController->evaluate($id);
                break;
            default:
                $entrepriseController->index();
                break;
        }
        break;
        
    // Action par defaut (redirection vers la page d'accueil ou login)
    default:
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=dashboard');
        } else {
            header('Location: index.php?controller=auth&action=login');
        }
        exit;
}
