<?php
/**
 * Point d'entrée principal de l'application
 * Gère le routage et l'initialisation des composants essentiels
 */

// Définir le répertoire racine
define('ROOT_DIR', __DIR__);

// Activer l'affichage des erreurs en développement
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuration des sessions sécurisées
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    ini_set('session.cookie_secure', 1);
}

// Chargement des classes (autoloader simple)
spl_autoload_register(function ($className) {
    $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
    $file = ROOT_DIR . DIRECTORY_SEPARATOR . $className . '.php';
    
    if (file_exists($file)) {
        require_once $file;
    }
});

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Instancier l'authentification
$auth = Models\Auth::getInstance();

// Récupérer la page demandée
$page = $_GET['page'] ?? 'home';
// Récupérer l'action spécifique si elle existe
$action = $_GET['action'] ?? null;

// Définir les routes disponibles
$routes = [
    // Routes d'authentification
    'login' => ['controller' => 'AuthController', 'action' => 'loginForm'],
    'authenticate' => ['controller' => 'AuthController', 'action' => 'login'],
    'logout' => ['controller' => 'AuthController', 'action' => 'logout'],
    'unauthorized' => ['controller' => 'AuthController', 'action' => 'unauthorized'],
    
    // Routes principales de l'application
    'home' => ['controller' => 'HomeController', 'action' => 'index'],
    'profile' => ['controller' => 'HomeController', 'action' => 'profile'],
    'password' => ['controller' => 'UserController', 'action' => 'changePassword'],
    
    // Routes des entreprises
    'companies' => ['controller' => 'CompanyController', 'action' => 'index'],
    'company_view' => ['controller' => 'CompanyController', 'action' => 'view'],
    'company_create' => ['controller' => 'CompanyController', 'action' => 'create'],
    'company_edit' => ['controller' => 'CompanyController', 'action' => 'edit'],
    'company_delete' => ['controller' => 'CompanyController', 'action' => 'delete'],
    'company_stats' => ['controller' => 'CompanyController', 'action' => 'stats'],
    'company_rate' => ['controller' => 'CompanyController', 'action' => 'rate'],
    'company_delete_rating' => ['controller' => 'CompanyController', 'action' => 'deleteRating'],
    
    // Routes des évaluations
    'evaluations' => ['controller' => 'EvaluationController', 'action' => 'mesEvaluations'],
    'evaluation_entreprise' => ['controller' => 'EvaluationController', 'action' => 'parEntreprise'],
    'evaluation_evaluer' => ['controller' => 'EvaluationController', 'action' => 'evaluer'],
    'evaluation_supprimer' => ['controller' => 'EvaluationController', 'action' => 'supprimer'],
    
    // Routes des offres
    'offers' => ['controller' => 'OfferController', 'action' => 'index'],
    'offer_view' => ['controller' => 'OfferController', 'action' => 'view'],
    'offer_create' => ['controller' => 'OfferController', 'action' => 'create'],
    'offer_edit' => ['controller' => 'OfferController', 'action' => 'edit'],
    'offer_delete' => ['controller' => 'OfferController', 'action' => 'delete'],
    'offer_stats' => ['controller' => 'OfferController', 'action' => 'stats'],
    
    // Routes des pilotes
    'pilots' => ['controller' => 'PilotController', 'action' => 'index'],
    'pilot_view' => ['controller' => 'PilotController', 'action' => 'view'],
    'pilot_create' => ['controller' => 'PilotController', 'action' => 'create'],
    'pilot_edit' => ['controller' => 'PilotController', 'action' => 'edit'],
    'pilot_delete' => ['controller' => 'PilotController', 'action' => 'delete'],
    
    // Routes des étudiants
    'students' => ['controller' => 'StudentController', 'action' => 'index'],
    'student_show' => ['controller' => 'StudentController', 'action' => 'show'],
    'student_create' => ['controller' => 'StudentController', 'action' => 'create'],
    'student_store' => ['controller' => 'StudentController', 'action' => 'store'],
    'student_edit' => ['controller' => 'StudentController', 'action' => 'edit'],
    'student_update' => ['controller' => 'StudentController', 'action' => 'update'],
    'student_delete' => ['controller' => 'StudentController', 'action' => 'delete'],
    'student_destroy' => ['controller' => 'StudentController', 'action' => 'destroy'],
    'student_statistics' => ['controller' => 'StudentController', 'action' => 'statistics'],
    
    // Routes des candidatures
    'wishlist' => ['controller' => 'WishlistController', 'action' => 'index'],
    'applications' => ['controller' => 'ApplicationController', 'action' => 'index'],
    
    // Routes des pages statiques
    'pages' => ['controller' => 'PageController', 'action' => 'legal'],

    // Profil
    'edit_profile' => ['controller' => 'UserController', 'action' => 'editProfile'],
    
    // Gestion des utilisateurs
    'users' => ['controller' => 'UserController', 'action' => 'index'],
    'user' => ['controller' => 'UserController', 'action' => 'view'],
];

// Gestion du paramètre action pour la rétrocompatibilité
if ($action && in_array($page, ['companies', 'offers', 'students', 'pilots', 'wishlist', 'applications', 'pages', 'evaluations', 'user', 'users'])) {
    // Mappage des actions spéciales
    $actionMappings = [
        'companies' => [
            'stats' => 'stats',
            'create' => 'create',
            'store' => 'store',
            'edit' => 'edit',
            'update' => 'update',
            'delete' => 'delete',
            'view' => 'view',
        ],
        'offers' => [
            'create' => 'create',
            'store' => 'store',
            'edit' => 'edit',
            'update' => 'update',
            'delete' => 'delete',
            'view' => 'view', 
            'statistics' => 'statistics'
        ],
        'students' => [
            'create' => 'create',
            'store' => 'store',
            'edit' => 'edit',
            'update' => 'update',
            'delete' => 'delete',
            'view' => 'view',
        ],
        'pilots' => [
            'create' => 'create',
            'store' => 'store',
            'edit' => 'edit',
            'update' => 'update',
            'delete' => 'delete',
            'view' => 'view',
        ],
        'wishlist' => [
            'add' => 'add',
            'remove' => 'remove',
        ],
        'applications' => [
            'apply' => 'apply',
            'view' => 'view',
            'update' => 'update',
            'store' => 'store',
        ],
        'pages' => [
            'legal' => 'legal',
            'privacy' => 'privacy',
        ],
        'evaluations' => [
            'mes_evaluations' => 'mesEvaluations',
            'par_entreprise' => 'parEntreprise',
            'evaluer' => 'evaluer',
            'supprimer' => 'supprimer',
        ],
        'user' => [
            'view' => 'view',
            'create' => 'create',
            'store' => 'create',
            'edit' => 'editProfile',
            'update' => 'editProfile',
            'delete' => 'delete',
            'change_password' => 'changePassword',
        ],
        'users' => [
            'index' => 'index',
        ]
    ];
    
    // Si l'action est définie dans le mappage, utiliser le contrôleur correspondant
    if (isset($actionMappings[$page][$action])) {
        // Récupérer le nom du contrôleur
        $controllerName = "Controllers\\";
        
        // Gestion correcte du singulier pour "companies" -> "Company"
        if ($page === 'companies') {
            $controllerName .= "CompanyController";
        } elseif ($page === 'wishlist') {
            $controllerName .= "WishlistController";
        } elseif ($page === 'applications') {
            $controllerName .= "ApplicationController";
        } elseif ($page === 'pages') {
            $controllerName .= "PageController";
        } elseif ($page === 'evaluations') {
            $controllerName .= "EvaluationController";
        } else {
            $controllerName .= ucfirst(rtrim($page, 's')) . "Controller";
        }
        
        $actionName = $actionMappings[$page][$action];
    } else {
        // Action inconnue, utiliser la route par défaut
        $controllerName = "Controllers\\{$routes[$page]['controller']}";
        $actionName = $routes[$page]['action'];
    }
} else {
    // Vérifier si la route existe
    if (!isset($routes[$page])) {
        // Route non trouvée, rediriger vers la page d'accueil
        header('Location: index.php?page=home');
        exit;
    }
    
    // Récupérer les informations de la route
    $route = $routes[$page];
    $controllerName = "Controllers\\{$route['controller']}";
    $actionName = $route['action'];
}

// Créer une instance du contrôleur
if (class_exists($controllerName)) {
    $controller = new $controllerName();
    
    // Vérifier si l'action existe
    if (method_exists($controller, $actionName)) {
        // Exécuter l'action
        $controller->$actionName();
    } else {
        // Action non trouvée, afficher une erreur
        die("L'action '{$actionName}' n'existe pas dans le contrôleur '{$controllerName}'");
    }
} else {
    // Contrôleur non trouvé, afficher une erreur
    die("Le contrôleur '{$controllerName}' n'existe pas");
}
