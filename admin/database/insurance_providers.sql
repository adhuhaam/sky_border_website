-- Insurance Providers Table (Simplified)
-- Sky Border Solutions CMS - Insurance Management Extension

CREATE TABLE IF NOT EXISTS insurance_providers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_name VARCHAR(100) NOT NULL,
    logo_url VARCHAR(255),
    is_featured BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_is_active (is_active),
    INDEX idx_is_featured (is_featured),
    INDEX idx_display_order (display_order)
);

-- Insert sample insurance providers (simplified)
INSERT INTO insurance_providers (
    provider_name, 
    is_featured, 
    display_order
) VALUES 
(
    'Maldivian Health Insurance Co.',
    TRUE,
    1
),
(
    'Allied Insurance Maldives',
    TRUE,
    2
),
(
    'Maldives Travel Insurance Ltd.',
    FALSE,
    3
);


