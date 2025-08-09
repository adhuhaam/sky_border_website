<?php
/**
 * Authentication Class
 * Sky Border Solutions CMS
 */

require_once __DIR__ . '/../config/database.php';

class Auth {
    private $conn;
    
    public function __construct() {
        session_start();
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function login($username, $password) {
        try {
            $query = "SELECT id, username, email, password_hash, full_name, role, is_active FROM admin_users WHERE username = :username AND is_active = 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch();
                
                if (password_verify($password, $user['password_hash'])) {
                    // Update last login
                    $updateQuery = "UPDATE admin_users SET last_login = NOW() WHERE id = :id";
                    $updateStmt = $this->conn->prepare($updateQuery);
                    $updateStmt->bindParam(':id', $user['id']);
                    $updateStmt->execute();
                    
                    // Set session variables
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_user_id'] = $user['id'];
                    $_SESSION['admin_username'] = $user['username'];
                    $_SESSION['admin_full_name'] = $user['full_name'];
                    $_SESSION['admin_role'] = $user['role'];
                    $_SESSION['admin_email'] = $user['email'];
                    
                    return true;
                }
            }
            return false;
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }
    
    public function logout() {
        session_destroy();
        header('Location: index.php?logged_out=1');
        exit();
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: index.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
            exit();
        }
    }
    
    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['admin_user_id'],
                'username' => $_SESSION['admin_username'],
                'full_name' => $_SESSION['admin_full_name'],
                'role' => $_SESSION['admin_role'],
                'email' => $_SESSION['admin_email']
            ];
        }
        return null;
    }
    
    public function hasPermission($permission) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        $role = $_SESSION['admin_role'];
        
        // Super admin has all permissions
        if ($role === 'super_admin') {
            return true;
        }
        
        // Define role permissions
        $permissions = [
            'admin' => ['view', 'edit', 'create', 'delete'],
            'editor' => ['view', 'edit', 'create']
        ];
        
        return isset($permissions[$role]) && in_array($permission, $permissions[$role]);
    }
    
    public function createUser($username, $email, $password, $full_name, $role = 'admin') {
        try {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            $query = "INSERT INTO admin_users (username, email, password_hash, full_name, role) VALUES (:username, :email, :password_hash, :full_name, :role)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password_hash', $password_hash);
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':role', $role);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Create user error: " . $e->getMessage());
            return false;
        }
    }
}
?>