<?php
namespace Models;

use Database\Database;

class User 
{
    private $db;
    
    public function __construct() 
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Crée un nouvel utilisateur
     * 
     * @param array $userData Les données de l'utilisateur
     * @return int|bool L'ID de l'utilisateur créé ou false en cas d'échec
     */
    public function create($userData) 
    {
        // Vérifier que l'email n'existe pas déjà
        if ($this->findByEmail($userData['email'])) {
            return false;
        }
        
        // Hacher le mot de passe
        $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (email, password, firstname, lastname, role) 
                VALUES (:email, :password, :firstname, :lastname, :role)";
                
        $params = [
            ':email' => $userData['email'],
            ':password' => $hashedPassword,
            ':firstname' => $userData['firstname'],
            ':lastname' => $userData['lastname'],
            ':role' => $userData['role']
        ];
        
        try {
            $this->db->query($sql, $params);
            return $this->db->getConnection()->lastInsertId();
        } catch (\Exception $e) {
            error_log("Erreur lors de la création de l'utilisateur: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Trouve un utilisateur par son ID
     * 
     * @param int $id ID de l'utilisateur
     * @return array|bool Les données de l'utilisateur ou false s'il n'est pas trouvé
     */
    public function find($id) 
    {
        $sql = "SELECT id, email, firstname, lastname, role, is_active, created_at, updated_at 
                FROM users WHERE id = :id";
        $stmt = $this->db->query($sql, [':id' => $id]);
        
        return $stmt->fetch() ?: false;
    }
    
    /**
     * Trouve un utilisateur par son email
     * 
     * @param string $email Email de l'utilisateur
     * @return array|bool Les données de l'utilisateur ou false s'il n'est pas trouvé
     */
    public function findByEmail($email) 
    {
        $sql = "SELECT id, email, password, firstname, lastname, role, is_active, created_at, updated_at 
                FROM users WHERE email = :email";
        $stmt = $this->db->query($sql, [':email' => $email]);
        
        return $stmt->fetch() ?: false;
    }
    
    /**
     * Vérifie les identifiants d'un utilisateur
     * 
     * @param string $email Email de l'utilisateur
     * @param string $password Mot de passe à vérifier
     * @return array|bool Les données de l'utilisateur ou false si les identifiants sont incorrects
     */
    public function authenticate($email, $password) 
    {
        $user = $this->findByEmail($email);
        
        if (!$user) {
            return false;
        }
        
        if (!$user['is_active']) {
            return false;
        }
        
        if (password_verify($password, $user['password'])) {
            // Ne pas renvoyer le mot de passe
            unset($user['password']);
            return $user;
        }
        
        return false;
    }
    
    /**
     * Récupère tous les utilisateurs avec un rôle spécifique
     * 
     * @param string $role Le rôle à filtrer (optionnel)
     * @return array La liste des utilisateurs
     */
    public function getAllByRole($role = null) 
    {
        $sql = "SELECT id, email, firstname, lastname, role, is_active, created_at, updated_at FROM users";
        $params = [];
        
        if ($role) {
            $sql .= " WHERE role = :role";
            $params[':role'] = $role;
        }
        
        $stmt = $this->db->query($sql, $params);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Met à jour un utilisateur
     * 
     * @param int $id ID de l'utilisateur
     * @param array $userData Données à mettre à jour
     * @return bool Succès ou échec
     */
    public function update($id, $userData) 
    {
        // Construire la requête dynamiquement en fonction des champs à mettre à jour
        $updateFields = [];
        $params = [':id' => $id];
        
        foreach ($userData as $key => $value) {
            if ($key === 'password' && !empty($value)) {
                $updateFields[] = "$key = :$key";
                $params[":$key"] = password_hash($value, PASSWORD_DEFAULT);
            } elseif ($key !== 'id' && $key !== 'password') {
                $updateFields[] = "$key = :$key";
                $params[":$key"] = $value;
            }
        }
        
        if (empty($updateFields)) {
            return true; // Rien à mettre à jour
        }
        
        $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = :id";
        
        try {
            $this->db->query($sql, $params);
            return true;
        } catch (\Exception $e) {
            error_log("Erreur lors de la mise à jour de l'utilisateur: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprime un utilisateur
     * 
     * @param int $id ID de l'utilisateur
     * @return bool Succès ou échec
     */
    public function delete($id) 
    {
        $sql = "DELETE FROM users WHERE id = :id";
        
        try {
            $this->db->query($sql, [':id' => $id]);
            return true;
        } catch (\Exception $e) {
            error_log("Erreur lors de la suppression de l'utilisateur: " . $e->getMessage());
            return false;
        }
    }
}
