<?php
/**
 * Production Database Configuration
 * Sky Border Solutions CMS - Production Environment
 * 
 * IMPORTANT: Update these credentials with your actual production server details
 */

class Database {
    private $host = '162.213.255.53'; // Production server IP address
    private $db_name = 'skydfcaf_sky_border';
    private $username = 'skydfcaf_sky_border_user';
    private $password = 'Ompl@65482*';
    public $conn;

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