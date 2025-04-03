<?php
/**
 * Controleur pour le tableau de bord
 */
class DashboardController {
    private $entrepriseModel;
    
    /**
     * Constructeur
     * @param Entreprise $entrepriseModel Instance du modele Entreprise
     */
    public function __construct($entrepriseModel) {
        $this->entrepriseModel = $entrepriseModel;
    }
    
    /**
     * Affiche le tableau de bord avec les entreprises recentes
     */
    public function index() {
        // Verifier si l'utilisateur est connecte
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        
        // Recuperer les entreprises recentes
        $this->recentEntreprises = $this->entrepriseModel->getRecentEntreprises(6);
        
        // Inclure la vue du tableau de bord
        include_once 'views/dashboard.php';
    }
}
