<?php
require_once 'config/database.php';

class Auth {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function login($username, $password) {
        $query = "SELECT id, username, email, password_hash, full_name, role, is_active 
                  FROM admin_users 
                  WHERE (username = :username OR email = :username) AND is_active = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password_hash'])) {
            // Update last login
            $update_query = "UPDATE admin_users SET last_login = NOW() WHERE id = :id";
            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->bindParam(':id', $user['id']);
            $update_stmt->execute();
            
            // Set session
            session_start();
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_email'] = $user['email'];
            $_SESSION['admin_name'] = $user['full_name'];
            $_SESSION['admin_role'] = $user['role'];
            $_SESSION['logged_in'] = true;
            
            $this->logActivity($user['id'], 'login', 'admin_users', $user['id']);
            
            return true;
        }
        
        return false;
    }
    
    public function logout() {
        session_start();
        if (isset($_SESSION['admin_id'])) {
            $this->logActivity($_SESSION['admin_id'], 'logout', 'admin_users', $_SESSION['admin_id']);
        }
        session_destroy();
        return true;
    }
    
    public function isLoggedIn() {
        session_start();
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit();
        }
    }
    
    public function getCurrentUser() {
        session_start();
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['admin_id'],
                'username' => $_SESSION['admin_username'],
                'email' => $_SESSION['admin_email'],
                'name' => $_SESSION['admin_name'],
                'role' => $_SESSION['admin_role']
            ];
        }
        return null;
    }
    
    public function hasPermission($permission) {
        $user = $this->getCurrentUser();
        if (!$user) return false;
        
        $role = $user['role'];
        
        switch ($permission) {
            case 'super_admin':
                return $role === 'super_admin';
            case 'admin':
                return in_array($role, ['super_admin', 'admin']);
            case 'editor':
                return in_array($role, ['super_admin', 'admin', 'editor']);
            default:
                return false;
        }
    }
    
    public function changePassword($current_password, $new_password) {
        $user = $this->getCurrentUser();
        if (!$user) return false;
        
        // Verify current password
        $query = "SELECT password_hash FROM admin_users WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $user['id']);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!password_verify($current_password, $result['password_hash'])) {
            return false;
        }
        
        // Update password
        $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $update_query = "UPDATE admin_users SET password_hash = :password_hash WHERE id = :id";
        $update_stmt = $this->conn->prepare($update_query);
        $update_stmt->bindParam(':password_hash', $new_hash);
        $update_stmt->bindParam(':id', $user['id']);
        
        if ($update_stmt->execute()) {
            $this->logActivity($user['id'], 'change_password', 'admin_users', $user['id']);
            return true;
        }
        
        return false;
    }
    
    public function createUser($username, $email, $password, $full_name, $role = 'admin') {
        $user = $this->getCurrentUser();
        if (!$user || !$this->hasPermission('admin')) return false;
        
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $query = "INSERT INTO admin_users (username, email, password_hash, full_name, role) 
                  VALUES (:username, :email, :password_hash, :full_name, :role)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password_hash', $password_hash);
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':role', $role);
        
        if ($stmt->execute()) {
            $new_user_id = $this->conn->lastInsertId();
            $this->logActivity($user['id'], 'create_user', 'admin_users', $new_user_id);
            return $new_user_id;
        }
        
        return false;
    }
    
    private function logActivity($user_id, $action, $table_name = null, $record_id = null, $old_values = null, $new_values = null) {
        $query = "INSERT INTO activity_log (user_id, action, table_name, record_id, old_values, new_values, ip_address, user_agent) 
                  VALUES (:user_id, :action, :table_name, :record_id, :old_values, :new_values, :ip_address, :user_agent)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':table_name', $table_name);
        $stmt->bindParam(':record_id', $record_id);
        $stmt->bindParam(':old_values', $old_values ? json_encode($old_values) : null);
        $stmt->bindParam(':new_values', $new_values ? json_encode($new_values) : null);
        $stmt->bindParam(':ip_address', $_SERVER['REMOTE_ADDR'] ?? '');
        $stmt->bindParam(':user_agent', $_SERVER['HTTP_USER_AGENT'] ?? '');
        
        $stmt->execute();
    }
}
?>
