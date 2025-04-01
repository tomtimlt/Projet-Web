<?php
namespace Models;

use Database\Database;

class Offer
{
    private $db;
    
    public function __construct()
    {
        $database = \Database\Database::getInstance();
        $this->db = $database->getConnection();
    }
    
    /**
     * Récupérer toutes les offres avec options de filtrage
     * 
     * @param array $filters Critères de filtrage
     * @param int $limit Limite de résultats
     * @param int $offset Décalage pour pagination
     * @return array Liste des offres
     */
    public function getAll($filters = [], $limit = 10, $offset = 0)
    {
        $sql = "SELECT o.*, c.name as company_name, 
                (SELECT COUNT(*) FROM applications WHERE offer_id = o.id) as application_count
                FROM offers o
                INNER JOIN companies c ON o.company_id = c.id
                WHERE 1=1";
        
        $params = [];
        
        // Appliquer les filtres
        if (!empty($filters['keyword'])) {
            // Convertir le mot-clé en minuscules pour une recherche insensible à la casse
            $keyword = strtolower(trim($filters['keyword']));
            
            $sql .= " AND (
                LOWER(o.title) LIKE :keyword 
                OR LOWER(o.description) LIKE :keyword
                OR LOWER(c.name) LIKE :keyword
                OR LOWER(o.skills_required) LIKE :keyword
                OR o.id IN (
                    SELECT os.offer_id 
                    FROM offer_skills os
                    JOIN skills s ON os.skill_id = s.id
                    WHERE LOWER(s.name) LIKE :keyword
                )
            )";
            $params[':keyword'] = '%' . $keyword . '%';
            
            error_log("Recherche par mot-clé: " . $keyword);
        }
        
        if (!empty($filters['company_id'])) {
            $sql .= " AND o.company_id = :company_id";
            $params[':company_id'] = $filters['company_id'];
        }
        
        if (!empty($filters['location'])) {
            $sql .= " AND o.location LIKE :location";
            $params[':location'] = '%' . $filters['location'] . '%';
        }
        
        if (!empty($filters['start_date'])) {
            $sql .= " AND o.start_date >= :start_date";
            $params[':start_date'] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $sql .= " AND o.end_date <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }
        
        if (!empty($filters['status'])) {
            $sql .= " AND o.status = :status";
            $params[':status'] = $filters['status'];
        }
        
        // Filtrage par compétences - Nouvelle approche simplifiée
        if (!empty($filters['skills']) && is_array($filters['skills']) && count($filters['skills']) > 0) {
            // Construire une sous-requête sans utiliser de paramètres nommés pour les IN
            // Convertir les IDs de compétences en entiers pour éviter les injections SQL
            $safeSkillIds = array_map('intval', $filters['skills']);
            $skillIdsString = implode(',', $safeSkillIds);
            
            if (!empty($skillIdsString)) {
                $sql .= " AND o.id IN (
                    SELECT offer_id FROM offer_skills 
                    WHERE skill_id IN ($skillIdsString)
                    GROUP BY offer_id
                    HAVING COUNT(DISTINCT skill_id) = " . count($safeSkillIds) . "
                )";
            }
        }
        
        // Tri
        $orderBy = !empty($filters['sort']) ? $filters['sort'] : "o.created_at DESC";
        $sql .= " ORDER BY $orderBy";
        
        // Pagination
        $sql .= " LIMIT :limit OFFSET :offset";
        $params[':limit'] = (int)$limit;
        $params[':offset'] = (int)$offset;
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $offers = $stmt->fetchAll();
            
            // Récupérer les compétences pour chaque offre
            foreach ($offers as &$offer) {
                $offer['skills'] = $this->getOfferSkills($offer['id']);
            }
            
            return $offers;
        } catch (\Exception $e) {
            error_log("Erreur dans getAll des offres: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Compter le nombre total d'offres avec les mêmes filtres
     * 
     * @param array $filters Critères de filtrage
     * @return int Nombre total d'offres
     */
    public function countAll($filters = [])
    {
        $sql = "SELECT COUNT(DISTINCT o.id) as total
                FROM offers o
                INNER JOIN companies c ON o.company_id = c.id
                WHERE 1=1";
        
        $params = [];
        
        // Appliquer les filtres
        if (!empty($filters['keyword'])) {
            // Convertir le mot-clé en minuscules pour une recherche insensible à la casse
            $keyword = strtolower(trim($filters['keyword']));
            
            $sql .= " AND (
                LOWER(o.title) LIKE :keyword 
                OR LOWER(o.description) LIKE :keyword
                OR LOWER(c.name) LIKE :keyword
                OR LOWER(o.skills_required) LIKE :keyword
                OR o.id IN (
                    SELECT os.offer_id 
                    FROM offer_skills os
                    JOIN skills s ON os.skill_id = s.id
                    WHERE LOWER(s.name) LIKE :keyword
                )
            )";
            $params[':keyword'] = '%' . $keyword . '%';
        }
        
        if (!empty($filters['company_id'])) {
            $sql .= " AND o.company_id = :company_id";
            $params[':company_id'] = $filters['company_id'];
        }
        
        if (!empty($filters['location'])) {
            $sql .= " AND o.location LIKE :location";
            $params[':location'] = '%' . $filters['location'] . '%';
        }
        
        if (!empty($filters['start_date'])) {
            $sql .= " AND o.start_date >= :start_date";
            $params[':start_date'] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $sql .= " AND o.end_date <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }
        
        if (!empty($filters['status'])) {
            $sql .= " AND o.status = :status";
            $params[':status'] = $filters['status'];
        }
        
        // Filtrage par compétences - Nouvelle approche simplifiée
        if (!empty($filters['skills']) && is_array($filters['skills']) && count($filters['skills']) > 0) {
            // Convertir les IDs de compétences en entiers pour éviter les injections SQL
            $safeSkillIds = array_map('intval', $filters['skills']);
            $skillIdsString = implode(',', $safeSkillIds);
            
            if (!empty($skillIdsString)) {
                $sql .= " AND o.id IN (
                    SELECT offer_id FROM offer_skills 
                    WHERE skill_id IN ($skillIdsString)
                    GROUP BY offer_id
                    HAVING COUNT(DISTINCT skill_id) = " . count($safeSkillIds) . "
                )";
            }
        }
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (\Exception $e) {
            error_log("Erreur dans countAll des offres: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Récupérer une offre par son ID
     * 
     * @param int $id ID de l'offre
     * @return array|false Données de l'offre ou false si non trouvée
     */
    public function getById($id)
    {
        $sql = "SELECT o.*, c.name as company_name, c.address as company_address,
                c.city as company_city, c.email as company_email, c.website as company_website,
                (SELECT COUNT(*) FROM applications WHERE offer_id = o.id) as application_count
                FROM offers o
                INNER JOIN companies c ON o.company_id = c.id
                WHERE o.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $offer = $stmt->fetch();
        
        if ($offer) {
            // Récupérer les compétences associées
            $offer['skills'] = $this->getOfferSkills($id);
        }
        
        return $offer ?: false;
    }
    
    /**
     * Récupérer les compétences associées à une offre
     * 
     * @param int $offerId ID de l'offre
     * @return array Liste des compétences
     */
    public function getOfferSkills($offerId)
    {
        $sql = "SELECT s.* FROM skills s
                INNER JOIN offer_skills os ON s.id = os.skill_id
                WHERE os.offer_id = :offer_id
                ORDER BY s.category, s.name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':offer_id' => $offerId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Créer une nouvelle offre
     * 
     * @param array $data Données de l'offre
     * @return int|false ID de l'offre créée ou false en cas d'échec
     */
    public function create($data)
    {
        try {
            // Commencer une transaction
            $this->db->getConnection()->beginTransaction();
            
            // Vérifier si la compagnie existe
            $companyCheck = "SELECT id FROM companies WHERE id = :company_id";
            $stmt = $this->db->prepare($companyCheck);
            $stmt->execute([':company_id' => $data['company_id']]);
            if ($stmt->rowCount() === 0) {
                error_log("Erreur lors de la création de l'offre: La compagnie ID {$data['company_id']} n'existe pas");
                $this->db->getConnection()->rollBack();
                return false;
            }
            
            // Vérification simplifiée - juste les champs essentiels
            $sql = "INSERT INTO offers (title, description, company_id, location, 
                    start_date, end_date, status) 
                    VALUES (:title, :description, :company_id, :location, 
                    :start_date, :end_date, :status)";
            
            $params = [
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':company_id' => $data['company_id'],
                ':location' => $data['location'],
                ':start_date' => $data['start_date'],
                ':end_date' => $data['end_date'],
                ':status' => $data['status'] ?? 'active'
            ];
            
            // Ajouter les champs optionnels uniquement s'ils sont supportés par la base de données
            // Vérifier si la colonne salary existe
            try {
                $columnCheck = $this->db->query("SHOW COLUMNS FROM offers LIKE 'salary'", []);
                if ($columnCheck->rowCount() > 0 && isset($data['salary'])) {
                    $sql = "INSERT INTO offers (title, description, company_id, location, salary,
                            start_date, end_date, status) 
                            VALUES (:title, :description, :company_id, :location, :salary,
                            :start_date, :end_date, :status)";
                    $params[':salary'] = $data['salary'];
                }
            } catch (\Exception $e) {
                error_log("La colonne 'salary' n'existe peut-être pas: " . $e->getMessage());
                // Continuer sans cette colonne
            }
            
            // Vérifier si la colonne skills_required existe
            try {
                $columnCheck = $this->db->query("SHOW COLUMNS FROM offers LIKE 'skills_required'", []);
                if ($columnCheck->rowCount() > 0 && isset($data['skills_required'])) {
                    $sql = "INSERT INTO offers (title, description, company_id, location, start_date, end_date, skills_required, status) 
                            VALUES (:title, :description, :company_id, :location, :start_date, :end_date, :skills_required, :status)";
                    $params[':skills_required'] = $data['skills_required'];
                }
            } catch (\Exception $e) {
                error_log("La colonne 'skills_required' n'existe peut-être pas: " . $e->getMessage());
                // Continuer sans cette colonne
            }
            
            // Log des paramètres pour débogage
            error_log("Tentative d'insertion d'offre avec les paramètres: " . json_encode($params));
            error_log("SQL: " . $sql);
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $offerId = $this->db->getConnection()->lastInsertId();
            
            if (!$offerId) {
                error_log("Erreur: Aucun ID d'offre retourné après insertion");
                $this->db->getConnection()->rollBack();
                return false;
            }
            
            // Vérifier si la table offer_skills existe avant d'essayer d'insérer
            $tableExists = false;
            try {
                $tableCheck = $this->db->query("SHOW TABLES LIKE 'offer_skills'", []);
                $tableExists = ($tableCheck->rowCount() > 0);
            } catch (\Exception $e) {
                error_log("Erreur lors de la vérification de l'existence de la table offer_skills: " . $e->getMessage());
            }
            
            // Associer les compétences uniquement si la table existe
            if ($tableExists && !empty($data['skills']) && is_array($data['skills'])) {
                foreach ($data['skills'] as $skillId) {
                    $skillSql = "INSERT INTO offer_skills (offer_id, skill_id) VALUES (:offer_id, :skill_id)";
                    try {
                        $stmt = $this->db->prepare($skillSql);
                        $stmt->execute([
                            ':offer_id' => $offerId,
                            ':skill_id' => $skillId
                        ]);
                    } catch (\Exception $e) {
                        error_log("Erreur lors de l'association de la compétence ID $skillId: " . $e->getMessage());
                        // On continue même si une compétence échoue
                    }
                }
            } else if (!$tableExists) {
                error_log("La table offer_skills n'existe pas. Les compétences n'ont pas été associées.");
            }
            
            // Valider la transaction
            $this->db->getConnection()->commit();
            error_log("Offre créée avec succès, ID: $offerId");
            return $offerId;
            
        } catch (\Exception $e) {
            error_log("Erreur détaillée lors de la création de l'offre: " . $e->getMessage());
            error_log("Trace: " . $e->getTraceAsString());
            
            // Annuler la transaction
            if ($this->db->getConnection()->inTransaction()) {
                $this->db->getConnection()->rollBack();
            }
            return false;
        }
    }
    
    /**
     * Mettre à jour une offre
     * 
     * @param int $id ID de l'offre
     * @param array $data Données à mettre à jour
     * @return bool Succès ou échec
     */
    public function update($id, $data)
    {
        $sql = "UPDATE offers SET 
                title = :title,
                description = :description,
                company_id = :company_id,
                location = :location,
                salary = :salary,
                start_date = :start_date, 
                end_date = :end_date,
                skills_required = :skills_required,
                status = :status
                WHERE id = :id";
        
        $params = [
            ':id' => $id,
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':company_id' => $data['company_id'],
            ':location' => $data['location'],
            ':salary' => $data['salary'] ?? null,
            ':start_date' => $data['start_date'],
            ':end_date' => $data['end_date'],
            ':skills_required' => $data['skills_required'] ?? null,
            ':status' => $data['status'] ?? 'active'
        ];
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            // Mettre à jour les compétences
            if (!empty($data['skills']) && is_array($data['skills'])) {
                $this->updateOfferSkills($id, $data['skills']);
            }
            
            return true;
        } catch (\Exception $e) {
            error_log("Erreur lors de la mise à jour de l'offre: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprimer une offre
     * 
     * @param int $id ID de l'offre
     * @return bool Succès ou échec
     */
    public function delete($id)
    {
        $sql = "DELETE FROM offers WHERE id = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return true;
        } catch (\Exception $e) {
            error_log("Erreur lors de la suppression de l'offre: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Mettre à jour les compétences associées à une offre
     * 
     * @param int $offerId ID de l'offre
     * @param array $skillIds IDs des compétences
     * @return bool Succès ou échec
     */
    private function updateOfferSkills($offerId, $skillIds)
    {
        try {
            // Supprimer les associations actuelles
            $sql = "DELETE FROM offer_skills WHERE offer_id = :offer_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':offer_id' => $offerId]);
            
            // Aucune compétence à ajouter
            if (empty($skillIds)) {
                return true;
            }
            
            // Insertion individuelle pour éviter les problèmes de paramètres
            foreach ($skillIds as $skillId) {
                $sql = "INSERT INTO offer_skills (offer_id, skill_id) VALUES (:offer_id, :skill_id)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    ':offer_id' => $offerId,
                    ':skill_id' => $skillId
                ]);
            }
            
            return true;
        } catch (\Exception $e) {
            error_log("Erreur lors de la mise à jour des compétences de l'offre: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtenir les statistiques des offres
     * 
     * @return array Statistiques
     */
    public function getStatistics()
    {
        $stats = [];
        
        // Nombre total d'offres
        $sql = "SELECT COUNT(*) as total FROM offers";
        $stmt = $this->db->query($sql);
        $stats['total_offers'] = (int)$stmt->fetch()['total'];
        
        // Nombre d'offres par statut
        $sql = "SELECT status, COUNT(*) as count FROM offers GROUP BY status";
        $stmt = $this->db->query($sql);
        $stats['offers_by_status'] = $stmt->fetchAll();
        
        // Offres les plus populaires (nombre de candidatures)
        $sql = "SELECT o.id, o.title, c.name as company_name, COUNT(a.id) as application_count
                FROM offers o
                INNER JOIN companies c ON o.company_id = c.id
                LEFT JOIN applications a ON o.id = a.offer_id
                GROUP BY o.id, o.title, c.name
                ORDER BY application_count DESC
                LIMIT 5";
        $stmt = $this->db->query($sql);
        $stats['most_popular_offers'] = $stmt->fetchAll();
        
        // Répartition par compétence
        $sql = "SELECT s.name, s.category, COUNT(DISTINCT os.offer_id) as offer_count
                FROM skills s
                INNER JOIN offer_skills os ON s.id = os.skill_id
                GROUP BY s.id, s.name, s.category
                ORDER BY offer_count DESC";
        $stmt = $this->db->query($sql);
        $stats['skills_distribution'] = $stmt->fetchAll();
        
        // Répartition par entreprise
        $sql = "SELECT c.name, COUNT(o.id) as offer_count
                FROM companies c
                LEFT JOIN offers o ON c.id = o.company_id
                GROUP BY c.id, c.name
                HAVING offer_count > 0
                ORDER BY offer_count DESC
                LIMIT 10";
        $stmt = $this->db->query($sql);
        $stats['companies_distribution'] = $stmt->fetchAll();
        
        return $stats;
    }
    
    /**
     * Récupérer le nombre d'offres par entreprise
     * 
     * @return array Tableau associatif [company_id => count]
     */
    public function getOfferCountByCompany()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT c.id, c.name, COUNT(o.id) as offer_count
                FROM companies c
                LEFT JOIN offers o ON c.id = o.company_id
                GROUP BY c.id, c.name
                ORDER BY offer_count DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération du nombre d'offres par entreprise: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer le nombre d'offres par lieu
     * 
     * @return array Tableau associatif [location => count]
     */
    public function getOfferCountByLocation()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT location, COUNT(*) as offer_count
                FROM offers
                GROUP BY location
                ORDER BY offer_count DESC
                LIMIT 10
            ");
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération du nombre d'offres par lieu: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer les offres à venir (date de début dans le futur)
     * 
     * @return array Liste des offres à venir
     */
    public function getUpcomingOffers()
    {
        try {
            $now = date('Y-m-d');
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count
                FROM offers
                WHERE start_date > :now
                AND status = 'active'
            ");
            $stmt->execute([':now' => $now]);
            return $stmt->fetch(\PDO::FETCH_ASSOC)['count'] ?? 0;
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des offres à venir: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Récupérer les offres en cours (date de début passée, date de fin dans le futur)
     * 
     * @return array Liste des offres en cours
     */
    public function getCurrentOffers()
    {
        try {
            $now = date('Y-m-d');
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count
                FROM offers
                WHERE start_date <= :now
                AND end_date >= :now
                AND status = 'active'
            ");
            $stmt->execute([':now' => $now]);
            return $stmt->fetch(\PDO::FETCH_ASSOC)['count'] ?? 0;
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des offres en cours: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Récupérer les offres passées (date de fin dans le passé)
     * 
     * @return array Liste des offres passées
     */
    public function getPastOffers()
    {
        try {
            $now = date('Y-m-d');
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count
                FROM offers
                WHERE end_date < :now
            ");
            $stmt->execute([':now' => $now]);
            return $stmt->fetch(\PDO::FETCH_ASSOC)['count'] ?? 0;
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des offres passées: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Récupérer les compétences les plus demandées dans les offres
     * 
     * @return array Liste des compétences et leur fréquence
     */
    public function getPopularSkills()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT s.id, s.name, s.category, COUNT(*) as skill_count
                FROM offer_skills os
                JOIN skills s ON os.skill_id = s.id
                JOIN offers o ON os.offer_id = o.id
                WHERE o.status = 'active'
                GROUP BY s.id, s.name, s.category
                ORDER BY skill_count DESC
                LIMIT 10
            ");
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des compétences populaires: " . $e->getMessage());
            return [];
        }
    }
}
