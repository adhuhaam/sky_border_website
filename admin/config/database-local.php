<?php
/**
 * Local Database Configuration
 * Sky Border Solutions CMS - For local development
 */

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        // Local development configuration
        $this->host = 'localhost';
        $this->db_name = 'sky_border';
        $this->username = 'root';
        $this->password = 'Ompl@65482*';
    }

    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch(PDOException $exception) {
            error_log("Database connection error: " . $exception->getMessage());
            throw new Exception("Database connection failed");
        }
        
        return $this->conn;
    }
    
    public function testConnection() {
        try {
            $conn = $this->getConnection();
            return $conn !== null;
        } catch (Exception $e) {
            return false;
        }
    }
}
?>
