<?php

namespace Controllers;

/**
 * Contrôleur pour la gestion des candidatures aux offres
 */
class ApplicationController
{
    private $applicationModel;
    private $offerModel;
    private $wishlistModel;
    private $auth;
    private $flash;
    private $validator;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->applicationModel = new \Models\Application();
        $this->offerModel = new \Models\Offer();
        $this->wishlistModel = new \Models\Wishlist();
        $this->auth = \Models\Auth::getInstance();
        $this->flash = new \Utils\Flash();
        $this->validator = new \Utils\Validator();
        
        // Vérifier que l'utilisateur est connecté
        if (!$this->auth->isLoggedIn()) {
            header('Location: index.php?page=login');
            exit;
        }
    }

    /**
     * Afficher la liste des candidatures de l'étudiant
     */
    public function index()
    {
        $userId = $this->auth->getUserId();
        $isAdmin = $this->auth->hasRole(['admin']);
        $studentId = isset($_GET['student_id']) && $isAdmin ? $_GET['student_id'] : $userId;
        
        // Si l'utilisateur n'est pas admin et tente de voir les candidatures d'un autre étudiant
        if (!$isAdmin && $studentId != $userId) {
            $this->flash->setFlash('error', "Vous n'avez pas les droits pour voir les candidatures d'un autre étudiant.");
            header('Location: index.php?page=applications');
            exit;
        }

        // Si admin sans student_id spécifié, afficher toutes les candidatures
        if ($isAdmin && !isset($_GET['student_id'])) {
            $applications = $this->applicationModel->getAllApplications();
            $stats = [
                'total' => count($applications),
                'pending' => array_reduce($applications, function($carry, $app) { return $carry + ($app['status'] == 'pending' ? 1 : 0); }, 0),
                'accepted' => array_reduce($applications, function($carry, $app) { return $carry + ($app['status'] == 'accepted' ? 1 : 0); }, 0),
                'rejected' => array_reduce($applications, function($carry, $app) { return $carry + ($app['status'] == 'rejected' ? 1 : 0); }, 0)
            ];
            
            // Charger la vue admin des candidatures
            require_once 'Views/Application/admin_index.php';
        } else {
            // Afficher les candidatures d'un étudiant spécifique
            $applications = $this->applicationModel->getApplicationsByStudentId($studentId);
            $stats = $this->applicationModel->getApplicationStatsByStudentId($studentId);
            
            // Si admin qui consulte un étudiant spécifique
            $student = null;
            if ($isAdmin && $studentId != $userId) {
                $userModel = new \Models\User();
                $student = $userModel->getUserById($studentId);
            }
            
            require_once 'Views/Application/index.php';
        }
    }

    /**
     * Afficher le formulaire de candidature pour une offre
     */
    public function apply()
    {
        // Vérifier que l'utilisateur est un étudiant
        if (!$this->auth->hasRole(['etudiant'])) {
            $this->flash->setFlash('error', "Vous n'avez pas les droits pour effectuer cette action.");
            header('Location: index.php?page=offers');
            exit;
        }

        // Vérifier que l'ID de l'offre est fourni
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            $this->flash->setFlash('error', "Identifiant d'offre invalide.");
            header('Location: index.php?page=offers');
            exit;
        }

        $offerId = (int)$_GET['id'];
        $userId = $this->auth->getUserId();

        // Vérifier que l'offre existe
        $offer = $this->offerModel->getById($offerId);
        if (!$offer) {
            $this->flash->setFlash('error', "Cette offre n'existe pas.");
            header('Location: index.php?page=offers');
            exit;
        }

        // Vérifier si l'étudiant a déjà postulé à cette offre
        if ($this->applicationModel->hasApplied($userId, $offerId)) {
            $this->flash->setFlash('error', "Vous avez déjà postulé à cette offre.");
            header('Location: index.php?page=offers');
            exit;
        }

        // Récupérer si l'offre est dans la wishlist
        $isInWishlist = $this->wishlistModel->isInWishlist($userId, $offerId);

        require_once 'Views/Application/apply.php';
    }

    /**
     * Traiter la soumission du formulaire de candidature
     */
    public function store()
    {
        // Vérifier que l'utilisateur est un étudiant
        if (!$this->auth->hasRole(['etudiant'])) {
            $this->flash->setFlash('error', "Vous n'avez pas les droits pour effectuer cette action.");
            header('Location: index.php?page=offers');
            exit;
        }

        // Vérifier que la requête est en POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->flash->setFlash('error', "Méthode non autorisée.");
            header('Location: index.php?page=offers');
            exit;
        }

        // Vérification des données du formulaire
        $offerId = isset($_POST['offer_id']) ? (int)$_POST['offer_id'] : 0;
        $motivation = isset($_POST['motivation']) ? trim($_POST['motivation']) : '';
        $userId = $this->auth->getUserId();

        // Validation des données
        $errors = [];

        if ($offerId <= 0) {
            $errors[] = "Identifiant d'offre invalide.";
        }

        if (empty($motivation)) {
            $errors[] = "La lettre de motivation est requise.";
        } elseif (strlen($motivation) < 100) {
            $errors[] = "La lettre de motivation doit contenir au moins 100 caractères.";
        }

        // Gestion de l'upload du CV
        $cvFilePath = null;
        if (isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->uploadCV($_FILES['cv_file'], $userId);
            
            if (isset($uploadResult['error'])) {
                $errors[] = $uploadResult['error'];
            } else {
                $cvFilePath = $uploadResult['path'];
            }
        } else {
            $errors[] = "Le CV est requis.";
        }

        // S'il y a des erreurs, rediriger vers le formulaire
        if (!empty($errors)) {
            $this->flash->setFlash('error', implode("<br>", $errors));
            header("Location: index.php?page=applications&action=apply&id={$offerId}");
            exit;
        }

        // Vérifier si l'étudiant a déjà postulé à cette offre
        if ($this->applicationModel->hasApplied($userId, $offerId)) {
            $this->flash->setFlash('error', "Vous avez déjà postulé à cette offre.");
            header('Location: index.php?page=applications');
            exit;
        }

        // Créer la candidature
        $result = $this->applicationModel->createApplication($offerId, $userId, $motivation, $cvFilePath);

        if ($result) {
            // Si l'offre était dans la wishlist, la supprimer
            $this->wishlistModel->removeFromWishlist($userId, $offerId);
            
            $this->flash->setFlash('success', "Votre candidature a été soumise avec succès.");
            header('Location: index.php?page=applications');
        } else {
            $this->flash->setFlash('error', "Une erreur est survenue lors de la soumission de votre candidature.");
            header("Location: index.php?page=applications&action=apply&id={$offerId}");
        }
        exit;
    }

    /**
     * Afficher le détail d'une candidature
     */
    public function view()
    {
        // Vérifier que l'utilisateur est connecté
        if (!$this->auth->isLoggedIn()) {
            header('Location: index.php?page=login');
            exit;
        }

        // Vérifier que l'ID de la candidature est fourni
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            $this->flash->setFlash('error', "Identifiant de candidature invalide.");
            header('Location: index.php?page=applications');
            exit;
        }

        $applicationId = (int)$_GET['id'];
        $userId = $this->auth->getUserId();
        $application = $this->applicationModel->getApplicationById($applicationId);

        // Vérifier que la candidature existe et appartient à l'étudiant ou que l'utilisateur est admin/pilote
        if (!$application || 
            ($application['student_id'] != $userId && !$this->auth->hasRole(['admin', 'pilote']))) {
            $this->flash->setFlash('error', "Vous n'avez pas accès à cette candidature.");
            header('Location: index.php?page=applications');
            exit;
        }

        require_once 'Views/Application/view.php';
    }

    /**
     * Annuler une candidature (pour les étudiants uniquement)
     */
    public function cancel()
    {
        // Vérifier que l'utilisateur est un étudiant
        if (!$this->auth->hasRole(['etudiant'])) {
            $this->flash->setFlash('error', "Vous n'avez pas les droits pour effectuer cette action.");
            header('Location: index.php?page=applications');
            exit;
        }

        // Vérifier que l'ID de la candidature est fourni
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            $this->flash->setFlash('error', "Identifiant de candidature invalide.");
            header('Location: index.php?page=applications');
            exit;
        }

        $applicationId = (int)$_GET['id'];
        $userId = $this->auth->getUserId();
        
        // Mettre à jour le statut de la candidature
        $result = $this->applicationModel->updateStatus($applicationId, 'withdrawn');

        if ($result) {
            $this->flash->setFlash('success', "Votre candidature a été annulée avec succès.");
        } else {
            $this->flash->setFlash('error', "Une erreur est survenue lors de l'annulation de votre candidature.");
        }

        header('Location: index.php?page=applications');
        exit;
    }

    /**
     * Mettre à jour le statut d'une candidature
     * 
     * @param int $id ID de la candidature
     * @param string $status Nouveau statut
     * @return bool Succès de l'opération
     */
    public function update()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $status = isset($_GET['status']) ? $_GET['status'] : '';
        
        if (empty($id) || empty($status) || !in_array($status, ['pending', 'accepted', 'rejected'])) {
            $this->flash->setFlash('error', "Paramètres invalides pour la mise à jour de la candidature.");
            header('Location: index.php?page=applications');
            exit;
        }
        
        // Vérifier que l'utilisateur a le droit de modifier cette candidature
        $application = $this->applicationModel->getApplicationById($id);
        
        if (!$application) {
            $this->flash->setFlash('error', "Cette candidature n'existe pas.");
            header('Location: index.php?page=applications');
            exit;
        }
        
        // Seul le propriétaire de la candidature ou un administrateur peut la modifier
        $isAdmin = $this->auth->hasRole(['admin']);
        $isOwner = $application['student_id'] == $this->auth->getUserId();
        
        if (!$isAdmin && !$isOwner) {
            $this->flash->setFlash('error', "Vous n'avez pas les droits pour modifier cette candidature.");
            header('Location: index.php?page=applications');
            exit;
        }
        
        // Mettre à jour le statut
        if ($this->applicationModel->updateStatus($id, $status)) {
            $this->flash->setFlash('success', "Le statut de la candidature a été mis à jour avec succès.");
            
            // Rediriger vers la bonne page selon le rôle
            if ($isAdmin && !$isOwner && isset($_GET['student_id'])) {
                header('Location: index.php?page=applications&student_id=' . $_GET['student_id']);
            } else if ($isAdmin && !$isOwner) {
                header('Location: index.php?page=applications');
            } else {
                header('Location: index.php?page=applications');
            }
        } else {
            $this->flash->setFlash('error', "Une erreur est survenue lors de la mise à jour du statut de la candidature.");
            header('Location: index.php?page=applications');
        }
        exit;
    }

    /**
     * Gérer l'upload du CV
     * 
     * @param array $file Données du fichier uploadé ($_FILES['cv_file'])
     * @param int $userId ID de l'utilisateur
     * @return array Résultat de l'upload avec chemin ou erreur
     */
    private function uploadCV($file, $userId)
    {
        // Vérifier que le fichier est bien un PDF
        $allowedTypes = ['application/pdf'];
        $fileType = mime_content_type($file['tmp_name']);
        
        if (!in_array($fileType, $allowedTypes)) {
            return ['error' => 'Le CV doit être au format PDF.'];
        }
        
        // Vérifier la taille du fichier (max 5 MB)
        $maxSize = 5 * 1024 * 1024; // 5 MB en bytes
        if ($file['size'] > $maxSize) {
            return ['error' => 'La taille du fichier ne doit pas dépasser 5 MB.'];
        }
        
        // Créer le répertoire de destination s'il n'existe pas
        $uploadDir = ROOT_DIR . '/Public/uploads/cvs/' . $userId;
        if (!file_exists($uploadDir) && !mkdir($uploadDir, 0755, true)) {
            return ['error' => "Impossible de créer le répertoire d'upload."];
        }
        
        // Générer un nom de fichier unique
        $fileName = uniqid('cv_') . '.pdf';
        $filePath = $uploadDir . '/' . $fileName;
        
        // Déplacer le fichier uploadé
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            return ['error' => "Échec lors de l'upload du fichier."];
        }
        
        // Retourner le chemin relatif pour stockage en BDD
        $relativePath = 'Public/uploads/cvs/' . $userId . '/' . $fileName;
        return ['path' => $relativePath];
    }

    /**
     * Afficher la wishlist de l'étudiant
     * Cette méthode est conservée pour rétrocompatibilité
     */
    public function wishlist()
    {
        // Rediriger vers le contrôleur de wishlist
        header('Location: index.php?page=wishlist');
        exit;
    }
}
