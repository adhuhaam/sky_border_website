<?php
require_once 'config/database.php';

class ContentManager {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    // Company Information Methods
    public function getCompanyInfo() {
        $query = "SELECT * FROM company_info LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function updateCompanyInfo($data) {
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
    }
    
    // Statistics Methods
    public function getStatistics() {
        $query = "SELECT * FROM statistics WHERE is_active = 1 ORDER BY display_order";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function updateStatistic($id, $stat_value, $stat_label) {
        $query = "UPDATE statistics SET stat_value = :stat_value, stat_label = :stat_label WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':stat_value', $stat_value);
        $stmt->bindParam(':stat_label', $stat_label);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    public function addStatistic($stat_name, $stat_value, $stat_label, $display_order = 0) {
        $query = "INSERT INTO statistics (stat_name, stat_value, stat_label, display_order) 
                  VALUES (:stat_name, :stat_value, :stat_label, :display_order)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':stat_name', $stat_name);
        $stmt->bindParam(':stat_value', $stat_value);
        $stmt->bindParam(':stat_label', $stat_label);
        $stmt->bindParam(':display_order', $display_order);
        return $stmt->execute();
    }
    
    // Team Members Methods
    public function getTeamMembers() {
        $query = "SELECT * FROM team_members WHERE is_active = 1 ORDER BY display_order";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getTeamMember($id) {
        $query = "SELECT * FROM team_members WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function addTeamMember($data) {
        $query = "INSERT INTO team_members (name, position, department, description, expertise, photo_url, display_order) 
                  VALUES (:name, :position, :department, :description, :expertise, :photo_url, :display_order)";
        $stmt = $this->conn->prepare($query);
        foreach ($data as $key => $value) {
            $stmt->bindParam(':' . $key, $value);
        }
        return $stmt->execute();
    }
    
    public function updateTeamMember($id, $data) {
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
    }
    
    public function deleteTeamMember($id) {
        $query = "UPDATE team_members SET is_active = 0 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    // Service Categories Methods
    public function getServiceCategories() {
        $query = "SELECT * FROM service_categories WHERE is_active = 1 ORDER BY display_order";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getServiceCategory($id) {
        $query = "SELECT * FROM service_categories WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function addServiceCategory($data) {
        $query = "INSERT INTO service_categories (category_name, category_description, icon_class, color_theme, display_order) 
                  VALUES (:category_name, :category_description, :icon_class, :color_theme, :display_order)";
        $stmt = $this->conn->prepare($query);
        foreach ($data as $key => $value) {
            $stmt->bindParam(':' . $key, $value);
        }
        return $stmt->execute();
    }
    
    public function updateServiceCategory($id, $data) {
        $query = "UPDATE service_categories SET 
                  category_name = :category_name,
                  category_description = :category_description,
                  icon_class = :icon_class,
                  color_theme = :color_theme,
                  display_order = :display_order
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        foreach ($data as $key => $value) {
            $stmt->bindParam(':' . $key, $value);
        }
        return $stmt->execute();
    }
    
    // Services Methods
    public function getServices($category_id = null) {
        $query = "SELECT s.*, sc.category_name 
                  FROM services s 
                  LEFT JOIN service_categories sc ON s.category_id = sc.id 
                  WHERE s.is_active = 1";
        
        if ($category_id) {
            $query .= " AND s.category_id = :category_id";
        }
        
        $query .= " ORDER BY s.display_order";
        
        $stmt = $this->conn->prepare($query);
        if ($category_id) {
            $stmt->bindParam(':category_id', $category_id);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getService($id) {
        $query = "SELECT * FROM services WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function addService($data) {
        $query = "INSERT INTO services (category_id, service_name, service_description, features, icon_class, display_order) 
                  VALUES (:category_id, :service_name, :service_description, :features, :icon_class, :display_order)";
        $stmt = $this->conn->prepare($query);
        foreach ($data as $key => $value) {
            $stmt->bindParam(':' . $key, $value);
        }
        return $stmt->execute();
    }
    
    public function updateService($id, $data) {
        $query = "UPDATE services SET 
                  category_id = :category_id,
                  service_name = :service_name,
                  service_description = :service_description,
                  features = :features,
                  icon_class = :icon_class,
                  display_order = :display_order
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        foreach ($data as $key => $value) {
            $stmt->bindParam(':' . $key, $value);
        }
        return $stmt->execute();
    }
    
    // Job Roles Methods
    public function getJobRoles($service_id = null) {
        $query = "SELECT jr.*, s.service_name 
                  FROM job_roles jr 
                  LEFT JOIN services s ON jr.service_id = s.id 
                  WHERE jr.is_active = 1";
        
        if ($service_id) {
            $query .= " AND jr.service_id = :service_id";
        }
        
        $query .= " ORDER BY jr.display_order";
        
        $stmt = $this->conn->prepare($query);
        if ($service_id) {
            $stmt->bindParam(':service_id', $service_id);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function addJobRole($service_id, $role_name, $category, $display_order = 0) {
        $query = "INSERT INTO job_roles (service_id, role_name, category, display_order) 
                  VALUES (:service_id, :role_name, :category, :display_order)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':service_id', $service_id);
        $stmt->bindParam(':role_name', $role_name);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':display_order', $display_order);
        return $stmt->execute();
    }
    
    // Portfolio Methods
    public function getPortfolioCategories() {
        $query = "SELECT * FROM portfolio_categories WHERE is_active = 1 ORDER BY display_order";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getPortfolioProjects($category_id = null) {
        $query = "SELECT pp.*, pc.category_name 
                  FROM portfolio_projects pp 
                  LEFT JOIN portfolio_categories pc ON pp.category_id = pc.id 
                  WHERE pp.is_active = 1";
        
        if ($category_id) {
            $query .= " AND pp.category_id = :category_id";
        }
        
        $query .= " ORDER BY pp.display_order";
        
        $stmt = $this->conn->prepare($query);
        if ($category_id) {
            $stmt->bindParam(':category_id', $category_id);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function addPortfolioProject($data) {
        $query = "INSERT INTO portfolio_projects (category_id, project_name, client_name, description, placements_count, project_status, completion_date, featured, display_order) 
                  VALUES (:category_id, :project_name, :client_name, :description, :placements_count, :project_status, :completion_date, :featured, :display_order)";
        $stmt = $this->conn->prepare($query);
        foreach ($data as $key => $value) {
            $stmt->bindParam(':' . $key, $value);
        }
        return $stmt->execute();
    }
    
    // Client Methods
    public function getClientCategories() {
        $query = "SELECT * FROM client_categories WHERE is_active = 1 ORDER BY display_order";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getClients($category_id = null) {
        $query = "SELECT c.*, cc.category_name 
                  FROM clients c 
                  LEFT JOIN client_categories cc ON c.category_id = cc.id 
                  WHERE c.is_active = 1";
        
        if ($category_id) {
            $query .= " AND c.category_id = :category_id";
        }
        
        $query .= " ORDER BY c.display_order";
        
        $stmt = $this->conn->prepare($query);
        if ($category_id) {
            $stmt->bindParam(':category_id', $category_id);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getClient($id) {
        $query = "SELECT * FROM clients WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function addClient($data) {
        $query = "INSERT INTO clients (category_id, client_name, company_type, contact_person, email, phone, address, website, description, partnership_start, total_placements, is_featured, display_order) 
                  VALUES (:category_id, :client_name, :company_type, :contact_person, :email, :phone, :address, :website, :description, :partnership_start, :total_placements, :is_featured, :display_order)";
        $stmt = $this->conn->prepare($query);
        foreach ($data as $key => $value) {
            $stmt->bindParam(':' . $key, $value);
        }
        return $stmt->execute();
    }
    
    public function updateClient($id, $data) {
        $query = "UPDATE clients SET 
                  category_id = :category_id,
                  client_name = :client_name,
                  company_type = :company_type,
                  contact_person = :contact_person,
                  email = :email,
                  phone = :phone,
                  address = :address,
                  website = :website,
                  description = :description,
                  partnership_start = :partnership_start,
                  total_placements = :total_placements,
                  is_featured = :is_featured,
                  display_order = :display_order
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        foreach ($data as $key => $value) {
            $stmt->bindParam(':' . $key, $value);
        }
        return $stmt->execute();
    }
    
    // Contact Messages Methods
    public function getContactMessages($status = null, $limit = 50) {
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
    }
    
    public function getContactMessage($id) {
        $query = "SELECT * FROM contact_messages WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function updateContactMessageStatus($id, $status, $admin_notes = null) {
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
    }
    
    // Website Settings Methods
    public function getSettings($group = null) {
        $query = "SELECT * FROM website_settings";
        
        if ($group) {
            $query .= " WHERE setting_group = :group";
        }
        
        $query .= " ORDER BY setting_group, setting_key";
        
        $stmt = $this->conn->prepare($query);
        if ($group) {
            $stmt->bindParam(':group', $group);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getSetting($key) {
        $query = "SELECT setting_value FROM website_settings WHERE setting_key = :key";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':key', $key);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['setting_value'] : null;
    }
    
    public function updateSetting($key, $value) {
        $query = "UPDATE website_settings SET setting_value = :value WHERE setting_key = :key";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':value', $value);
        $stmt->bindParam(':key', $key);
        return $stmt->execute();
    }
}
?>
