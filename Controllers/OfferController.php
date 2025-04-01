<?php
namespace Controllers;

use Models\Offer;
use Models\Skill;
use Models\Company;
use Models\Auth;

class OfferController 
{
    private $offerModel;
    private $skillModel;
    private $companyModel;
    private $auth;
    private $errors = [];
    
    public function __construct() 
    {
        $this->offerModel = new Offer();
        $this->skillModel = new Skill();
        $this->companyModel = new Company();
        $this->auth = Auth::getInstance();
    }
    
    /**
     * Affiche la liste des offres avec filtres
     */
    public function index() 
    {
        // Récupérer les paramètres de filtrage
        $filters = [
            'keyword' => $_GET['keyword'] ?? '',
            'company_id' => isset($_GET['company_id']) && is_numeric($_GET['company_id']) ? $_GET['company_id'] : '',
            'location' => $_GET['location'] ?? '',
            'start_date' => $_GET['start_date'] ?? '',
            'end_date' => $_GET['end_date'] ?? '',
            'status' => $_GET['status'] ?? '',
            'skills' => isset($_GET['skills']) && is_array($_GET['skills']) ? $_GET['skills'] : []
        ];
        
        // Pagination
        $page = isset($_GET['page_num']) ? max(1, intval($_GET['page_num'])) : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        // Récupérer les offres filtrées
        $offers = $this->offerModel->getAll($filters, $limit, $offset);
        $totalOffers = $this->offerModel->countAll($filters);
        $totalPages = ceil($totalOffers / $limit);
        
        // Récupérer les données pour les filtres
        $companies = $this->companyModel->getAll();
        $skills = $this->skillModel->getAllByCategory();
        
        // Définir le titre de la page
        $pageTitle = 'Liste des offres de stage';
        
        // Charger la vue
        require_once __DIR__ . '/../Views/Offer/index.php';
    }
    
    /**
     * Affiche les détails d'une offre
     */
    public function view() 
    {
        // Récupérer l'ID de l'offre
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$id) {
            $this->redirectWithError('offers', 'Offre non trouvée');
        }
        
        // Récupérer les détails de l'offre
        $offer = $this->offerModel->getById($id);
        
        if (!$offer) {
            $this->redirectWithError('offers', 'Offre non trouvée');
        }
        
        // Définir le titre de la page
        $pageTitle = 'Détails de l\'offre - ' . $offer['title'];
        
        // Charger la vue
        require_once __DIR__ . '/../Views/Offer/view.php';
    }
    
    /**
     * Affiche le formulaire de création d'une offre
     */
    public function create() 
    {
        // Vérifier que l'utilisateur a la permission de créer une offre et n'est pas un étudiant
        if (!$this->auth->hasPermission('SFx8') || $this->auth->hasRole(['etudiant'])) {
            $this->flash->setFlash('error', "Vous n'avez pas les droits pour créer une offre.");
            header('Location: index.php?page=offers');
            exit;
        }
        
        // Récupérer les entreprises et compétences pour le formulaire
        $companies = $this->companyModel->getAll();
        $skillsByCategory = $this->skillModel->getAllByCategory();
        
        // Définir le titre de la page
        $pageTitle = 'Créer une offre de stage';
        
        // Initialiser les données du formulaire
        $offer = [
            'title' => '',
            'description' => '',
            'company_id' => '',
            'location' => '',
            'salary' => '',
            'start_date' => '',
            'end_date' => '',
            'skills_required' => '',
            'status' => 'active',
            'skills' => []
        ];
        
        $errors = [];
        
        // Charger la vue du formulaire
        require_once __DIR__ . '/../Views/Offer/form.php';
    }
    
    /**
     * Traite la soumission du formulaire de création d'offre
     */
    public function store() 
    {
        // Vérifier que l'utilisateur a la permission de créer une offre
        if (!$this->auth->hasPermission('SFx8')) {
            header('Location: index.php?page=unauthorized');
            exit;
        }
        
        // Vérifier que la méthode est bien POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=offers&action=create');
            exit;
        }
        
        // Récupérer et valider les données du formulaire
        $data = $this->validateOfferData($_POST);
        
        // S'il y a des erreurs, réafficher le formulaire avec les erreurs
        if (!empty($this->errors)) {
            // Récupérer les entreprises et compétences pour le formulaire
            $companies = $this->companyModel->getAll();
            $skillsByCategory = $this->skillModel->getAllByCategory();
            
            $pageTitle = 'Créer une offre de stage';
            $errors = $this->errors;
            $offer = $_POST;
            require_once __DIR__ . '/../Views/Offer/form.php';
            return;
        }
        
        // Créer l'offre
        $offerId = $this->offerModel->create($data);
        
        if (!$offerId) {
            $this->redirectWithError('offers', 'Une erreur est survenue lors de la création de l\'offre');
        }
        
        // Rediriger vers la page de détails de l'offre avec un message de succès
        $this->redirectWithSuccess('offers&action=view&id=' . $offerId, 'Offre créée avec succès');
    }
    
    /**
     * Affiche le formulaire de modification d'une offre
     */
    public function edit() 
    {
        // Vérifier que l'utilisateur a la permission de modifier une offre
        if (!$this->auth->hasPermission('SFx9')) {
            header('Location: index.php?page=unauthorized');
            exit;
        }
        
        // Récupérer l'ID de l'offre
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$id) {
            $this->redirectWithError('offers', 'Offre non trouvée');
        }
        
        // Récupérer les détails de l'offre
        $offer = $this->offerModel->getById($id);
        
        if (!$offer) {
            $this->redirectWithError('offers', 'Offre non trouvée');
        }
        
        // Préparer les données pour le formulaire
        $skillIds = array_map(function($skill) {
            return $skill['id'];
        }, $offer['skills']);
        $offer['skills'] = $skillIds;
        
        // Récupérer les entreprises et compétences pour le formulaire
        $companies = $this->companyModel->getAll();
        $skillsByCategory = $this->skillModel->getAllByCategory();
        
        // Définir le titre de la page
        $pageTitle = 'Modifier l\'offre - ' . $offer['title'];
        
        $errors = [];
        
        // Charger la vue du formulaire
        require_once __DIR__ . '/../Views/Offer/form.php';
    }
    
    /**
     * Traite la soumission du formulaire de modification d'offre
     */
    public function update() 
    {
        // Vérifier que l'utilisateur a la permission de modifier une offre
        if (!$this->auth->hasPermission('SFx9')) {
            header('Location: index.php?page=unauthorized');
            exit;
        }
        
        // Vérifier que la méthode est bien POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=offers');
            exit;
        }
        
        // Récupérer l'ID de l'offre
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        if (!$id) {
            $this->redirectWithError('offers', 'Offre non trouvée');
        }
        
        // Vérifier que l'offre existe
        if (!$this->offerModel->getById($id)) {
            $this->redirectWithError('offers', 'Offre non trouvée');
        }
        
        // Récupérer et valider les données du formulaire
        $data = $this->validateOfferData($_POST);
        
        // S'il y a des erreurs, réafficher le formulaire avec les erreurs
        if (!empty($this->errors)) {
            // Récupérer les entreprises et compétences pour le formulaire
            $companies = $this->companyModel->getAll();
            $skillsByCategory = $this->skillModel->getAllByCategory();
            
            $pageTitle = 'Modifier l\'offre';
            $errors = $this->errors;
            $offer = $_POST;
            $offer['id'] = $id;
            require_once __DIR__ . '/../Views/Offer/form.php';
            return;
        }
        
        // Mettre à jour l'offre
        $result = $this->offerModel->update($id, $data);
        
        if (!$result) {
            $this->redirectWithError('offers&action=edit&id=' . $id, 'Une erreur est survenue lors de la mise à jour de l\'offre');
        }
        
        // Rediriger vers la page de détails de l'offre avec un message de succès
        $this->redirectWithSuccess('offers&action=view&id=' . $id, 'Offre mise à jour avec succès');
    }
    
    /**
     * Supprime une offre
     */
    public function delete() 
    {
        // Vérifier que l'utilisateur a la permission de supprimer une offre
        if (!$this->auth->hasPermission('SFx10')) {
            header('Location: index.php?page=unauthorized');
            exit;
        }
        
        // Récupérer l'ID de l'offre
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$id) {
            $this->redirectWithError('offers', 'Offre non trouvée');
        }
        
        // Vérifier que l'offre existe
        if (!$this->offerModel->getById($id)) {
            $this->redirectWithError('offers', 'Offre non trouvée');
        }
        
        // Supprimer l'offre
        $result = $this->offerModel->delete($id);
        
        if (!$result) {
            $this->redirectWithError('offers', 'Une erreur est survenue lors de la suppression de l\'offre');
        }
        
        // Rediriger vers la liste des offres avec un message de succès
        $this->redirectWithSuccess('offers', 'Offre supprimée avec succès');
    }
    
    /**
     * Affiche les statistiques des offres
     */
    public function stats() 
    {
        // Vérifier que l'utilisateur a la permission de consulter les statistiques
        if (!$this->auth->hasPermission('SFx11')) {
            header('Location: index.php?page=unauthorized');
            exit;
        }
        
        // Récupérer les statistiques
        $statistics = $this->offerModel->getStatistics();
        
        // Définir le titre de la page
        $pageTitle = 'Statistiques des offres de stage';
        
        // Charger la vue
        require_once __DIR__ . '/../Views/Offer/stats.php';
    }
    
    /**
     * Affiche les statistiques des offres
     */
    public function statistics() 
    {
        // Vérifier que l'utilisateur a la permission de voir les statistiques
        if (!$this->auth->hasRole(['admin', 'pilote'])) {
            header('Location: index.php?page=unauthorized');
            exit;
        }
        
        // Récupérer les statistiques générales des offres
        $stats = [
            'totalOffers' => $this->offerModel->countAll([]),
            'activeOffers' => $this->offerModel->countAll(['status' => 'active']),
            'inactiveOffers' => $this->offerModel->countAll(['status' => 'inactive']),
            'filledOffers' => $this->offerModel->countAll(['status' => 'filled']),
            
            // Statistiques par entreprise
            'offersByCompany' => $this->offerModel->getOfferCountByCompany(),
            
            // Statistiques par lieu
            'offersByLocation' => $this->offerModel->getOfferCountByLocation(),
            
            // Statistiques par période
            'upcomingOffers' => $this->offerModel->getUpcomingOffers(),
            'currentOffers' => $this->offerModel->getCurrentOffers(),
            'pastOffers' => $this->offerModel->getPastOffers(),
            
            // Statistiques par compétence
            'popularSkills' => $this->offerModel->getPopularSkills(),
        ];
        
        // Définir le titre de la page
        $pageTitle = 'Statistiques des offres de stage';
        
        // Charger la vue des statistiques
        require_once __DIR__ . '/../Views/Offer/statistics.php';
    }
    
    /**
     * Valide les données du formulaire d'offre
     * 
     * @param array $data Données brutes du formulaire
     * @return array Données validées
     */
    private function validateOfferData($data) 
    {
        $this->errors = [];
        $validatedData = [];
        
        // Validation du titre
        if (empty($data['title'])) {
            $this->errors['title'] = 'Le titre est obligatoire';
        } elseif (strlen($data['title']) > 150) {
            $this->errors['title'] = 'Le titre ne doit pas dépasser 150 caractères';
        } else {
            $validatedData['title'] = htmlspecialchars(trim($data['title']));
        }
        
        // Validation de la description
        if (empty($data['description'])) {
            $this->errors['description'] = 'La description est obligatoire';
        } else {
            $validatedData['description'] = htmlspecialchars(trim($data['description']));
        }
        
        // Validation de l'entreprise
        if (empty($data['company_id']) || !is_numeric($data['company_id'])) {
            $this->errors['company_id'] = 'L\'entreprise est obligatoire';
        } else {
            $validatedData['company_id'] = (int)$data['company_id'];
            
            // Vérifier que l'entreprise existe
            if (!$this->companyModel->getById($validatedData['company_id'])) {
                $this->errors['company_id'] = 'L\'entreprise sélectionnée n\'existe pas';
            }
        }
        
        // Validation du lieu
        if (empty($data['location'])) {
            $this->errors['location'] = 'Le lieu est obligatoire';
        } elseif (strlen($data['location']) > 100) {
            $this->errors['location'] = 'Le lieu ne doit pas dépasser 100 caractères';
        } else {
            $validatedData['location'] = htmlspecialchars(trim($data['location']));
        }
        
        // Validation du salaire
        if (!empty($data['salary'])) {
            if (!is_numeric(str_replace(',', '.', $data['salary']))) {
                $this->errors['salary'] = 'Le salaire doit être un nombre';
            } else {
                $validatedData['salary'] = floatval(str_replace(',', '.', $data['salary']));
            }
        } else {
            $validatedData['salary'] = null;
        }
        
        // Validation des dates
        if (empty($data['start_date'])) {
            $this->errors['start_date'] = 'La date de début est obligatoire';
        } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['start_date'])) {
            $this->errors['start_date'] = 'Format de date invalide (YYYY-MM-DD)';
        } else {
            $validatedData['start_date'] = $data['start_date'];
        }
        
        if (empty($data['end_date'])) {
            $this->errors['end_date'] = 'La date de fin est obligatoire';
        } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['end_date'])) {
            $this->errors['end_date'] = 'Format de date invalide (YYYY-MM-DD)';
        } else {
            $validatedData['end_date'] = $data['end_date'];
        }
        
        // Vérifier que la date de fin est après la date de début
        if (isset($validatedData['start_date']) && isset($validatedData['end_date'])) {
            if (strtotime($validatedData['end_date']) <= strtotime($validatedData['start_date'])) {
                $this->errors['end_date'] = 'La date de fin doit être postérieure à la date de début';
            }
        }
        
        // Validation des compétences requises (texte libre)
        if (!empty($data['skills_required'])) {
            $validatedData['skills_required'] = htmlspecialchars(trim($data['skills_required']));
        } else {
            $validatedData['skills_required'] = null;
        }
        
        // Validation du statut
        $validStatuses = ['active', 'inactive', 'filled'];
        if (isset($data['status']) && in_array($data['status'], $validStatuses)) {
            $validatedData['status'] = $data['status'];
        } else {
            $validatedData['status'] = 'active';
        }
        
        // Validation des compétences (IDs)
        if (isset($data['skills']) && is_array($data['skills'])) {
            $validSkills = [];
            foreach ($data['skills'] as $skillId) {
                if (is_numeric($skillId) && $this->skillModel->getById($skillId)) {
                    $validSkills[] = (int)$skillId;
                }
            }
            $validatedData['skills'] = $validSkills;
        } else {
            $validatedData['skills'] = [];
        }
        
        return $validatedData;
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
