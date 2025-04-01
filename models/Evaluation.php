<?php
/**
 * Modu00e8le Evaluation pour la gestion des u00e9valuations d'entreprises
 */
class Evaluation {
    // Connexion u00e0 la base de donnu00e9es et table
    private $conn;
    private $table = "evaluations";
    
    // Propriu00e9tu00e9s d'une u00e9valuation
    public $id;
    public $entreprise_id;
    public $user_id;
    public $note;
    public $commentaire;
    public $date_evaluation;
    
    /**
     * Constructeur avec $db comme connexion u00e0 la base de donnu00e9es
     * @param PDO $db Connexion u00e0 la base de donnu00e9es
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Ru00e9cupu00e8re toutes les u00e9valuations pour une entreprise
     * @param int $entreprise_id ID de l'entreprise
     * @return PDOStatement Ru00e9sultat de la requu00eate
     */
    public function readByEntreprise($entreprise_id) {
        $query = "SELECT e.*, u.name as user_name 
                FROM " . $this->table . " e
                JOIN users u ON e.user_id = u.id
                WHERE e.entreprise_id = :entreprise_id
                ORDER BY e.date_evaluation DESC";
        
        $stmt = $this->conn->prepare($query);
        
        // Su00e9curisation des donnu00e9es
        $entreprise_id = htmlspecialchars(strip_tags($entreprise_id));
        
        // Liaison des paramu00e8tres
        $stmt->bindParam(':entreprise_id', $entreprise_id);
        
        $stmt->execute();
        return $stmt;
    }
    
    /**
     * Vu00e9rifie si un utilisateur a du00e9ju00e0 u00e9valu00e9 une entreprise
     * @param int $entreprise_id ID de l'entreprise
     * @param int $user_id ID de l'utilisateur
     * @return bool True si l'utilisateur a du00e9ju00e0 u00e9valu00e9 l'entreprise, false sinon
     */
    public function userHasEvaluated($entreprise_id, $user_id) {
        $query = "SELECT id FROM " . $this->table . " WHERE entreprise_id = :entreprise_id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        
        // Su00e9curisation des donnu00e9es
        $entreprise_id = htmlspecialchars(strip_tags($entreprise_id));
        $user_id = htmlspecialchars(strip_tags($user_id));
        
        // Liaison des paramu00e8tres
        $stmt->bindParam(':entreprise_id', $entreprise_id);
        $stmt->bindParam(':user_id', $user_id);
        
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Ru00e9cupu00e8re l'u00e9valuation d'un utilisateur pour une entreprise
     * @param int $entreprise_id ID de l'entreprise
     * @param int $user_id ID de l'utilisateur
     * @return bool Succu00e8s de l'opu00e9ration
     */
    public function readUserEvaluation($entreprise_id, $user_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE entreprise_id = :entreprise_id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        
        // Su00e9curisation des donnu00e9es
        $entreprise_id = htmlspecialchars(strip_tags($entreprise_id));
        $user_id = htmlspecialchars(strip_tags($user_id));
        
        // Liaison des paramu00e8tres
        $stmt->bindParam(':entreprise_id', $entreprise_id);
        $stmt->bindParam(':user_id', $user_id);
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            // Du00e9finition des propriu00e9tu00e9s
            $this->id = $row['id'];
            $this->entreprise_id = $row['entreprise_id'];
            $this->user_id = $row['user_id'];
            $this->note = $row['note'];
            $this->commentaire = $row['commentaire'];
            $this->date_evaluation = $row['date_evaluation'];
            return true;
        }
        
        return false;
    }
    
    /**
     * Cru00e9e une nouvelle u00e9valuation
     * @return bool Succu00e8s de l'opu00e9ration
     */
    public function create() {
        // Vu00e9rifie si l'utilisateur a du00e9ju00e0 u00e9valu00e9 cette entreprise
        if ($this->userHasEvaluated($this->entreprise_id, $this->user_id)) {
            return $this->update(); // Met u00e0 jour l'u00e9valuation existante
        }
        
        $query = "INSERT INTO " . $this->table . " (entreprise_id, user_id, note, commentaire) VALUES(:entreprise_id, :user_id, :note, :commentaire)";
        $stmt = $this->conn->prepare($query);
        
        // Su00e9curisation des donnu00e9es
        $this->entreprise_id = htmlspecialchars(strip_tags($this->entreprise_id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->note = htmlspecialchars(strip_tags($this->note));
        $this->commentaire = htmlspecialchars(strip_tags($this->commentaire));
        
        // Liaison des paramu00e8tres
        $stmt->bindParam(':entreprise_id', $this->entreprise_id);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':note', $this->note);
        $stmt->bindParam(':commentaire', $this->commentaire);
        
        // Exu00e9cution de la requu00eate
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    /**
     * Met u00e0 jour une u00e9valuation existante
     * @return bool Succu00e8s de l'opu00e9ration
     */
    public function update() {
        $query = "UPDATE " . $this->table . " 
                SET note = :note, commentaire = :commentaire 
                WHERE entreprise_id = :entreprise_id AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        
        // Su00e9curisation des donnu00e9es
        $this->entreprise_id = htmlspecialchars(strip_tags($this->entreprise_id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->note = htmlspecialchars(strip_tags($this->note));
        $this->commentaire = htmlspecialchars(strip_tags($this->commentaire));
        
        // Liaison des paramu00e8tres
        $stmt->bindParam(':entreprise_id', $this->entreprise_id);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':note', $this->note);
        $stmt->bindParam(':commentaire', $this->commentaire);
        
        // Exu00e9cution de la requu00eate
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Supprime une u00e9valuation
     * @param int $id ID de l'u00e9valuation
     * @return bool Succu00e8s de l'opu00e9ration
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        // Su00e9curisation des donnu00e9es
        $id = htmlspecialchars(strip_tags($id));
        
        // Liaison du paramu00e8tre
        $stmt->bindParam(':id', $id);
        
        // Exu00e9cution de la requu00eate
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Calcule la note moyenne d'une entreprise
     * @param int $entreprise_id ID de l'entreprise
     * @return float Note moyenne de l'entreprise
     */
    public function getAverageRating($entreprise_id) {
        $query = "SELECT COALESCE(AVG(note), 0) as average_rating FROM " . $this->table . " WHERE entreprise_id = :entreprise_id";
        $stmt = $this->conn->prepare($query);
        
        // Su00e9curisation des donnu00e9es
        $entreprise_id = htmlspecialchars(strip_tags($entreprise_id));
        
        // Liaison du paramu00e8tre
        $stmt->bindParam(':entreprise_id', $entreprise_id);
        
        // Exu00e9cution de la requu00eate
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return round($row['average_rating'], 1);
    }
}
