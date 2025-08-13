<?php
// Include Composer autoloader for PHPMailer
require_once __DIR__ . '/../../vendor/autoload.php';

class Mailer {
    private $pdo;
    private $smtpConfig;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->loadSMTPConfig();
    }
    
    /**
     * Load active SMTP configuration
     */
    private function loadSMTPConfig() {
        $stmt = $this->pdo->prepare("SELECT * FROM smtp_config WHERE is_active = 1 LIMIT 1");
        $stmt->execute();
        $this->smtpConfig = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get SMTP configuration
     */
    public function getSMTPConfig() {
        return $this->smtpConfig;
    }
    
    /**
     * Update SMTP configuration
     */
    public function updateSMTPConfig($data) {
        $stmt = $this->pdo->prepare("
            UPDATE smtp_config SET 
                name = ?, host = ?, port = ?, username = ?, 
                password = ?, encryption = ?, from_email = ?, from_name = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['name'], $data['host'], $data['port'], $data['username'],
            $data['password'], $data['encryption'], $data['from_email'], $data['from_name'],
            $data['id']
        ]);
    }
    
    /**
     * Render website as HTML email
     */
    public function renderWebsiteAsEmail($url) {
        // Use the static email template instead of dynamic rendering
        return $this->getStaticWebsiteTemplate();
    }
    
    /**
     * Get the static website email template
     */
    private function getStaticWebsiteTemplate() {
        $templatePath = __DIR__ . '/../email-templates/website-template.html';
        
        if (!file_exists($templatePath)) {
            // Fallback to a basic template if file doesn't exist
            return $this->getFallbackTemplate();
        }
        
        $template = file_get_contents($templatePath);
        
        if ($template === false) {
            return $this->getFallbackTemplate();
        }
        
        // Get real data from database
        try {
            $contentManager = new \ContentManager();
            $clients = $contentManager->getClients();
            $services = $contentManager->getServiceCategories();
            $industries = $contentManager->getPortfolioCategories();
            $companyInfo = $contentManager->getCompanyInfo();
            $stats = $contentManager->getStatistics();
        } catch (Exception $e) {
            // Fallback data if database fails
            $clients = [];
            $services = [];
            $industries = [];
            $companyInfo = [];
            $stats = [];
        }
        
        // Replace placeholders with actual values
        $domain = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $baseUrl = $protocol . '://' . $domain;
        
        $template = str_replace('{{website_url}}', $baseUrl, $template);
        $template = str_replace('{{contact_email}}', '{{contact_email}}', $template); // Keep placeholder for personalization
        $template = str_replace('{{unsubscribe_link}}', '{{unsubscribe_link}}', $template); // Keep placeholder for personalization
        
        // Replace dynamic content placeholders
        $template = str_replace('{{clients_data}}', $this->generateClientsHTML($clients), $template);
        $template = str_replace('{{services_data}}', $this->generateServicesHTML($services), $template);
        $template = str_replace('{{industries_data}}', $this->generateIndustriesHTML($industries), $template);
        $template = str_replace('{{company_info}}', $this->generateCompanyInfoHTML($companyInfo), $template);
        $template = str_replace('{{stats_data}}', $this->generateStatsHTML($stats), $template);
        
        return $template;
    }
    
    /**
     * Generate clients HTML from database data
     */
    private function generateClientsHTML($clients) {
        if (empty($clients)) {
            return $this->getFallbackClientsHTML();
        }
        
        $html = '';
        foreach (array_slice($clients, 0, 4) as $client) {
            $initials = strtoupper(substr($client['client_name'], 0, 2));
            $html .= '
            <div class="client-card hover:shadow-lg hover:border-blue-300 transition-all duration-300">
                <div class="client-logo">' . $initials . '</div>
                <div class="client-name">' . htmlspecialchars($client['client_name']) . '</div>
                <div class="client-category">' . htmlspecialchars($client['category_name'] ?? 'General') . '</div>
            </div>';
        }
        
        return $html;
    }
    
    /**
     * Generate services HTML from database data
     */
    private function generateServicesHTML($services) {
        if (empty($services)) {
            return $this->getFallbackServicesHTML();
        }
        
        $html = '';
        foreach (array_slice($services, 0, 4) as $service) {
            $icon = $this->getServiceIcon($service['icon_class']);
            $html .= '
            <div class="service-card hover:shadow-lg transition-shadow duration-300">
                <div class="service-icon">' . $icon . '</div>
                <h3 class="service-title">' . htmlspecialchars($service['category_name']) . '</h3>
                <p class="service-description">' . htmlspecialchars($service['category_description']) . '</p>
            </div>';
        }
        
        return $html;
    }
    
    /**
     * Generate industries HTML from database data
     */
    private function generateIndustriesHTML($industries) {
        if (empty($industries)) {
            return $this->getFallbackIndustriesHTML();
        }
        
        $html = '';
        foreach (array_slice($industries, 0, 4) as $industry) {
            $icon = $this->getIndustryIcon($industry['icon_class']);
            $html .= '
            <div class="industry-card hover:shadow-md hover:scale-105 transition-all duration-300">
                <div class="industry-icon">' . $icon . '</div>
                <div class="industry-name">' . htmlspecialchars($industry['category_name']) . '</div>
                <div class="industry-description">' . htmlspecialchars($industry['description']) . '</div>
            </div>';
        }
        
        return $html;
    }
    
    /**
     * Generate company info HTML from database data
     */
    private function generateCompanyInfoHTML($companyInfo) {
        if (empty($companyInfo)) {
            return $this->getFallbackCompanyInfoHTML();
        }
        
        return '
        <div class="about-content">
            <h2 class="about-title">About Us</h2>
            <p class="about-description">' . htmlspecialchars($companyInfo['about_us'] ?? 'Sky Border Solution Pvt Ltd is a government-licensed HR consultancy and recruitment firm headquartered in the Republic of Maldives.') . '</p>
        </div>';
    }
    
    /**
     * Generate stats HTML from database data
     */
    private function generateStatsHTML($stats) {
        if (empty($stats)) {
            return $this->getFallbackStatsHTML();
        }
        
        $html = '';
        foreach (array_slice($stats, 0, 3) as $stat) {
            $html .= '
            <div class="stat-item hover:shadow-lg hover:scale-105 transition-all duration-300">
                <div class="stat-value">' . htmlspecialchars($stat['stat_value']) . '</div>
                <div class="stat-label">' . htmlspecialchars($stat['stat_label']) . '</div>
            </div>';
        }
        
        return $html;
    }
    
    /**
     * Get service icon from icon class
     */
    private function getServiceIcon($iconClass) {
        $iconMap = [
            'fas fa-user-tie' => '👔',
            'fas fa-users-cog' => '⚙️',
            'fas fa-passport' => '🛂',
            'fas fa-shield-alt' => '🛡️'
        ];
        
        return $iconMap[$iconClass] ?? '💼';
    }
    
    /**
     * Get industry icon from icon class
     */
    private function getIndustryIcon($iconClass) {
        $iconMap = [
            'fas fa-hard-hat' => '🏗️',
            'fas fa-concierge-bell' => '🏨',
            'fas fa-user-md' => '🏥',
            'fas fa-laptop-code' => '💻'
        ];
        
        return $iconMap[$iconClass] ?? '🏢';
    }
    
    /**
     * Get fallback clients HTML
     */
    private function getFallbackClientsHTML() {
        return '
        <div class="client-card hover:shadow-lg hover:border-blue-300 transition-all duration-300">
            <div class="client-logo">LC</div>
            <div class="client-name">Leading Construction Company</div>
            <div class="client-category">Construction & Engineering</div>
        </div>
        <div class="client-card hover:shadow-lg hover:border-blue-300 transition-all duration-300">
            <div class="client-logo">LR</div>
            <div class="client-name">Luxury Resort & Spa</div>
            <div class="client-category">Tourism & Hospitality</div>
        </div>
        <div class="client-card hover:shadow-lg hover:border-blue-300 transition-all duration-300">
            <div class="client-logo">IH</div>
            <div class="client-name">Investment Holdings Group</div>
            <div class="client-category">Investments & Trading</div>
        </div>
        <div class="client-card hover:shadow-lg hover:border-blue-300 transition-all duration-300">
            <div class="client-logo">MC</div>
            <div class="client-name">Medical Center Plus</div>
            <div class="client-category">Healthcare Services</div>
        </div>';
    }
    
    /**
     * Get fallback services HTML
     */
    private function getFallbackServicesHTML() {
        return '
        <div class="service-card hover:shadow-lg transition-shadow duration-300">
            <div class="service-icon">👔</div>
            <h3 class="service-title">Recruitment Services</h3>
            <p class="service-description">Source and screen candidates across multiple sectors with our comprehensive recruitment solutions.</p>
        </div>
        <div class="service-card hover:shadow-lg transition-shadow duration-300">
            <div class="service-icon">⚙️</div>
            <h3 class="service-title">HR Support Services</h3>
            <p class="service-description">Comprehensive post-recruitment support and compliance management for your workforce.</p>
        </div>
        <div class="service-card hover:shadow-lg transition-shadow duration-300">
            <div class="service-icon">🛂</div>
            <h3 class="service-title">Permits & Visa Processing</h3>
            <p class="service-description">Government approvals for legal expatriate employment with streamlined processing.</p>
        </div>
        <div class="service-card hover:shadow-lg transition-shadow duration-300">
            <div class="service-icon">🛡️</div>
            <h3 class="service-title">Insurance Services</h3>
            <p class="service-description">Comprehensive insurance coverage for expatriate employees and their families.</p>
        </div>';
    }
    
    /**
     * Get fallback industries HTML
     */
    private function getFallbackIndustriesHTML() {
        return '
        <div class="industry-card hover:shadow-md hover:scale-105 transition-all duration-300">
            <div class="industry-icon">🏗️</div>
            <div class="industry-name">Construction & Engineering</div>
            <div class="industry-description">Major construction and infrastructure projects</div>
        </div>
        <div class="industry-card hover:shadow-md hover:scale-105 transition-all duration-300">
            <div class="industry-icon">🏨</div>
            <div class="industry-name">Tourism & Hospitality</div>
            <div class="industry-description">Leading resorts and hotels</div>
        </div>
        <div class="industry-card hover:shadow-md hover:scale-105 transition-all duration-300">
            <div class="industry-icon">🏥</div>
            <div class="industry-name">Healthcare Services</div>
            <div class="industry-description">Hospitals, clinics, and medical facilities</div>
        </div>
        <div class="industry-card hover:shadow-md hover:scale-105 transition-all duration-300">
            <div class="industry-icon">💻</div>
            <div class="industry-name">Professional Services</div>
            <div class="industry-description">IT, finance, administration, and consultancy</div>
        </div>';
    }
    
    /**
     * Get fallback company info HTML
     */
    private function getFallbackCompanyInfoHTML() {
        return '
        <div class="about-content">
            <h2 class="about-title">About Us</h2>
            <p class="about-description">Sky Border Solution Pvt Ltd is a government-licensed HR consultancy and recruitment firm headquartered in the Republic of Maldives.</p>
        </div>';
    }
    
    /**
     * Get fallback stats HTML
     */
    private function getFallbackStatsHTML() {
        return '
        <div class="stat-item hover:shadow-lg hover:scale-105 transition-all duration-300">
            <div class="stat-value">1000+</div>
            <div class="stat-label">Successful Placements</div>
        </div>
        <div class="stat-item hover:shadow-lg hover:scale-105 transition-all duration-300">
            <div class="stat-value">50+</div>
            <div class="stat-label">Partner Companies</div>
        </div>
        <div class="stat-item hover:shadow-lg hover:scale-105 transition-all duration-300">
            <div class="stat-value">100%</div>
            <div class="stat-label">Licensed & Compliant</div>
        </div>';
    }
    
    /**
     * Get a fallback template if the main template file is missing
     */
    private function getFallbackTemplate() {
        $domain = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $baseUrl = $protocol . '://' . $domain;
        
        return '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Sky Border Solutions</title>
        </head>
        <body style="font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f5f5f5;">
            <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <div style="text-align: center; margin-bottom: 30px;">
                    <h1 style="color: #1e40af; margin-bottom: 10px;">Sky Border Solutions</h1>
                    <p style="color: #666; font-size: 18px;">Your Trusted Partner in Business Growth</p>
                </div>
                
                <div style="background: linear-gradient(135deg, #1e40af, #166534); color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px;">
                    <h2 style="margin-bottom: 20px;">Welcome to Sky Border Solutions</h2>
                    <p style="font-size: 16px; line-height: 1.6;">We specialize in helping businesses achieve their growth objectives through strategic consulting, process optimization, and innovative solutions.</p>
                </div>
                
                <div style="margin-bottom: 30px;">
                    <h3 style="color: #1e40af; margin-bottom: 15px;">Our Services</h3>
                    <ul style="color: #666; line-height: 1.8;">
                        <li>Business Strategy & Planning</li>
                        <li>Process Optimization</li>
                        <li>Growth Consulting</li>
                        <li>Market Entry Strategies</li>
                        <li>Performance Improvement</li>
                    </ul>
                </div>
                
                <div style="text-align: center; margin-top: 30px;">
                    <a href="' . $baseUrl . '" style="background: linear-gradient(135deg, #1-40af, #166534); color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block;">Visit Our Website</a>
                </div>
                
                <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; text-align: center; color: #999; font-size: 14px;">
                    <p>This email was sent to {{contact_email}}</p>
                    <p>&copy; 2025 Sky Border Solutions. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>';
    }
    
    /**
     * Convert website HTML to email-friendly HTML
     */
    private function convertToEmailHTML($html, $baseUrl) {
        // Remove only JavaScript (keep CSS for styling)
        $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $html);
        
        // Convert relative URLs to absolute
        $html = preg_replace('/src="\/([^"]*)"/', 'src="' . $baseUrl . '/$1"', $html);
        $html = preg_replace('/href="\/([^"]*)"/', 'href="' . $baseUrl . '/$1"', $html);
        
        // Convert CSS background images
        $html = preg_replace('/url\(\/([^)]*)\)/', 'url(' . $baseUrl . '/$1)', $html);
        
        // Remove external CSS links and style tags (we'll convert Tailwind to inline)
        $html = preg_replace('/<link[^>]*rel=["\']stylesheet["\'][^>]*>/i', '', $html);
        $html = preg_replace('/<style[^>]*>.*?<\/style>/si', '', $html);
        
        // Convert Tailwind classes to inline styles BEFORE extracting body content
        $html = $this->convertTailwindToInlineStyles($html);
        
        // Add email-specific CSS
        $emailCSS = '
        <style>
            /* Email-specific overrides for better compatibility */
            body { margin: 0; padding: 0; font-family: Arial, sans-serif; }
            .email-container { max-width: 100%; margin: 0; padding: 0; }
            .email-content { width: 100%; max-width: 100%; }
            /* Ensure images scale properly */
            img { max-width: 100%; height: auto; }
            /* Fix button styling for email clients */
            .btn, button { display: inline-block; text-decoration: none; }
            /* Ensure proper spacing */
            * { box-sizing: border-box; }
            /* Preserve gradients and colors */
            .bg-gradient-to-r { background: linear-gradient(to right, var(--gradient-colors)) !important; }
            /* Additional email client compatibility */
            .floating-element { animation: none !important; }
            .scroll-reveal { animation: none !important; }
            .animate-float { animation: none !important; }
            .animate-pulse { animation: none !important; }
            /* Ensure proper text rendering */
            h1, h2, h3, h4, h5, h6 { margin: 0.5em 0; }
            p { margin: 0.5em 0; }
            /* Fix for Outlook */
            table { border-collapse: collapse; }
            td { vertical-align: top; }
        </style>';
        
        // Extract body content
        if (preg_match('/<body[^>]*>(.*?)<\/body>/si', $html, $matches)) {
            $bodyContent = $matches[1];
        } else {
            $bodyContent = $html;
        }
        
        // Clean up any remaining problematic classes or attributes
        $bodyContent = $this->cleanEmailContent($bodyContent);
        
        // Create email template with proper encoding
        $emailHtml = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <title>Sky Border Solutions</title>
            <style>
                /* Email-specific overrides for better compatibility */
                body { 
                    margin: 0; 
                    padding: 0; 
                    font-family: Arial, Helvetica, sans-serif; 
                    font-size: 14px;
                    line-height: 1.6;
                    color: #333333;
                    background-color: #ffffff;
                }
                .email-container { 
                    max-width: 100%; 
                    margin: 0; 
                    padding: 0; 
                }
                .email-content { 
                    width: 100%; 
                    max-width: 100%; 
                }
                /* Ensure images scale properly */
                img { 
                    max-width: 100%; 
                    height: auto; 
                    border: 0;
                }
                /* Fix button styling for email clients */
                .btn, button { 
                    display: inline-block; 
                    text-decoration: none; 
                    padding: 10px 20px;
                    border-radius: 5px;
                    border: none;
                    cursor: pointer;
                }
                /* Ensure proper spacing */
                * { 
                    box-sizing: border-box; 
                }
                /* Additional email client compatibility */
                .floating-element, .scroll-reveal, .animate-float, .animate-pulse { 
                    animation: none !important; 
                }
                /* Ensure proper text rendering */
                h1, h2, h3, h4, h5, h6 { 
                    margin: 0.5em 0; 
                    line-height: 1.2;
                }
                p { 
                    margin: 0.5em 0; 
                }
                /* Fix for Outlook */
                table { 
                    border-collapse: collapse; 
                    mso-table-lspace: 0pt;
                    mso-table-rspace: 0pt;
                }
                td { 
                    vertical-align: top; 
                }
                /* Ensure links are visible */
                a { 
                    color: #2563eb; 
                    text-decoration: underline;
                }
                /* Fix for dark mode emails */
                @media (prefers-color-scheme: dark) {
                    body { 
                        background-color: #1f2937; 
                        color: #f9fafb; 
                    }
                }
            </style>
        </head>
        <body>
            <div class="email-container">
                <div class="email-content">
                    ' . $bodyContent . '
                </div>
                <div class="email-footer" style="text-align: center; padding: 20px; background: #f8f9fa; border-top: 1px solid #dee2e6; margin-top: 30px;">
                    <p style="margin: 0 0 10px 0; color: #6c757d; font-size: 14px;">This email was sent by Sky Border Solutions</p>
                    <p style="margin: 0 10px 0; font-size: 12px;">
                        <a href="' . $baseUrl . '/unsubscribe?email={EMAIL}&campaign={CAMPAIGN_ID}" style="color: #6c757d; text-decoration: none;">Unsubscribe</a>
                    </p>
                    <div style="width: 1px; height: 1px; opacity: 0;">
                        <img src="' . $baseUrl . '/track-email.php?type=open&email={EMAIL}&campaign={CAMPAIGN_ID}" width="1" height="1">
                    </div>
                </div>
            </div>
        </body>
        </html>';
        
        return $emailHtml;
    }
    
    /**
     * Convert Tailwind CSS classes to inline styles for email compatibility
     */
    private function convertTailwindToInlineStyles($html) {
        // Common Tailwind to inline style mappings
        $tailwindMap = [
            // Layout
            'container' => 'max-width: 1200px; margin: 0 auto; padding: 0 1rem;',
            'mx-auto' => 'margin-left: auto; margin-right: auto;',
            'px-4' => 'padding-left: 1rem; padding-right: 1rem;',
            'px-8' => 'padding-left: 2rem; padding-right: 2rem;',
            'py-8' => 'padding-top: 2rem; padding-bottom: 2rem;',
            'py-4' => 'padding-top: 1rem; padding-bottom: 1rem;',
            'py-6' => 'padding-top: 1.5rem; padding-bottom: 1.5rem;',
            'px-6' => 'padding-left: 1.5rem; padding-right: 1.5rem;',
            'mb-8' => 'margin-bottom: 2rem;',
            'mt-8' => 'margin-top: 2rem;',
            'mb-4' => 'margin-bottom: 1rem;',
            'mt-4' => 'margin-top: 1rem;',
            'mb-6' => 'margin-bottom: 1.5rem;',
            'mt-6' => 'margin-top: 1.5rem;',
            
            // Colors
            'bg-white' => 'background-color: #ffffff;',
            'bg-slate-900' => 'background-color: #0f172a;',
            'bg-slate-800' => 'background-color: #1e293b;',
            'bg-slate-700' => 'background-color: #334155;',
            'bg-slate-600' => 'background-color: #475569;',
            'bg-slate-500' => 'background-color: #64748b;',
            'bg-slate-400' => 'background-color: #94a3b8;',
            'bg-slate-300' => 'background-color: #cbd5e1;',
            'bg-slate-200' => 'background-color: #e2e8f0;',
            'bg-slate-100' => 'background-color: #f1f5f9;',
            'bg-slate-50' => 'background-color: #f8fafc;',
            
            'text-white' => 'color: #ffffff;',
            'text-slate-900' => 'color: #0f172a;',
            'text-slate-800' => 'color: #1e293b;',
            'text-slate-700' => 'color: #334155;',
            'text-slate-600' => 'color: #475569;',
            'text-slate-500' => 'color: #64748b;',
            'text-slate-400' => 'color: #94a3b8;',
            'text-slate-300' => 'color: #cbd5e1;',
            'text-slate-200' => 'color: #e2e8f0;',
            'text-slate-100' => 'color: #f1f5f9;',
            
            // Blue colors
            'bg-blue-800' => 'background-color: #1e40af;',
            'bg-blue-900' => 'background-color: #1e3a8a;',
            'text-blue-600' => 'color: #2563eb;',
            'text-blue-700' => 'color: #1d4ed8;',
            'text-blue-800' => 'color: #1e40af;',
            
            // Green colors
            'bg-green-700' => 'background-color: #15803d;',
            'bg-green-800' => 'background-color: #166534;',
            'text-green-600' => 'color: #16a34a;',
            'text-green-700' => 'color: #15803d;',
            
            // Gradients - convert to solid colors for email compatibility
            'bg-gradient-to-r' => 'background: linear-gradient(to right, #1e40af, #166534);',
            'from-blue-800' => 'background-color: #1e40af;',
            'to-blue-900' => 'background-color: #1e3a8a;',
            'from-green-700' => 'background-color: #15803d;',
            'to-green-800' => 'background-color: #166534;',
            'from-blue-600' => 'background-color: #2563eb;',
            'to-indigo-600' => 'background-color: #4f46e5;',
            'via-indigo-600' => 'background-color: #4f46e5;',
            'to-purple-600' => 'background-color: #9333ea;',
            
            // Typography
            'text-4xl' => 'font-size: 2.25rem; line-height: 2.5rem;',
            'text-3xl' => 'font-size: 1.875rem; line-height: 2.25rem;',
            'text-2xl' => 'font-size: 1.5rem; line-height: 2rem;',
            'text-xl' => 'font-size: 1.25rem; line-height: 1.75rem;',
            'text-lg' => 'font-size: 1.125rem; line-height: 1.75rem;',
            'text-base' => 'font-size: 1rem; line-height: 1.5rem;',
            'text-sm' => 'font-size: 0.875rem; line-height: 1.25rem;',
            'text-xs' => 'font-size: 0.75rem; line-height: 1rem;',
            'font-bold' => 'font-weight: 700;',
            'font-semibold' => 'font-weight: 600;',
            'font-medium' => 'font-weight: 500;',
            'text-center' => 'text-align: center;',
            'text-left' => 'text-align: left;',
            'text-right' => 'text-align: right;',
            
            // Spacing
            'p-6' => 'padding: 1.5rem;',
            'p-4' => 'padding: 1rem;',
            'p-2' => 'padding: 0.5rem;',
            'm-4' => 'margin: 1rem;',
            'm-2' => 'margin: 0.5rem;',
            'rounded-lg' => 'border-radius: 0.5rem;',
            'rounded-2xl' => 'border-radius: 1rem;',
            'rounded-xl' => 'border-radius: 0.75rem;',
            'rounded-md' => 'border-radius: 0.375rem;',
            'rounded' => 'border-radius: 0.25rem;',
            
            // Flexbox
            'flex' => 'display: flex;',
            'flex-col' => 'flex-direction: column;',
            'flex-row' => 'flex-direction: row;',
            'items-center' => 'align-items: center;',
            'items-start' => 'align-items: flex-start;',
            'items-end' => 'align-items: flex-end;',
            'justify-center' => 'justify-content: center;',
            'justify-between' => 'justify-content: space-between;',
            'justify-start' => 'justify-content: flex-start;',
            'justify-end' => 'justify-content: flex-end;',
            'justify-around' => 'justify-content: space-around;',
            'justify-evenly' => 'justify-content: space-evenly;',
            
            // Grid
            'grid' => 'display: grid;',
            'grid-cols-1' => 'grid-template-columns: repeat(1, minmax(0, 1fr));',
            'grid-cols-2' => 'grid-template-columns: repeat(2, minmax(0, 1fr));',
            'grid-cols-3' => 'grid-template-columns: repeat(3, minmax(0, 1fr));',
            'gap-4' => 'gap: 1rem;',
            'gap-6' => 'gap: 1.5rem;',
            'gap-8' => 'gap: 2rem;',
            
            // Shadows
            'shadow-lg' => 'box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);',
            'shadow-xl' => 'box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);',
            'shadow-md' => 'box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);',
            'shadow-sm' => 'box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);',
            
            // Responsive
            'sm:' => '',
            'md:' => '',
            'lg:' => '',
            'xl:' => '',
            '2xl:' => '',
            
            // Hover states (convert to regular styles for email)
            'hover:' => '',
            'focus:' => '',
            'active:' => '',
            'group-hover:' => '',
        ];
        
        // Apply the mappings
        foreach ($tailwindMap as $class => $style) {
            $html = str_replace('class="' . $class . '"', 'style="' . $style . '"', $html);
            $html = str_replace('class="' . $class . ' ', 'style="' . $style . ' ', $html);
            $html = str_replace(' ' . $class . '"', ' ' . $style . '"', $html);
        }
        
        // Handle complex gradient combinations
        $html = preg_replace('/class="([^"]*bg-gradient-to-r[^"]*from-blue-800[^"]*to-green-800[^"]*)"/', 'style="background: linear-gradient(to right, #1e40af, #166534);"', $html);
        $html = preg_replace('/class="([^"]*bg-gradient-to-r[^"]*from-blue-600[^"]*to-indigo-600[^"]*)"/', 'style="background: linear-gradient(to right, #2563eb, #4f46e5);"', $html);
        $html = preg_replace('/class="([^"]*bg-gradient-to-r[^"]*from-green-700[^"]*to-green-800[^"]*)"/', 'style="background: linear-gradient(to right, #15803d, #166534);"', $html);
        
        return $html;
    }
    
    /**
     * Create a new campaign
     */
    public function createCampaign($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO campaigns (name, subject, url_to_render, status, smtp_config_id, scheduled_at, rendered_html)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $data['name'],
            $data['subject'],
            $data['url_to_render'],
            $data['status'],
            $data['smtp_config_id'],
            $data['scheduled_at'],
            $data['rendered_html'] ?? null
        ]);
        
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Update campaign
     */
    public function updateCampaign($id, $data) {
        $stmt = $this->pdo->prepare("
            UPDATE campaigns SET 
                name = ?, subject = ?, url_to_render = ?, status = ?, 
                smtp_config_id = ?, scheduled_at = ?, rendered_html = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['name'], $data['subject'], $data['url_to_render'], $data['status'],
            $data['smtp_config_id'], $data['scheduled_at'], $data['rendered_html'],
            $id
        ]);
    }
    
    /**
     * Get campaign by ID
     */
    public function getCampaign($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM campaigns WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all campaigns
     */
    public function getAllCampaigns() {
        $stmt = $this->pdo->prepare("
            SELECT c.*, 
                   COUNT(cr.id) as total_recipients,
                   COUNT(CASE WHEN cr.status = 'sent' THEN 1 END) as sent_count,
                   COUNT(CASE WHEN cr.status = 'delivered' THEN 1 END) as delivered_count,
                   COUNT(CASE WHEN cr.status = 'opened' THEN 1 END) as opened_count,
                   COUNT(CASE WHEN cr.status = 'clicked' THEN 1 END) as clicked_count
            FROM campaigns c
            LEFT JOIN campaign_recipients cr ON c.id = cr.campaign_id
            GROUP BY c.id
            ORDER BY c.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Add recipients to campaign
     */
    public function addCampaignRecipients($campaignId, $contactIds, $listId = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO campaign_recipients (campaign_id, contact_id, list_id, email, tracking_id)
            SELECT ?, c.id, ?, c.email, CONCAT(?, '_', c.id, '_', UNIX_TIMESTAMP())
            FROM contacts c
            WHERE c.id IN (" . str_repeat('?,', count($contactIds) - 1) . "?)
            AND c.status = 'active'
        ");
        
        $params = array_merge([$campaignId, $listId, $campaignId], $contactIds);
        return $stmt->execute($params);
    }
    
    /**
     * Test campaign by sending to admin email
     */
    public function testCampaign($campaignId) {
        try {
            $campaign = $this->getCampaign($campaignId);
            if (!$campaign) {
                return ['success' => false, 'error' => 'Campaign not found'];
            }
            
            // Get SMTP config
            $smtpConfig = $this->getSMTPConfig();
            if (!$smtpConfig) {
                return ['success' => false, 'error' => 'No active SMTP configuration found'];
            }
            
            // Render the website as email
            $renderedHtml = $this->renderWebsiteAsEmail('/');
            
            // Send test email to admin using PHPMailer
            try {
                // Check if PHPMailer is available via Composer
                if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                } 
                // Check if PHPMailer is available in local classes directory
                elseif (class_exists('PHPMailer')) {
                    $mail = new PHPMailer(true);
                } else {
                    return ['success' => false, 'error' => 'PHPMailer not found. Please install it first.'];
                }
                
                // Server settings
                $mail->isSMTP();
                $mail->Host = $smtpConfig['host'];
                $mail->SMTPAuth = true;
                $mail->Username = $smtpConfig['username'];
                $mail->Password = $smtpConfig['password'];
                $mail->SMTPSecure = $smtpConfig['encryption'];
                $mail->Port = $smtpConfig['port'];
                
                // Recipients
                $mail->setFrom($smtpConfig['from_email'], $smtpConfig['from_name']);
                $mail->addAddress($smtpConfig['username']); // Send to admin email
                
                // Content
                $mail->isHTML(true);
                $mail->Subject = '[TEST] ' . $campaign['subject'];
                $mail->Body = $renderedHtml;
                
                $mail->send();
                
                return ['success' => true, 'message' => 'Test campaign sent successfully to ' . $smtpConfig['username']];
                
            } catch (Exception $e) {
                return ['success' => false, 'error' => 'Email could not be sent. Mailer Error: ' . $e->getMessage()];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send test email to any email address
     */
    public function sendTestEmail($email, $htmlContent, $subject = 'Test Email') {
        try {
            // Get SMTP config
            $smtpConfig = $this->getSMTPConfig();
            if (!$smtpConfig) {
                return ['success' => false, 'error' => 'No active SMTP configuration found'];
            }
            
            // Send test email using PHPMailer
            try {
                // Check if PHPMailer is available via Composer
                if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                } 
                // Check if PHPMailer is available in local classes directory
                elseif (class_exists('PHPMailer')) {
                    $mail = new PHPMailer(true);
                } else {
                    return ['success' => false, 'error' => 'PHPMailer not found. Please install it first.'];
                }
                
                // Server settings
                $mail->isSMTP();
                $mail->Host = $smtpConfig['host'];
                $mail->SMTPAuth = true;
                $mail->Username = $smtpConfig['username'];
                $mail->Password = $smtpConfig['password'];
                $mail->SMTPSecure = $smtpConfig['encryption'];
                $mail->Port = $smtpConfig['port'];
                
                // Enhanced debugging and authentication for sendTestEmail method
                $mail->SMTPDebug = 0; // Set to 2 for detailed debugging
                $mail->Debugoutput = 'error_log';
                
                // Try different authentication methods
                $mail->AuthType = 'LOGIN'; // Try LOGIN instead of default
                
                // Set timeout values
                $mail->Timeout = 30;
                $mail->SMTPKeepAlive = true;
                
                // Recipients
                $mail->setFrom($smtpConfig['from_email'], $smtpConfig['from_name']);
                $mail->addAddress($email);
                
                // Content
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body = $htmlContent;
                
                $mail->send();
                
                return ['success' => true, 'message' => 'Test email sent successfully to ' . $email];
                
            } catch (Exception $e) {
                return ['success' => false, 'error' => 'Email could not be sent. Mailer Error: ' . $e->getMessage()];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Send campaign emails
     */
    public function sendCampaign($campaignId, $batchSize = 10) {
        $campaign = $this->getCampaign($campaignId);
        if (!$campaign) {
            throw new Exception("Campaign not found");
        }
        
        // Get pending recipients
        $stmt = $this->pdo->prepare("
            SELECT cr.*, c.email, c.name
            FROM campaign_recipients cr
            JOIN contacts c ON cr.contact_id = c.id
            WHERE cr.campaign_id = ? AND cr.status = 'pending'
            LIMIT ?
        ");
        $stmt->execute([$campaignId, $batchSize]);
        $recipients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $sentCount = 0;
        $errors = [];
        
        foreach ($recipients as $recipient) {
            try {
                $success = $this->sendSingleEmail($campaign, $recipient);
                if ($success) {
                    $this->updateRecipientStatus($recipient['id'], 'sent');
                    $this->logEmailEvent($campaignId, $recipient['id'], 'sent');
                    $sentCount++;
                } else {
                    $this->updateRecipientStatus($recipient['id'], 'failed', 'Failed to send email');
                    $this->logEmailEvent($campaignId, $recipient['id'], 'failed');
                    $errors[] = "Failed to send to {$recipient['email']}";
                }
            } catch (Exception $e) {
                $this->updateRecipientStatus($recipient['id'], 'failed', $e->getMessage());
                $this->logEmailEvent($campaignId, $recipient['id'], 'failed');
                $errors[] = "Error sending to {$recipient['email']}: " . $e->getMessage();
            }
        }
        
        // Update campaign status if all emails sent
        if ($sentCount > 0) {
            $this->updateCampaignStatus($campaignId, 'sent');
        }
        
        return [
            'sent' => $sentCount,
            'errors' => $errors
        ];
    }
    
    /**
     * Send single email
     */
    private function sendSingleEmail($campaign, $recipient) {
        if (!$this->smtpConfig) {
            throw new Exception("No SMTP configuration found");
        }
        
        // Prepare email content
        $emailHtml = $this->prepareEmailContent($campaign, $recipient);
        
        // Use PHPMailer or similar for SMTP sending
        // For now, we'll simulate sending
        return $this->sendViaSMTP($recipient['email'], $campaign['subject'], $emailHtml);
    }
    
    /**
     * Prepare email content with personalization
     */
    private function prepareEmailContent($campaign, $recipient) {
        $html = $campaign['rendered_html'];
        
        // Replace placeholders
        $html = str_replace('{EMAIL}', $recipient['email'], $html);
        $html = str_replace('{NAME}', $recipient['name'], $html);
        $html = str_replace('{CAMPAIGN_ID}', $campaign['id'], $html);
        
        return $html;
    }
    
    /**
     * Send email via SMTP
     */
    private function sendViaSMTP($to, $subject, $html) {
        // This is a simplified version - in production, use PHPMailer
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . $this->smtpConfig['from_name'] . ' <' . $this->smtpConfig['from_email'] . '>',
            'Reply-To: ' . $this->smtpConfig['from_email'],
            'X-Mailer: Sky Border Mailer'
        ];
        
        // For now, simulate successful sending
        // In production, implement actual SMTP sending here
        return mail($to, $subject, $html, implode("\r\n", $headers));
    }
    
    /**
     * Update recipient status
     */
    private function updateRecipientStatus($recipientId, $status, $errorMessage = null) {
        $stmt = $this->pdo->prepare("
            UPDATE campaign_recipients SET 
                status = ?, 
                " . ($status === 'sent' ? 'sent_at = NOW()' : '') . "
                " . ($status === 'delivered' ? 'delivered_at = NOW()' : '') . "
                " . ($status === 'opened' ? 'opened_at = NOW()' : '') . "
                " . ($status === 'clicked' ? 'clicked_at = NOW()' : '') . "
                " . ($status === 'bounced' ? 'bounced_at = NOW()' : '') . "
                " . ($status === 'unsubscribed' ? 'unsubscribed_at = NOW()' : '') . "
                error_message = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([$status, $errorMessage, $recipientId]);
    }
    
    /**
     * Update campaign status
     */
    private function updateCampaignStatus($campaignId, $status) {
        $stmt = $this->pdo->prepare("
            UPDATE campaigns SET 
                status = ?, 
                " . ($status === 'sent' ? 'sent_at = NOW()' : '') . "
            WHERE id = ?
        ");
        
        return $stmt->execute([$status, $campaignId]);
    }
    
    /**
     * Log email event
     */
    private function logEmailEvent($campaignId, $recipientId, $eventType, $eventData = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO email_events (campaign_id, recipient_id, event_type, event_data, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $campaignId,
            $recipientId,
            $eventType,
            $eventData ? json_encode($eventData) : null,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }
    
    /**
     * Track email open
     */
    public function trackEmailOpen($trackingId) {
        $stmt = $this->pdo->prepare("
            SELECT cr.*, c.id as campaign_id
            FROM campaign_recipients cr
            JOIN campaigns c ON cr.campaign_id = c.id
            WHERE cr.tracking_id = ?
        ");
        $stmt->execute([$trackingId]);
        $recipient = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($recipient) {
            $this->updateRecipientStatus($recipient['id'], 'opened');
            $this->logEmailEvent($recipient['campaign_id'], $recipient['id'], 'opened');
        }
    }
    
    /**
     * Track email click
     */
    public function trackEmailClick($trackingId) {
        $stmt = $this->pdo->prepare("
            SELECT cr.*, c.id as campaign_id
            FROM campaign_recipients cr
            JOIN campaigns c ON cr.campaign_id = c.id
            WHERE cr.tracking_id = ?
        ");
        $stmt->execute([$trackingId]);
        $recipient = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($recipient) {
            $this->updateRecipientStatus($recipient['id'], 'clicked');
            $this->logEmailEvent($recipient['campaign_id'], $recipient['id'], 'clicked');
        }
    }
    
    /**
     * Get campaign analytics
     */
    public function getCampaignAnalytics($campaignId) {
        $stmt = $this->pdo->prepare("
            SELECT 
                COUNT(*) as total_recipients,
                COUNT(CASE WHEN status = 'sent' THEN 1 END) as sent,
                COUNT(CASE WHEN status = 'delivered' THEN 1 END) as delivered,
                COUNT(CASE WHEN status = 'opened' THEN 1 END) as opened,
                COUNT(CASE WHEN status = 'clicked' THEN 1 END) as clicked,
                COUNT(CASE WHEN status = 'bounced' THEN 1 END) as bounced,
                COUNT(CASE WHEN status = 'unsubscribed' THEN 1 END) as unsubscribed,
                COUNT(CASE WHEN status = 'failed' THEN 1 END) as failed
            FROM campaign_recipients
            WHERE campaign_id = ?
        ");
        $stmt->execute([$campaignId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get overall mailer statistics
     */
    public function getMailerStats() {
        $stats = [];
        
        // Total contacts
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM contacts");
        $stats['total_contacts'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Active contacts
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM contacts WHERE status = 'active'");
        $stats['active_contacts'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Total campaigns
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM campaigns");
        $stats['total_campaigns'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Total emails sent
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM campaign_recipients WHERE status IN ('sent', 'delivered', 'opened', 'clicked')");
        $stats['total_emails_sent'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Open rate
        $stmt = $this->pdo->query("
            SELECT 
                ROUND(
                    (COUNT(CASE WHEN status = 'opened' THEN 1 END) * 100.0) / 
                    COUNT(CASE WHEN status IN ('sent', 'delivered', 'opened', 'clicked') THEN 1 END), 
                    2
                ) as open_rate
            FROM campaign_recipients 
            WHERE status IN ('sent', 'delivered', 'opened', 'clicked')
        ");
        $stats['open_rate'] = $stmt->fetch(PDO::FETCH_ASSOC)['open_rate'] ?? 0;
        
        // Click rate
        $stmt = $this->pdo->query("
            SELECT 
                ROUND(
                    (COUNT(CASE WHEN status = 'clicked' THEN 1 END) * 100.0) / 
                    COUNT(CASE WHEN status IN ('sent', 'delivered', 'opened', 'clicked') THEN 1 END), 
                    2
                ) as click_rate
            FROM campaign_recipients 
            WHERE status IN ('sent', 'delivered', 'opened', 'clicked')
        ");
        $stats['click_rate'] = $stmt->fetch(PDO::FETCH_ASSOC)['click_rate'] ?? 0;
        
        return $stats;
    }
    
    /**
     * Personalize email content with contact information
     */
    public function personalizeEmail($content, $contact) {
        $personalized = $content;
        
        // Replace placeholders with contact information
        $replacements = [
            '{{contact_name}}' => $contact['name'] ?? '',
            '{{contact_email}}' => $contact['email'] ?? '',
            '{{contact_company}}' => $contact['company'] ?? '',
            '{{contact_phone}}' => $contact['phone'] ?? '',
            '{{unsubscribe_link}}' => $this->generateUnsubscribeLink($contact['id']),
            '{{current_date}}' => date('F j, Y'),
            '{{current_year}}' => date('Y')
        ];
        
        foreach ($replacements as $placeholder => $value) {
            $personalized = str_replace($placeholder, $value, $personalized);
        }
        
        return $personalized;
    }
    
    /**
     * Generate unsubscribe link for a contact
     */
    private function generateUnsubscribeLink($contactId) {
        $token = bin2hex(random_bytes(32));
        $baseUrl = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        
        return $protocol . '://' . $baseUrl . '/unsubscribe.php?token=' . $token . '&contact=' . $contactId;
    }
    
    /**
     * Log email activity
     */
    public function logEmailActivity($contactId, $subject, $status, $error = '') {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO email_activity (contact_id, subject, status, error_message, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ");
            return $stmt->execute([$contactId, $subject, $status, $error]);
        } catch (Exception $e) {
            error_log("Log email activity error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get recent email activity
     */
    public function getRecentEmailActivity($limit = 10) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT ea.*, c.email as contact_email, c.name as contact_name
                FROM email_activity ea
                JOIN contacts c ON ea.contact_id = c.id
                ORDER BY ea.created_at DESC
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get recent email activity error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get email template by ID
     */
    public function getEmailTemplate($templateId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM email_templates WHERE id = ? AND is_active = 1 LIMIT 1
            ");
            $stmt->execute([$templateId]);
            $template = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($template) {
                return $template['content'];
            }
            
            return null;
        } catch (Exception $e) {
            error_log("Get email template error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Clean up email content for better compatibility
     */
    private function cleanEmailContent($content) {
        // Remove any remaining problematic attributes
        $content = preg_replace('/\s+class="[^"]*"/', '', $content);
        $content = preg_replace('/\s+id="[^"]*"/', '', $content);
        $content = preg_replace('/\s+data-[^=]*="[^"]*"/', '', $content);
        
        // Remove any remaining Tailwind-like classes that might have been missed
        $content = preg_replace('/\s+[a-z-]+:\s*[a-z-]+/', '', $content);
        
        // Clean up empty attributes
        $content = preg_replace('/\s+[a-z-]+="\s*"/', '', $content);
        
        // Ensure proper HTML structure
        $content = preg_replace('/<div[^>]*>\s*<\/div>/', '', $content);
        
        // Fix common email client issues
        $content = str_replace('&nbsp;', ' ', $content);
        $content = str_replace('&amp;', '&', $content);
        
        return $content;
    }
}
?>
