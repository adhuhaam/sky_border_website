<?php
require_once __DIR__ . '/../config/database.php';

class ContentManager {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    // Company Information Methods (simplified)
    public function getCompanyInfo() {
        // Return default company info if no database table exists
        return [
            'company_name' => 'Sky Border Solutions',
            'tagline' => 'Where compliance meets competence',
            'description' => 'Leading HR consultancy and recruitment firm in Maldives. Government-licensed professional workforce solutions.',
            'mission' => 'To provide exceptional workforce solutions while ensuring full compliance with Maldivian employment laws.',
            'vision' => 'To be the most trusted partner for organizations seeking skilled professionals in the Maldives.',
            'phone' => '+960 330-0043',
            'hotline1' => '+960 9777843',
            'hotline2' => '+960 7777843',
            'email' => 'info@skybordersolutions.com',
            'address' => 'Orchid Magu, Malé 20026, Maldives',
            'business_hours' => 'Saturday - Thursday: 9:00 AM - 5:00 PM'
        ];
    }
    
    // Statistics Methods (simplified)
    public function getStatistics() {
        return [
            ['stat_name' => 'clients', 'stat_value' => '50+', 'stat_label' => 'Satisfied Clients'],
            ['stat_name' => 'placements', 'stat_value' => '500+', 'stat_label' => 'Successful Placements'],
            ['stat_name' => 'experience', 'stat_value' => '10+', 'stat_label' => 'Years Experience'],
            ['stat_name' => 'success', 'stat_value' => '95%', 'stat_label' => 'Success Rate']
        ];
    }
    
    // Team Members Methods (simplified)
    public function getTeamMembers() {
        return [
            [
                'name' => 'Ahmed Ali',
                'position' => 'CEO & Founder',
                'department' => 'Management',
                'description' => 'Experienced leader with over 10 years in HR and recruitment.',
                'expertise' => 'Strategic Planning, Business Development'
            ]
        ];
    }
    
    // Service Categories Methods (simplified)
    public function getServiceCategories() {
        return [
            [
                'category_name' => 'HR Support Services',
                'category_description' => 'Comprehensive human resource support and consulting services',
                'icon_class' => 'fas fa-users',
                'color_theme' => 'blue'
            ],
            [
                'category_name' => 'Permits & Visa Services',
                'category_description' => 'Work permits, visa processing, and immigration support',
                'icon_class' => 'fas fa-passport',
                'color_theme' => 'green'
            ]
        ];
    }
    
    // Portfolio Categories Methods (simplified)
    public function getPortfolioCategories() {
        return [
            [
                'category_name' => 'Construction & Engineering',
                'category_slug' => 'construction',
                'description' => 'Major construction and infrastructure projects',
                'icon_class' => 'fas fa-hard-hat',
                'total_placements' => 200
            ],
            [
                'category_name' => 'Tourism & Hospitality',
                'category_slug' => 'hospitality',
                'description' => 'Leading resorts and hotels',
                'icon_class' => 'fas fa-concierge-bell',
                'total_placements' => 150
            ]
        ];
    }
    
    // Client Methods (using simple table structure)
    public function getClients($category = null) {
        try {
            // Check if clients_simple table exists, if not create it
            $this->conn->exec("CREATE TABLE IF NOT EXISTS clients_simple (
                id INT AUTO_INCREMENT PRIMARY KEY,
                client_name VARCHAR(200) NOT NULL,
                category VARCHAR(100) DEFAULT 'Other',
                logo_url VARCHAR(255),
                display_order INT DEFAULT 0,
                is_active BOOLEAN DEFAULT TRUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )");
            
            $query = "SELECT * FROM clients_simple WHERE is_active = 1";
            
            if ($category) {
                $query .= " AND category = :category";
            }
            
            $query .= " ORDER BY display_order, client_name";
            
            $stmt = $this->conn->prepare($query);
            if ($category) {
                $stmt->bindParam(':category', $category);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Return sample data if database query fails
            return [
                ['client_name' => 'Sample Construction Company', 'category' => 'Construction & Engineering', 'logo_url' => '', 'display_order' => 1],
                ['client_name' => 'Sample Resort & Spa', 'category' => 'Tourism & Hospitality', 'logo_url' => '', 'display_order' => 2],
                ['client_name' => 'Sample Investment Group', 'category' => 'Investments, Services & Trading', 'logo_url' => '', 'display_order' => 3]
            ];
        }
    }
    
    public function getClient($id) {
        try {
            $query = "SELECT * FROM clients_simple WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return null;
        }
    }
    
    public function addClient($client_name, $category, $logo_url = '', $display_order = 0) {
        try {
            $query = "INSERT INTO clients_simple (client_name, category, logo_url, display_order) 
                      VALUES (:client_name, :category, :logo_url, :display_order)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':client_name', $client_name);
            $stmt->bindParam(':category', $category);
            $stmt->bindParam(':logo_url', $logo_url);
            $stmt->bindParam(':display_order', $display_order);
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function updateClient($id, $client_name, $category, $logo_url = '', $display_order = 0) {
        try {
            $query = "UPDATE clients_simple SET 
                      client_name = :client_name,
                      category = :category,
                      logo_url = :logo_url,
                      display_order = :display_order
                      WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':client_name', $client_name);
            $stmt->bindParam(':category', $category);
            $stmt->bindParam(':logo_url', $logo_url);
            $stmt->bindParam(':display_order', $display_order);
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function deleteClient($id) {
        try {
            $query = "UPDATE clients_simple SET is_active = 0 WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Client Categories Methods (simplified)
    public function getClientCategories() {
        return [
            ['id' => 1, 'category_name' => 'Construction & Engineering'],
            ['id' => 2, 'category_name' => 'Tourism & Hospitality'],
            ['id' => 3, 'category_name' => 'Investments, Services & Trading']
        ];
    }
    
    // Contact Messages Methods (simplified)
    public function getContactMessages($status = null, $limit = 50) {
        return [];
    }
    
    public function getContactMessage($id) {
        return null;
    }
    
    public function updateContactMessageStatus($id, $status, $admin_notes = null) {
        return true;
    }
}
?>
