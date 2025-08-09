-- Sky Border Solutions CMS - Final Database Setup
-- Complete database schema with all updates and enhancements
-- Version: 2.0 (Final)

USE skydfcaf_sky_border;

-- =====================================================
-- CORE TABLES
-- =====================================================

-- Admin Users Table
CREATE TABLE IF NOT EXISTS admin_users (
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

-- Company Information Table (Enhanced with about_us)
CREATE TABLE IF NOT EXISTS company_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(200) NOT NULL DEFAULT 'Sky Border Solution Pvt Ltd',
    tagline VARCHAR(200) NOT NULL DEFAULT 'Where compliance meets competence',
    description TEXT,
    about_us TEXT,
    mission TEXT,
    vision TEXT,
    phone VARCHAR(20),
    hotline1 VARCHAR(20),
    hotline2 VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    website VARCHAR(255),
    established_year YEAR,
    business_type VARCHAR(100),
    registration_number VARCHAR(100),
    license_number VARCHAR(100),
    business_hours TEXT,
    facebook_url VARCHAR(255),
    linkedin_url VARCHAR(255),
    twitter_url VARCHAR(255),
    instagram_url VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Statistics Table
CREATE TABLE IF NOT EXISTS statistics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stat_name VARCHAR(50) NOT NULL,
    stat_value VARCHAR(20) NOT NULL,
    stat_label VARCHAR(100) NOT NULL,
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =====================================================
-- SERVICE MANAGEMENT TABLES
-- =====================================================

-- Service Categories Table
CREATE TABLE IF NOT EXISTS service_categories (
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

-- Industries Table
CREATE TABLE IF NOT EXISTS industries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    industry_name VARCHAR(200) NOT NULL,
    industry_description TEXT,
    icon_class VARCHAR(100) DEFAULT 'fas fa-briefcase',
    color_theme VARCHAR(50) DEFAULT 'blue',
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Job Positions Table
CREATE TABLE IF NOT EXISTS job_positions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    industry_id INT NOT NULL,
    position_name VARCHAR(200) NOT NULL,
    position_description TEXT,
    is_featured BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (industry_id) REFERENCES industries(id) ON DELETE CASCADE
);

-- =====================================================
-- CLIENT MANAGEMENT TABLES
-- =====================================================

-- Portfolio Categories Table
CREATE TABLE IF NOT EXISTS portfolio_categories (
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

-- Client Categories Table
CREATE TABLE IF NOT EXISTS client_categories (
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
CREATE TABLE IF NOT EXISTS clients (
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

-- =====================================================
-- COMMUNICATION TABLES
-- =====================================================

-- Contact Messages Table
CREATE TABLE IF NOT EXISTS contact_messages (
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

-- =====================================================
-- SYSTEM TABLES
-- =====================================================

-- Website Settings Table
CREATE TABLE IF NOT EXISTS website_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_name VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_description TEXT,
    setting_type ENUM('text', 'textarea', 'number', 'boolean', 'json') DEFAULT 'text',
    is_active BOOLEAN DEFAULT TRUE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Activity Log Table (for tracking admin actions)
CREATE TABLE IF NOT EXISTS activity_log (
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

-- =====================================================
-- DEFAULT DATA INSERTION
-- =====================================================

-- Default admin user (password: admin123)
INSERT IGNORE INTO admin_users (id, username, email, password_hash, full_name, role) VALUES
(1, 'admin', 'admin@skybordersolutions.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'super_admin');

-- Company information with complete details
INSERT INTO company_info (
    id, company_name, tagline, description, about_us, mission, vision, 
    phone, hotline1, hotline2, email, address, website, established_year,
    business_type, registration_number, license_number, business_hours
) VALUES (
    1,
    'Sky Border Solution Pvt Ltd',
    'Where compliance meets competence',
    'Government-licensed HR consultancy and recruitment firm headquartered in the Republic of Maldives',
    'Sky Border Solution Pvt Ltd is a government-licensed HR consultancy and recruitment firm headquartered in the Republic of Maldives. Established in response to the rising demand for skilled foreign labor, we are strategically positioned to provide end-to-end manpower solutions. Our operations are driven by a long-term vision, well-defined mission, and a strong foundation of core values. With a seasoned leadership team that brings decades of recruitment expertise, we are adept at identifying, sourcing, and placing the most qualified talent to meet diverse organizational needs. Our consistent year-on-year growth, backed by a solid financial framework, reflects our commitment to service excellence, operational integrity, and client satisfaction. At Sky Border Solution, we are dedicated to bridging workforce gaps with professionalism, precision, and purpose.',
    'To foster enduring partnerships with organizations by delivering superior recruitment solutions that align with their strategic goals. We are committed to offering unparalleled client service, acting as a seamless extension of our clients'' human resource operations.',
    'To be the most trusted and recognized recruitment company in the Maldives, known for our professionalism, excellence and ability to deliver outstanding outcomes for both employers and candidates.',
    '+960 330-5462',
    '+960 755-9001',
    '+960 911-1409',
    'info@skybordersolutions.com',
    'H. Dhoorihaa (5A), Kalaafaanu Hingun, Male'' City, Republic of Maldives',
    'https://skybordersolutions.com',
    2020,
    'HR Consultancy & Recruitment',
    'NPO/2020-0042',
    'HR-LIC-2020-001',
    'Sunday - Thursday: 8:00 AM - 5:00 PM\nSaturday: 9:00 AM - 1:00 PM\nFriday: Closed'
) ON DUPLICATE KEY UPDATE
    company_name = VALUES(company_name),
    about_us = VALUES(about_us),
    description = VALUES(description),
    mission = VALUES(mission),
    vision = VALUES(vision),
    phone = VALUES(phone),
    email = VALUES(email),
    address = VALUES(address),
    website = VALUES(website),
    established_year = VALUES(established_year),
    business_type = VALUES(business_type);

-- Default statistics
INSERT IGNORE INTO statistics (id, stat_name, stat_value, stat_label, display_order) VALUES
(1, 'placements', '1000+', 'Successful Placements', 1),
(2, 'partners', '50+', 'Partner Companies', 2),
(3, 'compliance', '100%', 'Licensed & Compliant', 3),
(4, 'experience', '4+', 'Years of Experience', 4);

-- Service categories
INSERT IGNORE INTO service_categories (id, category_name, category_description, icon_class, color_theme, display_order) VALUES
(1, 'Recruitment Services', 'Source and screen candidates across multiple sectors', 'fas fa-user-tie', 'indigo', 1),
(2, 'HR Support Services', 'Comprehensive post-recruitment support and compliance', 'fas fa-users-cog', 'green', 2),
(3, 'Permits & Visa Processing', 'Government approvals for legal expatriate employment', 'fas fa-passport', 'purple', 3),
(4, 'Insurance Services', 'Comprehensive insurance coverage for expatriate employees', 'fas fa-shield-alt', 'blue', 4);

-- Industries data
INSERT IGNORE INTO industries (id, industry_name, industry_description, icon_class, color_theme, display_order) VALUES
(1, 'Construction & Engineering', 'Major construction and infrastructure projects across the Maldives', 'fas fa-hard-hat', 'amber', 1),
(2, 'Healthcare', 'Medical professionals for hospitals, clinics, and healthcare facilities', 'fas fa-user-md', 'emerald', 2),
(3, 'Tourism & Hospitality', 'Resort staff and hospitality professionals for the tourism industry', 'fas fa-concierge-bell', 'blue', 3),
(4, 'Administration & Office', 'Administrative and office support professionals', 'fas fa-briefcase', 'violet', 4),
(5, 'Transport & Logistics', 'Transportation and logistics personnel', 'fas fa-truck', 'rose', 5),
(6, 'Education & Childcare', 'Educational professionals and childcare providers', 'fas fa-graduation-cap', 'indigo', 6);

-- Job positions (sample data for each industry)
INSERT IGNORE INTO job_positions (id, industry_id, position_name, position_description, is_featured, display_order) VALUES
-- Construction & Engineering positions
(1, 1, 'Civil Engineer', 'Construction project design and supervision', TRUE, 1),
(2, 1, 'Carpenter', 'Skilled woodworking professionals for construction projects', TRUE, 2),
(3, 1, 'Mason', 'Stone and brick laying specialists', TRUE, 3),
(4, 1, 'Electrician', 'Electrical installation and maintenance professionals', TRUE, 4),
(5, 1, 'Plumber', 'Plumbing installation and maintenance experts', FALSE, 5),
(6, 1, 'Welder', 'Metal welding and fabrication specialists', FALSE, 6),
(7, 1, 'Project Manager', 'Overall project coordination and management', TRUE, 7),
(8, 1, 'Safety Officer', 'Construction site safety and compliance', FALSE, 8),

-- Healthcare positions
(9, 2, 'General Practitioners', 'Primary care physicians', TRUE, 1),
(10, 2, 'Nurses', 'Registered nurses for various medical specialties', TRUE, 2),
(11, 2, 'Surgeons', 'Surgical specialists for various procedures', TRUE, 3),
(12, 2, 'Pharmacists', 'Medication management professionals', TRUE, 4),
(13, 2, 'Radiologists', 'Medical imaging specialists', TRUE, 5),
(14, 2, 'Physiotherapists', 'Physical rehabilitation specialists', TRUE, 6),
(15, 2, 'Laboratory Technicians', 'Medical testing and analysis specialists', FALSE, 7),
(16, 2, 'Dentists', 'Oral health specialists', FALSE, 8),

-- Tourism & Hospitality positions
(17, 3, 'Head Chefs', 'Executive kitchen management professionals', TRUE, 1),
(18, 3, 'Spa Therapists', 'Wellness and spa treatment specialists', TRUE, 2),
(19, 3, 'Bartenders', 'Beverage service professionals', TRUE, 3),
(20, 3, 'Resort Managers', 'Overall resort operations management', TRUE, 4),
(21, 3, 'Diving Instructors', 'Scuba diving education and safety', TRUE, 5),
(22, 3, 'Boat Captains', 'Marine vessel operation', TRUE, 6),
(23, 3, 'Front Office Receptionists', 'Guest check-in and customer service', FALSE, 7),
(24, 3, 'Housekeeping Supervisors', 'Housekeeping management', FALSE, 8);

-- Portfolio categories
INSERT IGNORE INTO portfolio_categories (id, category_name, category_slug, description, icon_class, color_theme, total_placements, total_projects, display_order) VALUES
(1, 'Construction & Engineering', 'construction', 'Major construction and infrastructure projects', 'fas fa-hard-hat', 'blue', 200, 15, 1),
(2, 'Tourism & Hospitality', 'hospitality', 'Leading resorts and hotels', 'fas fa-concierge-bell', 'yellow', 150, 25, 2),
(3, 'Healthcare Services', 'healthcare', 'Hospitals, clinics, and medical facilities', 'fas fa-user-md', 'red', 80, 10, 3),
(4, 'Professional Services', 'professional', 'IT, finance, administration, and consultancy', 'fas fa-laptop-code', 'green', 120, 30, 4);

-- Client categories
INSERT IGNORE INTO client_categories (id, category_name, category_description, icon_class, color_theme, display_order) VALUES
(1, 'Construction & Engineering', 'Construction and engineering companies', 'fas fa-building', 'blue', 1),
(2, 'Tourism & Hospitality', 'Hotels, resorts, and hospitality businesses', 'fas fa-hotel', 'yellow', 2),
(3, 'Healthcare Services', 'Hospitals, clinics, and medical facilities', 'fas fa-hospital', 'red', 3),
(4, 'Investments, Services & Trading', 'Investment firms and service companies', 'fas fa-chart-line', 'green', 4);

-- Sample clients
INSERT IGNORE INTO clients (id, category_id, client_name, company_type, description, is_featured, display_order) VALUES
(1, 1, 'Leading Construction Company', 'Construction', 'Major infrastructure development partner', TRUE, 1),
(2, 2, 'Luxury Resort & Spa', 'Hospitality', 'Premium resort hospitality services', TRUE, 2),
(3, 3, 'Regional Medical Center', 'Healthcare', 'Leading healthcare facility partner', TRUE, 3),
(4, 4, 'Investment Holdings Group', 'Investment', 'Financial services and investment management', FALSE, 4);

-- Website settings
INSERT IGNORE INTO website_settings (setting_name, setting_value, setting_description, setting_type) VALUES
('site_maintenance', 'false', 'Enable/disable site maintenance mode', 'boolean'),
('contact_email', 'info@skybordersolutions.com', 'Primary contact email for inquiries', 'text'),
('max_file_upload', '10', 'Maximum file upload size in MB', 'number'),
('analytics_code', '', 'Google Analytics tracking code', 'textarea'),
('social_facebook', '', 'Facebook page URL', 'text'),
('social_linkedin', '', 'LinkedIn company page URL', 'text'),
('social_twitter', '', 'Twitter profile URL', 'text'),
('social_instagram', '', 'Instagram profile URL', 'text');

-- =====================================================
-- INDEXES FOR PERFORMANCE
-- =====================================================

-- Add indexes for better query performance
CREATE INDEX IF NOT EXISTS idx_clients_active ON clients(is_active, display_order);
CREATE INDEX IF NOT EXISTS idx_clients_category ON clients(category_id);
CREATE INDEX IF NOT EXISTS idx_clients_featured ON clients(is_featured);

CREATE INDEX IF NOT EXISTS idx_job_positions_industry ON job_positions(industry_id);
CREATE INDEX IF NOT EXISTS idx_job_positions_featured ON job_positions(is_featured);
CREATE INDEX IF NOT EXISTS idx_job_positions_active ON job_positions(is_active);

CREATE INDEX IF NOT EXISTS idx_contact_messages_status ON contact_messages(status);
CREATE INDEX IF NOT EXISTS idx_contact_messages_created ON contact_messages(created_at);

CREATE INDEX IF NOT EXISTS idx_industries_active ON industries(is_active, display_order);
CREATE INDEX IF NOT EXISTS idx_service_categories_active ON service_categories(is_active, display_order);

-- =====================================================
-- FINAL COMPLETION MESSAGE
-- =====================================================

-- This will be displayed at the end
SELECT 'Sky Border Solutions CMS Database Setup Complete!' as message,
       'Version 2.0 Final' as version,
       NOW() as completed_at;
