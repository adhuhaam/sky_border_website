-- Add company information table for managing company details
-- This script creates a company info table without affecting existing data

-- Create company info table if it doesn't exist
CREATE TABLE IF NOT EXISTS company_info (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_name VARCHAR(255) NOT NULL DEFAULT 'Sky Border Solutions',
    tagline VARCHAR(500) DEFAULT 'Where compliance meets competence',
    description TEXT,
    about_us TEXT,
    mission TEXT,
    vision TEXT,
    phone VARCHAR(50) DEFAULT '+960 4000-444',
    hotline1 VARCHAR(50) DEFAULT '+960 755-9001',
    hotline2 VARCHAR(50) DEFAULT '+960 911-1409',
    email VARCHAR(255) DEFAULT 'info@skybordersolutions.com',
    address TEXT DEFAULT 'H. Dhoorihaa (5A), Kalaafaanu Hingun, Male\' City, Republic of Maldives',
    business_hours TEXT DEFAULT 'Sunday - Thursday: 8:00 AM - 5:00 PM\nSaturday: 9:00 AM - 1:00 PM\nFriday: Closed',
    website VARCHAR(255),
    established_year INT,
    business_type VARCHAR(255),
    registration_number VARCHAR(255),
    license_number VARCHAR(255),
    facebook_url VARCHAR(500),
    linkedin_url VARCHAR(500),
    twitter_url VARCHAR(500),
    instagram_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default company information if table is empty
INSERT IGNORE INTO company_info (id, company_name, tagline, description, about_us, mission, vision, phone, hotline1, hotline2, email, address, business_hours) VALUES
(1, 'Sky Border Solutions', 'Where compliance meets competence', 'Leading HR consultancy and recruitment firm in the Republic of Maldives, providing end-to-end manpower solutions with excellence and integrity.', 'Sky Border Solution Pvt Ltd is a government-licensed HR consultancy and recruitment firm headquartered in the Republic of Maldives. Established in response to the rising demand for skilled foreign labor, we are strategically positioned to provide end-to-end manpower solutions. Our operations are driven by a long-term vision, well-defined mission, and a strong foundation of core values. With a seasoned leadership team that brings decades of recruitment expertise, we are adept at identifying, sourcing, and placing the most qualified talent to meet diverse organizational needs. Our consistent year-on-year growth, backed by a solid financial framework, reflects our commitment to service excellence, operational integrity, and client satisfaction. At Sky Border Solution, we are dedicated to bridging workforce gaps with professionalism, precision, and purpose.', 'To foster enduring partnerships with organizations by delivering superior recruitment solutions that align with their strategic goals.', 'To be the most trusted and recognized recruitment company in the Maldives, known for our professionalism, excellence and ability to deliver outstanding outcomes.', '+960 4000-444', '+960 755-9001', '+960 911-1409', 'info@skybordersolutions.com', 'H. Dhoorihaa (5A), Kalaafaanu Hingun, Male\' City, Republic of Maldives', 'Sunday - Thursday: 8:00 AM - 5:00 PM\nSaturday: 9:00 AM - 1:00 PM\nFriday: Closed');

-- Show the table structure
DESCRIBE company_info;

-- Show sample data
SELECT id, company_name, tagline, phone, email FROM company_info;
