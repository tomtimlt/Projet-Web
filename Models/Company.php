<?php
namespace Models;

use Database\Database;

class Company 
{
    private $db;
    
    public function __construct() 
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Récupère toutes les entreprises avec possibilité de filtrage
     * 
     * @param array $filters Critères de filtrage (nom, secteur, taille, etc.)
     * @param int $limit Nombre max d'entreprises à récupérer
     * @param int $offset Pagination
     * @return array Liste des entreprises
     */
    public function getAll($filters = [], $limit = 100, $offset = 0) 
    {
        $sql = "SELECT c.*, 
                (SELECT COUNT(*) FROM offers WHERE company_id = c.id) AS offer_count,
                (SELECT AVG(rating) FROM company_ratings WHERE company_id = c.id) AS average_rating
                FROM companies c WHERE 1=1";
        
        $params = [];
        
        // Application des filtres
        if (!empty($filters['name'])) {
            $sql .= " AND c.name LIKE :name";
            $params[':name'] = '%' . $filters['name'] . '%';
        }
        
        if (!empty($filters['sector'])) {
            $sql .= " AND c.sector = :sector";
            $params[':sector'] = $filters['sector'];
        }
        
        if (!empty($filters['size'])) {
            $sql .= " AND c.size = :size";
            $params[':size'] = $filters['size'];
        }
        
        if (!empty($filters['city'])) {
            $sql .= " AND c.city LIKE :city";
            $params[':city'] = '%' . $filters['city'] . '%';
        }
        
        // Tri
        $sql .= " ORDER BY c.name ASC";
        
        // Pagination
        $sql .= " LIMIT :limit OFFSET :offset";
        $params[':limit'] = (int)$limit;
        $params[':offset'] = (int)$offset;
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère une entreprise par son ID
     * 
     * @param int $id ID de l'entreprise
     * @return array|bool Les données de l'entreprise ou false si non trouvée
     */
    public function getById($id) 
    {
        $sql = "SELECT c.*, 
                (SELECT COUNT(*) FROM offers WHERE company_id = c.id) AS offer_count,
                (SELECT AVG(rating) FROM company_ratings WHERE company_id = c.id) AS average_rating
                FROM companies c WHERE c.id = :id";
        
        $stmt = $this->db->query($sql, [':id' => $id]);
        return $stmt->fetch() ?: false;
    }
    
    /**
     * Crée une nouvelle entreprise
     * 
     * @param array $data Les données de l'entreprise
     * @return int|bool L'ID de l'entreprise créée ou false en cas d'échec
     */
    public function create($data) 
    {
        $sql = "INSERT INTO companies (name, address, postal_code, city, country, phone, email, 
                                      website, description, sector, size, created_by) 
                VALUES (:name, :address, :postal_code, :city, :country, :phone, :email, 
                        :website, :description, :sector, :size, :created_by)";
        
        $params = [
            ':name' => $data['name'],
            ':address' => $data['address'],
            ':postal_code' => $data['postal_code'],
            ':city' => $data['city'],
            ':country' => $data['country'] ?? 'France',
            ':phone' => $data['phone'] ?? null,
            ':email' => $data['email'] ?? null,
            ':website' => $data['website'] ?? null,
            ':description' => $data['description'] ?? null,
            ':sector' => $data['sector'] ?? null,
            ':size' => $data['size'] ?? null,
            ':created_by' => $data['created_by'] ?? null
        ];
        
        try {
            $this->db->query($sql, $params);
            return $this->db->getConnection()->lastInsertId();
        } catch (\Exception $e) {
            error_log("Erreur lors de la création de l'entreprise: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Met à jour une entreprise
     * 
     * @param int $id ID de l'entreprise
     * @param array $data Données à mettre à jour
     * @return bool Succès ou échec
     */
    public function update($id, $data) 
    {
        // Construire la requête dynamiquement en fonction des champs à mettre à jour
        $updateFields = [];
        $params = [':id' => $id];
        
        $allowedFields = [
            'name', 'address', 'postal_code', 'city', 'country', 'phone', 
            'email', 'website', 'description', 'sector', 'size'
        ];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateFields[] = "$field = :$field";
                $params[":$field"] = $data[$field];
            }
        }
        
        if (empty($updateFields)) {
            return true; // Rien à mettre à jour
        }
        
        $sql = "UPDATE companies SET " . implode(', ', $updateFields) . " WHERE id = :id";
        
        try {
            $this->db->query($sql, $params);
            return true;
        } catch (\Exception $e) {
            error_log("Erreur lors de la mise à jour de l'entreprise: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprime une entreprise
     * 
     * @param int $id ID de l'entreprise
     * @return bool Succès ou échec
     */
    public function delete($id) 
    {
        // Vérifier si l'entreprise existe
        if (!$this->getById($id)) {
            return false;
        }
        
        $sql = "DELETE FROM companies WHERE id = :id";
        
        try {
            $this->db->query($sql, [':id' => $id]);
            return true;
        } catch (\Exception $e) {
            error_log("Erreur lors de la suppression de l'entreprise: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupère les secteurs d'activité distincts
     * 
     * @return array Liste des secteurs
     */
    public function getAllSectors() 
    {
        $sql = "SELECT DISTINCT sector FROM companies WHERE sector IS NOT NULL ORDER BY sector";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetchAll();
        
        $sectors = [];
        foreach ($result as $row) {
            $sectors[] = $row['sector'];
        }
        
        return $sectors;
    }
    
    /**
     * Récupère les statistiques globales sur les entreprises
     * 
     * @return array Statistiques (nombre d'entreprises, répartition par taille, etc.)
     */
    public function getStatistics() 
    {
        // Nombre total d'entreprises
        $sql1 = "SELECT COUNT(*) as total_count FROM companies";
        $stmt1 = $this->db->query($sql1);
        $totalCount = $stmt1->fetch()['total_count'];
        
        // Répartition par taille
        $sql2 = "SELECT size, COUNT(*) as count FROM companies GROUP BY size";
        $stmt2 = $this->db->query($sql2);
        $sizeDistribution = $stmt2->fetchAll();
        
        // Entreprises avec le plus d'offres
        $sql3 = "SELECT c.id, c.name, COUNT(o.id) AS offer_count 
                FROM companies c 
                LEFT JOIN offers o ON c.id = o.company_id 
                GROUP BY c.id, c.name 
                ORDER BY offer_count DESC 
                LIMIT 5";
        $stmt3 = $this->db->query($sql3);
        $topCompanies = $stmt3->fetchAll();
        
        // Meilleures évaluations
        $sql4 = "SELECT c.id, c.name, AVG(cr.rating) AS avg_rating, COUNT(cr.id) AS rating_count 
                FROM companies c 
                JOIN company_ratings cr ON c.id = cr.company_id 
                GROUP BY c.id, c.name 
                HAVING rating_count > 0
                ORDER BY avg_rating DESC 
                LIMIT 5";
        $stmt4 = $this->db->query($sql4);
        $topRated = $stmt4->fetchAll();
        
        return [
            'total_count' => $totalCount,
            'size_distribution' => $sizeDistribution,
            'top_companies' => $topCompanies,
            'top_rated' => $topRated
        ];
    }
}
