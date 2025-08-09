-- Sky Border Solutions Database Schema
CREATE DATABASE IF NOT EXISTS skydfcaf_sky_border;
USE skydfcaf_sky_border;

-- Admin Users Table
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('super_admin', 'admin', 'editor') DEFAULT 'admin',
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Company Information Table
CREATE TABLE company_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(100) NOT NULL DEFAULT 'Sky Border Solutions',
    tagline VARCHAR(200) NOT NULL DEFAULT 'Where compliance meets competence',
    description TEXT,
    mission TEXT,
    vision TEXT,
    phone VARCHAR(20),
    hotline1 VARCHAR(20),
    hotline2 VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    business_hours TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Statistics Table
CREATE TABLE statistics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stat_name VARCHAR(50) NOT NULL,
    stat_value VARCHAR(20) NOT NULL,
    stat_label VARCHAR(100) NOT NULL,
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Team Members Table
CREATE TABLE team_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    position VARCHAR(100) NOT NULL,
    department VARCHAR(100),
    description TEXT,
    expertise TEXT,
    photo_url VARCHAR(255),
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Service Categories Table
CREATE TABLE service_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL,
    category_description TEXT,
    icon_class VARCHAR(50),
    color_theme VARCHAR(20),
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Services Table
CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    service_name VARCHAR(100) NOT NULL,
    service_description TEXT,
    features TEXT,
    icon_class VARCHAR(50),
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES service_categories(id) ON DELETE SET NULL
);

-- Job Roles Table
CREATE TABLE job_roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_id INT,
    role_name VARCHAR(100) NOT NULL,
    category VARCHAR(50),
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
);

-- Portfolio Categories Table
CREATE TABLE portfolio_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL,
    category_slug VARCHAR(50) NOT NULL,
    description TEXT,
    icon_class VARCHAR(50),
    color_theme VARCHAR(20),
    total_placements INT DEFAULT 0,
    total_projects INT DEFAULT 0,
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Portfolio Projects Table
CREATE TABLE portfolio_projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    project_name VARCHAR(200) NOT NULL,
    client_name VARCHAR(100),
    description TEXT,
    placements_count INT DEFAULT 0,
    project_status ENUM('completed', 'in_progress', 'planned') DEFAULT 'completed',
    completion_date DATE,
    featured BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES portfolio_categories(id) ON DELETE SET NULL
);

-- Client Categories Table
CREATE TABLE client_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL,
    category_description TEXT,
    icon_class VARCHAR(50),
    color_theme VARCHAR(20),
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Clients Table
CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    client_name VARCHAR(200) NOT NULL,
    company_type VARCHAR(100),
    contact_person VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    website VARCHAR(255),
    logo_url VARCHAR(255),
    description TEXT,
    partnership_start DATE,
    total_placements INT DEFAULT 0,
    is_featured BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES client_categories(id) ON DELETE SET NULL
);

-- Contact Messages Table
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    company VARCHAR(100),
    phone VARCHAR(20),
    subject VARCHAR(200),
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied', 'archived') DEFAULT 'new',
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    admin_notes TEXT,
    replied_at TIMESTAMP NULL,
    replied_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (replied_by) REFERENCES admin_users(id) ON DELETE SET NULL
);

-- Website Settings Table
CREATE TABLE website_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('text', 'textarea', 'number', 'boolean', 'json') DEFAULT 'text',
    setting_group VARCHAR(50) DEFAULT 'general',
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Activity Log Table
CREATE TABLE activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES admin_users(id) ON DELETE SET NULL
);

-- Insert Default Data
INSERT INTO admin_users (username, email, password_hash, full_name, role) VALUES
('admin', 'admin@skybordersolutions.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'super_admin');

INSERT INTO company_info (company_name, tagline, description, mission, vision, phone, hotline1, hotline2, email, address, business_hours) VALUES
('Sky Border Solutions', 
 'Where compliance meets competence',
 'Leading HR consultancy and recruitment firm in the Republic of Maldives, providing end-to-end manpower solutions with excellence and integrity.',
 'To foster enduring partnerships with organizations by delivering superior recruitment solutions that align with their strategic goals. We are committed to offering unparalleled client service, acting as a seamless extension of our clients\' human resource operations.',
 'To be the most trusted and recognized recruitment company in the Maldives, known for our professionalism, excellence and ability to deliver outstanding outcomes for both employers and candidates.',
 '+960 4000-444',
 '+960 755-9001',
 '+960 911-1409',
 'info@skybordersolutions.com',
 'H. Dhoorihaa (5A), Kalaafaanu Hingun, Male\' City, Republic of Maldives',
 'Sunday - Thursday: 8:00 AM - 5:00 PM\nSaturday: 9:00 AM - 1:00 PM\nFriday: Closed');

INSERT INTO statistics (stat_name, stat_value, stat_label, display_order) VALUES
('placements', '1000+', 'Successful Placements', 1),
('partners', '50+', 'Partner Companies', 2),
('compliance', '100%', 'Licensed & Compliant', 3);

INSERT INTO service_categories (category_name, category_description, icon_class, color_theme, display_order) VALUES
('Recruitment Services', 'Source and screen candidates across multiple sectors', 'fas fa-user-tie', 'indigo', 1),
('HR Support Services', 'Comprehensive post-recruitment support and compliance', 'fas fa-users-cog', 'green', 2),
('Permits & Visa Processing', 'Government approvals for legal expatriate employment', 'fas fa-passport', 'purple', 3),
('Insurance Services', 'Comprehensive insurance coverage for expatriate employees', 'fas fa-shield-alt', 'blue', 4);

INSERT INTO portfolio_categories (category_name, category_slug, description, icon_class, color_theme, total_placements, total_projects, display_order) VALUES
('Construction & Engineering', 'construction', 'Major construction and infrastructure projects', 'fas fa-hard-hat', 'blue', 200, 15, 1),
('Tourism & Hospitality', 'hospitality', 'Leading resorts and hotels', 'fas fa-concierge-bell', 'yellow', 150, 25, 2),
('Healthcare Services', 'healthcare', 'Hospitals, clinics, and medical facilities', 'fas fa-user-md', 'red', 80, 10, 3),
('Professional Services', 'professional', 'IT, finance, administration, and consultancy', 'fas fa-laptop-code', 'green', 120, 30, 4);

INSERT INTO client_categories (category_name, category_description, icon_class, color_theme, display_order) VALUES
('Construction & Engineering', 'Construction and engineering companies', 'fas fa-building', 'blue', 1),
('Tourism & Hospitality', 'Hotels, resorts, and hospitality businesses', 'fas fa-hotel', 'yellow', 2),
('Investments, Services & Trading', 'Investment firms and service companies', 'fas fa-chart-line', 'green', 3);

INSERT INTO website_settings (setting_key, setting_value, setting_type, setting_group, description) VALUES
('site_maintenance', 'false', 'boolean', 'general', 'Enable maintenance mode'),
('contact_form_enabled', 'true', 'boolean', 'contact', 'Enable contact form submissions'),
('google_analytics_id', '', 'text', 'analytics', 'Google Analytics tracking ID'),
('meta_description', 'Leading HR consultancy and recruitment firm in Maldives. Government-licensed professional workforce solutions.', 'textarea', 'seo', 'Site meta description'),
('social_facebook', '', 'text', 'social', 'Facebook page URL'),
('social_linkedin', '', 'text', 'social', 'LinkedIn page URL');
