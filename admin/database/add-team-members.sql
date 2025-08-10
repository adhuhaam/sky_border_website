-- Add team members table for company team management
-- This script creates a team members table without affecting existing data

-- Create team members table if it doesn't exist
CREATE TABLE IF NOT EXISTS team_members (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    designation VARCHAR(255) NOT NULL,
    description TEXT,
    photo_url VARCHAR(500),
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Add index for team members
CREATE INDEX idx_team_members_order ON team_members(display_order, is_active);

-- Insert sample team members if they don't exist
INSERT IGNORE INTO team_members (name, designation, description, display_order) VALUES
('Ahmed Rasheed', 'CEO & Founder', 'Experienced HR professional with over 15 years in recruitment and human resources management across the Maldives.', 1),
('Aisha Mohamed', 'Head of Recruitment', 'Specialist in talent acquisition and candidate screening with expertise in various industries.', 2),
('Mohamed Hassan', 'HR Consultant', 'Certified HR consultant providing strategic human resources solutions to businesses.', 3);

-- Show the table structure
DESCRIBE team_members;

-- Show sample data
SELECT id, name, designation, display_order FROM team_members ORDER BY display_order;
