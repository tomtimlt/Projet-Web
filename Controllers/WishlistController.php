<?php

namespace Controllers;

/**
 * Contrôleur pour la gestion de la wishlist (favoris)
 */
class WishlistController
{
    private $wishlistModel;
    private $offerModel;
    private $auth;
    private $flash;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->wishlistModel = new \Models\Wishlist();
        $this->offerModel = new \Models\Offer();
        $this->auth = \Models\Auth::getInstance();
        $this->flash = new \Utils\Flash();
        
        // Vérifier que l'utilisateur est connecté
        if (!$this->auth->isLoggedIn()) {
            header('Location: index.php?page=login');
            exit;
        }
    }

    /**
     * Afficher la liste des offres en favoris
     */
    public function index()
    {
        $userId = $this->auth->getUserId();
        $isAdmin = $this->auth->hasRole(['admin']);
        $studentId = isset($_GET['student_id']) && $isAdmin ? $_GET['student_id'] : $userId;
        
        // Si l'utilisateur n'est pas admin et tente de voir la wishlist d'un autre étudiant
        if (!$isAdmin && $studentId != $userId) {
            $this->flash->setFlash('error', "Vous n'avez pas les droits pour voir la wishlist d'un autre étudiant.");
            header('Location: index.php?page=wishlist');
            exit;
        }

        // Si admin sans student_id spécifié, afficher toutes les wishlists
        if ($isAdmin && !isset($_GET['student_id'])) {
            $wishlists = $this->wishlistModel->getAllWishlists();
            // Regrouper par étudiant
            $wishlistsByStudent = [];
            $userModel = new \Models\User();
            
            foreach ($wishlists as $item) {
                if (!isset($wishlistsByStudent[$item['user_id']])) {
                    $student = $userModel->find($item['user_id']);
                    $wishlistsByStudent[$item['user_id']] = [
                        'student' => $student,
                        'items' => []
                    ];
                }
                $item['offer_details'] = $this->offerModel->getById($item['offer_id']);
                $wishlistsByStudent[$item['user_id']]['items'][] = $item;
            }
            
            // Charger la vue admin des wishlists
            require_once 'Views/Wishlist/admin_index.php';
        } else {
            // Afficher la wishlist d'un étudiant spécifique
            $wishlist = $this->wishlistModel->getWishlistByStudentId($studentId);
            
            // Récupérer les détails de chaque offre
            foreach ($wishlist as &$item) {
                $item['offer_details'] = $this->offerModel->getById($item['offer_id']);
            }
            
            // Si admin qui consulte un étudiant spécifique
            $student = null;
            if ($isAdmin && $studentId != $userId) {
                $userModel = new \Models\User();
                $student = $userModel->find($studentId);
            }
            
            // Ajouter les statistiques des candidatures
            $applicationModel = new \Models\Application();
            $stats = $applicationModel->getApplicationStatsByStudentId($studentId);
            
            require_once 'Views/Wishlist/index.php';
        }
    }

    /**
     * Ajouter une offre à la wishlist
     */
    public function add()
    {
        // Récupérer les paramètres
        $offerId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $userId = $this->auth->getUserId();
        $isAdmin = $this->auth->hasRole(['admin']);
        
        // Si c'est un admin et qu'un student_id est spécifié, utiliser ce student_id
        $studentId = isset($_GET['student_id']) && $isAdmin ? $_GET['student_id'] : $userId;
        
        // Vérifier que l'utilisateur a les droits (soit c'est sa propre wishlist, soit c'est un admin)
        if (!$isAdmin && $studentId != $userId) {
            $this->flash->setFlash('error', "Vous n'avez pas les droits pour effectuer cette action.");
            header('Location: index.php?page=offers');
            exit;
        }

        // Vérifier que l'ID de l'offre est fourni
        if (empty($offerId)) {
            $this->flash->setFlash('error', "Identifiant d'offre invalide.");
            header('Location: index.php?page=offers');
            exit;
        }

        // Vérifier que l'offre existe
        $offer = $this->offerModel->getById($offerId);
        if (!$offer) {
            $this->flash->setFlash('error', "Cette offre n'existe pas.");
            header('Location: index.php?page=offers');
            exit;
        }

        // Ajouter l'offre à la wishlist
        $result = $this->wishlistModel->addToWishlist($studentId, $offerId);

        if ($result) {
            $this->flash->setFlash('success', "L'offre a été ajoutée aux favoris.");
        } else {
            $this->flash->setFlash('error', "L'offre est déjà dans les favoris ou une erreur est survenue.");
        }

        // Redirection vers la page précédente ou la liste des offres
        $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php?page=offers';
        
        // Si admin qui gère un étudiant spécifique, redirectionner vers la wishlist de cet étudiant
        if ($isAdmin && $studentId != $userId) {
            header('Location: index.php?page=wishlist&student_id=' . $studentId);
        } else {
            header('Location: ' . $referer);
        }
        exit;
    }

    /**
     * Retirer une offre de la wishlist
     */
    public function remove()
    {
        // Récupérer les paramètres
        $offerId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $userId = $this->auth->getUserId();
        $isAdmin = $this->auth->hasRole(['admin']);
        
        // Si c'est un admin et qu'un student_id est spécifié, utiliser ce student_id
        $studentId = isset($_GET['student_id']) && $isAdmin ? $_GET['student_id'] : $userId;
        
        // Vérifier que l'utilisateur a les droits (soit c'est sa propre wishlist, soit c'est un admin)
        if (!$isAdmin && $studentId != $userId) {
            $this->flash->setFlash('error', "Vous n'avez pas les droits pour effectuer cette action.");
            header('Location: index.php?page=wishlist');
            exit;
        }

        // Vérifier que l'ID de l'offre est fourni
        if (empty($offerId)) {
            $this->flash->setFlash('error', "Identifiant d'offre invalide.");
            header('Location: index.php?page=wishlist');
            exit;
        }

        // Supprimer l'offre de la wishlist
        $result = $this->wishlistModel->removeFromWishlist($studentId, $offerId);

        if ($result) {
            $this->flash->setFlash('success', "L'offre a été retirée des favoris.");
        } else {
            $this->flash->setFlash('error', "Une erreur est survenue lors de la suppression de l'offre des favoris.");
        }

        // Redirection vers la page précédente ou la liste des favoris
        $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php?page=wishlist';
        
        // Si admin qui gère un étudiant spécifique, redirectionner vers la wishlist de cet étudiant
        if ($isAdmin && $studentId != $userId) {
            header('Location: index.php?page=wishlist&student_id=' . $studentId);
        } else {
            header('Location: ' . $referer);
        }
        exit;
    }
}
