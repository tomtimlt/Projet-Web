<?php
/**
 * Contrôleur pour la gestion des entreprises
 * Gère toutes les actions liées aux entreprises
 */
class EntrepriseController {
    private $entrepriseModel;
    private $evaluationModel;
    private $pdo;

    /**
     * Constructeur
     * @param PDO $pdo Instance de connexion à la base de données
     */
    public function __construct($pdo) {
        $this->pdo = $pdo;
        require_once 'models/EntrepriseModel.php';
        require_once 'models/Evaluation.php';
        $this->entrepriseModel = new EntrepriseModel($pdo);
        $this->evaluationModel = new Evaluation($pdo);
    }

    /**
     * Affiche la liste des entreprises avec filtres de recherche
     */
    public function index() {
        // Récupérer les critères de recherche depuis la requête
        $criteres = [
            'nom' => isset($_GET['nom']) ? trim($_GET['nom']) : '',
            'secteur' => isset($_GET['secteur']) ? trim($_GET['secteur']) : '',
            'ville' => isset($_GET['ville']) ? trim($_GET['ville']) : ''
        ];
        
        // Pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 10; // Nombre d'entreprises par page
        
        // Récupérer les entreprises correspondant aux critères
        $entreprises = $this->entrepriseModel->rechercher($criteres, $page, $perPage);
        $total = $this->entrepriseModel->compterTotal($criteres);
        
        // Calcul pour la pagination
        $totalPages = ceil($total / $perPage);
        
        // Récupérer les listes pour les filtres
        $secteurs = $this->entrepriseModel->getSecteurs();
        $villes = $this->entrepriseModel->getVilles();
        
        // Inclure la vue
        include 'views/entreprise/index.php';
    }

    /**
     * Affiche les détails d'une entreprise
     * @param int $id ID de l'entreprise
     */
    public function voir($id) {
        // Récupérer les données de l'entreprise
        $entreprise = $this->entrepriseModel->getById($id);
        
        if (!$entreprise) {
            // Rediriger si l'entreprise n'existe pas
            header('Location: index.php?controller=entreprise&action=index&error=entreprise_introuvable');
            exit;
        }
        
        // Récupérer les statistiques
        $statistiques = $this->entrepriseModel->getStatistiques($id);
        
        // Récupérer la notation moyenne de l'entreprise
        $rating = $this->evaluationModel->getAverageRating($id);
        
        // Récupérer les évaluations (limité à 3 pour l'aperçu)
        $stmt = $this->evaluationModel->readByEntreprise($id, 1, 3);
        $evaluations = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        // Nombre total d'évaluations
        $totalEvaluations = $this->evaluationModel->countByEntreprise($id);
        
        // Vérifier si l'utilisateur a déjà évalué cette entreprise
        $evaluation_utilisateur = null;
        if (isset($_SESSION['user_id'])) {
            if ($this->evaluationModel->userHasEvaluated($id, $_SESSION['user_id'])) {
                $this->evaluationModel->readUserEvaluation($id, $_SESSION['user_id']);
                $evaluation_utilisateur = [
                    'id' => $this->evaluationModel->id,
                    'note' => $this->evaluationModel->note,
                    'commentaire' => $this->evaluationModel->commentaire,
                    'date_evaluation' => $this->evaluationModel->date_evaluation
                ];
            }
        }
        
        // Inclure la vue
        include 'views/entreprise/voir.php';
    }

    /**
     * Affiche le formulaire de création d'entreprise
     */
    public function creer() {
        // Vérifier les permissions (seuls les administrateurs et pilotes peuvent créer)
        if (!$this->verifierPermission('creer')) {
            header('Location: index.php?controller=home&action=index&error=permission_denied');
            exit;
        }
        
        // Récupérer les secteurs pour la liste déroulante
        $secteurs = $this->entrepriseModel->getSecteurs();
        
        // Inclure la vue
        include 'views/entreprise/form.php';
    }

    /**
     * Affiche le formulaire de modification d'une entreprise
     * @param int $id ID de l'entreprise à modifier
     */
    public function modifier($id) {
        // Vérifier les permissions
        if (!$this->verifierPermission('modifier')) {
            header('Location: index.php?controller=home&action=index&error=permission_denied');
            exit;
        }
        
        // Récupérer les données de l'entreprise
        $entreprise = $this->entrepriseModel->getById($id);
        
        if (!$entreprise) {
            header('Location: index.php?controller=entreprise&action=index&error=entreprise_introuvable');
            exit;
        }
        
        // Récupérer les secteurs pour la liste déroulante
        $secteurs = $this->entrepriseModel->getSecteurs();
        
        // Inclure la vue
        include 'views/entreprise/form.php';
    }

    /**
     * Enregistre les données d'une entreprise (création ou modification)
     */
    public function enregistrer() {
        // Vérifier si c'est une création ou une modification
        $estModification = isset($_POST['id']) && !empty($_POST['id']);
        
        // Vérifier les permissions
        if (!$this->verifierPermission($estModification ? 'modifier' : 'creer')) {
            header('Location: index.php?controller=home&action=index&error=permission_denied');
            exit;
        }
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=entreprise&action=index');
            exit;
        }
        
        // Valider les données
        $erreurs = $this->validerDonnees($_POST);
        
        if (!empty($erreurs)) {
            // S'il y a des erreurs, rediriger vers le formulaire avec les erreurs
            $_SESSION['form_errors'] = $erreurs;
            $_SESSION['form_data'] = $_POST;
            
            if ($estModification) {
                header('Location: index.php?controller=entreprise&action=modifier&id=' . $_POST['id']);
            } else {
                header('Location: index.php?controller=entreprise&action=creer');
            }
            exit;
        }
        
        // Préparer les données pour l'enregistrement
        $data = [
            'nom' => htmlspecialchars(trim($_POST['nom'])),
            'description' => htmlspecialchars(trim($_POST['description'])),
            'secteur_activite' => htmlspecialchars(trim($_POST['secteur_activite'])),
            'adresse' => htmlspecialchars(trim($_POST['adresse'])),
            'ville' => htmlspecialchars(trim($_POST['ville'])),
            'code_postal' => htmlspecialchars(trim($_POST['code_postal'])),
            'pays' => htmlspecialchars(trim($_POST['pays'])),
            'email_contact' => htmlspecialchars(trim($_POST['email_contact'])),
            'telephone_contact' => htmlspecialchars(trim($_POST['telephone_contact'])),
            'site_web' => htmlspecialchars(trim($_POST['site_web']))
        ];
        
        // Enregistrer les données
        if ($estModification) {
            $success = $this->entrepriseModel->modifier($_POST['id'], $data);
            $message = $success ? 'Entreprise modifiée avec succès.' : 'Erreur lors de la modification de l\'entreprise.';
        } else {
            $id = $this->entrepriseModel->creer($data);
            $success = $id !== false;
            $message = $success ? 'Entreprise créée avec succès.' : 'Erreur lors de la création de l\'entreprise.';
        }
        
        // Rediriger avec un message de succès ou d'erreur
        $type = $success ? 'success' : 'error';
        header('Location: index.php?controller=entreprise&action=index&' . $type . '=' . urlencode($message));
        exit;
    }

    /**
     * Supprime une entreprise
     * @param int $id ID de l'entreprise à supprimer
     */
    public function supprimer($id) {
        // Vérifier les permissions
        if (!$this->verifierPermission('supprimer')) {
            header('Location: index.php?controller=home&action=index&error=permission_denied');
            exit;
        }
        
        // Vérifier si la demande est une confirmation POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            // Afficher une page de confirmation
            $entreprise = $this->entrepriseModel->getById($id);
            if (!$entreprise) {
                header('Location: index.php?controller=entreprise&action=index&error=entreprise_introuvable');
                exit;
            }
            include 'views/entreprise/supprimer.php';
            return;
        }
        
        // Supprimer l'entreprise
        $success = $this->entrepriseModel->supprimer($id);
        
        // Rediriger avec un message de succès ou d'erreur
        if ($success) {
            header('Location: index.php?controller=entreprise&action=index&success=entreprise_supprimee');
        } else {
            header('Location: index.php?controller=entreprise&action=index&error=suppression_impossible');
        }
        exit;
    }

    /**
     * Redirige vers le contrôleur d'évaluation
     * @param int $id ID de l'entreprise à évaluer
     */
    public function evaluer($id) {
        header('Location: index.php?controller=evaluation&action=evaluer&id=' . $id);
        exit;
    }

    /**
     * Affiche les statistiques globales des entreprises
     */
    public function statistiques() {
        // Vérifier les permissions
        if (!$this->verifierPermission('statistiques')) {
            header('Location: index.php?controller=home&action=index&error=permission_denied');
            exit;
        }
        
        // Récupérer les statistiques
        $stats = $this->entrepriseModel->getStatistiquesGlobales();
        
        // Inclure la vue
        include 'views/entreprise/statistiques.php';
    }

    /**
     * Valide les données du formulaire
     * @param array $data Données à valider
     * @return array Tableau des erreurs (vide si aucune erreur)
     */
    private function validerDonnees($data) {
        $erreurs = [];
        
        // Valider le nom (obligatoire)
        if (empty(trim($data['nom']))) {
            $erreurs['nom'] = 'Le nom de l\'entreprise est obligatoire.';
        } elseif (strlen(trim($data['nom'])) > 100) {
            $erreurs['nom'] = 'Le nom de l\'entreprise ne doit pas dépasser 100 caractères.';
        }
        
        // Valider la description (obligatoire)
        if (empty(trim($data['description']))) {
            $erreurs['description'] = 'La description est obligatoire.';
        }
        
        // Valider le secteur d'activité (obligatoire)
        if (empty(trim($data['secteur_activite']))) {
            $erreurs['secteur_activite'] = 'Le secteur d\'activité est obligatoire.';
        }
        
        // Valider l'email de contact (obligatoire et format valide)
        if (empty(trim($data['email_contact']))) {
            $erreurs['email_contact'] = 'L\'email de contact est obligatoire.';
        } elseif (!filter_var(trim($data['email_contact']), FILTER_VALIDATE_EMAIL)) {
            $erreurs['email_contact'] = 'L\'email de contact n\'est pas valide.';
        }
        
        // Valider le téléphone (obligatoire)
        if (empty(trim($data['telephone_contact']))) {
            $erreurs['telephone_contact'] = 'Le téléphone de contact est obligatoire.';
        }
        
        // Valider la ville (obligatoire)
        if (empty(trim($data['ville']))) {
            $erreurs['ville'] = 'La ville est obligatoire.';
        }
        
        // Valider le code postal (obligatoire)
        if (empty(trim($data['code_postal']))) {
            $erreurs['code_postal'] = 'Le code postal est obligatoire.';
        }
        
        // Valider le pays (obligatoire)
        if (empty(trim($data['pays']))) {
            $erreurs['pays'] = 'Le pays est obligatoire.';
        }
        
        // Valider le site web (facultatif mais doit être une URL valide si renseigné)
        if (!empty(trim($data['site_web'])) && !filter_var(trim($data['site_web']), FILTER_VALIDATE_URL)) {
            $erreurs['site_web'] = 'Le site web n\'est pas une URL valide.';
        }
        
        return $erreurs;
    }

    /**
     * Vérifie si l'utilisateur a la permission pour une action spécifique
     * @param string $action Action à vérifier (creer, modifier, supprimer, evaluer, statistiques)
     * @return bool L'utilisateur a-t-il la permission
     */
    private function verifierPermission($action) {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        // Récupérer le rôle de l'utilisateur
        $query = "SELECT role FROM utilisateur WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            return false;
        }
        
        // Définir les permissions par rôle
        $permissions = [
            // Administrateur
            'admin' => ['creer', 'modifier', 'supprimer', 'evaluer', 'statistiques'],
            'administrateur' => ['creer', 'modifier', 'supprimer', 'evaluer', 'statistiques'],
            // Pilote
            'pilote' => ['creer', 'modifier', 'supprimer', 'evaluer', 'statistiques'],
            // Étudiant
            'etudiant' => ['evaluer']
        ];
        
        // Vérifier si l'utilisateur a la permission
        return isset($permissions[$user['role']]) && in_array($action, $permissions[$user['role']]);
    }
}