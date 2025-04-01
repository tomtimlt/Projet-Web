<?php
namespace Database;

class Database 
{
    private static $instance = null;
    private $connection;

    private function __construct() 
    {
        // Modification pour charger correctement le fichier de configuration
        $dbConfigFile = __DIR__ . '/../Config/database.php';
        if (file_exists($dbConfigFile)) {
            $dbConfig = require $dbConfigFile;
            
            // Vérifier que dbConfig est bien un tableau
            if (is_array($dbConfig)) {
                $host = $dbConfig['host'] ?? 'localhost';
                $dbname = $dbConfig['dbname'] ?? 'projet_web';
                $username = $dbConfig['username'] ?? 'root';
                $password = $dbConfig['password'] ?? '';
                $charset = $dbConfig['charset'] ?? 'utf8';
                $options = $dbConfig['options'] ?? [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                ];
                
                $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";
                
                try {
                    $this->connection = new \PDO($dsn, $username, $password, $options);
                } catch (\PDOException $e) {
                    throw new \Exception("Erreur de connexion à la base de données: " . $e->getMessage());
                }
            } else {
                throw new \Exception("Le fichier de configuration de la base de données ne retourne pas un tableau valide.");
            }
        } else {
            throw new \Exception("Le fichier de configuration de la base de données est introuvable.");
        }
    }

    private function __clone() {}

    public static function getInstance() 
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() 
    {
        return $this->connection;
    }

    public function query($sql, $params = []) 
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
