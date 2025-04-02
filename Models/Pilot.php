<?php
namespace Models;

use Database\Database;

class Pilot
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Récupère tous les pilotes avec options de filtrage
     * 
     * @param array $filters Filtres de recherche
     * @param int $limit Limite de résultats par page
     * @param int $offset Décalage pour pagination
     * @return array Liste des pilotes
     */
    public function getAll($filters = [], $limit = 10, $offset = 0)
    {
        $sql = "SELECT u.* FROM users u WHERE u.role = 'pilote'";
        $params = [];
        
        // Filtres de recherche
        if (!empty($filters['search'])) {
            $sql .= " AND (u.firstname LIKE :search OR u.lastname LIKE :search OR u.email LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        // Tri
        $sql .= " ORDER BY u.lastname ASC, u.firstname ASC";
        
        // Pagination - Utilisons des paramètres positionnels pour LIMIT et OFFSET
        if ($limit > 0) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = (int)$limit;
            $params[] = (int)$offset;
        }
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Compte le nombre total de pilotes (pour pagination)
     * 
     * @param array $filters Filtres de recherche
     * @return int Nombre total de pilotes
     */
    public function countAll($filters = [])
    {
        $sql = "SELECT COUNT(*) as total FROM users u WHERE u.role = 'pilote'";
        $params = [];
        
        // Filtres de recherche
        if (!empty($filters['search'])) {
            $sql .= " AND (u.firstname LIKE :search OR u.lastname LIKE :search OR u.email LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        $stmt = $this->db->query($sql, $params);
        $result = $stmt->fetch();
        
        return (int)$result['total'];
    }
    
    /**
     * Récupère un pilote par son ID
     * 
     * @param int $id ID du pilote
     * @return array|false Données du pilote ou false si non trouvé
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM users WHERE id = :id AND role = 'pilote'";
        $stmt = $this->db->query($sql, [':id' => $id]);
        
        return $stmt->fetch();
    }
    
    /**
     * Crée un nouveau compte pilote
     * 
     * @param array $data Données du pilote
     * @return int|false ID du pilote créé ou false en cas d'échec
     */
    public function create($data)
    {
        // Vérifier si l'email existe déjà
        if ($this->emailExists($data['email'])) {
            return false;
        }
        
        // Hashage du mot de passe
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (firstname, lastname, email, password, role) 
                VALUES (:firstname, :lastname, :email, :password, 'pilote')";
        
        $params = [
            ':firstname' => $data['firstname'],
            ':lastname' => $data['lastname'],
            ':email' => $data['email'],
            ':password' => $hashedPassword
        ];
        
        try {
            $this->db->query($sql, $params);
            return $this->db->getConnection()->lastInsertId();
        } catch (\Exception $e) {
            error_log("Erreur lors de la création du pilote: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Met à jour un compte pilote
     * 
     * @param int $id ID du pilote
     * @param array $data Données à mettre à jour
     * @return bool Succès ou échec
     */
    public function update($id, $data)
    {
        // Vérifier si l'email existe déjà (pour un autre utilisateur)
        if (!empty($data['email']) && $this->emailExists($data['email'], $id)) {
            return false;
        }
        
        $sql = "UPDATE users SET 
                firstname = :firstname,
                lastname = :lastname";
        
        $params = [
            ':id' => $id,
            ':firstname' => $data['firstname'],
            ':lastname' => $data['lastname']
        ];
        
        // Mise à jour de l'email si fourni
        if (!empty($data['email'])) {
            $sql .= ", email = :email";
            $params[':email'] = $data['email'];
        }
        
        // Mise à jour du mot de passe si fourni
        if (!empty($data['password'])) {
            $sql .= ", password = :password";
            $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        $sql .= " WHERE id = :id AND role = 'pilote'";
        
        try {
            $stmt = $this->db->query($sql, $params);
            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            error_log("Erreur lors de la mise à jour du pilote: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprime un compte pilote
     * 
     * @param int $id ID du pilote
     * @return bool Succès ou échec
     */
    public function delete($id)
    {
        // Vérifier si le pilote existe avant suppression
        $pilot = $this->getById($id);
        if (!$pilot) {
            error_log("Tentative de suppression d'un pilote inexistant: ID $id");
            return false;
        }
        
        try {
            // Commencer une transaction
            $this->db->getConnection()->beginTransaction();
            
            // Vérifier s'il y a des entreprises associées au pilote
            // Note: Cette partie est à ajuster selon votre schéma réel
            $sql = "SELECT COUNT(*) as count FROM companies WHERE created_by = :pilot_id";
            $stmt = $this->db->query($sql, [':pilot_id' => $id]);
            $result = $stmt->fetch();
            
            if ((int)$result['count'] > 0) {
                error_log("Impossible de supprimer le pilote ID $id car il a des entreprises associées");
                
                // Option 1: Échouer proprement
                $this->db->getConnection()->rollBack();
                return false;
                
                // Option 2 (alternative): Mettre à jour les entreprises pour enlever la référence au pilote
                // $updateSql = "UPDATE companies SET created_by = NULL WHERE created_by = :pilot_id";
                // $this->db->query($updateSql, [':pilot_id' => $id]);
            }
            
            // Vérifier d'autres relations potentielles
            // ... (ajuster selon votre schéma)
            
            // Supprimer le pilote
            $sql = "DELETE FROM users WHERE id = :id AND role = 'pilote'";
            $stmt = $this->db->query($sql, [':id' => $id]);
            
            if ($stmt->rowCount() > 0) {
                $this->db->getConnection()->commit();
                return true;
            } else {
                error_log("Échec de la suppression du pilote ID $id: aucune ligne affectée");
                $this->db->getConnection()->rollBack();
                return false;
            }
        } catch (\Exception $e) {
            error_log("Erreur lors de la suppression du pilote ID $id: " . $e->getMessage());
            $this->db->getConnection()->rollBack();
            return false;
        }
    }
    
    /**
     * Vérifie si un email existe déjà dans la base de données
     * 
     * @param string $email Email à vérifier
     * @param int|null $excludeId ID à exclure de la vérification (pour la mise à jour)
     * @return bool True si l'email existe déjà, false sinon
     */
    private function emailExists($email, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as count FROM users WHERE email = :email";
        $params = [':email' => $email];
        
        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        
        $stmt = $this->db->query($sql, $params);
        $result = $stmt->fetch();
        
        return (int)$result['count'] > 0;
    }
    
    /**
     * Récupère les stats des pilotes
     * 
     * @return array Statistiques des pilotes
     */
    public function getStats()
    {
        $stats = [];
        
        // Nombre total de pilotes
        $sql = "SELECT COUNT(*) as total FROM users WHERE role = 'pilote'";
        $stmt = $this->db->query($sql);
        $stats['total_pilots'] = (int)$stmt->fetch()['total'];
        
        // Nombre total d'entreprises
        $sql = "SELECT COUNT(*) as total FROM companies";
        $stmt = $this->db->query($sql);
        $stats['total_companies'] = (int)$stmt->fetch()['total'];
        
        // Top 5 des pilotes (pour l'instant, juste afficher les 5 premiers)
        $sql = "SELECT u.id, u.firstname, u.lastname, 0 as company_count 
                FROM users u 
                WHERE u.role = 'pilote' 
                ORDER BY u.lastname ASC, u.firstname ASC 
                LIMIT 5";
        $stmt = $this->db->query($sql);
        $stats['pilots_by_companies'] = $stmt->fetchAll();
        
        return $stats;
    }
}
