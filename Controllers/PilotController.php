<?php
namespace Controllers;

use Models\Pilot;
use Models\Auth;

class PilotController
{
    private $pilotModel;
    private $auth;
    
    public function __construct()
    {
        $this->pilotModel = new Pilot();
        $this->auth = Auth::getInstance();
        
        // Vérifier si l'utilisateur est admin pour toutes les méthodes de ce contrôleur
        if (!$this->auth->hasRole('admin')) {
            $this->redirectToUnauthorized();
        }
    }
    
    /**
     * Affiche la liste des pilotes avec recherche et pagination
     */
    public function index()
    {
        // Récupérer les paramètres de recherche et pagination
        $search = $_GET['search'] ?? '';
        $page = isset($_GET['page_num']) ? max(1, (int)$_GET['page_num']) : 1;
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        
        // Filtres de recherche
        $filters = [];
        if (!empty($search)) {
            $filters['search'] = $search;
        }
        
        // Récupérer les pilotes et leur nombre total
        $pilots = $this->pilotModel->getAll($filters, $perPage, $offset);
        $totalPilots = $this->pilotModel->countAll($filters);
        
        // Calculer le nombre total de pages
        $totalPages = ceil($totalPilots / $perPage);
        
        // Afficher la vue
        $pageTitle = 'Gestion des pilotes';
        
        require_once 'Views/Pilot/index.php';
    }
    
    /**
     * Affiche le formulaire de création d'un pilote
     */
    public function create()
    {
        // Initialiser les données et les erreurs
        $pilot = [
            'firstname' => '',
            'lastname' => '',
            'email' => ''
        ];
        $errors = [];
        
        // Afficher la vue
        $pageTitle = 'Créer un compte pilote';
        
        require_once 'Views/Pilot/form.php';
    }
    
    /**
     * Traite la création d'un pilote
     */
    public function store()
    {
        // Vérifier si la requête est en POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=pilots');
            exit;
        }
        
        // Récupérer et nettoyer les données
        $pilot = [
            'firstname' => trim($_POST['firstname'] ?? ''),
            'lastname' => trim($_POST['lastname'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? ''
        ];
        
        // Valider les données
        $errors = $this->validatePilotData($pilot, true);
        
        if (empty($errors)) {
            // Créer le pilote
            $result = $this->pilotModel->create($pilot);
            
            if ($result) {
                // Rediriger avec un message de succès
                $_SESSION['flash'] = [
                    'type' => 'success',
                    'message' => 'Le compte pilote a été créé avec succès.'
                ];
                header('Location: index.php?page=pilots');
                exit;
            } else {
                $errors['general'] = "Une erreur est survenue lors de la création du compte. L'adresse email est peut-être déjà utilisée.";
            }
        }
        
        // En cas d'erreur, afficher le formulaire à nouveau
        $pageTitle = 'Créer un compte pilote';
        require_once 'Views/Pilot/form.php';
    }
    
    /**
     * Affiche le formulaire de modification d'un pilote
     */
    public function edit()
    {
        // Récupérer l'ID du pilote
        $id = $_GET['id'] ?? 0;
        
        // Récupérer les données du pilote
        $pilot = $this->pilotModel->getById($id);
        
        if (!$pilot) {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'Le pilote demandé n\'existe pas.'
            ];
            header('Location: index.php?page=pilots');
            exit;
        }
        
        // Initialiser les erreurs
        $errors = [];
        
        // Afficher la vue
        $pageTitle = 'Modifier un compte pilote';
        require_once 'Views/Pilot/form.php';
    }
    
    /**
     * Traite la modification d'un pilote
     */
    public function update()
    {
        // Vérifier si la requête est en POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=pilots');
            exit;
        }
        
        // Récupérer l'ID du pilote
        $id = $_POST['id'] ?? 0;
        
        // Vérifier si le pilote existe
        $existingPilot = $this->pilotModel->getById($id);
        
        if (!$existingPilot) {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'Le pilote demandé n\'existe pas.'
            ];
            header('Location: index.php?page=pilots');
            exit;
        }
        
        // Récupérer et nettoyer les données
        $pilot = [
            'id' => $id,
            'firstname' => trim($_POST['firstname'] ?? ''),
            'lastname' => trim($_POST['lastname'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? ''
        ];
        
        // Si les champs de mot de passe sont vides, ne pas les modifier
        $isPasswordChange = !empty($pilot['password']) || !empty($pilot['password_confirm']);
        
        // Valider les données
        $errors = $this->validatePilotData($pilot, false, $isPasswordChange);
        
        if (empty($errors)) {
            // Mettre à jour le pilote
            $result = $this->pilotModel->update($id, $pilot);
            
            if ($result) {
                // Rediriger avec un message de succès
                $_SESSION['flash'] = [
                    'type' => 'success',
                    'message' => 'Le compte pilote a été modifié avec succès.'
                ];
                header('Location: index.php?page=pilots');
                exit;
            } else {
                $errors['general'] = "Une erreur est survenue lors de la modification du compte. L'adresse email est peut-être déjà utilisée.";
            }
        }
        
        // En cas d'erreur, afficher le formulaire à nouveau
        $pageTitle = 'Modifier un compte pilote';
        require_once 'Views/Pilot/form.php';
    }
    
    /**
     * Affiche la page de confirmation de suppression d'un pilote
     */
    public function confirmDelete()
    {
        // Récupérer l'ID du pilote
        $id = $_GET['id'] ?? 0;
        
        // Récupérer les données du pilote
        $pilot = $this->pilotModel->getById($id);
        
        if (!$pilot) {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'Le pilote demandé n\'existe pas.'
            ];
            header('Location: index.php?page=pilots');
            exit;
        }
        
        // Afficher la vue
        $pageTitle = 'Confirmer la suppression';
        require_once 'Views/Pilot/delete.php';
    }
    
    /**
     * Traite la suppression d'un pilote
     */
    public function delete()
    {
        // Vérifier que la requête est bien en POST pour plus de sécurité
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=pilots');
            exit;
        }
        
        // Récupérer l'ID du pilote depuis POST uniquement
        $id = $_POST['id'] ?? 0;
        
        if (!$id) {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'Identifiant de pilote invalide.'
            ];
            header('Location: index.php?page=pilots');
            exit;
        }
        
        // Vérifier que le pilote existe avant de tenter de le supprimer
        $pilot = $this->pilotModel->getById($id);
        if (!$pilot) {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'Le pilote demandé n\'existe pas.'
            ];
            header('Location: index.php?page=pilots');
            exit;
        }
        
        // Supprimer le pilote
        $result = $this->pilotModel->delete($id);
        
        if ($result) {
            $_SESSION['flash'] = [
                'type' => 'success',
                'message' => 'Le compte pilote a été supprimé avec succès.'
            ];
        } else {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'Une erreur est survenue lors de la suppression du compte pilote. Il pourrait être associé à des entreprises ou offres existantes.'
            ];
        }
        
        header('Location: index.php?page=pilots');
        exit;
    }
    
    /**
     * Affiche les statistiques des pilotes
     */
    public function stats()
    {
        // Récupérer les statistiques des pilotes
        $statistics = $this->pilotModel->getStats();
        
        // Afficher la vue
        $pageTitle = 'Statistiques des pilotes';
        require_once 'Views/Pilot/stats.php';
    }
    
    /**
     * Valide les données d'un pilote
     * 
     * @param array $data Données du pilote
     * @param bool $isNewPilot S'il s'agit d'un nouveau pilote
     * @param bool $validatePassword Si le mot de passe doit être validé
     * @return array Erreurs de validation
     */
    private function validatePilotData($data, $isNewPilot = true, $validatePassword = true)
    {
        $errors = [];
        
        // Valider le prénom
        if (empty($data['firstname'])) {
            $errors['firstname'] = 'Le prénom est obligatoire.';
        } elseif (strlen($data['firstname']) > 50) {
            $errors['firstname'] = 'Le prénom ne doit pas dépasser 50 caractères.';
        }
        
        // Valider le nom
        if (empty($data['lastname'])) {
            $errors['lastname'] = 'Le nom est obligatoire.';
        } elseif (strlen($data['lastname']) > 50) {
            $errors['lastname'] = 'Le nom ne doit pas dépasser 50 caractères.';
        }
        
        // Valider l'email
        if (empty($data['email'])) {
            $errors['email'] = 'L\'adresse email est obligatoire.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'L\'adresse email est invalide.';
        } elseif (strlen($data['email']) > 100) {
            $errors['email'] = 'L\'adresse email ne doit pas dépasser 100 caractères.';
        }
        
        // Valider le mot de passe (pour création ou modification explicite)
        if ($validatePassword) {
            if ($isNewPilot && empty($data['password'])) {
                $errors['password'] = 'Le mot de passe est obligatoire.';
            } elseif (!empty($data['password']) && strlen($data['password']) < 8) {
                $errors['password'] = 'Le mot de passe doit contenir au moins 8 caractères.';
            } elseif (!empty($data['password']) && $data['password'] !== $data['password_confirm']) {
                $errors['password_confirm'] = 'Les mots de passe ne correspondent pas.';
            }
        }
        
        return $errors;
    }
    
    /**
     * Redirige vers la page non autorisée
     */
    private function redirectToUnauthorized()
    {
        header('Location: index.php?page=auth&action=unauthorized');
        exit;
    }
}
