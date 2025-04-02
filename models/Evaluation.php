<?php
/**
 * Modèle Evaluation pour la gestion des évaluations d'entreprises
 */
class Evaluation {
    // Connexion à la base de données et table
    private $conn;
    private $table = "company_ratings";
    
    // Propriétés d'une évaluation
    public $id;
    public $entreprise_id; // Correspond à company_id dans la base
    public $user_id;
    public $note;          // Correspond à rating dans la base
    public $commentaire;   // Correspond à comment dans la base
    public $date_evaluation; // Correspond à created_at dans la base
    
    /**
     * Constructeur avec $db comme connexion à la base de données
     * @param PDO $db Connexion à la base de données
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Récupère toutes les évaluations pour une entreprise
     * @param int $entreprise_id ID de l'entreprise
     * @param int $page Numéro de la page (pour pagination)
     * @param int $perPage Nombre d'évaluations par page
     * @return PDOStatement Résultat de la requête
     */
    public function readByEntreprise($entreprise_id, $page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        
        $query = "SELECT e.*, u.firstname as user_firstname, u.lastname as user_lastname, u.role as user_role
                FROM " . $this->table . " e
                JOIN users u ON e.user_id = u.id
                WHERE e.company_id = :entreprise_id
                ORDER BY e.created_at DESC
                LIMIT :offset, :perPage";
        
        $stmt = $this->conn->prepare($query);
        
        // Sécurisation des données
        $entreprise_id = htmlspecialchars(strip_tags($entreprise_id));
        
        // Liaison des paramètres
        $stmt->bindParam(':entreprise_id', $entreprise_id, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':perPage', $perPage, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt;
    }
    
    /**
     * Récupère toutes les évaluations d'un utilisateur
     * @param int $user_id ID de l'utilisateur
     * @return PDOStatement Résultat de la requête
     */
    public function readByUser($user_id) {
        $query = "SELECT e.*, c.name as entreprise_nom, c.sector as secteur_activite, c.city as ville, c.country as pays
                FROM " . $this->table . " e
                JOIN companies c ON e.company_id = c.id
                WHERE e.user_id = :user_id
                ORDER BY e.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        
        // Sécurisation des données
        $user_id = htmlspecialchars(strip_tags($user_id));
        
        // Liaison des paramètres
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt;
    }
    
    /**
     * Vérifie si un utilisateur a déjà évalué une entreprise
     * @param int $entreprise_id ID de l'entreprise
     * @param int $user_id ID de l'utilisateur
     * @return bool True si l'utilisateur a déjà évalué l'entreprise, false sinon
     */
    public function userHasEvaluated($entreprise_id, $user_id) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                 WHERE company_id = :entreprise_id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        
        // Sécurisation des données
        $entreprise_id = htmlspecialchars(strip_tags($entreprise_id));
        $user_id = htmlspecialchars(strip_tags($user_id));
        
        // Liaison des paramètres
        $stmt->bindParam(':entreprise_id', $entreprise_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['count'] > 0;
    }
    
    /**
     * Récupère l'évaluation d'un utilisateur pour une entreprise
     * @param int $entreprise_id ID de l'entreprise
     * @param int $user_id ID de l'utilisateur
     * @return bool Succès de l'opération
     */
    public function readUserEvaluation($entreprise_id, $user_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE company_id = :entreprise_id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        
        // Sécurisation des données
        $entreprise_id = htmlspecialchars(strip_tags($entreprise_id));
        $user_id = htmlspecialchars(strip_tags($user_id));
        
        // Liaison des paramètres
        $stmt->bindParam(':entreprise_id', $entreprise_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            // Définition des propriétés
            $this->id = $row['id'];
            $this->entreprise_id = $row['company_id'];
            $this->user_id = $row['user_id'];
            $this->note = $row['rating'];
            $this->commentaire = $row['comment'];
            $this->date_evaluation = $row['created_at'];
            return true;
        }
        
        return false;
    }
    
    /**
     * Crée une nouvelle évaluation ou met à jour une évaluation existante
     * @return bool Succès de l'opération
     */
    public function create() {
        // Vérifier si l'utilisateur a déjà évalué cette entreprise
        if ($this->userHasEvaluated($this->entreprise_id, $this->user_id)) {
            return $this->update(); // Met à jour l'évaluation existante
        }
        
        try {
            $this->conn->beginTransaction();
            
            $query = "INSERT INTO " . $this->table . " (company_id, user_id, rating, comment) 
                     VALUES(:entreprise_id, :user_id, :note, :commentaire)";
            $stmt = $this->conn->prepare($query);
            
            // Sécurisation des données
            $this->entreprise_id = htmlspecialchars(strip_tags($this->entreprise_id));
            $this->user_id = htmlspecialchars(strip_tags($this->user_id));
            $this->note = htmlspecialchars(strip_tags($this->note));
            $this->commentaire = htmlspecialchars(strip_tags($this->commentaire));
            
            // Validation supplémentaire
            if ($this->note < 1 || $this->note > 5) {
                throw new Exception("La note doit être comprise entre 1 et 5");
            }
            
            // Liaison des paramètres
            $stmt->bindParam(':entreprise_id', $this->entreprise_id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
            $stmt->bindParam(':note', $this->note, PDO::PARAM_INT);
            $stmt->bindParam(':commentaire', $this->commentaire, PDO::PARAM_STR);
            
            // Exécution de la requête
            $result = $stmt->execute();
            
            if ($result) {
                $this->id = $this->conn->lastInsertId();
                // Mise à jour de la note moyenne de l'entreprise
                $this->updateCompanyAverageRating($this->entreprise_id);
                $this->conn->commit();
                return true;
            }
            
            $this->conn->rollBack();
            return false;
            
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log("Erreur lors de la création de l'évaluation: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Met à jour une évaluation existante
     * @return bool Succès de l'opération
     */
    public function update() {
        try {
            $this->conn->beginTransaction();
            
            $query = "UPDATE " . $this->table . " 
                    SET rating = :note, comment = :commentaire
                    WHERE company_id = :entreprise_id AND user_id = :user_id";
            
            $stmt = $this->conn->prepare($query);
            
            // Sécurisation des données
            $this->entreprise_id = htmlspecialchars(strip_tags($this->entreprise_id));
            $this->user_id = htmlspecialchars(strip_tags($this->user_id));
            $this->note = htmlspecialchars(strip_tags($this->note));
            $this->commentaire = htmlspecialchars(strip_tags($this->commentaire));
            
            // Validation supplémentaire
            if ($this->note < 1 || $this->note > 5) {
                throw new Exception("La note doit être comprise entre 1 et 5");
            }
            
            // Liaison des paramètres
            $stmt->bindParam(':entreprise_id', $this->entreprise_id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
            $stmt->bindParam(':note', $this->note, PDO::PARAM_INT);
            $stmt->bindParam(':commentaire', $this->commentaire, PDO::PARAM_STR);
            
            // Exécution de la requête
            $result = $stmt->execute();
            
            if ($result && $stmt->rowCount() > 0) {
                // Mise à jour de la note moyenne de l'entreprise
                $this->updateCompanyAverageRating($this->entreprise_id);
                $this->conn->commit();
                return true;
            }
            
            $this->conn->rollBack();
            return false;
            
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log("Erreur lors de la mise à jour de l'évaluation: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprime une évaluation
     * @param int $id ID de l'évaluation
     * @param int $user_id ID de l'utilisateur (pour vérification des droits)
     * @param string $user_role Rôle de l'utilisateur
     * @return bool Succès de l'opération
     */
    public function delete($id, $user_id, $user_role = null) {
        try {
            $this->conn->beginTransaction();
            
            // Récupérer l'entreprise_id avant la suppression
            $query = "SELECT company_id FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $evaluation = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$evaluation) {
                $this->conn->rollBack();
                return false;
            }
            
            $entreprise_id = $evaluation['company_id'];
            
            // Construction de la requête en fonction des droits
            $query = "DELETE FROM " . $this->table . " WHERE id = :id";
            $params = [':id' => $id];
            
            // Si l'utilisateur n'est pas admin, il ne peut supprimer que ses propres évaluations
            if ($user_role !== 'admin' && $user_role !== 'administrateur' && $user_role !== 'pilote') {
                $query .= " AND user_id = :user_id";
                $params[':user_id'] = $user_id;
            }
            
            $stmt = $this->conn->prepare($query);
            
            // Liaison des paramètres
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            
            // Exécution de la requête
            $result = $stmt->execute();
            
            if ($result && $stmt->rowCount() > 0) {
                // Mise à jour de la note moyenne de l'entreprise
                $this->updateCompanyAverageRating($entreprise_id);
                $this->conn->commit();
                return true;
            }
            
            $this->conn->rollBack();
            return false;
            
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log("Erreur lors de la suppression de l'évaluation: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Calcule et retourne la note moyenne d'une entreprise
     * @param int $entreprise_id ID de l'entreprise
     * @return array Données de notation (moyenne, nombre d'évaluations)
     */
    public function getAverageRating($entreprise_id) {
        $query = "SELECT AVG(rating) as average, COUNT(*) as count 
                 FROM " . $this->table . " 
                 WHERE company_id = :entreprise_id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sécurisation des données
        $entreprise_id = htmlspecialchars(strip_tags($entreprise_id));
        
        // Liaison du paramètre
        $stmt->bindParam(':entreprise_id', $entreprise_id, PDO::PARAM_INT);
        
        // Exécution de la requête
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'average' => $result['average'] ? round($result['average'], 1) : 0,
            'count' => (int) $result['count']
        ];
    }
    
    /**
     * Met à jour la note moyenne stockée dans la table entreprises
     * @param int $entreprise_id ID de l'entreprise
     * @return bool Succès de l'opération
     */
    private function updateCompanyAverageRating($entreprise_id) {
        $rating = $this->getAverageRating($entreprise_id);
        
        // Note: La mise à jour n'est pas nécessaire car la table companies n'a pas de colonnes pour stocker la moyenne
        // Cette fonction est conservée pour maintenir la cohérence avec le reste du code et pour une future extension
        return true;
    }
    
    /**
     * Compte le nombre total d'évaluations pour une entreprise
     * @param int $entreprise_id ID de l'entreprise
     * @return int Nombre total d'évaluations
     */
    public function countByEntreprise($entreprise_id) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE company_id = :entreprise_id";
        $stmt = $this->conn->prepare($query);
        
        // Liaison du paramètre
        $stmt->bindParam(':entreprise_id', $entreprise_id, PDO::PARAM_INT);
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (int) $row['total'];
    }
}
