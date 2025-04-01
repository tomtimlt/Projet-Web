<?php

namespace Models;

/**
 * Modèle pour la gestion des wishlists (favoris)
 */
class Wishlist
{
    private $db;

    /**
     * Constructeur
     */
    public function __construct()
    {
        // Utiliser le singleton Database au lieu de créer une nouvelle connexion
        $database = \Database\Database::getInstance();
        $this->db = $database->getConnection();
    }

    /**
     * Ajouter une offre à la wishlist d'un étudiant
     *
     * @param int $userId ID de l'étudiant
     * @param int $offerId ID de l'offre
     * @return bool Succès de l'opération
     */
    public function addToWishlist($userId, $offerId)
    {
        try {
            // Vérifier que l'étudiant n'a pas déjà cette offre dans sa wishlist
            $check = $this->db->prepare("
                SELECT id FROM wishlists 
                WHERE user_id = :user_id AND offer_id = :offer_id
            ");
            $check->execute([
                ':user_id' => $userId,
                ':offer_id' => $offerId
            ]);

            if ($check->rowCount() > 0) {
                return false; // L'offre est déjà dans la wishlist
            }

            // Ajouter l'offre à la wishlist
            $stmt = $this->db->prepare("
                INSERT INTO wishlists (user_id, offer_id, created_at) 
                VALUES (:user_id, :offer_id, NOW())
            ");
            $result = $stmt->execute([
                ':user_id' => $userId,
                ':offer_id' => $offerId
            ]);

            return $result;
        } catch (\PDOException $e) {
            error_log("Erreur lors de l'ajout à la wishlist: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprimer une offre de la wishlist d'un étudiant
     *
     * @param int $userId ID de l'étudiant
     * @param int $offerId ID de l'offre
     * @return bool Succès de l'opération
     */
    public function removeFromWishlist($userId, $offerId)
    {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM wishlists 
                WHERE user_id = :user_id AND offer_id = :offer_id
            ");
            $result = $stmt->execute([
                ':user_id' => $userId,
                ':offer_id' => $offerId
            ]);

            return $result;
        } catch (\PDOException $e) {
            error_log("Erreur lors de la suppression de la wishlist: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer la wishlist d'un étudiant
     *
     * @param int $studentId ID de l'étudiant
     * @return array Liste des offres en wishlist
     */
    public function getWishlistByStudentId($studentId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT w.*, o.title, o.company_id, o.location, o.start_date, o.end_date, 
                       o.salary, o.description, o.status, c.name as company_name
                FROM wishlists w
                INNER JOIN offers o ON w.offer_id = o.id
                INNER JOIN companies c ON o.company_id = c.id
                WHERE w.user_id = :user_id
                ORDER BY w.created_at DESC
            ");
            $stmt->execute([':user_id' => $studentId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Erreur lors de la récupération de la wishlist: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupérer toutes les wishlists (pour administrateurs)
     * 
     * @return array Liste de toutes les wishlists
     */
    public function getAllWishlists()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT w.*, u.firstname, u.lastname, u.email, o.title as offer_title
                FROM wishlists w
                INNER JOIN users u ON w.user_id = u.id
                INNER JOIN offers o ON w.offer_id = o.id
                ORDER BY w.user_id, w.created_at DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Erreur lors de la récupération de toutes les wishlists: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Vérifier si une offre est dans la wishlist d'un étudiant
     *
     * @param int $userId ID de l'étudiant
     * @param int $offerId ID de l'offre
     * @return bool True si l'offre est dans la wishlist
     */
    public function isInWishlist($userId, $offerId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id FROM wishlists 
                WHERE user_id = :user_id AND offer_id = :offer_id
            ");
            $stmt->execute([
                ':user_id' => $userId,
                ':offer_id' => $offerId
            ]);

            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            error_log("Erreur lors de la vérification de la wishlist: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer les wishlists contenant une offre spécifique
     * 
     * @param int $offerId ID de l'offre
     * @return array Liste des wishlists pour cette offre
     */
    public function getWishlistsByOfferId($offerId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT w.*, u.firstname, u.lastname, u.email
                FROM wishlists w
                INNER JOIN users u ON w.user_id = u.id
                WHERE w.offer_id = :offer_id
                ORDER BY w.created_at DESC
            ");
            $stmt->execute([':offer_id' => $offerId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des wishlists pour une offre: " . $e->getMessage());
            return [];
        }
    }
}
