<?php
require_once __DIR__ . '/../config/database.php';

class ContentManager {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    // Company Information Methods
    public function getCompanyInfo() {
        try {
            $query = "SELECT * FROM company_info LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return $result;
            }
        } catch (Exception $e) {
            // Log error if needed
        }
        
        // Fallback data if database fails
        return [
            'company_name' => 'Sky Border Solutions',
            'tagline' => 'Where compliance meets competence',
            'description' => 'Leading HR consultancy and recruitment firm in the Republic of Maldives, providing end-to-end manpower solutions with excellence and integrity.',
            'mission' => 'To foster enduring partnerships with organizations by delivering superior recruitment solutions that align with their strategic goals.',
            'vision' => 'To be the most trusted and recognized recruitment company in the Maldives, known for our professionalism, excellence and ability to deliver outstanding outcomes.',
            'phone' => '+960 4000-444',
            'hotline1' => '+960 755-9001',
            'hotline2' => '+960 911-1409',
            'email' => 'info@skybordersolutions.com',
            'address' => 'H. Dhoorihaa (5A), Kalaafaanu Hingun, Male\' City, Republic of Maldives',
            'business_hours' => 'Sunday - Thursday: 8:00 AM - 5:00 PM\nSaturday: 9:00 AM - 1:00 PM\nFriday: Closed'
        ];
    }
    
    public function updateCompanyInfo($data) {
        try {
            $query = "UPDATE company_info SET 
                      company_name = :company_name,
                      tagline = :tagline,
                      description = :description,
                      mission = :mission,
                      vision = :vision,
                      phone = :phone,
                      hotline1 = :hotline1,
                      hotline2 = :hotline2,
                      email = :email,
                      address = :address,
                      business_hours = :business_hours
                      WHERE id = 1";
            
            $stmt = $this->conn->prepare($query);
            foreach ($data as $key => $value) {
                $stmt->bindParam(':' . $key, $value);
            }
            
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Statistics Methods
    public function getStatistics() {
        try {
            $query = "SELECT * FROM statistics WHERE is_active = 1 ORDER BY display_order";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($result)) {
                return $result;
            }
        } catch (Exception $e) {
            // Log error if needed
        }
        
        // Fallback data
        return [
            ['stat_name' => 'placements', 'stat_value' => '1000+', 'stat_label' => 'Successful Placements'],
            ['stat_name' => 'partners', 'stat_value' => '50+', 'stat_label' => 'Partner Companies'],
            ['stat_name' => 'compliance', 'stat_value' => '100%', 'stat_label' => 'Licensed & Compliant']
        ];
    }
    
    // Service Categories Methods
    public function getServiceCategories() {
        try {
            $query = "SELECT * FROM service_categories WHERE is_active = 1 ORDER BY display_order";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($result)) {
                return $result;
            }
        } catch (Exception $e) {
            // Log error if needed
        }
        
        // Fallback data
        return [
            [
                'category_name' => 'Recruitment Services',
                'category_description' => 'Source and screen candidates across multiple sectors',
                'icon_class' => 'fas fa-user-tie',
                'color_theme' => 'indigo'
            ],
            [
                'category_name' => 'HR Support Services',
                'category_description' => 'Comprehensive post-recruitment support and compliance',
                'icon_class' => 'fas fa-users-cog',
                'color_theme' => 'green'
            ],
            [
                'category_name' => 'Permits & Visa Processing',
                'category_description' => 'Government approvals for legal expatriate employment',
                'icon_class' => 'fas fa-passport',
                'color_theme' => 'purple'
            ],
            [
                'category_name' => 'Insurance Services',
                'category_description' => 'Comprehensive insurance coverage for expatriate employees',
                'icon_class' => 'fas fa-shield-alt',
                'color_theme' => 'blue'
            ]
        ];
    }
    
    // Portfolio Categories Methods
    public function getPortfolioCategories() {
        try {
            $query = "SELECT * FROM portfolio_categories WHERE is_active = 1 ORDER BY display_order";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($result)) {
                return $result;
            }
        } catch (Exception $e) {
            // Log error if needed
        }
        
        // Fallback data
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
            ],
            [
                'category_name' => 'Healthcare Services',
                'category_slug' => 'healthcare',
                'description' => 'Hospitals, clinics, and medical facilities',
                'icon_class' => 'fas fa-user-md',
                'total_placements' => 80
            ],
            [
                'category_name' => 'Professional Services',
                'category_slug' => 'professional',
                'description' => 'IT, finance, administration, and consultancy',
                'icon_class' => 'fas fa-laptop-code',
                'total_placements' => 120
            ]
        ];
    }
    
    // Team Members Methods
    public function getTeamMembers() {
        try {
            $query = "SELECT * FROM team_members WHERE is_active = 1 ORDER BY display_order";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($result)) {
                return $result;
            }
        } catch (Exception $e) {
            // Log error if needed
        }
        
        // Return empty array if no team members in database
        return [];
    }
    
    // Client Methods - Using the real database structure
    public function getClients($category = null) {
        try {
            $query = "SELECT c.*, cc.category_name 
                      FROM clients c 
                      LEFT JOIN client_categories cc ON c.category_id = cc.id 
                      WHERE c.is_active = 1";
            
            if ($category) {
                $query .= " AND cc.category_name = :category";
            }
            
            $query .= " ORDER BY c.display_order, c.client_name";
            
            $stmt = $this->conn->prepare($query);
            if ($category) {
                $stmt->bindParam(':category', $category);
            }
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Return real data if available
            if (!empty($result)) {
                return $result;
            }
        } catch (Exception $e) {
            // Log error if needed
        }
        
        // Fallback sample data
        return [
            ['client_name' => 'Sample Construction Company', 'category_name' => 'Construction & Engineering', 'logo_url' => ''],
            ['client_name' => 'Sample Resort & Spa', 'category_name' => 'Tourism & Hospitality', 'logo_url' => ''],
            ['client_name' => 'Sample Investment Group', 'category_name' => 'Investments, Services & Trading', 'logo_url' => '']
        ];
    }
    
    public function getClient($id) {
        try {
            $query = "SELECT c.*, cc.category_name 
                      FROM clients c 
                      LEFT JOIN client_categories cc ON c.category_id = cc.id 
                      WHERE c.id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return null;
        }
    }
    
    public function addClient($client_name, $category_id, $logo_url = '', $display_order = 0) {
        try {
            $query = "INSERT INTO clients (client_name, category_id, logo_url, display_order) 
                      VALUES (:client_name, :category_id, :logo_url, :display_order)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':client_name', $client_name);
            $stmt->bindParam(':category_id', $category_id);
            $stmt->bindParam(':logo_url', $logo_url);
            $stmt->bindParam(':display_order', $display_order);
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function updateClient($id, $client_name, $category_id, $logo_url = '', $display_order = 0) {
        try {
            $query = "UPDATE clients SET 
                      client_name = :client_name,
                      category_id = :category_id,
                      logo_url = :logo_url,
                      display_order = :display_order
                      WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':client_name', $client_name);
            $stmt->bindParam(':category_id', $category_id);
            $stmt->bindParam(':logo_url', $logo_url);
            $stmt->bindParam(':display_order', $display_order);
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function deleteClient($id) {
        try {
            $query = "UPDATE clients SET is_active = 0 WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Client Categories Methods
    public function getClientCategories() {
        try {
            $query = "SELECT * FROM client_categories WHERE is_active = 1 ORDER BY display_order";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($result)) {
                return $result;
            }
        } catch (Exception $e) {
            // Log error if needed
        }
        
        // Fallback data
        return [
            ['id' => 1, 'category_name' => 'Construction & Engineering'],
            ['id' => 2, 'category_name' => 'Tourism & Hospitality'],
            ['id' => 3, 'category_name' => 'Investments, Services & Trading']
        ];
    }
    
    // Contact Messages Methods
    public function getContactMessages($status = null, $limit = 50) {
        try {
            $query = "SELECT * FROM contact_messages";
            
            if ($status) {
                $query .= " WHERE status = :status";
            }
            
            $query .= " ORDER BY created_at DESC LIMIT :limit";
            
            $stmt = $this->conn->prepare($query);
            if ($status) {
                $stmt->bindParam(':status', $status);
            }
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    public function addContactMessage($name, $email, $company, $phone, $subject, $message) {
        try {
            $query = "INSERT INTO contact_messages (name, email, company, phone, subject, message) 
                      VALUES (:name, :email, :company, :phone, :subject, :message)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':company', $company);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':subject', $subject);
            $stmt->bindParam(':message', $message);
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function getContactMessage($id) {
        try {
            $query = "SELECT * FROM contact_messages WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return null;
        }
    }
    
    public function updateContactMessageStatus($id, $status, $admin_notes = null) {
        try {
            $query = "UPDATE contact_messages SET status = :status";
            
            if ($admin_notes) {
                $query .= ", admin_notes = :admin_notes";
            }
            
            $query .= " WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $id);
            
            if ($admin_notes) {
                $stmt->bindParam(':admin_notes', $admin_notes);
            }
            
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Additional helper methods for admin
    public function getTeamMember($id) {
        try {
            $query = "SELECT * FROM team_members WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return null;
        }
    }
    
    public function addTeamMember($data) {
        try {
            $query = "INSERT INTO team_members (name, position, department, description, expertise, photo_url, display_order) 
                      VALUES (:name, :position, :department, :description, :expertise, :photo_url, :display_order)";
            $stmt = $this->conn->prepare($query);
            foreach ($data as $key => $value) {
                $stmt->bindParam(':' . $key, $value);
            }
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function updateTeamMember($id, $data) {
        try {
            $query = "UPDATE team_members SET 
                      name = :name,
                      position = :position,
                      department = :department,
                      description = :description,
                      expertise = :expertise,
                      photo_url = :photo_url,
                      display_order = :display_order
                      WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            foreach ($data as $key => $value) {
                $stmt->bindParam(':' . $key, $value);
            }
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function deleteTeamMember($id) {
        try {
            $query = "UPDATE team_members SET is_active = 0 WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Statistics management
    public function updateStatistic($id, $stat_value, $stat_label) {
        try {
            $query = "UPDATE statistics SET stat_value = :stat_value, stat_label = :stat_label WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':stat_value', $stat_value);
            $stmt->bindParam(':stat_label', $stat_label);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }
}
?>
