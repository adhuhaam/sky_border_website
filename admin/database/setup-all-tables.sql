-- Sky Border Solutions - Complete Database Setup
-- This script creates all necessary tables and fixes existing issues
-- Run this in your database to set up the complete system

USE skydfcaf_sky_border;

-- =====================================================
-- 1. CAMPAIGNS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `campaigns` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `subject` varchar(255) NOT NULL,
    `url_to_render` varchar(500) NOT NULL DEFAULT '/',
    `rendered_html` longtext DEFAULT NULL,
    `status` enum('draft', 'scheduled', 'sending', 'sent', 'paused', 'cancelled') DEFAULT 'draft',
    `scheduled_at` timestamp NULL DEFAULT NULL,
    `sent_at` timestamp NULL DEFAULT NULL,
    `total_recipients` int(11) DEFAULT 0,
    `smtp_config_id` int(11) DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_status` (`status`),
    KEY `idx_scheduled_at` (`scheduled_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 2. SMTP CONFIGURATION TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `smtp_config` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `host` varchar(255) NOT NULL,
    `port` int(11) NOT NULL DEFAULT 587,
    `username` varchar(255) NOT NULL,
    `password` text NOT NULL,
    `encryption` enum('tls', 'ssl', 'none') DEFAULT 'tls',
    `from_email` varchar(255) NOT NULL,
    `from_name` varchar(255) NOT NULL,
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 3. CONTACTS TABLE (Enhanced)
-- =====================================================
CREATE TABLE IF NOT EXISTS `contacts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL UNIQUE,
    `phone` varchar(50) DEFAULT NULL,
    `company` varchar(255) DEFAULT NULL,
    `status` enum('active', 'unsubscribed', 'bounced', 'inactive') DEFAULT 'active',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_email` (`email`),
    KEY `idx_status` (`status`),
    KEY `idx_company` (`company`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 4. CONTACT LISTS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `contact_lists` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `description` text DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 5. CONTACT LIST RELATIONSHIPS
-- =====================================================
CREATE TABLE IF NOT EXISTS `contact_list_contacts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `list_id` int(11) NOT NULL,
    `contact_id` int(11) NOT NULL,
    `added_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_list_contact` (`list_id`, `contact_id`),
    FOREIGN KEY (`list_id`) REFERENCES `contact_lists`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`contact_id`) REFERENCES `contacts`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 6. CAMPAIGN RECIPIENTS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `campaign_recipients` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `campaign_id` int(11) NOT NULL,
    `contact_id` int(11) NOT NULL,
    `list_id` int(11) DEFAULT NULL,
    `email` varchar(255) NOT NULL,
    `status` enum('pending', 'sent', 'delivered', 'opened', 'clicked', 'bounced', 'unsubscribed', 'failed') DEFAULT 'pending',
    `sent_at` timestamp NULL DEFAULT NULL,
    `delivered_at` timestamp NULL DEFAULT NULL,
    `opened_at` timestamp NULL DEFAULT NULL,
    `clicked_at` timestamp NULL DEFAULT NULL,
    `bounced_at` timestamp NULL DEFAULT NULL,
    `unsubscribed_at` timestamp NULL DEFAULT NULL,
    `error_message` text DEFAULT NULL,
    `retry_count` int(11) DEFAULT 0,
    `max_retries` int(11) DEFAULT 3,
    `tracking_id` varchar(255) DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_campaign_id` (`campaign_id`),
    KEY `idx_contact_id` (`contact_id`),
    KEY `idx_status` (`status`),
    KEY `idx_tracking_id` (`tracking_id`),
    FOREIGN KEY (`campaign_id`) REFERENCES `campaigns`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`contact_id`) REFERENCES `contacts`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`list_id`) REFERENCES `contact_lists`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 7. EMAIL TRACKING EVENTS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `email_events` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `campaign_id` int(11) NOT NULL,
    `recipient_id` int(11) NOT NULL,
    `event_type` enum('sent', 'delivered', 'opened', 'clicked', 'bounced', 'unsubscribed', 'failed') NOT NULL,
    `event_data` json DEFAULT NULL,
    `ip_address` varchar(45) DEFAULT NULL,
    `user_agent` text DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_campaign_id` (`campaign_id`),
    KEY `idx_recipient_id` (`recipient_id`),
    KEY `idx_event_type` (`event_type`),
    KEY `idx_created_at` (`created_at`),
    FOREIGN KEY (`campaign_id`) REFERENCES `campaigns`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`recipient_id`) REFERENCES `campaign_recipients`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 8. UNSUBSCRIBE TRACKING TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `unsubscribes` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(255) NOT NULL,
    `campaign_id` int(11) DEFAULT NULL,
    `reason` text DEFAULT NULL,
    `unsubscribed_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_email_campaign` (`email`, `campaign_id`),
    KEY `idx_email` (`email`),
    KEY `idx_campaign_id` (`campaign_id`),
    FOREIGN KEY (`campaign_id`) REFERENCES `campaigns`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 9. BOUNCE TRACKING TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `bounces` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(255) NOT NULL,
    `campaign_id` int(11) DEFAULT NULL,
    `bounce_type` enum('hard', 'soft') DEFAULT 'hard',
    `bounce_reason` text DEFAULT NULL,
    `bounced_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_email` (`email`),
    KEY `idx_campaign_id` (`campaign_id`),
    KEY `idx_bounce_type` (`bounce_type`),
    FOREIGN KEY (`campaign_id`) REFERENCES `campaigns`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 10. INSERT DEFAULT DATA
-- =====================================================

-- Insert default SMTP configuration
INSERT IGNORE INTO `smtp_config` (`id`, `name`, `host`, `port`, `username`, `password`, `encryption`, `from_email`, `from_name`, `is_active`) VALUES
(1, 'Sky Border Solutions SMTP', 'skybordersolutions.com', 465, 'hello@skybordersolutions.com', 'Ompl@65482*', 'ssl', 'hello@skybordersolutions.com', 'Sky Border Solutions', 1);

-- Insert sample contact list
INSERT IGNORE INTO `contact_lists` (`id`, `name`, `description`) VALUES
(1, 'All Contacts', 'Default list containing all contacts'),
(2, 'Newsletter Subscribers', 'Contacts who have subscribed to newsletters'),
(3, 'Clients', 'Current and potential clients');

-- Insert sample contacts
INSERT IGNORE INTO `contacts` (`name`, `email`, `phone`, `company`, `status`) VALUES
('John Doe', 'john.doe@example.com', '+960 123 4567', 'Example Corp', 'active'),
('Jane Smith', 'jane.smith@example.com', '+960 987 6543', 'Test Company', 'active'),
('Bob Johnson', 'bob.johnson@example.com', '+960 555 1234', 'Sample Inc', 'active');

-- Add contacts to default list
INSERT IGNORE INTO `contact_list_contacts` (`list_id`, `contact_id`) VALUES
(1, 1), (1, 2), (1, 3),
(2, 1), (2, 2),
(3, 1), (3, 3);

-- =====================================================
-- 11. VERIFICATION QUERIES
-- =====================================================

-- Show all tables created
SHOW TABLES LIKE '%campaign%';
SHOW TABLES LIKE '%smtp%';
SHOW TABLES LIKE '%contact%';

-- Show sample data
SELECT 'SMTP Configs' as table_name, COUNT(*) as count FROM smtp_config
UNION ALL
SELECT 'Contacts' as table_name, COUNT(*) as count FROM contacts
UNION ALL
SELECT 'Contact Lists' as table_name, COUNT(*) as count FROM contact_lists
UNION ALL
SELECT 'Campaigns' as table_name, COUNT(*) as count FROM campaigns;

-- =====================================================
-- COMPLETION MESSAGE
-- =====================================================
SELECT 'Database setup completed successfully!' as message,
       NOW() as completed_at;
