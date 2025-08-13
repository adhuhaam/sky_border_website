-- Contact Messages Table Migration
-- Sky Border Solutions CMS

-- Create contact_messages table if it doesn't exist
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `company` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('new','read','replied','archived') NOT NULL DEFAULT 'new',
  `admin_notes` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample contact messages for testing
INSERT IGNORE INTO `contact_messages` (`name`, `email`, `company`, `phone`, `subject`, `message`, `status`, `admin_notes`) VALUES
('John Smith', 'john.smith@example.com', 'ABC Company Ltd', '+960-123-4567', 'HR Consulting Inquiry', 'Hello, I am interested in your HR consulting services for our company. We have about 50 employees and need help with performance management systems. Could you please provide more information about your services and pricing?', 'new', NULL),
('Sarah Johnson', 'sarah.j@techcorp.mv', 'TechCorp Maldives', '+960-987-6543', 'Recruitment Services', 'We are looking to hire 3 software developers and 2 project managers. Your company was recommended by a business partner. Please let us know your recruitment process and timeline.', 'read', 'Client is looking for tech talent. Follow up with recruitment package details.'),
('Ahmed Hassan', 'ahmed@maldivesbusiness.mv', 'Maldives Business Group', '+960-555-1234', 'Workforce Management', 'We need assistance with workforce planning and training programs. Our company is expanding and we want to ensure we have the right people in place. Can you help us develop a comprehensive workforce strategy?', 'replied', 'Sent detailed proposal for workforce planning services. Client responded positively.'),
('Maria Rodriguez', 'maria@hospitality.mv', 'Luxury Resorts Maldives', '+960-777-8888', 'Employee Training Programs', 'We operate several luxury resorts in the Maldives and need to improve our customer service training programs. Do you offer customized training solutions for the hospitality industry?', 'archived', 'Client decided to go with in-house training program instead.');
