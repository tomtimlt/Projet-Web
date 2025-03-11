<?php
namespace Models;

use Database\Database;

class CompanyRating 
{
    private $db;
    
    public function __construct() 
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Récupère toutes les évaluations d'une entreprise
     * 
     * @param int $companyId ID de l'entreprise
     * @return array Liste des évaluations
     */
    public function getAllByCompany($companyId) 
    {
        $sql = "SELECT cr.*, u.firstname, u.lastname, u.role 
                FROM company_ratings cr
                JOIN users u ON cr.user_id = u.id
                WHERE cr.company_id = :company_id
                ORDER BY cr.created_at DESC";
                
        $stmt = $this->db->query($sql, [':company_id' => $companyId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Vérifie si un utilisateur a déjà évalué une entreprise
     * 
     * @param int $companyId ID de l'entreprise
     * @param int $userId ID de l'utilisateur
     * @return bool
     */
    public function hasUserRated($companyId, $userId) 
    {
        $sql = "SELECT COUNT(*) as count FROM company_ratings 
                WHERE company_id = :company_id AND user_id = :user_id";
                
        $stmt = $this->db->query($sql, [
            ':company_id' => $companyId,
            ':user_id' => $userId
        ]);
        
        return $stmt->fetch()['count'] > 0;
    }
    
    /**
     * Ajoute ou met à jour une évaluation
     * 
     * @param array $data Données de l'évaluation
     * @return bool Succès ou échec
     */
    public function saveRating($data) 
    {
        // Vérifier si l'utilisateur a déjà évalué l'entreprise
        if ($this->hasUserRated($data['company_id'], $data['user_id'])) {
            // Mise à jour de l'évaluation existante
            $sql = "UPDATE company_ratings SET 
                    rating = :rating, 
                    comment = :comment,
                    updated_at = CURRENT_TIMESTAMP
                    WHERE company_id = :company_id AND user_id = :user_id";
        } else {
            // Création d'une nouvelle évaluation
            $sql = "INSERT INTO company_ratings (company_id, user_id, rating, comment) 
                    VALUES (:company_id, :user_id, :rating, :comment)";
        }
        
        $params = [
            ':company_id' => $data['company_id'],
            ':user_id' => $data['user_id'],
            ':rating' => $data['rating'],
            ':comment' => $data['comment'] ?? null
        ];
        
        try {
            $this->db->query($sql, $params);
            return true;
        } catch (\Exception $e) {
            error_log("Erreur lors de l'enregistrement de l'évaluation: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprime une évaluation
     * 
     * @param int $id ID de l'évaluation
     * @param int $userId ID de l'utilisateur (pour vérification des droits)
     * @param string $userRole Rôle de l'utilisateur
     * @return bool Succès ou échec
     */
    public function deleteRating($id, $userId, $userRole) 
    {
        // Construction de la requête en fonction des droits
        $sql = "DELETE FROM company_ratings WHERE id = :id";
        $params = [':id' => $id];
        
        // Si l'utilisateur n'est pas admin, il ne peut supprimer que ses propres évaluations
        if ($userRole !== 'admin') {
            $sql .= " AND user_id = :user_id";
            $params[':user_id'] = $userId;
        }
        
        try {
            $stmt = $this->db->query($sql, $params);
            // Vérifier si une ligne a été affectée
            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            error_log("Erreur lors de la suppression de l'évaluation: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtient la note moyenne d'une entreprise
     * 
     * @param int $companyId ID de l'entreprise
     * @return array Données de notation (moyenne, nombre d'évaluations)
     */
    public function getAverageRating($companyId) 
    {
        $sql = "SELECT AVG(rating) as average, COUNT(*) as count 
                FROM company_ratings 
                WHERE company_id = :company_id";
                
        $stmt = $this->db->query($sql, [':company_id' => $companyId]);
        $result = $stmt->fetch();
        
        return [
            'average' => $result['average'] ? round($result['average'], 1) : 0,
            'count' => (int) $result['count']
        ];
    }
}
