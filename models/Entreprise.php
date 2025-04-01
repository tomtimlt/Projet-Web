<?php
/**
 * Modèle Entreprise pour la gestion des entreprises
 */
class Entreprise {
    // Connexion à la base de données et table
    private $conn;
    private $table = "entreprises";
    
    // Propriétés d'une entreprise
    public $id;
    public $nom;
    public $secteur_activite;
    public $adresse;
    public $ville;
    public $code_postal;
    public $pays;
    public $telephone;
    public $email;
    public $site_web;
    public $description;
    public $logo;
    public $date_creation;
    public $date_modification;
    
    /**
     * Constructeur avec $db comme connexion à la base de données
     * @param PDO $db Connexion à la base de données
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Récupère toutes les entreprises
     * @return PDOStatement Résultat de la requête
     */
    public function readAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY nom ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    /**
     * Recherche d'entreprises selon plusieurs critères
     * @param array $criteria Critères de recherche
     * @return PDOStatement Résultat de la requête
     */
    public function search($criteria) {
        // Initialisation de la requête
        $query = "SELECT e.*, 
                (SELECT COUNT(*) FROM offres o WHERE o.entreprise_id = e.id) AS nombre_offres,
                (SELECT COALESCE(AVG(note), 0) FROM evaluations ev WHERE ev.entreprise_id = e.id) AS note_moyenne
                FROM " . $this->table . " e WHERE 1=1";
        
        // Tableau de paramètres pour la requête
        $params = [];
        
        // Ajout des critères de recherche
        if (!empty($criteria['nom'])) {
            $query .= " AND e.nom LIKE :nom";
            $params[':nom'] = "%" . $criteria['nom'] . "%";
        }
        
        if (!empty($criteria['secteur'])) {
            $query .= " AND e.secteur_activite LIKE :secteur";
            $params[':secteur'] = "%" . $criteria['secteur'] . "%";
        }
        
        if (!empty($criteria['ville'])) {
            $query .= " AND e.ville LIKE :ville";
            $params[':ville'] = "%" . $criteria['ville'] . "%";
        }
        
        if (!empty($criteria['pays'])) {
            $query .= " AND e.pays LIKE :pays";
            $params[':pays'] = "%" . $criteria['pays'] . "%";
        }
        
        // Tri des résultats
        $query .= " ORDER BY e.nom ASC";
        
        // Préparation et exécution de la requête
        $stmt = $this->conn->prepare($query);
        
        // Liaison des paramètres
        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value);
        }
        
        $stmt->execute();
        return $stmt;
    }
    
    /**
     * Récupère une entreprise par son ID
     * @param int $id ID de l'entreprise
     * @return bool Succès de l'opération
     */
    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        // Protection contre l'injection SQL
        $id = htmlspecialchars(strip_tags($id));
        $stmt->bindParam(':id', $id);
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            // Définition des propriétés de l'objet
            $this->id = $row['id'];
            $this->nom = $row['nom'];
            $this->secteur_activite = $row['secteur_activite'];
            $this->adresse = $row['adresse'];
            $this->ville = $row['ville'];
            $this->code_postal = $row['code_postal'];
            $this->pays = $row['pays'];
            $this->telephone = $row['telephone'];
            $this->email = $row['email'];
            $this->site_web = $row['site_web'];
            $this->description = $row['description'];
            $this->logo = $row['logo'];
            $this->date_creation = $row['date_creation'];
            $this->date_modification = $row['date_modification'];
            return true;
        }
        
        return false;
    }
    
    /**
     * Crée une nouvelle entreprise
     * @return bool Succès de l'opération
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                (nom, secteur_activite, adresse, ville, code_postal, pays, telephone, email, site_web, description, logo) 
                VALUES 
                (:nom, :secteur_activite, :adresse, :ville, :code_postal, :pays, :telephone, :email, :site_web, :description, :logo)";
        
        $stmt = $this->conn->prepare($query);
        
        // Sécurisation des données
        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->secteur_activite = htmlspecialchars(strip_tags($this->secteur_activite));
        $this->adresse = htmlspecialchars(strip_tags($this->adresse));
        $this->ville = htmlspecialchars(strip_tags($this->ville));
        $this->code_postal = htmlspecialchars(strip_tags($this->code_postal));
        $this->pays = htmlspecialchars(strip_tags($this->pays));
        $this->telephone = htmlspecialchars(strip_tags($this->telephone));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->site_web = htmlspecialchars(strip_tags($this->site_web));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->logo = htmlspecialchars(strip_tags($this->logo));
        
        // Liaison des paramètres
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':secteur_activite', $this->secteur_activite);
        $stmt->bindParam(':adresse', $this->adresse);
        $stmt->bindParam(':ville', $this->ville);
        $stmt->bindParam(':code_postal', $this->code_postal);
        $stmt->bindParam(':pays', $this->pays);
        $stmt->bindParam(':telephone', $this->telephone);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':site_web', $this->site_web);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':logo', $this->logo);
        
        // Exécution de la requête
        if ($stmt->execute()) {
            // Récupération de l'ID inséré
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    /**
     * Met à jour une entreprise
     * @return bool Succès de l'opération
     */
    public function update() {
        $query = "UPDATE " . $this->table . " SET 
                nom = :nom, 
                secteur_activite = :secteur_activite, 
                adresse = :adresse, 
                ville = :ville, 
                code_postal = :code_postal, 
                pays = :pays, 
                telephone = :telephone, 
                email = :email, 
                site_web = :site_web, 
                description = :description, 
                logo = :logo
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sécurisation des données
        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->secteur_activite = htmlspecialchars(strip_tags($this->secteur_activite));
        $this->adresse = htmlspecialchars(strip_tags($this->adresse));
        $this->ville = htmlspecialchars(strip_tags($this->ville));
        $this->code_postal = htmlspecialchars(strip_tags($this->code_postal));
        $this->pays = htmlspecialchars(strip_tags($this->pays));
        $this->telephone = htmlspecialchars(strip_tags($this->telephone));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->site_web = htmlspecialchars(strip_tags($this->site_web));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->logo = htmlspecialchars(strip_tags($this->logo));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        // Liaison des paramètres
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':secteur_activite', $this->secteur_activite);
        $stmt->bindParam(':adresse', $this->adresse);
        $stmt->bindParam(':ville', $this->ville);
        $stmt->bindParam(':code_postal', $this->code_postal);
        $stmt->bindParam(':pays', $this->pays);
        $stmt->bindParam(':telephone', $this->telephone);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':site_web', $this->site_web);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':logo', $this->logo);
        $stmt->bindParam(':id', $this->id);
        
        // Exécution de la requête
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Supprime une entreprise
     * @param int $id ID de l'entreprise à supprimer
     * @return bool Succès de l'opération
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        // Sécurisation des données
        $id = htmlspecialchars(strip_tags($id));
        
        // Liaison du paramètre
        $stmt->bindParam(':id', $id);
        
        // Exécution de la requête
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Récupère les statistiques d'une entreprise
     * @param int $id ID de l'entreprise
     * @return array Statistiques de l'entreprise
     */
    public function getStats($id) {
        $query = "SELECT 
                (SELECT COUNT(*) FROM offres WHERE entreprise_id = :id) AS nombre_offres,
                (SELECT COALESCE(AVG(note), 0) FROM evaluations WHERE entreprise_id = :id) AS note_moyenne,
                (SELECT COUNT(*) FROM evaluations WHERE entreprise_id = :id) AS nombre_evaluations";
        
        $stmt = $this->conn->prepare($query);
        
        // Sécurisation des données
        $id = htmlspecialchars(strip_tags($id));
        
        // Liaison du paramètre
        $stmt->bindParam(':id', $id);
        
        // Exécution de la requête
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère les entreprises récemment ajoutées
     * @param int $limit Nombre d'entreprises à récupérer
     * @return array Liste des entreprises récentes
     */
    public function getRecentEntreprises($limit = 5) {
        // Requête pour récupérer les entreprises récentes avec leur note moyenne
        $query = "SELECT e.*, AVG(ev.note) as note_moyenne, COUNT(ev.id) as nb_evaluations 
                 FROM " . $this->table . " e 
                 LEFT JOIN evaluations ev ON e.id = ev.entreprise_id 
                 GROUP BY e.id 
                 ORDER BY e.date_creation DESC 
                 LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
