<?php
namespace Controllers;

use Models\Company;
use Models\CompanyRating;
use Models\Auth;

class CompanyController 
{
    private $companyModel;
    private $ratingModel;
    private $auth;
    
    public function __construct() 
    {
        $this->companyModel = new Company();
        $this->ratingModel = new CompanyRating();
        $this->auth = Auth::getInstance();
    }
    
    /**
     * Affiche la liste des entreprises avec filtres
     */
    public function index() 
    {
        // Récupérer les paramètres de filtrage
        $filters = [
            'name' => $_GET['name'] ?? '',
            'sector' => $_GET['sector'] ?? '',
            'size' => $_GET['size'] ?? '',
            'city' => $_GET['city'] ?? ''
        ];
        
        // Récupérer la liste des entreprises filtrées
        $companies = $this->companyModel->getAll($filters);
        
        // Récupérer la liste des secteurs pour le filtre
        $sectors = $this->companyModel->getAllSectors();
        
        // Définir le titre de la page
        $pageTitle = 'Liste des entreprises';
        
        // Charger la vue
        require_once __DIR__ . '/../Views/Company/index.php';
    }
    
    /**
     * Affiche les détails d'une entreprise
     */
    public function view() 
    {
        // Récupérer l'ID de l'entreprise
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$id) {
            $this->redirectWithError('companies', 'Entreprise non trouvée');
        }
        
        // Récupérer les détails de l'entreprise
        $company = $this->companyModel->getById($id);
        
        if (!$company) {
            $this->redirectWithError('companies', 'Entreprise non trouvée');
        }
        
        // Récupérer les évaluations de l'entreprise
        $ratings = $this->ratingModel->getAllByCompany($id);
        
        // Déterminer si l'utilisateur connecté a déjà évalué cette entreprise
        $hasRated = false;
        if ($this->auth->isLoggedIn()) {
            $userId = $this->auth->getUser()['id'];
            $hasRated = $this->ratingModel->hasUserRated($id, $userId);
        }
        
        // Définir le titre de la page
        $pageTitle = 'Détails de l\'entreprise - ' . $company['name'];
        
        // Charger la vue
        require_once __DIR__ . '/../Views/Company/view.php';
    }
    
    /**
     * Affiche le formulaire de création d'une entreprise
     */
    public function create() 
    {
        // Vérifier que l'utilisateur a la permission de créer une entreprise
        if (!$this->auth->hasPermission('SFx3')) {
            header('Location: index.php?page=unauthorized');
            exit;
        }
        
        // Définir le titre de la page
        $pageTitle = 'Ajouter une entreprise';
        
        // Initialiser les données du formulaire
        $company = [
            'name' => '',
            'address' => '',
            'postal_code' => '',
            'city' => '',
            'country' => 'France',
            'phone' => '',
            'email' => '',
            'website' => '',
            'description' => '',
            'sector' => '',
            'size' => ''
        ];
        
        $errors = [];
        
        // Charger la vue du formulaire
        require_once __DIR__ . '/../Views/Company/form.php';
    }
    
    /**
     * Traite la soumission du formulaire de création d'entreprise
     */
    public function store() 
    {
        // Vérifier que l'utilisateur a la permission de créer une entreprise
        if (!$this->auth->hasPermission('SFx3')) {
            header('Location: index.php?page=unauthorized');
            exit;
        }
        
        // Vérifier que la méthode est bien POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=companies&action=create');
            exit;
        }
        
        // Récupérer et nettoyer les données du formulaire
        $company = $this->sanitizeCompanyData($_POST);
        
        // Valider les données
        $errors = $this->validateCompanyData($company);
        
        // S'il y a des erreurs, réafficher le formulaire avec les erreurs
        if (!empty($errors)) {
            $pageTitle = 'Ajouter une entreprise';
            require_once __DIR__ . '/../Views/Company/form.php';
            return;
        }
        
        // Ajouter l'ID de l'utilisateur qui crée l'entreprise
        $company['created_by'] = $this->auth->getUser()['id'];
        
        // Enregistrer l'entreprise
        $result = $this->companyModel->create($company);
        
        if (!$result) {
            $errors['general'] = 'Une erreur est survenue lors de la création de l\'entreprise';
            $pageTitle = 'Ajouter une entreprise';
            require_once __DIR__ . '/../Views/Company/form.php';
            return;
        }
        
        // Rediriger vers la liste des entreprises avec un message de succès
        $this->redirectWithSuccess('companies', 'Entreprise créée avec succès');
    }
    
    /**
     * Affiche le formulaire de modification d'une entreprise
     */
    public function edit() 
    {
        // Vérifier que l'utilisateur a la permission de modifier une entreprise
        if (!$this->auth->hasPermission('SFx4')) {
            header('Location: index.php?page=unauthorized');
            exit;
        }
        
        // Récupérer l'ID de l'entreprise
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$id) {
            $this->redirectWithError('companies', 'Entreprise non trouvée');
        }
        
        // Récupérer les détails de l'entreprise
        $company = $this->companyModel->getById($id);
        
        if (!$company) {
            $this->redirectWithError('companies', 'Entreprise non trouvée');
        }
        
        // Définir le titre de la page
        $pageTitle = 'Modifier l\'entreprise - ' . $company['name'];
        
        $errors = [];
        
        // Charger la vue du formulaire
        require_once __DIR__ . '/../Views/Company/form.php';
    }
    
    /**
     * Traite la soumission du formulaire de modification d'entreprise
     */
    public function update() 
    {
        // Vérifier que l'utilisateur a la permission de modifier une entreprise
        if (!$this->auth->hasPermission('SFx4')) {
            header('Location: index.php?page=unauthorized');
            exit;
        }
        
        // Vérifier que la méthode est bien POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=companies');
            exit;
        }
        
        // Récupérer l'ID de l'entreprise
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        if (!$id) {
            $this->redirectWithError('companies', 'Entreprise non trouvée');
        }
        
        // Vérifier que l'entreprise existe
        if (!$this->companyModel->getById($id)) {
            $this->redirectWithError('companies', 'Entreprise non trouvée');
        }
        
        // Récupérer et nettoyer les données du formulaire
        $company = $this->sanitizeCompanyData($_POST);
        $company['id'] = $id;
        
        // Valider les données
        $errors = $this->validateCompanyData($company);
        
        // S'il y a des erreurs, réafficher le formulaire avec les erreurs
        if (!empty($errors)) {
            $pageTitle = 'Modifier l\'entreprise - ' . $company['name'];
            require_once __DIR__ . '/../Views/Company/form.php';
            return;
        }
        
        // Mettre à jour l'entreprise
        $result = $this->companyModel->update($id, $company);
        
        if (!$result) {
            $errors['general'] = 'Une erreur est survenue lors de la mise à jour de l\'entreprise';
            $pageTitle = 'Modifier l\'entreprise - ' . $company['name'];
            require_once __DIR__ . '/../Views/Company/form.php';
            return;
        }
        
        // Rediriger vers la liste des entreprises avec un message de succès
        $this->redirectWithSuccess('companies', 'Entreprise mise à jour avec succès');
    }
    
    /**
     * Supprime une entreprise
     */
    public function delete() 
    {
        // Vérifier que l'utilisateur a la permission de supprimer une entreprise
        if (!$this->auth->hasPermission('SFx6')) {
            header('Location: index.php?page=unauthorized');
            exit;
        }
        
        // Récupérer l'ID de l'entreprise
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$id) {
            $this->redirectWithError('companies', 'Entreprise non trouvée');
        }
        
        // Vérifier que l'entreprise existe
        if (!$this->companyModel->getById($id)) {
            $this->redirectWithError('companies', 'Entreprise non trouvée');
        }
        
        // Supprimer l'entreprise
        $result = $this->companyModel->delete($id);
        
        if (!$result) {
            $this->redirectWithError('companies', 'Une erreur est survenue lors de la suppression de l\'entreprise');
        }
        
        // Rediriger vers la liste des entreprises avec un message de succès
        $this->redirectWithSuccess('companies', 'Entreprise supprimée avec succès');
    }
    
    /**
     * Affiche les statistiques des entreprises
     */
    public function stats() 
    {
        // Vérifier que l'utilisateur a la permission de consulter les statistiques
        if (!$this->auth->hasPermission('SFx7')) {
            header('Location: index.php?page=unauthorized');
            exit;
        }
        
        // Récupérer les statistiques
        $statistics = $this->companyModel->getStatistics();
        
        // Définir le titre de la page
        $pageTitle = 'Statistiques des entreprises';
        
        // Charger la vue
        require_once __DIR__ . '/../Views/Company/stats.php';
    }
    
    /**
     * Gère l'évaluation d'une entreprise
     */
    public function rate()
    {
        // Vérifier que l'utilisateur est connecté et a la permission d'évaluer
        if (!$this->auth->hasPermission('SFx5')) {
            header('Location: index.php?page=unauthorized');
            exit;
        }

        // Vérifier que la méthode est bien POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=companies');
            exit;
        }

        // Récupérer l'ID de l'entreprise et la note
        $companyId = isset($_POST['entreprise_id']) ? (int)$_POST['entreprise_id'] : 0;
        $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
        $comment = isset($_POST['commentaire']) ? trim($_POST['commentaire']) : ''; // <-- ICI la bonne clé

        if (!$companyId || $rating < 1 || $rating > 5) {
            $this->redirectWithError('companies&action=view&id=' . $companyId, 'Données d\'évaluation invalides');
        }

        // Vérifier que l'entreprise existe
        if (!$this->companyModel->getById($companyId)) {
            $this->redirectWithError('companies', 'Entreprise non trouvée');
        }

        // Préparer les données de l'évaluation
        $ratingData = [
            'company_id' => $companyId,
            'user_id' => $this->auth->getUser()['id'],
            'rating' => $rating,
            'comment' => $comment
        ];

        // Enregistrer l'évaluation
        $result = $this->ratingModel->saveRating($ratingData);

        if (!$result) {
            $this->redirectWithError('companies&action=view&id=' . $companyId, 'Une erreur est survenue lors de l\'enregistrement de l\'évaluation');
        }

        // Rediriger vers la page de détails de l'entreprise avec un message de succès
        $this->redirectWithSuccess('companies&action=view&id=' . $companyId, 'Évaluation enregistrée avec succès');
    }


    /**
     * Supprime une évaluation
     */
    public function deleteRating() 
    {
        // Vérifier que l'utilisateur est connecté
        if (!$this->auth->isLoggedIn()) {
            header('Location: index.php?page=login');
            exit;
        }
        
        // Récupérer l'ID de l'évaluation et de l'entreprise
        $ratingId = isset($_GET['rating_id']) ? (int)$_GET['rating_id'] : 0;
        $companyId = isset($_GET['company_id']) ? (int)$_GET['company_id'] : 0;
        
        if (!$ratingId || !$companyId) {
            $this->redirectWithError('companies', 'Données invalides');
        }
        
        // Récupérer les informations de l'utilisateur connecté
        $userId = $this->auth->getUser()['id'];
        $userRole = $this->auth->getUser()['role'];
        
        // Supprimer l'évaluation (avec vérification des droits dans le modèle)
        $result = $this->ratingModel->deleteRating($ratingId, $userId, $userRole);
        
        if (!$result) {
            $this->redirectWithError('companies&action=view&id=' . $companyId, 'Vous n\'êtes pas autorisé à supprimer cette évaluation');
        }
        
        // Rediriger vers la page de détails de l'entreprise avec un message de succès
        $this->redirectWithSuccess('companies&action=view&id=' . $companyId, 'Évaluation supprimée avec succès');
    }
    
    /**
     * Nettoie les données du formulaire d'entreprise
     * 
     * @param array $data Données brutes du formulaire
     * @return array Données nettoyées
     */
    private function sanitizeCompanyData($data) 
    {
        return [
            'name' => htmlspecialchars(trim($data['name'] ?? '')),
            'address' => htmlspecialchars(trim($data['address'] ?? '')),
            'postal_code' => htmlspecialchars(trim($data['postal_code'] ?? '')),
            'city' => htmlspecialchars(trim($data['city'] ?? '')),
            'country' => htmlspecialchars(trim($data['country'] ?? 'France')),
            'phone' => htmlspecialchars(trim($data['phone'] ?? '')),
            'email' => filter_var(trim($data['email'] ?? ''), FILTER_SANITIZE_EMAIL),
            'website' => filter_var(trim($data['website'] ?? ''), FILTER_SANITIZE_URL),
            'description' => htmlspecialchars(trim($data['description'] ?? '')),
            'sector' => htmlspecialchars(trim($data['sector'] ?? '')),
            'size' => in_array($data['size'] ?? '', ['TPE', 'PME', 'ETI', 'GE']) ? $data['size'] : ''
        ];
    }
    
    /**
     * Valide les données du formulaire d'entreprise
     * 
     * @param array $data Données à valider
     * @return array Erreurs de validation
     */
    private function validateCompanyData($data) 
    {
        $errors = [];
        
        // Validation du nom (obligatoire)
        if (empty($data['name'])) {
            $errors['name'] = 'Le nom de l\'entreprise est obligatoire';
        } elseif (strlen($data['name']) > 100) {
            $errors['name'] = 'Le nom de l\'entreprise ne doit pas dépasser 100 caractères';
        }
        
        // Validation de l'adresse (obligatoire)
        if (empty($data['address'])) {
            $errors['address'] = 'L\'adresse est obligatoire';
        } elseif (strlen($data['address']) > 255) {
            $errors['address'] = 'L\'adresse ne doit pas dépasser 255 caractères';
        }
        
        // Validation du code postal (obligatoire)
        if (empty($data['postal_code'])) {
            $errors['postal_code'] = 'Le code postal est obligatoire';
        } elseif (strlen($data['postal_code']) > 10) {
            $errors['postal_code'] = 'Le code postal ne doit pas dépasser 10 caractères';
        }
        
        // Validation de la ville (obligatoire)
        if (empty($data['city'])) {
            $errors['city'] = 'La ville est obligatoire';
        } elseif (strlen($data['city']) > 100) {
            $errors['city'] = 'La ville ne doit pas dépasser 100 caractères';
        }
        
        // Validation du pays (obligatoire)
        if (empty($data['country'])) {
            $errors['country'] = 'Le pays est obligatoire';
        } elseif (strlen($data['country']) > 100) {
            $errors['country'] = 'Le pays ne doit pas dépasser 100 caractères';
        }
        
        // Validation du téléphone (optionnel)
        if (!empty($data['phone']) && strlen($data['phone']) > 20) {
            $errors['phone'] = 'Le numéro de téléphone ne doit pas dépasser 20 caractères';
        }
        
        // Validation de l'email (optionnel mais doit être valide si fourni)
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'L\'adresse email n\'est pas valide';
        } elseif (!empty($data['email']) && strlen($data['email']) > 100) {
            $errors['email'] = 'L\'adresse email ne doit pas dépasser 100 caractères';
        }
        
        // Validation du site web (optionnel mais doit être valide si fourni)
        if (!empty($data['website']) && !filter_var($data['website'], FILTER_VALIDATE_URL)) {
            $errors['website'] = 'L\'URL du site web n\'est pas valide';
        } elseif (!empty($data['website']) && strlen($data['website']) > 255) {
            $errors['website'] = 'L\'URL du site web ne doit pas dépasser 255 caractères';
        }
        
        // Validation du secteur (optionnel)
        if (!empty($data['sector']) && strlen($data['sector']) > 100) {
            $errors['sector'] = 'Le secteur ne doit pas dépasser 100 caractères';
        }
        
        // Validation de la taille (optionnel mais doit être valide si fournie)
        if (!empty($data['size']) && !in_array($data['size'], ['TPE', 'PME', 'ETI', 'GE'])) {
            $errors['size'] = 'La taille sélectionnée n\'est pas valide';
        }
        
        return $errors;
    }
    
    /**
     * Redirige avec un message d'erreur
     * 
     * @param string $page Page de redirection
     * @param string $message Message d'erreur
     */
    private function redirectWithError($page, $message) 
    {
        $_SESSION['flash'] = [
            'type' => 'danger',
            'message' => $message
        ];
        
        header("Location: index.php?page=$page");
        exit;
    }
    
    /**
     * Redirige avec un message de succès
     * 
     * @param string $page Page de redirection
     * @param string $message Message de succès
     */
    private function redirectWithSuccess($page, $message) 
    {
        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => $message
        ];
        
        header("Location: index.php?page=$page");
        exit;
    }
}
