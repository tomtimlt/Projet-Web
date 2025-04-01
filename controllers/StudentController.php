<?php
namespace Controllers;

use Models\Student;
use Models\Auth;
use Utils\Flash;
use Utils\Validator;

class StudentController
{
    private $student;
    private $auth;
    
    public function __construct()
    {
        $this->student = new Student();
        $this->auth = Auth::getInstance();
        
        // Vérifier si l'utilisateur est connecté
        if (!$this->auth->isLoggedIn()) {
            Flash::setFlash('error', 'Vous devez être connecté pour accéder à cette page.');
            header('Location: index.php?page=home');
            exit;
        }
    }
    
    /**
     * Affiche la liste des étudiants
     */
    public function index()
    {
        // Vérifier les permissions (Admin ou Pilote uniquement)
        if (!$this->auth->hasRole(['admin', 'pilote'])) {
            Flash::setFlash('error', 'Vous n\'avez pas les permissions nécessaires pour accéder à cette page.');
            header('Location: index.php?page=home');
            exit;
        }
        
        // Récupérer les filtres de recherche
        $filters = [];
        if (isset($_GET['firstname'])) $filters['firstname'] = $_GET['firstname'];
        if (isset($_GET['lastname'])) $filters['lastname'] = $_GET['lastname'];
        if (isset($_GET['email'])) $filters['email'] = $_GET['email'];
        
        // Pagination
        $page = isset($_GET['page_num']) ? max(1, intval($_GET['page_num'])) : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        // Récupérer les étudiants
        $students = $this->student->getAll($filters, $limit, $offset);
        
        // Charger la vue
        require_once 'Views/Student/index.php';
    }
    
    /**
     * Affiche le formulaire pour créer un étudiant
     */
    public function create()
    {
        // Vérifier les permissions (Admin ou Pilote uniquement)
        if (!$this->auth->hasRole(['admin', 'pilote'])) {
            Flash::setFlash('error', 'Vous n\'avez pas les permissions nécessaires pour accéder à cette page.');
            header('Location: index.php?page=home');
            exit;
        }
        
        // Charger la vue
        require_once 'Views/Student/form.php';
    }
    
    /**
     * Traite le formulaire de création d'étudiant
     */
    public function store()
    {
        // Vérifier les permissions (Admin ou Pilote uniquement)
        if (!$this->auth->hasRole(['admin', 'pilote'])) {
            Flash::setFlash('error', 'Vous n\'avez pas les permissions nécessaires pour accéder à cette page.');
            header('Location: index.php?page=home');
            exit;
        }
        
        // Vérifier la méthode de requête
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Flash::setFlash('error', 'Méthode non autorisée.');
            header('Location: index.php?page=students');
            exit;
        }
        
        // Récupérer et valider les données
        $data = [
            'firstname' => $_POST['firstname'] ?? '',
            'lastname' => $_POST['lastname'] ?? '',
            'email' => $_POST['email'] ?? '',
            'password' => $_POST['password'] ?? ''
        ];
        
        // Validation côté serveur
        $validator = new Validator();
        $validator->validate($data['firstname'], 'Prénom', 'required|alpha|min:2|max:50');
        $validator->validate($data['lastname'], 'Nom', 'required|alpha|min:2|max:50');
        $validator->validate($data['email'], 'Email', 'required|email|max:100');
        $validator->validate($data['password'], 'Mot de passe', 'required|min:8|max:255');
        
        if (!$validator->isValid()) {
            Flash::setFlash('error', $validator->getErrors()[0]);
            header('Location: index.php?page=create-student');
            exit;
        }
        
        // Créer l'étudiant
        $result = $this->student->create($data);
        
        if ($result) {
            Flash::setFlash('success', 'L\'étudiant a été créé avec succès.');
            header('Location: index.php?page=students');
        } else {
            Flash::setFlash('error', 'Une erreur est survenue lors de la création de l\'étudiant.');
            header('Location: index.php?page=create-student');
        }
        exit;
    }
    
    /**
     * Affiche les détails d'un étudiant
     */
    public function show()
    {
        // Vérifier les permissions (Admin ou Pilote uniquement)
        if (!$this->auth->hasRole(['admin', 'pilote'])) {
            Flash::setFlash('error', 'Vous n\'avez pas les permissions nécessaires pour accéder à cette page.');
            header('Location: index.php?page=home');
            exit;
        }
        
        // Récupérer l'ID de l'étudiant
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($id <= 0) {
            Flash::setFlash('error', 'ID d\'étudiant invalide.');
            header('Location: index.php?page=students');
            exit;
        }
        
        // Récupérer l'étudiant
        $student = $this->student->getById($id);
        
        if (!$student) {
            Flash::setFlash('error', 'Étudiant non trouvé.');
            header('Location: index.php?page=students');
            exit;
        }
        
        // Récupérer les statistiques
        $stats = $this->student->getStatistics($id);
        
        // Charger la vue
        require_once 'Views/Student/show.php';
    }
    
    /**
     * Affiche le formulaire pour modifier un étudiant
     */
    public function edit()
    {
        // Vérifier les permissions (Admin ou Pilote uniquement)
        if (!$this->auth->hasRole(['admin', 'pilote'])) {
            Flash::setFlash('error', 'Vous n\'avez pas les permissions nécessaires pour accéder à cette page.');
            header('Location: index.php?page=home');
            exit;
        }
        
        // Récupérer l'ID de l'étudiant
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($id <= 0) {
            Flash::setFlash('error', 'ID d\'étudiant invalide.');
            header('Location: index.php?page=students');
            exit;
        }
        
        // Récupérer l'étudiant
        $student = $this->student->getById($id);
        
        if (!$student) {
            Flash::setFlash('error', 'Étudiant non trouvé.');
            header('Location: index.php?page=students');
            exit;
        }
        
        // Charger la vue
        require_once 'Views/Student/form.php';
    }
    
    /**
     * Traite le formulaire de modification d'étudiant
     */
    public function update()
    {
        // Vérifier les permissions (Admin ou Pilote uniquement)
        if (!$this->auth->hasRole(['admin', 'pilote'])) {
            Flash::setFlash('error', 'Vous n\'avez pas les permissions nécessaires pour accéder à cette page.');
            header('Location: index.php?page=home');
            exit;
        }
        
        // Vérifier la méthode de requête
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Flash::setFlash('error', 'Méthode non autorisée.');
            header('Location: index.php?page=students');
            exit;
        }
        
        // Récupérer l'ID de l'étudiant
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        if ($id <= 0) {
            Flash::setFlash('error', 'ID d\'étudiant invalide.');
            header('Location: index.php?page=students');
            exit;
        }
        
        // Récupérer et valider les données
        $data = [
            'firstname' => $_POST['firstname'] ?? '',
            'lastname' => $_POST['lastname'] ?? '',
            'email' => $_POST['email'] ?? '',
            'password' => $_POST['password'] ?? '' // Optionnel pour la mise à jour
        ];
        
        // Validation côté serveur
        $validator = new Validator();
        $validator->validate($data['firstname'], 'Prénom', 'required|alpha|min:2|max:50');
        $validator->validate($data['lastname'], 'Nom', 'required|alpha|min:2|max:50');
        $validator->validate($data['email'], 'Email', 'required|email|max:100');
        
        // Valider le mot de passe uniquement s'il est fourni
        if (!empty($data['password'])) {
            $validator->validate($data['password'], 'Mot de passe', 'min:8|max:255');
        } else {
            // Si le mot de passe est vide, le supprimer pour éviter de l'enregistrer comme vide
            unset($data['password']);
        }
        
        if (!$validator->isValid()) {
            Flash::setFlash('error', $validator->getErrors()[0]);
            header('Location: index.php?page=edit-student&id=' . $id);
            exit;
        }
        
        // Mettre à jour l'étudiant
        $result = $this->student->update($id, $data);
        
        if ($result) {
            Flash::setFlash('success', 'L\'étudiant a été mis à jour avec succès.');
            header('Location: index.php?page=show-student&id=' . $id);
        } else {
            Flash::setFlash('error', 'Une erreur est survenue lors de la mise à jour de l\'étudiant.');
            header('Location: index.php?page=edit-student&id=' . $id);
        }
        exit;
    }
    
    /**
     * Affiche la page de confirmation de suppression
     */
    public function delete()
    {
        // Vérifier les permissions (Admin uniquement)
        if (!$this->auth->hasRole(['admin'])) {
            Flash::setFlash('error', 'Vous n\'avez pas les permissions nécessaires pour accéder à cette page.');
            header('Location: index.php?page=home');
            exit;
        }
        
        // Récupérer l'ID de l'étudiant
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($id <= 0) {
            Flash::setFlash('error', 'ID d\'étudiant invalide.');
            header('Location: index.php?page=students');
            exit;
        }
        
        // Récupérer l'étudiant
        $student = $this->student->getById($id);
        
        if (!$student) {
            Flash::setFlash('error', 'Étudiant non trouvé.');
            header('Location: index.php?page=students');
            exit;
        }
        
        // Charger la vue
        require_once 'Views/Student/delete.php';
    }
    
    /**
     * Traite la suppression d'un étudiant
     */
    public function destroy()
    {
        // Vérifier les permissions (Admin uniquement)
        if (!$this->auth->hasRole(['admin'])) {
            Flash::setFlash('error', 'Vous n\'avez pas les permissions nécessaires pour accéder à cette page.');
            header('Location: index.php?page=home');
            exit;
        }
        
        // Vérifier la méthode de requête
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Flash::setFlash('error', 'Méthode non autorisée.');
            header('Location: index.php?page=students');
            exit;
        }
        
        // Récupérer l'ID de l'étudiant
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        if ($id <= 0) {
            Flash::setFlash('error', 'ID d\'étudiant invalide.');
            header('Location: index.php?page=students');
            exit;
        }
        
        // Supprimer l'étudiant
        $result = $this->student->delete($id);
        
        if ($result) {
            Flash::setFlash('success', 'L\'étudiant a été supprimé avec succès.');
        } else {
            Flash::setFlash('error', 'Une erreur est survenue lors de la suppression de l\'étudiant. Vérifiez qu\'il n\'a pas de candidatures actives.');
        }
        
        header('Location: index.php?page=students');
        exit;
    }
    
    /**
     * Affiche les statistiques globales des étudiants
     */
    public function statistics()
    {
        // Vérifier les permissions (Admin ou Pilote uniquement)
        if (!$this->auth->hasRole(['admin', 'pilote'])) {
            Flash::setFlash('error', 'Vous n\'avez pas les permissions nécessaires pour accéder à cette page.');
            header('Location: index.php?page=home');
            exit;
        }
        
        // Récupérer les statistiques globales
        $globalStats = $this->student->getGlobalStatistics();
        
        // Charger la vue
        require_once 'Views/Student/statistics.php';
    }
}
