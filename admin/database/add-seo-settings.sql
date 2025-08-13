-- SEO Settings Table
CREATE TABLE IF NOT EXISTS `seo_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_name` varchar(100) NOT NULL DEFAULT 'global',
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `og_title` varchar(255) DEFAULT NULL,
  `og_description` text DEFAULT NULL,
  `og_image` varchar(255) DEFAULT NULL,
  `twitter_title` varchar(255) DEFAULT NULL,
  `twitter_description` text DEFAULT NULL,
  `twitter_image` varchar(255) DEFAULT NULL,
  `canonical_url` varchar(255) DEFAULT NULL,
  `robots_txt` text DEFAULT NULL,
  `google_analytics_id` varchar(50) DEFAULT NULL,
  `google_tag_manager_id` varchar(50) DEFAULT NULL,
  `facebook_pixel_id` varchar(50) DEFAULT NULL,
  `schema_markup` longtext DEFAULT NULL,
  `custom_meta_tags` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_name` (`page_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default global SEO settings
INSERT IGNORE INTO `seo_settings` (`page_name`, `meta_title`, `meta_description`, `meta_keywords`, `og_title`, `og_description`, `og_image`, `twitter_title`, `twitter_description`, `twitter_image`, `canonical_url`, `robots_txt`, `google_analytics_id`, `google_tag_manager_id`, `facebook_pixel_id`, `schema_markup`, `custom_meta_tags`) VALUES
('global', 'Sky Border Solutions | Professional HR Consulting & Recruitment Agency', 'Leading HR consultancy and recruitment firm in the Republic of Maldives, providing end-to-end manpower solutions with excellence and integrity.', 'HR consulting, recruitment agency, Maldives, workforce solutions, HR services, professional recruitment, talent acquisition, HR consultancy', 'Sky Border Solutions | Professional HR Consulting & Recruitment Agency', 'Leading HR consultancy and recruitment firm in the Republic of Maldives, providing end-to-end manpower solutions with excellence and integrity.', '/images/logo.svg', 'Sky Border Solutions | Professional HR Consulting & Recruitment Agency', 'Leading HR consultancy and recruitment firm in the Republic of Maldives, providing end-to-end manpower solutions with excellence and integrity.', '/images/logo.svg', 'https://skybordersolutions.com', 'User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /uploads/\nSitemap: https://skybordersolutions.com/sitemap.xml', 'G-XXXXXXXXXX', 'GTM-XXXXXXX', 'XXXXXXXXXX', '{"@context":"https://schema.org","@type":"Organization","name":"Sky Border Solutions","url":"https://skybordersolutions.com","logo":"https://skybordersolutions.com/images/logo.svg","description":"Leading HR consultancy and recruitment firm in the Republic of Maldives","address":{"@type":"PostalAddress","addressCountry":"MV"},"contactPoint":{"@type":"ContactPoint","telephone":"+960-XXX-XXXX","contactType":"customer service"}}', '<meta name="author" content="Sky Border Solutions">\n<meta name="theme-color" content="#667eea">\n<meta name="msapplication-TileColor" content="#667eea">');

-- Insert page-specific SEO settings
INSERT IGNORE INTO `seo_settings` (`page_name`, `meta_title`, `meta_description`, `meta_keywords`, `og_title`, `og_description`, `og_image`, `twitter_title`, `twitter_description`, `twitter_image`, `canonical_url`) VALUES
('home', 'Sky Border Solutions | Professional HR Consulting & Recruitment Agency', 'Leading HR consultancy and recruitment firm in the Republic of Maldives, providing end-to-end manpower solutions with excellence and integrity.', 'HR consulting, recruitment agency, Maldives, workforce solutions, HR services, professional recruitment, talent acquisition, HR consultancy', 'Sky Border Solutions | Professional HR Consulting & Recruitment Agency', 'Leading HR consultancy and recruitment firm in the Republic of Maldives, providing end-to-end manpower solutions with excellence and integrity.', '/images/logo.svg', 'Sky Border Solutions | Professional HR Consulting & Recruitment Agency', 'Leading HR consultancy and recruitment firm in the Republic of Maldives, providing end-to-end manpower solutions with excellence and integrity.', '/images/logo.svg', 'https://skybordersolutions.com/'),
('about', 'About Us | Sky Border Solutions | HR Consulting & Recruitment', 'Learn about Sky Border Solutions, a leading HR consultancy and recruitment firm in Maldives. Discover our mission, vision, and commitment to excellence.', 'about us, company history, mission vision, HR consultancy Maldives, recruitment company Maldives, workforce solutions', 'About Us | Sky Border Solutions | HR Consulting & Recruitment', 'Learn about Sky Border Solutions, a leading HR consultancy and recruitment firm in Maldives. Discover our mission, vision, and commitment to excellence.', '/images/logo.svg', 'About Us | Sky Border Solutions | HR Consulting & Recruitment', 'Learn about Sky Border Solutions, a leading HR consultancy and recruitment firm in Maldives. Discover our mission, vision, and commitment to excellence.', '/images/logo.svg', 'https://skybordersolutions.com/about'),
('services', 'Our Services | Sky Border Solutions | HR Consulting & Recruitment', 'Comprehensive HR solutions including recruitment, consulting, workforce management, and talent acquisition services in Maldives.', 'HR services, recruitment services, consulting services, workforce management, talent acquisition, HR solutions Maldives', 'Our Services | Sky Border Solutions | HR Consulting & Recruitment', 'Comprehensive HR solutions including recruitment, consulting, workforce management, and talent acquisition services in Maldives.', '/images/logo.svg', 'Our Services | Sky Border Solutions | HR Consulting & Recruitment', 'Comprehensive HR solutions including recruitment, consulting, workforce management, and talent acquisition services in Maldives.', '/images/logo.svg', 'https://skybordersolutions.com/services'),
('contact', 'Contact Us | Sky Border Solutions | HR Consulting & Recruitment', 'Get in touch with Sky Border Solutions for professional HR consulting and recruitment services in Maldives. Contact our expert team today.', 'contact us, get quote, HR consulting Maldives, recruitment services Maldives, workforce solutions contact', 'Contact Us | Sky Border Solutions | HR Consulting & Recruitment', 'Get in touch with Sky Border Solutions for professional HR consulting and recruitment services in Maldives. Contact our expert team today.', '/images/logo.svg', 'Contact Us | Sky Border Solutions | HR Consulting & Recruitment', 'Get in touch with Sky Border Solutions for professional HR consulting and recruitment services in Maldives. Contact our expert team today.', '/images/logo.svg', 'https://skybordersolutions.com/contact');
