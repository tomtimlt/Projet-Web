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
     * Récupère tous les étudiants avec possibilité de filtrage
     * 
     * @param array $filters Critères de filtrage (nom, prénom, email, etc.)
     * @param int $limit Nombre max d'étudiants à récupérer
     * @param int $offset Pagination
     * @return array Liste des étudiants
     */
    public function getAll($filters = [], $limit = 100, $offset = 0)
    {
        $sql = "SELECT u.*, 
                (SELECT COUNT(*) FROM applications WHERE student_id = u.id) AS application_count,
                (SELECT COUNT(*) FROM wishlists WHERE user_id = u.id) AS wishlist_count
                FROM users u WHERE u.role = 'etudiant'";
        
        $params = [];
        
        // Application des filtres
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
        
        // Tri
        $sql .= " ORDER BY u.lastname ASC, u.firstname ASC";
        
        // Pagination
        $sql .= " LIMIT :limit OFFSET :offset";
        $params[':limit'] = (int)$limit;
        $params[':offset'] = (int)$offset;
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère un étudiant par son ID
     * 
     * @param int $id ID de l'étudiant
     * @return array|bool Les données de l'étudiant ou false si non trouvé
     */
    public function getById($id)
    {
        $sql = "SELECT u.*, 
                (SELECT COUNT(*) FROM applications WHERE student_id = u.id) AS application_count,
                (SELECT COUNT(*) FROM wishlists WHERE user_id = u.id) AS wishlist_count
                FROM users u 
                WHERE u.id = :id AND u.role = 'etudiant'";
        
        $stmt = $this->db->query($sql, [':id' => $id]);
        return $stmt->fetch() ?: false;
    }
    
    /**
     * Crée un nouvel étudiant
     * 
     * @param array $data Les données de l'étudiant
     * @return int|bool L'ID de l'étudiant créé ou false en cas d'échec
     */
    public function create($data)
    {
        try {
            // Vérifier si l'email existe déjà
            $checkSql = "SELECT id FROM users WHERE email = :email";
            $checkStmt = $this->db->query($checkSql, [':email' => $data['email']]);
            
            if ($checkStmt->rowCount() > 0) {
                error_log("Erreur lors de la création de l'étudiant: L'email existe déjà");
                return false;
            }
            
            // Hashage du mot de passe
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO users (firstname, lastname, email, password, role) 
                    VALUES (:firstname, :lastname, :email, :password, 'etudiant')";
            
            $params = [
                ':firstname' => $data['firstname'],
                ':lastname' => $data['lastname'],
                ':email' => $data['email'],
                ':password' => $hashedPassword
            ];
            
            $this->db->query($sql, $params);
            return $this->db->getConnection()->lastInsertId();
        } catch (\Exception $e) {
            error_log("Erreur lors de la création de l'étudiant: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Met à jour un étudiant
     * 
     * @param int $id ID de l'étudiant
     * @param array $data Données à mettre à jour
     * @return bool Succès ou échec
     */
    public function update($id, $data)
    {
        try {
            // Vérifier si l'étudiant existe
            $student = $this->getById($id);
            if (!$student) {
                error_log("Erreur lors de la mise à jour: L'étudiant ID $id n'existe pas");
                return false;
            }
            
            // Vérifier si l'email est déjà utilisé par un autre utilisateur
            if (isset($data['email']) && $data['email'] !== $student['email']) {
                $checkSql = "SELECT id FROM users WHERE email = :email AND id != :id";
                $checkStmt = $this->db->query($checkSql, [':email' => $data['email'], ':id' => $id]);
                
                if ($checkStmt->rowCount() > 0) {
                    error_log("Erreur lors de la mise à jour: L'email existe déjà");
                    return false;
                }
            }
            
            // Construire la requête dynamiquement
            $updateFields = [];
            $params = [':id' => $id];
            
            if (isset($data['firstname'])) {
                $updateFields[] = "firstname = :firstname";
                $params[':firstname'] = $data['firstname'];
            }
            
            if (isset($data['lastname'])) {
                $updateFields[] = "lastname = :lastname";
                $params[':lastname'] = $data['lastname'];
            }
            
            if (isset($data['email'])) {
                $updateFields[] = "email = :email";
                $params[':email'] = $data['email'];
            }
            
            if (isset($data['password']) && !empty($data['password'])) {
                $updateFields[] = "password = :password";
                $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            
            if (empty($updateFields)) {
                return true; // Rien à mettre à jour
            }
            
            $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = :id AND role = 'etudiant'";
            $this->db->query($sql, $params);
            return true;
        } catch (\Exception $e) {
            error_log("Erreur lors de la mise à jour de l'étudiant: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprime un étudiant
     * 
     * @param int $id ID de l'étudiant
     * @return bool Succès ou échec
     */
    public function delete($id)
    {
        try {
            // Vérifier si l'étudiant existe
            $student = $this->getById($id);
            if (!$student) {
                error_log("Erreur lors de la suppression: L'étudiant ID $id n'existe pas");
                return false;
            }
            
            // Vérifier les candidatures actives
            $checkAppsSql = "SELECT COUNT(*) as count FROM applications 
                             WHERE student_id = :id AND status = 'accepted'";
            $checkAppsStmt = $this->db->query($checkAppsSql, [':id' => $id]);
            $result = $checkAppsStmt->fetch();
            
            if ($result['count'] > 0) {
                error_log("Erreur lors de la suppression: L'étudiant a des candidatures acceptées");
                return false;
            }
            
            // Supprimer toutes les candidatures et wishlists en premier (si pas de contraintes FK)
            $this->db->query("DELETE FROM applications WHERE student_id = :id", [':id' => $id]);
            $this->db->query("DELETE FROM wishlists WHERE user_id = :id", [':id' => $id]);
            
            // Supprimer l'étudiant
            $sql = "DELETE FROM users WHERE id = :id AND role = 'etudiant'";
            $this->db->query($sql, [':id' => $id]);
            return true;
        } catch (\Exception $e) {
            error_log("Erreur lors de la suppression de l'étudiant: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupère les statistiques d'un étudiant
     * 
     * @param int $id ID de l'étudiant
     * @return array|bool Statistiques ou false en cas d'échec
     */
    public function getStatistics($id)
    {
        try {
            // Vérifier si l'étudiant existe
            $student = $this->getById($id);
            if (!$student) {
                return false;
            }
            
            // Obtenir le nombre total de candidatures
            $appsSql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                        SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted,
                        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                        SUM(CASE WHEN status = 'withdrawn' THEN 1 ELSE 0 END) as withdrawn
                        FROM applications WHERE student_id = :id";
            $appsStmt = $this->db->query($appsSql, [':id' => $id]);
            $apps = $appsStmt->fetch();
            
            // Obtenir le nombre d'offres en wishlist
            $wishSql = "SELECT COUNT(*) as count FROM wishlists WHERE user_id = :id";
            $wishStmt = $this->db->query($wishSql, [':id' => $id]);
            $wish = $wishStmt->fetch();
            
            // Fusionner les résultats
            return [
                'applications' => [
                    'total' => (int)$apps['total'],
                    'pending' => (int)$apps['pending'],
                    'accepted' => (int)$apps['accepted'],
                    'rejected' => (int)$apps['rejected'],
                    'withdrawn' => (int)$apps['withdrawn']
                ],
                'wishlist' => [
                    'total' => (int)$wish['count']
                ]
            ];
        } catch (\Exception $e) {
            error_log("Erreur lors de la récupération des statistiques: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupère des statistiques globales sur les étudiants
     * 
     * @return array|bool Statistiques ou false en cas d'échec
     */
    public function getGlobalStatistics()
    {
        try {
            // Nombre total d'étudiants
            $countSql = "SELECT COUNT(*) as count FROM users WHERE role = 'etudiant'";
            $countStmt = $this->db->query($countSql);
            $count = $countStmt->fetch();
            
            // Statistiques sur les candidatures
            $appsSql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                        SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted,
                        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                        SUM(CASE WHEN status = 'withdrawn' THEN 1 ELSE 0 END) as withdrawn
                        FROM applications";
            $appsStmt = $this->db->query($appsSql);
            $apps = $appsStmt->fetch();
            
            // Nombre moyen de candidatures par étudiant
            $avgSql = "SELECT AVG(app_count) as avg_applications FROM 
                       (SELECT student_id, COUNT(*) as app_count FROM applications 
                        GROUP BY student_id) as app_counts";
            $avgStmt = $this->db->query($avgSql);
            $avg = $avgStmt->fetch();
            
            return [
                'student_count' => (int)$count['count'],
                'applications' => [
                    'total' => (int)$apps['total'],
                    'pending' => (int)$apps['pending'],
                    'accepted' => (int)$apps['accepted'],
                    'rejected' => (int)$apps['rejected'],
                    'withdrawn' => (int)$apps['withdrawn']
                ],
                'average_applications' => round($avg['avg_applications'], 2)
            ];
        } catch (\Exception $e) {
            error_log("Erreur lors de la récupération des statistiques globales: " . $e->getMessage());
            return false;
        }
    }
}
