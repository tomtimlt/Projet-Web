<?php
namespace Models;

use Database\Database;

class Student
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Récupère tous les étudiants avec options de filtrage
     * 
     * @param array $filters Filtres de recherche
     * @param int $limit Limite de résultats par page
     * @param int $offset Décalage pour pagination
     * @return array Liste des étudiants
     */
    public function getAll($filters = [], $limit = 10, $offset = 0)
    {
        $sql = "SELECT u.* FROM users u WHERE u.role = 'etudiant'";
        $params = [];
        
        // Filtres de recherche
        if (!empty($filters['search'])) {
            $sql .= " AND (u.firstname LIKE :search OR u.lastname LIKE :search OR u.email LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        } else {
            // Filtres individuels si pas de recherche globale
            if (!empty($filters['firstname'])) {
                $sql .= " AND u.firstname LIKE :firstname";
                $params[':firstname'] = '%' . $filters['firstname'] . '%';
            }
            
            if (!empty($filters['lastname'])) {
                $sql .= " AND u.lastname LIKE :lastname";
                $params[':lastname'] = '%' . $filters['lastname'] . '%';
            }
            
            if (!empty($filters['email'])) {
                $sql .= " AND u.email LIKE :email";
                $params[':email'] = '%' . $filters['email'] . '%';
            }
        }
        
        // Tri
        $sql .= " ORDER BY u.lastname ASC, u.firstname ASC";
        
        // Pagination - Utilisons des paramètres placeholders positionnels pour éviter les problèmes avec PDO
        if ($limit > 0) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = (int)$limit;
            $params[] = (int)$offset;
        }
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Compte le nombre total d'étudiants (pour pagination)
     * 
     * @param array $filters Filtres de recherche
     * @return int Nombre total d'étudiants
     */
    public function countAll($filters = [])
    {
        $sql = "SELECT COUNT(*) as total FROM users u WHERE u.role = 'etudiant'";
        $params = [];
        
        // Filtres de recherche
        if (!empty($filters['search'])) {
            $sql .= " AND (u.firstname LIKE :search OR u.lastname LIKE :search OR u.email LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        } else {
            // Filtres individuels si pas de recherche globale
            if (!empty($filters['firstname'])) {
                $sql .= " AND u.firstname LIKE :firstname";
                $params[':firstname'] = '%' . $filters['firstname'] . '%';
            }
            
            if (!empty($filters['lastname'])) {
                $sql .= " AND u.lastname LIKE :lastname";
                $params[':lastname'] = '%' . $filters['lastname'] . '%';
            }
            
            if (!empty($filters['email'])) {
                $sql .= " AND u.email LIKE :email";
                $params[':email'] = '%' . $filters['email'] . '%';
            }
        }
        
        $stmt = $this->db->query($sql, $params);
        $result = $stmt->fetch();
        
        return (int)$result['total'];
    }
    
    /**
     * Récupère un étudiant par son ID
     * 
     * @param int $id ID de l'étudiant
     * @return array|false Données de l'étudiant ou false si non trouvé
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM users WHERE id = :id AND role = 'etudiant'";
        $stmt = $this->db->query($sql, [':id' => $id]);
        
        return $stmt->fetch();
    }
    
    /**
     * Crée un nouveau compte étudiant
     * 
     * @param array $data Données de l'étudiant
     * @return int|false ID de l'étudiant créé ou false en cas d'échec
     */
    public function create($data)
    {
        // Vérifier si l'email existe déjà
        if ($this->emailExists($data['email'])) {
            return false;
        }
        
        // Hashage du mot de passe
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (firstname, lastname, email, password, role, is_active) 
                VALUES (:firstname, :lastname, :email, :password, 'etudiant', :is_active)";
        
        $params = [
            ':firstname' => $data['firstname'],
            ':lastname' => $data['lastname'],
            ':email' => $data['email'],
            ':password' => $hashedPassword,
            ':is_active' => isset($data['is_active']) ? 1 : 0
        ];
        
        try {
            $this->db->query($sql, $params);
            return $this->db->getConnection()->lastInsertId();
        } catch (\Exception $e) {
            error_log("Erreur lors de la création de l'étudiant: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Met à jour un compte étudiant
     * 
     * @param int $id ID de l'étudiant
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
                lastname = :lastname,
                is_active = :is_active";
        
        $params = [
            ':id' => $id,
            ':firstname' => $data['firstname'],
            ':lastname' => $data['lastname'],
            ':is_active' => isset($data['is_active']) ? 1 : 0
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
        
        $sql .= " WHERE id = :id AND role = 'etudiant'";
        
        try {
            $stmt = $this->db->query($sql, $params);
            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            error_log("Erreur lors de la mise à jour de l'étudiant: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprime un compte étudiant
     * 
     * @param int $id ID de l'étudiant
     * @return bool Succès ou échec
     */
    public function delete($id)
    {
        // Vérifier si l'étudiant existe avant suppression
        $student = $this->getById($id);
        if (!$student) {
            error_log("Tentative de suppression d'un étudiant inexistant: ID $id");
            return false;
        }
        
        try {
            // Commencer une transaction
            $this->db->getConnection()->beginTransaction();
            
            // Vérifier s'il y a des relations avec d'autres tables
            // Note: Ajuster en fonction du schéma de votre base de données
            
            // Supprimer l'étudiant
            $sql = "DELETE FROM users WHERE id = :id AND role = 'etudiant'";
            $stmt = $this->db->query($sql, [':id' => $id]);
            
            if ($stmt->rowCount() > 0) {
                $this->db->getConnection()->commit();
                return true;
            } else {
                error_log("Échec de la suppression de l'étudiant ID $id: aucune ligne affectée");
                $this->db->getConnection()->rollBack();
                return false;
            }
        } catch (\Exception $e) {
            error_log("Erreur lors de la suppression de l'étudiant ID $id: " . $e->getMessage());
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
     * Récupère les statistiques globales des étudiants
     * 
     * @return array Statistiques des étudiants
     */
    public function getGlobalStatistics()
    {
        $stats = [];
        
        // Nombre total d'étudiants
        $sql = "SELECT COUNT(*) as total FROM users WHERE role = 'etudiant'";
        $stmt = $this->db->query($sql);
        $stats['total_students'] = (int)$stmt->fetch()['total'];
        
        // Autres statistiques selon besoin
        
        return $stats;
    }
}
