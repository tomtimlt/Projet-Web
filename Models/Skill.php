<?php
namespace Models;

use Database\Database;

class Skill
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Récupère toutes les compétences
     * 
     * @return array Liste des compétences
     */
    public function getAll()
    {
        $sql = "SELECT * FROM skills ORDER BY category, name";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère une compétence par son ID
     * 
     * @param int $id ID de la compétence
     * @return array|bool Les données de la compétence ou false si non trouvée
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM skills WHERE id = :id";
        $stmt = $this->db->query($sql, [':id' => $id]);
        return $stmt->fetch() ?: false;
    }
    
    /**
     * Recherche des compétences par nom
     * 
     * @param string $term Terme de recherche
     * @return array Liste des compétences correspondantes
     */
    public function searchByName($term)
    {
        $sql = "SELECT * FROM skills WHERE name LIKE :term ORDER BY name";
        $stmt = $this->db->query($sql, [':term' => '%' . $term . '%']);
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère toutes les compétences par catégorie
     * 
     * @return array Liste des compétences groupées par catégorie
     */
    public function getAllByCategory()
    {
        $skills = $this->getAll();
        $grouped = [];
        
        foreach ($skills as $skill) {
            $category = $skill['category'] ?? 'Non catégorisé';
            if (!isset($grouped[$category])) {
                $grouped[$category] = [];
            }
            $grouped[$category][] = $skill;
        }
        
        return $grouped;
    }
    
    /**
     * Crée une nouvelle compétence
     * 
     * @param array $data Les données de la compétence
     * @return int|bool L'ID de la compétence créée ou false en cas d'échec
     */
    public function create($data)
    {
        $sql = "INSERT INTO skills (name, category) VALUES (:name, :category)";
        
        $params = [
            ':name' => $data['name'],
            ':category' => $data['category'] ?? null
        ];
        
        try {
            $this->db->query($sql, $params);
            return $this->db->getConnection()->lastInsertId();
        } catch (\Exception $e) {
            error_log("Erreur lors de la création de la compétence: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Met à jour une compétence
     * 
     * @param int $id ID de la compétence
     * @param array $data Les données à mettre à jour
     * @return bool Succès ou échec
     */
    public function update($id, $data)
    {
        $sql = "UPDATE skills SET name = :name, category = :category WHERE id = :id";
        
        $params = [
            ':id' => $id,
            ':name' => $data['name'],
            ':category' => $data['category'] ?? null
        ];
        
        try {
            $this->db->query($sql, $params);
            return true;
        } catch (\Exception $e) {
            error_log("Erreur lors de la mise à jour de la compétence: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprime une compétence
     * 
     * @param int $id ID de la compétence
     * @return bool Succès ou échec
     */
    public function delete($id)
    {
        $sql = "DELETE FROM skills WHERE id = :id";
        
        try {
            $this->db->query($sql, [':id' => $id]);
            return true;
        } catch (\Exception $e) {
            error_log("Erreur lors de la suppression de la compétence: " . $e->getMessage());
            return false;
        }
    }
}
