-- Simplified clients table schema for immediate deployment
-- This creates a simple structure that matches the current ContentManager methods

-- Use your database
USE skydfcaf_sky_border;

-- Create a simplified clients table (without categories for now)
CREATE TABLE IF NOT EXISTS clients_simple (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(200) NOT NULL,
    category VARCHAR(100) DEFAULT 'Other',
    logo_url VARCHAR(255),
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert some sample data
INSERT INTO clients_simple (client_name, category, display_order) VALUES
('Sample Construction Company', 'Construction & Engineering', 1),
('Sample Resort & Spa', 'Tourism & Hospitality', 2),
('Sample Investment Group', 'Investments, Services & Trading', 3);

-- If you want to keep your existing complex schema, you can run this instead:
-- Just make sure the client_categories table exists first
