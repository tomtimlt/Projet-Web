<?php
namespace Controllers;

/**
 * Contrôleur pour la gestion des évaluations
 * Gère toutes les actions liées aux évaluations d'entreprises
 */
class EvaluationController {
    private $evaluationModel;
    private $pdo;
    
    /**
     * Constructeur
     */
    public function __construct() {
        // Initialisation de la connexion à la base de données
        require_once 'Database/Database.php';
        $this->pdo = \Database\Database::getInstance()->getConnection();
        
        require_once 'models/Evaluation.php';
        $this->evaluationModel = new \Evaluation($this->pdo);
    }
    
    /**
     * Affiche les évaluations de l'utilisateur connecté
     */
    public function mesEvaluations() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login&error=login_required');
            exit;
        }
        
        // Récupérer les évaluations de l'utilisateur
        $stmt = $this->evaluationModel->readByUser($_SESSION['user_id']);
        $evaluations = $stmt->fetchAll(\PDO::FETCH_OBJ);
        
        // Passer les données à la vue
        $this->evaluations = $evaluations;
        
        // Inclure la vue
        include 'views/evaluations/my_evaluations.php';
    }
    
    /**
     * Affiche toutes les évaluations d'une entreprise
     */
    public function parEntreprise() {
        // Valider l'ID de l'entreprise
        $entrepriseId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($entrepriseId <= 0) {
            header('Location: index.php?page=companies&error=entreprise_invalide');
            exit;
        }
        
        // Pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 5; // Nombre d'évaluations par page
        
        // Récupérer les évaluations de l'entreprise
        $stmt = $this->evaluationModel->readByEntreprise($entrepriseId, $page, $perPage);
        $evaluations = $stmt->fetchAll(\PDO::FETCH_OBJ);
        
        // Récupérer le nombre total d'évaluations pour cette entreprise
        $totalEvaluations = $this->evaluationModel->countByEntreprise($entrepriseId);
        $totalPages = ceil($totalEvaluations / $perPage);
        
        // Récupérer les détails de l'entreprise
        $query = "SELECT * FROM companies WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $entrepriseId, \PDO::PARAM_INT);
        $stmt->execute();
        $entreprise = $stmt->fetch(\PDO::FETCH_OBJ);
        
        if (!$entreprise) {
            header('Location: index.php?page=companies&error=entreprise_introuvable');
            exit;
        }
        
        // Récupérer la note moyenne
        $rating = $this->evaluationModel->getAverageRating($entrepriseId);
        
        // Vérifier si l'utilisateur a déjà évalué cette entreprise
        $evaluation_utilisateur = null;
        if (isset($_SESSION['user_id'])) {
            if ($this->evaluationModel->userHasEvaluated($entrepriseId, $_SESSION['user_id'])) {
                $this->evaluationModel->readUserEvaluation($entrepriseId, $_SESSION['user_id']);
                $evaluation_utilisateur = [
                    'id' => $this->evaluationModel->id,
                    'note' => $this->evaluationModel->note,
                    'commentaire' => $this->evaluationModel->commentaire,
                    'date_evaluation' => $this->evaluationModel->date_evaluation
                ];
            }
        }
        
        // Inclure la vue
        include 'views/evaluations/list.php';
    }
    
    /**
     * Enregistre une évaluation pour une entreprise
     */
    public function evaluer() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login&error=login_required');
            exit;
        }
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['entreprise_id'])) {
            // Si l'ID de l'entreprise est passé en GET, rediriger vers la page de l'entreprise
            if (isset($_GET['id'])) {
                header('Location: index.php?page=evaluations&action=par_entreprise&id=' . (int)$_GET['id']);
            } else {
                header('Location: index.php?page=companies');
            }
            exit;
        }
        
        // Récupérer et valider les données
        $entrepriseId = (int)$_POST['entreprise_id'];
        $note = isset($_POST['note']) ? (int)$_POST['note'] : 0;
        $commentaire = isset($_POST['commentaire']) ? trim($_POST['commentaire']) : '';
        
        // Validation de base
        if ($entrepriseId <= 0) {
            header('Location: index.php?page=companies&error=entreprise_invalide');
            exit;
        }
        
        if ($note < 1 || $note > 5) {
            header('Location: index.php?page=evaluations&action=par_entreprise&id=' . $entrepriseId . '&error=note_invalide');
            exit;
        }
        
        // Configurer les propriétés du modèle
        $this->evaluationModel->entreprise_id = $entrepriseId;
        $this->evaluationModel->user_id = $_SESSION['user_id'];
        $this->evaluationModel->note = $note;
        $this->evaluationModel->commentaire = $commentaire;
        
        // Enregistrer l'évaluation
        $success = $this->evaluationModel->create();
        
        // Rediriger avec un message
        $message = $success ? 'Évaluation enregistrée avec succès.' : 'Erreur lors de l\'enregistrement de l\'évaluation.';
        $type = $success ? 'success' : 'error';
        header('Location: index.php?page=evaluations&action=par_entreprise&id=' . $entrepriseId . '&' . $type . '=' . urlencode($message));
        exit;
    }
    
    /**
     * Supprime une évaluation
     */
    public function supprimer() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login&error=login_required');
            exit;
        }
        
        // Valider l'ID
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            header('Location: index.php?page=evaluations&error=id_invalide');
            exit;
        }
        
        // Récupérer le rôle de l'utilisateur
        $query = "SELECT role FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $_SESSION['user_id'], \PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        $userRole = $user ? $user['role'] : '';
        
        // Supprimer l'évaluation
        $success = $this->evaluationModel->delete($id, $_SESSION['user_id'], $userRole);
        
        // Rediriger avec un message
        $message = $success ? 'Évaluation supprimée avec succès.' : 'Erreur lors de la suppression de l\'évaluation.';
        $type = $success ? 'success' : 'error';
        
        // Récupérer la page d'origine si disponible
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php?page=evaluations';
        
        // Ajouter le message à l'URL de redirection
        $refererParts = parse_url($referer);
        $query = isset($refererParts['query']) ? $refererParts['query'] : '';
        parse_str($query, $params);
        $params[$type] = urlencode($message);
        
        $redirectUrl = $refererParts['path'] . '?' . http_build_query($params);
        header('Location: ' . $redirectUrl);
        exit;
    }
}
