<?php
namespace Controllers;

use Models\Student;
use Models\Auth;

class StudentController
{
    private $studentModel;
    private $auth;
    
    public function __construct()
    {
        $this->studentModel = new Student();
        $this->auth = Auth::getInstance();
        
        // Vérifier si l'utilisateur est admin ou pilote pour toutes les méthodes de ce contrôleur
        if (!$this->auth->hasRole(['admin', 'pilote'])) {
            $this->redirectToUnauthorized();
        }
    }
    
    /**
     * Affiche la liste des étudiants avec recherche et pagination
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
        
        // Récupérer les étudiants et leur nombre total
        $students = $this->studentModel->getAll($filters, $perPage, $offset);
        $totalStudents = $this->studentModel->countAll($filters);
        
        // Calculer le nombre total de pages
        $totalPages = ceil($totalStudents / $perPage);
        
        // Afficher la vue
        $pageTitle = 'Gestion des étudiants';
        
        require_once 'Views/Student/index.php';
    }
    
    /**
     * Affiche le formulaire de création d'un étudiant
     */
    public function create()
    {
        // Initialiser les données et les erreurs
        $student = [
            'firstname' => '',
            'lastname' => '',
            'email' => '',
            'is_active' => 1
        ];
        $errors = [];
        
        // Afficher la vue
        $pageTitle = 'Créer un compte étudiant';
        
        require_once 'Views/Student/form.php';
    }
    
    /**
     * Traite la création d'un étudiant
     */
    public function store()
    {
        // Vérifier si la requête est en POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=students');
            exit;
        }
        
        // Récupérer et nettoyer les données
        $student = [
            'firstname' => trim($_POST['firstname'] ?? ''),
            'lastname' => trim($_POST['lastname'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];
        
        // Valider les données
        $errors = $this->validateStudentData($student, true);
        
        if (empty($errors)) {
            // Créer l'étudiant
            $result = $this->studentModel->create($student);
            
            if ($result) {
                // Rediriger avec un message de succès
                $_SESSION['flash'] = [
                    'type' => 'success',
                    'message' => 'Le compte étudiant a été créé avec succès.'
                ];
                header('Location: index.php?page=students');
                exit;
            } else {
                $errors['general'] = "Une erreur est survenue lors de la création du compte. L'adresse email est peut-être déjà utilisée.";
            }
        }
        
        // En cas d'erreur, afficher le formulaire à nouveau
        $pageTitle = 'Créer un compte étudiant';
        require_once 'Views/Student/form.php';
    }
    
    /**
     * Affiche le formulaire de modification d'un étudiant
     */
    public function edit()
    {
        // Récupérer l'ID de l'étudiant
        $id = $_GET['id'] ?? 0;
        
        // Récupérer les données de l'étudiant
        $student = $this->studentModel->getById($id);
        
        if (!$student) {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => "L'étudiant demandé n'existe pas."
            ];
            header('Location: index.php?page=students');
            exit;
        }
        
        // Initialiser les erreurs
        $errors = [];
        
        // Afficher la vue
        $pageTitle = 'Modifier un compte étudiant';
        require_once 'Views/Student/form.php';
    }
    
    /**
     * Traite la modification d'un étudiant
     */
    public function update()
    {
        // Vérifier si la requête est en POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=students');
            exit;
        }
        
        // Récupérer l'ID de l'étudiant
        $id = $_POST['id'] ?? 0;
        
        // Vérifier si l'étudiant existe
        $existingStudent = $this->studentModel->getById($id);
        
        if (!$existingStudent) {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => "L'étudiant demandé n'existe pas."
            ];
            header('Location: index.php?page=students');
            exit;
        }
        
        // Récupérer et nettoyer les données
        $student = [
            'id' => $id,
            'firstname' => trim($_POST['firstname'] ?? ''),
            'lastname' => trim($_POST['lastname'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];
        
        // Si les champs de mot de passe sont vides, ne pas les modifier
        $isPasswordChange = !empty($student['password']) || !empty($student['password_confirm']);
        
        // Valider les données
        $errors = $this->validateStudentData($student, false, $isPasswordChange);
        
        if (empty($errors)) {
            // Mettre à jour l'étudiant
            $result = $this->studentModel->update($id, $student);
            
            if ($result) {
                // Rediriger avec un message de succès
                $_SESSION['flash'] = [
                    'type' => 'success',
                    'message' => 'Le compte étudiant a été modifié avec succès.'
                ];
                header('Location: index.php?page=students');
                exit;
            } else {
                $errors['general'] = "Une erreur est survenue lors de la modification du compte. L'adresse email est peut-être déjà utilisée.";
            }
        }
        
        // En cas d'erreur, afficher le formulaire à nouveau
        $pageTitle = 'Modifier un compte étudiant';
        require_once 'Views/Student/form.php';
    }
    
    /**
     * Affiche la page de confirmation de suppression d'un étudiant
     */
    public function confirmDelete()
    {
        // Récupérer l'ID de l'étudiant
        $id = $_GET['id'] ?? 0;
        
        // Récupérer les données de l'étudiant
        $student = $this->studentModel->getById($id);
        
        if (!$student) {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => "L'étudiant demandé n'existe pas."
            ];
            header('Location: index.php?page=students');
            exit;
        }
        
        // Afficher la vue
        $pageTitle = 'Confirmer la suppression';
        require_once 'Views/Student/delete.php';
    }
    
    /**
     * Traite la suppression d'un étudiant
     */
    public function destroy()
    {
        // Récupérer l'ID de l'étudiant (maintenant depuis POST)
        $id = $_POST['id'] ?? 0;
        
        if (!$id) {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => "Identifiant d'étudiant invalide."
            ];
            header('Location: index.php?page=students');
            exit;
        }
        
        // Supprimer l'étudiant
        $result = $this->studentModel->delete($id);
        
        if ($result) {
            $_SESSION['flash'] = [
                'type' => 'success',
                'message' => "Le compte étudiant a été supprimé avec succès."
            ];
        } else {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => "Une erreur est survenue lors de la suppression du compte étudiant."
            ];
        }
        
        header('Location: index.php?page=students');
        exit;
    }
    
    /**
     * Affiche les statistiques des étudiants
     */
    public function statistics()
    {
        // Récupérer les statistiques des étudiants
        $statistics = $this->studentModel->getGlobalStatistics();
        
        // Afficher la vue
        $pageTitle = 'Statistiques des étudiants';
        require_once 'Views/Student/statistics.php';
    }
    
    /**
     * Redirige vers la page d'accès non autorisé
     */
    private function redirectToUnauthorized()
    {
        $_SESSION['flash'] = [
            'type' => 'danger',
            'message' => "Vous n'avez pas les droits nécessaires pour accéder à cette page."
        ];
        header('Location: index.php?page=unauthorized');
        exit;
    }
    
    /**
     * Valide les données d'un étudiant
     * 
     * @param array $data Données de l'étudiant
     * @param bool $isNewStudent S'il s'agit d'un nouvel étudiant
     * @param bool $validatePassword Si le mot de passe doit être validé
     * @return array Erreurs de validation
     */
    private function validateStudentData($data, $isNewStudent = true, $validatePassword = true)
    {
        $errors = [];
        
        // Valider le prénom
        if (empty($data['firstname'])) {
            $errors['firstname'] = "Le prénom est obligatoire.";
        } elseif (strlen($data['firstname']) > 50) {
            $errors['firstname'] = "Le prénom ne peut pas dépasser 50 caractères.";
        }
        
        // Valider le nom
        if (empty($data['lastname'])) {
            $errors['lastname'] = "Le nom est obligatoire.";
        } elseif (strlen($data['lastname']) > 50) {
            $errors['lastname'] = "Le nom ne peut pas dépasser 50 caractères.";
        }
        
        // Valider l'email
        if (empty($data['email'])) {
            $errors['email'] = "L'adresse email est obligatoire.";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "L'adresse email n'est pas valide.";
        } elseif (strlen($data['email']) > 100) {
            $errors['email'] = "L'adresse email ne peut pas dépasser 100 caractères.";
        }
        
        // Valider le mot de passe si nécessaire
        if ($validatePassword) {
            if (empty($data['password'])) {
                $errors['password'] = "Le mot de passe est obligatoire.";
            } elseif (strlen($data['password']) < 8) {
                $errors['password'] = "Le mot de passe doit contenir au moins 8 caractères.";
            } elseif ($data['password'] !== $data['password_confirm']) {
                $errors['password_confirm'] = "Les mots de passe ne correspondent pas.";
            }
        }
        
        return $errors;
    }
}
