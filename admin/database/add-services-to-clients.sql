-- Add Services Field to Clients Table
-- Migration script to add services functionality

USE skydfcaf_sky_border;

-- Add services field to clients table if it doesn't exist
ALTER TABLE clients ADD COLUMN IF NOT EXISTS services TEXT AFTER description;

-- Add index for better performance
CREATE INDEX IF NOT EXISTS idx_clients_services ON clients(services(100));

-- Update existing clients with sample services data
UPDATE clients SET services = 'Recruitment, HR Consulting' WHERE id = 1 AND (services IS NULL OR services = '');
UPDATE clients SET services = 'Recruitment, Staffing' WHERE id = 2 AND (services IS NULL OR services = '');
UPDATE clients SET services = 'HR Consulting, Training' WHERE id = 3 AND (services IS NULL OR services = '');

-- Insert sample service categories if they don't exist
INSERT IGNORE INTO service_categories (category_name, category_description, icon_class, color_theme, display_order) VALUES
('Recruitment', 'End-to-end recruitment and talent acquisition services', 'fas fa-user-plus', 'blue', 1),
('HR Consulting', 'Human resources strategy and process optimization', 'fas fa-users-cog', 'green', 2),
('Staffing', 'Temporary and contract staffing solutions', 'fas fa-user-tie', 'purple', 3),
('Training', 'Employee development and skills training', 'fas fa-graduation-cap', 'orange', 4),
('Compliance', 'HR compliance and legal advisory services', 'fas fa-shield-alt', 'red', 5),
('Visa Processing', 'Work permit and visa application services', 'fas fa-passport', 'indigo', 6),
('Insurance Services', 'Employee insurance and benefits management', 'fas fa-shield-alt', 'teal', 7);

-- Show the updated table structure
DESCRIBE clients;

-- Show sample data
SELECT id, client_name, services FROM clients LIMIT 5;
