<?php
/**
 * Database Configuration
 * Sky Border Solutions CMS - Auto-detects environment
 */

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        // Production server configuration
        $this->host = '162.213.255.53';
        $this->db_name = 'skydfcaf_sky_border';
        $this->username = 'skydfcaf_sky_border_user';
        $this->password = 'Ompl@65482*';
    }

    private function isLocalEnvironment() {
        // Check if we're on localhost or development environment
        $serverName = $_SERVER['SERVER_NAME'] ?? '';
        $serverAddr = $_SERVER['SERVER_ADDR'] ?? '';
        $httpHost = $_SERVER['HTTP_HOST'] ?? '';
        
        // Check for command line usage
        if (php_sapi_name() === 'cli') {
            // If running from command line, check if we're on local machine
            $hostname = gethostname();
            return strpos($hostname, 'Adhus-MacBook-Air') !== false || 
                   strpos($hostname, 'MacBook') !== false ||
                   strpos($hostname, 'localhost') !== false;
        }
        
        return in_array($serverName, ['localhost', '127.0.0.1', '::1']) || 
               in_array($serverAddr, ['127.0.0.1', '::1']) ||
               strpos($serverName, '.local') !== false ||
               strpos($serverName, '.test') !== false ||
               strpos($httpHost, 'localhost') !== false ||
               strpos($httpHost, '127.0.0.1') !== false;
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

    public function getEnvironmentInfo() {
        return [
            'environment' => $this->isLocalEnvironment() ? 'local' : 'production',
            'host' => $this->host,
            'database' => $this->db_name,
            'username' => $this->username,
            'sapi' => php_sapi_name(),
            'hostname' => gethostname()
        ];
    }
}
?>
