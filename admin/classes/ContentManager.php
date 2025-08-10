<?php
/**
 * Content Manager Class
 * Sky Border Solutions CMS
 */

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
            error_log("Get company info error: " . $e->getMessage());
        }
        
        // Fallback data if database fails
        return [
            'id' => 1,
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
            // Build dynamic query based on provided data
            $updateFields = [];
            $params = [':id' => 1];
            
            $allowedFields = [
                'company_name', 'tagline', 'description', 'about_us', 'mission', 'vision',
                'phone', 'hotline1', 'hotline2', 'email', 'address', 'business_hours',
                'website', 'established_year', 'business_type', 'registration_number',
                'license_number', 'facebook_url', 'linkedin_url', 'twitter_url', 'instagram_url'
            ];
            
            foreach ($allowedFields as $field) {
                if (array_key_exists($field, $data)) {
                    $updateFields[] = "$field = :$field";
                    $params[":$field"] = $data[$field];
                }
            }
            
            if (empty($updateFields)) {
                return false; // No data to update
            }
            
            $query = "UPDATE company_info SET " . implode(', ', $updateFields) . " WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            return $stmt->execute($params);
        } catch (Exception $e) {
            error_log("Update company info error: " . $e->getMessage());
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
            error_log("Get statistics error: " . $e->getMessage());
        }
        
        // Fallback data
        return [
            ['id' => 1, 'stat_name' => 'placements', 'stat_value' => '1000+', 'stat_label' => 'Successful Placements', 'display_order' => 1],
            ['id' => 2, 'stat_name' => 'partners', 'stat_value' => '50+', 'stat_label' => 'Partner Companies', 'display_order' => 2],
            ['id' => 3, 'stat_name' => 'compliance', 'stat_value' => '100%', 'stat_label' => 'Licensed & Compliant', 'display_order' => 3]
        ];
    }
    
    public function updateStatistic($id, $stat_value, $stat_label) {
        try {
            $query = "UPDATE statistics SET stat_value = :stat_value, stat_label = :stat_label WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([
                ':stat_value' => $stat_value,
                ':stat_label' => $stat_label,
                ':id' => $id
            ]);
        } catch (Exception $e) {
            error_log("Update statistic error: " . $e->getMessage());
            return false;
        }
    }
    
    // Service Categories Methods
    public function getServiceCategories() {
        try {
            $query = "SELECT * FROM service_categories WHERE is_active = 1 ORDER BY display_order";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Return actual database results (can be empty array)
            return $result;
            
        } catch (Exception $e) {
            error_log("Get service categories error: " . $e->getMessage());
            // Only return fallback data on database connection errors
            return [
                ['id' => 1, 'category_name' => 'Recruitment Services', 'category_description' => 'Source and screen candidates across multiple sectors', 'icon_class' => 'fas fa-user-tie', 'color_theme' => 'indigo'],
                ['id' => 2, 'category_name' => 'HR Support Services', 'category_description' => 'Comprehensive post-recruitment support and compliance', 'icon_class' => 'fas fa-users-cog', 'color_theme' => 'green'],
                ['id' => 3, 'category_name' => 'Permits & Visa Processing', 'category_description' => 'Government approvals for legal expatriate employment', 'icon_class' => 'fas fa-passport', 'color_theme' => 'purple'],
                ['id' => 4, 'category_name' => 'Insurance Services', 'category_description' => 'Comprehensive insurance coverage for expatriate employees', 'icon_class' => 'fas fa-shield-alt', 'color_theme' => 'blue']
            ];
        }
    }
    
    public function addServiceCategory($data) {
        try {
            $query = "INSERT INTO service_categories (category_name, category_description, icon_class, color_theme, display_order) 
                      VALUES (:category_name, :category_description, :icon_class, :color_theme, :display_order)";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute($data);
        } catch (Exception $e) {
            error_log("Add service category error: " . $e->getMessage());
            return false;
        }
    }
    
    public function updateServiceCategory($id, $data) {
        try {
            $query = "UPDATE service_categories SET 
                      category_name = :category_name,
                      category_description = :category_description,
                      icon_class = :icon_class,
                      color_theme = :color_theme,
                      display_order = :display_order
                      WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $data[':id'] = $id;
            return $stmt->execute($data);
        } catch (Exception $e) {
            error_log("Update service category error: " . $e->getMessage());
            return false;
        }
    }
    
    public function deleteServiceCategory($id) {
        try {
            $query = "UPDATE service_categories SET is_active = 0 WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([':id' => $id]);
        } catch (Exception $e) {
            error_log("Delete service category error: " . $e->getMessage());
            return false;
        }
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
            error_log("Get portfolio categories error: " . $e->getMessage());
        }
        
        // Fallback data
        return [
            ['id' => 1, 'category_name' => 'Construction & Engineering', 'category_slug' => 'construction', 'description' => 'Major construction and infrastructure projects', 'icon_class' => 'fas fa-hard-hat', 'total_placements' => 200],
            ['id' => 2, 'category_name' => 'Tourism & Hospitality', 'category_slug' => 'hospitality', 'description' => 'Leading resorts and hotels', 'icon_class' => 'fas fa-concierge-bell', 'total_placements' => 150],
            ['id' => 3, 'category_name' => 'Healthcare Services', 'category_slug' => 'healthcare', 'description' => 'Hospitals, clinics, and medical facilities', 'icon_class' => 'fas fa-user-md', 'total_placements' => 80],
            ['id' => 4, 'category_name' => 'Professional Services', 'category_slug' => 'professional', 'description' => 'IT, finance, administration, and consultancy', 'icon_class' => 'fas fa-laptop-code', 'total_placements' => 120]
        ];
    }
    
    public function updatePortfolioCategory($id, $data) {
        try {
            $query = "UPDATE portfolio_categories SET 
                      category_name = :category_name,
                      description = :description,
                      icon_class = :icon_class,
                      total_placements = :total_placements
                      WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $data[':id'] = $id;
            return $stmt->execute($data);
        } catch (Exception $e) {
            error_log("Update portfolio category error: " . $e->getMessage());
            return false;
        }
    }
    
    // Client Methods
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
            
            // Return actual database results (can be empty array)
            return $result;
            
        } catch (Exception $e) {
            error_log("Get clients error: " . $e->getMessage());
            // Only return fallback data on database connection errors
            return [
                ['id' => 1, 'client_name' => 'Leading Construction Company', 'category_name' => 'Construction & Engineering', 'category_id' => 1, 'logo_url' => '', 'display_order' => 0],
                ['id' => 2, 'client_name' => 'Luxury Resort & Spa', 'category_name' => 'Tourism & Hospitality', 'category_id' => 2, 'logo_url' => '', 'display_order' => 1],
                ['id' => 3, 'client_name' => 'Investment Holdings Group', 'category_name' => 'Investments, Services & Trading', 'category_id' => 3, 'logo_url' => '', 'display_order' => 2]
            ];
        }
    }
    
    public function getClient($id) {
        try {
            $query = "SELECT c.*, cc.category_name 
                      FROM clients c 
                      LEFT JOIN client_categories cc ON c.category_id = cc.id 
                      WHERE c.id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get client error: " . $e->getMessage());
            return null;
        }
    }
    
    public function getClientCategories() {
        try {
            $query = "SELECT * FROM client_categories WHERE is_active = 1 ORDER BY display_order";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Return actual database results (can be empty array)
            return $result;
            
        } catch (Exception $e) {
            error_log("Get client categories error: " . $e->getMessage());
            // Only return fallback data on database connection errors
            return [
                ['id' => 1, 'category_name' => 'Construction & Engineering'],
                ['id' => 2, 'category_name' => 'Tourism & Hospitality'],
                ['id' => 3, 'category_name' => 'Investments, Services & Trading']
            ];
        }
    }
    
    public function addClient($client_name, $category_id, $logo_url = '', $display_order = 0, $services = '', $service_duration_type = 'ongoing', $service_start_date = null, $service_end_date = null) {
        try {
            $query = "INSERT INTO clients (client_name, category_id, logo_url, display_order, services, service_duration_type, service_start_date, service_end_date) 
                      VALUES (:client_name, :category_id, :logo_url, :display_order, :services, :service_duration_type, :service_start_date, :service_end_date)";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([
                ':client_name' => $client_name,
                ':category_id' => $category_id,
                ':logo_url' => $logo_url,
                ':display_order' => $display_order,
                ':services' => $services,
                ':service_duration_type' => $service_duration_type,
                ':service_start_date' => $service_start_date,
                ':service_end_date' => $service_end_date
            ]);
        } catch (Exception $e) {
            error_log("Add client error: " . $e->getMessage());
            return false;
        }
    }
    
    public function updateClient($id, $client_name, $category_id, $logo_url = '', $display_order = 0, $services = '', $service_duration_type = 'ongoing', $service_start_date = null, $service_end_date = null) {
        try {
            $query = "UPDATE clients SET 
                      client_name = :client_name,
                      category_id = :category_id,
                      logo_url = :logo_url,
                      display_order = :display_order,
                      services = :services,
                      service_duration_type = :service_duration_type,
                      service_start_date = :service_start_date,
                      service_end_date = :service_end_date
                      WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([
                ':client_name' => $client_name,
                ':category_id' => $category_id,
                ':logo_url' => $logo_url,
                ':display_order' => $display_order,
                ':services' => $services,
                ':service_duration_type' => $service_duration_type,
                ':service_start_date' => $service_start_date,
                ':service_end_date' => $service_end_date,
                ':id' => $id
            ]);
        } catch (Exception $e) {
            error_log("Update client error: " . $e->getMessage());
            return false;
        }
    }
    
    public function deleteClient($id) {
        try {
            $query = "UPDATE clients SET is_active = 0 WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([':id' => $id]);
        } catch (Exception $e) {
            error_log("Delete client error: " . $e->getMessage());
            return false;
        }
    }
    
    // Industries Methods
    public function getIndustries() {
        try {
            $query = "SELECT * FROM industries WHERE is_active = 1 ORDER BY display_order, industry_name";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get industries error: " . $e->getMessage());
            return [];
        }
    }

    public function getIndustry($id) {
        try {
            $query = "SELECT * FROM industries WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get industry error: " . $e->getMessage());
            return null;
        }
    }

    public function addIndustry($industry_name, $industry_description, $icon_class, $color_theme, $display_order = 0) {
        try {
            $query = "INSERT INTO industries (industry_name, industry_description, icon_class, color_theme, display_order) 
                      VALUES (:industry_name, :industry_description, :icon_class, :color_theme, :display_order)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':industry_name', $industry_name);
            $stmt->bindParam(':industry_description', $industry_description);
            $stmt->bindParam(':icon_class', $icon_class);
            $stmt->bindParam(':color_theme', $color_theme);
            $stmt->bindParam(':display_order', $display_order);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Add industry error: " . $e->getMessage());
            return false;
        }
    }

    public function updateIndustry($id, $industry_name, $industry_description, $icon_class, $color_theme, $display_order = 0) {
        try {
            $query = "UPDATE industries SET industry_name = :industry_name, industry_description = :industry_description, 
                      icon_class = :icon_class, color_theme = :color_theme, display_order = :display_order 
                      WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':industry_name', $industry_name);
            $stmt->bindParam(':industry_description', $industry_description);
            $stmt->bindParam(':icon_class', $icon_class);
            $stmt->bindParam(':color_theme', $color_theme);
            $stmt->bindParam(':display_order', $display_order);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Update industry error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteIndustry($id) {
        try {
            $query = "UPDATE industries SET is_active = 0 WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Delete industry error: " . $e->getMessage());
            return false;
        }
    }

    // Job Positions Methods
    public function getJobPositions($industry_id = null) {
        try {
            if ($industry_id) {
                $query = "SELECT jp.*, i.industry_name 
                          FROM job_positions jp 
                          LEFT JOIN industries i ON jp.industry_id = i.id 
                          WHERE jp.is_active = 1 AND jp.industry_id = :industry_id 
                          ORDER BY jp.display_order, jp.position_name";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':industry_id', $industry_id);
            } else {
                $query = "SELECT jp.*, i.industry_name 
                          FROM job_positions jp 
                          LEFT JOIN industries i ON jp.industry_id = i.id 
                          WHERE jp.is_active = 1 
                          ORDER BY i.display_order, jp.display_order, jp.position_name";
                $stmt = $this->conn->prepare($query);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get job positions error: " . $e->getMessage());
            return [];
        }
    }

    public function getJobPosition($id) {
        try {
            $query = "SELECT jp.*, i.industry_name 
                      FROM job_positions jp 
                      LEFT JOIN industries i ON jp.industry_id = i.id 
                      WHERE jp.id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get job position error: " . $e->getMessage());
            return null;
        }
    }

    public function addJobPosition($industry_id, $position_name, $position_description, $is_featured = false, $display_order = 0) {
        try {
            $query = "INSERT INTO job_positions (industry_id, position_name, position_description, is_featured, display_order) 
                      VALUES (:industry_id, :position_name, :position_description, :is_featured, :display_order)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':industry_id', $industry_id);
            $stmt->bindParam(':position_name', $position_name);
            $stmt->bindParam(':position_description', $position_description);
            $stmt->bindParam(':is_featured', $is_featured, PDO::PARAM_BOOL);
            $stmt->bindParam(':display_order', $display_order);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Add job position error: " . $e->getMessage());
            return false;
        }
    }

    public function updateJobPosition($id, $industry_id, $position_name, $position_description, $is_featured = false, $display_order = 0) {
        try {
            $query = "UPDATE job_positions SET industry_id = :industry_id, position_name = :position_name, 
                      position_description = :position_description, is_featured = :is_featured, display_order = :display_order 
                      WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':industry_id', $industry_id);
            $stmt->bindParam(':position_name', $position_name);
            $stmt->bindParam(':position_description', $position_description);
            $stmt->bindParam(':is_featured', $is_featured, PDO::PARAM_BOOL);
            $stmt->bindParam(':display_order', $display_order);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Update job position error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteJobPosition($id) {
        try {
            $query = "UPDATE job_positions SET is_active = 0 WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Delete job position error: " . $e->getMessage());
            return false;
        }
    }

    public function getFeaturedPositions($industry_id = null, $limit = 6) {
        try {
            if ($industry_id) {
                $query = "SELECT jp.*, i.industry_name, i.color_theme 
                          FROM job_positions jp 
                          LEFT JOIN industries i ON jp.industry_id = i.id 
                          WHERE jp.is_active = 1 AND jp.is_featured = 1 AND jp.industry_id = :industry_id 
                          ORDER BY jp.display_order, jp.position_name 
                          LIMIT :limit";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':industry_id', $industry_id);
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            } else {
                $query = "SELECT jp.*, i.industry_name, i.color_theme 
                          FROM job_positions jp 
                          LEFT JOIN industries i ON jp.industry_id = i.id 
                          WHERE jp.is_active = 1 AND jp.is_featured = 1 
                          ORDER BY i.display_order, jp.display_order, jp.position_name 
                          LIMIT :limit";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get featured positions error: " . $e->getMessage());
            return [];
        }
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
            error_log("Get contact messages error: " . $e->getMessage());
            return [];
        }
    }
    
    public function addContactMessage($name, $email, $company, $phone, $subject, $message) {
        try {
            $query = "INSERT INTO contact_messages (name, email, company, phone, subject, message) 
                      VALUES (:name, :email, :company, :phone, :subject, :message)";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':company' => $company,
                ':phone' => $phone,
                ':subject' => $subject,
                ':message' => $message
            ]);
        } catch (Exception $e) {
            error_log("Add contact message error: " . $e->getMessage());
            return false;
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
            $params = [':status' => $status, ':id' => $id];
            
            if ($admin_notes) {
                $params[':admin_notes'] = $admin_notes;
            }
            
            return $stmt->execute($params);
        } catch (Exception $e) {
            error_log("Update contact message status error: " . $e->getMessage());
            return false;
        }
    }
    
    // Insurance Provider Methods
    public function getInsuranceProviders($type = null, $featured_only = false) {
        try {
            $query = "SELECT * FROM insurance_providers WHERE is_active = 1";
            $params = [];
            
            if ($type) {
                $query .= " AND provider_type = :type";
                $params[':type'] = $type;
            }
            
            if ($featured_only) {
                $query .= " AND is_featured = 1";
            }
            
            $query .= " ORDER BY display_order ASC, provider_name ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get insurance providers error: " . $e->getMessage());
            return [];
        }
    }
    
    public function getInsuranceProvider($id) {
        try {
            $query = "SELECT * FROM insurance_providers WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get insurance provider error: " . $e->getMessage());
            return null;
        }
    }
    
    public function addInsuranceProvider($data) {
        try {
            $query = "INSERT INTO insurance_providers (
                provider_name, logo_url, is_featured, display_order
            ) VALUES (
                :provider_name, :logo_url, :is_featured, :display_order
            )";
            
            $stmt = $this->conn->prepare($query);
            
            // Set default values if not provided
            $data['is_featured'] = isset($data['is_featured']) ? (bool)$data['is_featured'] : false;
            $data['display_order'] = isset($data['display_order']) ? (int)$data['display_order'] : 0;
            
            return $stmt->execute([
                ':provider_name' => $data['provider_name'],
                ':logo_url' => $data['logo_url'] ?? '',
                ':is_featured' => $data['is_featured'],
                ':display_order' => $data['display_order']
            ]);
        } catch (Exception $e) {
            error_log("Add insurance provider error: " . $e->getMessage());
            return false;
        }
    }
    
    public function updateInsuranceProvider($id, $data) {
        try {
            $query = "UPDATE insurance_providers SET 
                provider_name = :provider_name,
                logo_url = :logo_url,
                is_featured = :is_featured,
                display_order = :display_order
                WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            
            // Set default values if not provided
            $data['is_featured'] = isset($data['is_featured']) ? (bool)$data['is_featured'] : false;
            $data['display_order'] = isset($data['display_order']) ? (int)$data['display_order'] : 0;
            
            return $stmt->execute([
                ':provider_name' => $data['provider_name'],
                ':logo_url' => $data['logo_url'] ?? '',
                ':is_featured' => $data['is_featured'],
                ':display_order' => $data['display_order'],
                ':id' => $id
            ]);
        } catch (Exception $e) {
            error_log("Update insurance provider error: " . $e->getMessage());
            return false;
        }
    }
    
    public function deleteInsuranceProvider($id) {
        try {
            $query = "UPDATE insurance_providers SET is_active = 0 WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([':id' => $id]);
        } catch (Exception $e) {
            error_log("Delete insurance provider error: " . $e->getMessage());
            return false;
        }
    }
    
    
}
?>