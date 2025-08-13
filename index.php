<?php
// Include database connection for dynamic content with error handling
$databaseAvailable = false;
$contentManager = null;

// Try to include database files
try {
    if (file_exists('admin/config/database.php') && file_exists('admin/classes/ContentManager.php')) {
        require_once 'admin/config/database.php';
        require_once 'admin/classes/ContentManager.php';
        
        // Initialize content manager
        $contentManager = new ContentManager();
        $databaseAvailable = true;
    }
} catch (Exception $e) {
    $databaseAvailable = false;
}

// Get dynamic content (with fallbacks if database not available)
if ($databaseAvailable && $contentManager) {
    try {
        $companyInfo = $contentManager->getCompanyInfo();
        $stats = $contentManager->getStatistics();
        $services = $contentManager->getServiceCategories();
        $portfolioCategories = $contentManager->getPortfolioCategories();
        $clients = $contentManager->getClients();
        
        // Validate that we got the essential data
        if (empty($companyInfo) || empty($clients)) {
            error_log("Sky Border: Essential data missing from database");
            $databaseAvailable = false;
        }
    } catch (Exception $e) {
        error_log("Sky Border: Database content loading error - " . $e->getMessage());
        $databaseAvailable = false;
    }
}

// Fallback data if database is not available
if (!$databaseAvailable) {
    $companyInfo = [
        'company_name' => 'Sky Border Solutions',
        'tagline' => 'Where compliance meets competence',
        'description' => 'Leading HR consultancy and recruitment firm in the Republic of Maldives, providing end-to-end manpower solutions with excellence and integrity.',
        'about_us' => 'Sky Border Solution Pvt Ltd is a government-licensed HR consultancy and recruitment firm headquartered in the Republic of Maldives. Established in response to the rising demand for skilled foreign labor, we are strategically positioned to provide end-to-end manpower solutions. Our operations are driven by a long-term vision, well-defined mission, and a strong foundation of core values. With a seasoned leadership team that brings decades of recruitment expertise, we are adept at identifying, sourcing, and placing the most qualified talent to meet diverse organizational needs. Our consistent year-on-year growth, backed by a solid financial framework, reflects our commitment to service excellence, operational integrity, and client satisfaction. At Sky Border Solution, we are dedicated to bridging workforce gaps with professionalism, precision, and purpose.',
        'mission' => 'To foster enduring partnerships with organizations by delivering superior recruitment solutions that align with their strategic goals.',
        'vision' => 'To be the most trusted and recognized recruitment company in the Maldives, known for our professionalism, excellence and ability to deliver outstanding outcomes.',
        'phone' => '+960 4000-444',
        'hotline1' => '+960 755-9001',
        'hotline2' => '+960 911-1409',
        'email' => 'info@skybordersolutions.com',
        'address' => 'H. Dhoorihaa (5A), Kalaafaanu Hingun, Male\' City, Republic of Maldives',
        'business_hours' => 'Sunday - Thursday: 8:00 AM - 5:00 PM\nSaturday: 9:00 AM - 1:00 PM\nFriday: Closed'
    ];
    
    $stats = [
        ['stat_name' => 'placements', 'stat_value' => '1000+', 'stat_label' => 'Successful Placements'],
        ['stat_name' => 'partners', 'stat_value' => '50+', 'stat_label' => 'Partner Companies'],
        ['stat_name' => 'compliance', 'stat_value' => '100%', 'stat_label' => 'Licensed & Compliant']
    ];
    
    $services = [
        [
            'category_name' => 'Recruitment Services',
            'category_description' => 'Source and screen candidates across multiple sectors',
            'icon_class' => 'fas fa-user-tie',
            'color_theme' => 'indigo'
        ],
        [
            'category_name' => 'HR Support Services',
            'category_description' => 'Comprehensive post-recruitment support and compliance',
            'icon_class' => 'fas fa-users-cog',
            'color_theme' => 'green'
        ],
        [
            'category_name' => 'Permits & Visa Processing',
            'category_description' => 'Government approvals for legal expatriate employment',
            'icon_class' => 'fas fa-passport',
            'color_theme' => 'purple'
        ],
        [
            'category_name' => 'Insurance Services',
            'category_description' => 'Comprehensive insurance coverage for expatriate employees',
            'icon_class' => 'fas fa-shield-alt',
            'color_theme' => 'blue'
        ]
    ];
    
    $portfolioCategories = [
        [
            'category_name' => 'Construction & Engineering',
            'category_slug' => 'construction',
            'description' => 'Major construction and infrastructure projects',
            'icon_class' => 'fas fa-hard-hat',
            'total_placements' => 200
        ],
        [
            'category_name' => 'Tourism & Hospitality',
            'category_slug' => 'hospitality',
            'description' => 'Leading resorts and hotels',
            'icon_class' => 'fas fa-concierge-bell',
            'total_placements' => 150
        ],
        [
            'category_name' => 'Healthcare Services',
            'category_slug' => 'healthcare',
            'description' => 'Hospitals, clinics, and medical facilities',
            'icon_class' => 'fas fa-user-md',
            'total_placements' => 80
        ],
        [
            'category_name' => 'Professional Services',
            'category_slug' => 'professional',
            'description' => 'IT, finance, administration, and consultancy',
            'icon_class' => 'fas fa-laptop-code',
            'total_placements' => 120
        ]
    ];
    
    $clients = [
        ['client_name' => 'Leading Construction Company', 'category_name' => 'Construction & Engineering', 'logo_url' => ''],
        ['client_name' => 'Luxury Resort & Spa', 'category_name' => 'Tourism & Hospitality', 'logo_url' => ''],
        ['client_name' => 'Investment Holdings Group', 'category_name' => 'Investments, Services & Trading', 'logo_url' => '']
    ];
}

// Handle contact form submission
$contactMessage = '';
$contactError = '';

if ($_POST && isset($_POST['contact_form'])) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $company = $_POST['company'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    
    if (!empty($name) && !empty($email) && !empty($message)) {
        // Try to save to database if available
        $messageSaved = false;
        
        if ($databaseAvailable && $contentManager) {
            try {
                $messageSaved = $contentManager->addContactMessage($name, $email, $company, '', $subject, $message);
            } catch (Exception $e) {
                $messageSaved = false;
            }
        }
        
        // Always show success message (database save is optional)
        $contactMessage = 'Thank you for your message! We will get back to you soon.';
        // Clear form data
        $_POST = [];
    } else {
        $contactError = 'Please fill in all required fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    // Get SEO settings for current page
    $currentPage = 'home'; // Default to home page
    $seoSettings = [];
    
    if ($databaseAvailable && $contentManager) {
        try {
            $seoSettings = $contentManager->getSEOSettings($currentPage);
        } catch (Exception $e) {
            // Fallback to global settings
            $seoSettings = $contentManager->getSEOSettings('global');
        }
    } else {
        // Fallback to global settings
        $seoSettings = [
            'meta_title' => 'Sky Border Solutions | Professional HR Consulting & Recruitment Agency',
            'meta_description' => 'Leading HR consultancy and recruitment firm in Maldives. Government-licensed professional workforce solutions.',
            'meta_keywords' => 'HR consulting, recruitment agency, Maldives, workforce solutions, HR services, professional recruitment, talent acquisition, HR consultancy',
            'google_analytics_id' => '',
            'google_tag_manager_id' => '',
            'facebook_pixel_id' => ''
        ];
    }
    ?>
    
    <title><?php echo htmlspecialchars($seoSettings['meta_title'] ?? $companyInfo['company_name'] ?? 'Sky Border Solutions'); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($seoSettings['meta_description'] ?? $companyInfo['description'] ?? 'Leading HR consultancy and recruitment firm in Maldives. Government-licensed professional workforce solutions.'); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($seoSettings['meta_keywords'] ?? 'HR consulting, recruitment agency, Maldives, workforce solutions, HR services, professional recruitment, talent acquisition, HR consultancy'); ?>">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo htmlspecialchars($seoSettings['og_title'] ?? $seoSettings['meta_title'] ?? 'Sky Border Solutions | Professional HR Consulting & Recruitment Agency'); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($seoSettings['og_description'] ?? $seoSettings['meta_description'] ?? 'Leading HR consultancy and recruitment firm in Maldives. Government-licensed professional workforce solutions.'); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($seoSettings['og_image'] ?? '/images/logo.svg'); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($seoSettings['canonical_url'] ?? 'https://skybordersolutions.com'); ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Sky Border Solutions">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($seoSettings['twitter_title'] ?? $seoSettings['meta_title'] ?? 'Sky Border Solutions | Professional HR Consulting & Recruitment Agency'); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($seoSettings['twitter_description'] ?? $seoSettings['meta_description'] ?? 'Leading HR consultancy and recruitment firm in Maldives. Government-licensed professional workforce solutions.'); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($seoSettings['twitter_image'] ?? '/images/logo.svg'); ?>">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="<?php echo htmlspecialchars($seoSettings['canonical_url'] ?? 'https://skybordersolutions.com'); ?>">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Headless UI for interactive components -->
    <script src="https://unpkg.com/@headlessui/vue@latest/dist/headlessui.umd.js"></script>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    
    <!-- Google Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Preload critical resources -->
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="//cdn.tailwindcss.com">
    
    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
            scroll-behavior: smooth;
            background: var(--bg-light);
            color: var(--text-primary);
            font-weight: 300;
            line-height: 1.6;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-weight: 400;
            color: var(--navy-blue);
        }
        
        .text-bold {
            font-weight: 500;
        }
        
        .text-semibold {
            font-weight: 400;
        }
        
        /* Navy Blue and Olive Green Theme */
        :root {
            --navy-blue: #1e3a8a;
            --navy-blue-light: #3b82f6;
            --navy-blue-dark: #1e40af;
            --olive-green: #6b7280;
            --olive-green-light: #9ca3af;
            --olive-green-dark: #4b5563;
            --accent-gold: #f59e0b;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --bg-light: #f8fafc;
            --bg-white: #ffffff;
        }
        
        /* Minimalist Theme */
        .minimalist-bg {
            background: var(--bg-light);
        }
        
        .minimalist-card {
            background: var(--bg-white);
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .minimalist-card:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        
        .minimalist-button {
            background: var(--navy-blue);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 400;
            transition: all 0.3s ease;
        }
        
        .minimalist-button:hover {
            background: var(--navy-blue-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
        }
        
        .minimalist-button-secondary {
            background: var(--olive-green);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 400;
            transition: all 0.3s ease;
        }
        
        .minimalist-button-secondary:hover {
            background: var(--olive-green-dark);
            transform: translateY(-1px);
        }
        
        /* Navy Blue and Olive Green Gradients */
        .gradient-bg { 
            background: linear-gradient(135deg, var(--navy-blue) 0%, var(--olive-green) 100%); 
        }
        
        .gradient-text { 
            background: linear-gradient(135deg, var(--navy-blue) 0%, var(--olive-green) 100%); 
            -webkit-background-clip: text; 
            -webkit-text-fill-color: transparent; 
            background-clip: text; 
        }
        
        .gradient-text-animated {
            background: linear-gradient(45deg, var(--navy-blue), var(--olive-green), var(--accent-gold), var(--navy-blue-light), var(--navy-blue));
            background-size: 400% 400%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradient-flow 4s ease-in-out infinite;
        }
        
        /* Advanced Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInLeft {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes fadeInRight {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes scale {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(46, 134, 171, 0.3); }
            50% { box-shadow: 0 0 40px rgba(46, 134, 171, 0.6); }
        }
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        @keyframes gradient-flow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        @keyframes countUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        @keyframes progressFill {
            from {
                transform: translateX(-100%);
            }
            to {
                transform: translateX(0%);
            }
        }

        
        .animate-float { animation: float 3s ease-in-out infinite; }
        .animate-fadeInUp { animation: fadeInUp 0.8s ease-out; }
        .animate-fadeInLeft { animation: fadeInLeft 0.8s ease-out; }
        .animate-fadeInRight { animation: fadeInRight 0.8s ease-out; }
        .animate-scale { animation: scale 2s ease-in-out infinite; }
        .animate-pulse-glow { animation: pulse-glow 2s ease-in-out infinite; }
        .animate-countUp { animation: countUp 0.6s ease-out; }
        .animate-progressFill { animation: progressFill 2s ease-out; }

        
        /* Enhanced Mobile Animations */
        @media (max-width: 768px) {
            .animate-float { animation: float 2s ease-in-out infinite; }
            .animate-scale { animation: scale 1.5s ease-in-out infinite; }
            .scroll-reveal { 
                opacity: 0;
                transform: translateY(30px);
                transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            }
        }
        .animate-shimmer {
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            background-size: 200% 100%;
            animation: shimmer 2s infinite;
        }
        
        /* Transparent Hover Effects */
        .hover-lift {
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        
        .hover-lift:hover {
            transform: translateY(-15px) scale(1.05);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.2);
        }
        
        .hover-glow:hover {
            box-shadow: 0 0 50px rgba(255, 255, 255, 0.3);
        }
        
        /* Enhanced Transparent Elements */
        .glass {
            backdrop-filter: blur(40px);
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        /* Transparent Button Variants */
        .transparent-btn {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 1.5rem;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            color: #ffffff;
        }
        
        .transparent-btn:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.25);
            transform: translateY(-3px);
        }
        
        /* Transparent Card Styles */
        .modern-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(40px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            border-radius: 2rem;
        }
        
        .modern-card:hover {
            transform: translateY(-10px) scale(1.03);
            box-shadow: 0 35px 70px rgba(0, 0, 0, 0.2);
            border-color: rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.08);
        }
        
        /* Mobile-specific optimizations */
        @media (max-width: 768px) {
            .hover-lift:hover { 
                transform: translateY(-2px);
            }
            .modern-card:hover { 
                transform: translateY(-1px);
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            }
            .dark .modern-card:hover {
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
            }
            /* Touch-friendly interactive elements */
            .nav-link, .mobile-nav-link {
                min-height: 44px;
                display: flex;
                align-items: center;
            }
            /* Improved mobile spacing */
            .scroll-reveal {
                animation-delay: 0s !important;
            }
        }
        
        /* Transparent Button Styles */
        .btn-primary {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            position: relative;
            overflow: hidden;
            border-radius: 1.5rem;
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.6s;
        }
        
        .btn-primary:hover::before {
            left: 100%;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
        }
        
        /* Scroll Reveal */
        .scroll-reveal {
            opacity: 0;
            transform: translateY(50px);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .scroll-reveal.revealed {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Ensure immediate visibility for elements with animation-delay */
        .scroll-reveal.revealed[style*="animation-delay"] {
            animation: fadeInUp 0.8s ease-out forwards;
        }
        
        /* Parallax Background */
        .parallax {
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }
        
        /* Transparent Scrollbar */
        ::-webkit-scrollbar {
            width: 16px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            border: 2px solid rgba(255, 255, 255, 0.1);
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        /* Transparent Additional Styles */
        .text-shadow {
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }
        
        .blur-bg {
            backdrop-filter: blur(50px);
            background: rgba(255, 255, 255, 0.05);
        }
        
        .transparent-overlay {
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(20px);
        }
        
        .transparent-border {
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .transparent-text {
            color: rgba(255, 255, 255, 0.9);
        }
        
        .transparent-text-light {
            color: rgba(255, 255, 255, 0.7);
        }
        
        /* Ensure transparent elements are visible */
        .transparent-card {
            background: rgba(255, 255, 255, 0.08) !important;
            backdrop-filter: blur(40px) !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
        }
        
        .transparent-card-strong {
            background: rgba(255, 255, 255, 0.12) !important;
            backdrop-filter: blur(50px) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
        }
        
        /* Enhanced visibility for transparent elements */
        .transparent-bg {
            background: rgba(0, 0, 0, 0.2) !important;
            backdrop-filter: blur(30px) !important;
        }
        
        /* Poppins font optimization */
        body, h1, h2, h3, h4, h5, h6, p, span, div, a, button {
            font-family: 'Poppins', sans-serif !important;
        }
        
        /* Enhanced transparent effects */
        .transparent-theme {
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 50%, #16213e 100%);
        }
        
        /* Glowing effects for transparent elements */
        .transparent-card:hover {
            box-shadow: 0 0 30px rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.3);
        }
        
        .transparent-card-strong:hover {
            box-shadow: 0 0 40px rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.4);
        }
        
        /* Animated background */
        @keyframes gradient-shift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .transparent-theme {
            background-size: 400% 400%;
            animation: gradient-shift 15s ease infinite;
        }
        
        /* Enhanced text shadows for better readability */
        h1, h2, h3, h4, h5, h6 {
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }
        
        p, span {
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }
    </style>
    
    <script>
        // Transparent theme initialization
        (function() {
            // Add transparent theme class
            document.documentElement.classList.add('transparent-theme');
            
            // Initialize scroll reveal
            function initScrollReveal() {
                const elements = document.querySelectorAll('.scroll-reveal');
                elements.forEach((el, index) => {
                    setTimeout(() => {
                        el.classList.add('revealed');
                    }, index * 100);
                });
            }
            
            // Initialize on load
            window.addEventListener('load', initScrollReveal);
            
            // Initialize on scroll
            window.addEventListener('scroll', () => {
                const elements = document.querySelectorAll('.scroll-reveal:not(.revealed)');
                elements.forEach(el => {
                    const rect = el.getBoundingClientRect();
                    if (rect.top < window.innerHeight * 0.8) {
                        el.classList.add('revealed');
                    }
                });
            });
        })();
        
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        // Transparent Theme Colors
                        'transparent': {
                            'white': 'rgba(255, 255, 255, 0.1)',
                            'white-strong': 'rgba(255, 255, 255, 0.2)',
                            'black': 'rgba(0, 0, 0, 0.1)',
                            'black-strong': 'rgba(0, 0, 0, 0.2)'
                        },
                        'glass': {
                            'primary': 'rgba(255, 255, 255, 0.05)',
                            'secondary': 'rgba(255, 255, 255, 0.08)',
                            'strong': 'rgba(255, 255, 255, 0.15)'
                        }
                    },
                    backdropBlur: {
                        'xs': '2px',
                        'sm': '4px',
                        'md': '8px',
                        'lg': '16px',
                        'xl': '24px',
                        '2xl': '40px',
                        '3xl': '64px'
                    }
                }
            }
        }
    </script>
    
    <!-- Google Analytics -->
    <?php if (!empty($seoSettings['google_analytics_id'])): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo htmlspecialchars($seoSettings['google_analytics_id']); ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?php echo htmlspecialchars($seoSettings['google_analytics_id']); ?>');
    </script>
    <?php endif; ?>
    
    <!-- Google Tag Manager -->
    <?php if (!empty($seoSettings['google_tag_manager_id'])): ?>
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','<?php echo htmlspecialchars($seoSettings['google_tag_manager_id']); ?>');</script>
    <?php endif; ?>
    
    <!-- Facebook Pixel -->
    <?php if (!empty($seoSettings['facebook_pixel_id'])): ?>
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '<?php echo htmlspecialchars($seoSettings['facebook_pixel_id']); ?>');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id=<?php echo htmlspecialchars($seoSettings['facebook_pixel_id']); ?>&ev=PageView&noscript=1"
    /></noscript>
    <?php endif; ?>
    
    <!-- Schema Markup -->
    <?php if (!empty($seoSettings['schema_markup'])): ?>
    <script type="application/ld+json">
        <?php echo $seoSettings['schema_markup']; ?>
    </script>
    <?php endif; ?>
    
    <!-- Custom Meta Tags -->
    <?php if (!empty($seoSettings['custom_meta_tags'])): ?>
        <?php echo $seoSettings['custom_meta_tags']; ?>
    <?php endif; ?>
</head>

<body class="h-full bg-transparent theme-transition">
    <!-- Google Tag Manager (noscript) -->
    <?php if (!empty($seoSettings['google_tag_manager_id'])): ?>
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo htmlspecialchars($seoSettings['google_tag_manager_id']); ?>"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <?php endif; ?>
    
    <!-- Transparent Dark Mode Toggle -->
    <div class="fixed top-6 right-6 z-50">
        <button id="theme-toggle" class="group relative p-4 rounded-2xl transparent-card-strong shadow-2xl text-white hover:scale-110 active:scale-95 theme-transition focus:outline-none focus:ring-4 focus:ring-white/30 focus:ring-offset-2" aria-label="Toggle dark mode" title="Toggle dark/light mode">
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-white/10 to-white/20 opacity-0 group-hover:opacity-100 transition-opacity duration-400"></div>
            <i id="theme-icon" class="relative z-10 fas fa-moon text-xl transition-all duration-300" aria-hidden="true"></i>
            <i id="theme-icon-dark" class="relative z-10 fas fa-sun text-xl hidden transition-all duration-300" aria-hidden="true"></i>
        </button>
    </div>



    <!-- Minimalist Hero Section -->
    <section id="home" class="relative overflow-hidden minimalist-bg theme-transition min-h-screen flex items-center">
        <!-- Subtle Background Pattern -->
        <div class="absolute inset-0 -z-20">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-white to-green-50"></div>
            
            <!-- Subtle Accent Elements -->
            <div class="absolute top-20 left-1/4 w-96 h-96 bg-gradient-to-br from-blue-100 to-transparent rounded-full opacity-30 animate-float blur-md"></div>
            <div class="absolute top-40 right-1/4 w-80 h-80 bg-gradient-to-br from-green-100 to-transparent rounded-full opacity-25 animate-float blur-md" style="animation-delay: 2s;"></div>
            <div class="absolute -bottom-20 left-1/2 w-72 h-72 bg-gradient-to-br from-blue-50 to-green-50 rounded-full opacity-20 animate-float blur-md" style="animation-delay: 4s;"></div>
        </div>
        
        <!-- Main Content -->
        <div class="relative mx-auto max-w-7xl px-4 py-24 sm:px-6 sm:py-32 lg:px-8 lg:py-40">
            <div class="text-center">
                <!-- Main Heading -->
                <div class="scroll-reveal" style="animation-delay: 0.2s;">
                    <h1 class="mx-auto max-w-6xl text-5xl font-semibold tracking-tight text-gray-900 sm:text-6xl lg:text-7xl">
                        <span class="block">
                            <span class="gradient-text">
                        <?php echo htmlspecialchars($companyInfo['company_name'] ?? 'Sky Border Solutions'); ?>
                            </span>
                        </span>
                        <span class="mt-4 block text-2xl sm:text-3xl lg:text-4xl font-normal text-gray-600">
                            Professional Workforce Solutions
                    </span>
                </h1>
                </div>

                <!-- Tagline -->
                <div class="scroll-reveal" style="animation-delay: 0.4s;">
                    <div class="mx-auto mt-10 max-w-3xl">
                        <div class="minimalist-card p-8">
                            <p class="text-xl leading-8 text-gray-700 font-normal">
                        <span class="relative">
                                    <span class="absolute -left-8 top-0 text-gray-400 text-3xl">"</span>
                    <?php echo htmlspecialchars($companyInfo['tagline'] ?? 'Where compliance meets competence'); ?>
                                    <span class="absolute -right-8 bottom-0 text-gray-400 text-3xl">"</span>
                        </span>
                </p>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="scroll-reveal" style="animation-delay: 0.6s;">
                    <p class="mx-auto mt-8 max-w-4xl text-lg leading-relaxed text-gray-600">
                    <?php echo htmlspecialchars($companyInfo['description'] ?? 'Leading HR consultancy and recruitment firm in the Republic of Maldives, providing end-to-end manpower solutions with excellence and integrity.'); ?>
                </p>
                </div>

                <!-- Status Badges -->
                <div class="mx-auto mt-12 mb-8 flex flex-wrap justify-center gap-4 scroll-reveal" style="animation-delay: 0.7s;">
                    <div class="group relative inline-flex items-center minimalist-card px-6 py-3 text-sm font-normal text-gray-700 hover:scale-105 transition-all duration-300">
                        <i class="fas fa-certificate mr-3 text-green-600 text-lg"></i>
                        <span class="relative z-10">Government Licensed</span>
                    </div>
                    <div class="group relative inline-flex items-center minimalist-card px-6 py-3 text-sm font-normal text-gray-700 hover:scale-105 transition-all duration-300" style="animation-delay: 0.1s;">
                        <i class="fas fa-award mr-3 text-blue-600 text-lg"></i>
                        <span class="relative z-10">HR Consulting & Recruitment</span>
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div class="mt-16 flex flex-col sm:flex-row items-center justify-center gap-6 scroll-reveal" style="animation-delay: 0.8s;">
                    <a href="#contact" class="group relative inline-flex items-center justify-center px-10 py-5 text-base font-normal text-white minimalist-button rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 focus:outline-none focus:ring-4 focus:ring-blue-300 focus:ring-offset-2 w-full sm:w-auto">
                        <i class="fas fa-comments mr-3 text-lg"></i>
                        <span>Get Started Today</span>
                        <i class="fas fa-arrow-right ml-3 transition-transform group-hover:translate-x-2"></i>
                    </a>
                    <a href="#services" class="group inline-flex items-center justify-center px-10 py-5 text-base font-normal text-white minimalist-button-secondary rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 focus:outline-none focus:ring-4 focus:ring-gray-300 focus:ring-offset-2 w-full sm:w-auto">
                        <i class="fas fa-eye mr-3 transition-transform group-hover:scale-110 text-lg"></i>
                        <span>Explore Services</span>
                        <i class="fas fa-arrow-down ml-3 transition-transform group-hover:translate-y-2"></i>
                    </a>
                </div>



                <!-- Transparent Scroll Indicator -->
                <div class="mt-24">
                    <div class="flex flex-col items-center">
                        <p class="text-sm text-white/70 mb-4 font-medium">Learn more about us</p>
                        <a href="#about" class="group inline-flex items-center justify-center w-16 h-16 transparent-card rounded-2xl text-white hover:text-white/80 transition-all duration-400 hover:scale-110">
                            <i class="fas fa-chevron-down text-xl animate-bounce group-hover:translate-y-1 transition-transform duration-300"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- iOS 26 About Section -->
    <section id="about" class="relative py-32 sm:py-40 bg-gradient-to-br from-slate-50/50 via-blue-50/20 to-purple-50/20 dark:from-slate-900/50 dark:via-blue-900/10 dark:to-purple-900/10 theme-transition overflow-hidden">
        <!-- iOS 26 Background Pattern -->
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-[linear-gradient(to_right,#8881_1px,transparent_1px),linear-gradient(to_bottom,#8881_1px,transparent_1px)] bg-[size:40px_40px] [mask-image:radial-gradient(ellipse_80%_50%_at_50%_0%,#000_50%,transparent_100%)]"></div>
        </div>
        
        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="mx-auto max-w-4xl text-center scroll-reveal">
                <div class="inline-flex items-center glass-card rounded-2xl px-6 py-3 text-sm font-medium text-blue-600 dark:text-blue-400 mb-8">
                    <i class="fas fa-info-circle mr-3 text-lg"></i>
                    About Our Company
                </div>
                <h2 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-5xl lg:text-6xl">
                    About <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Sky Border Solutions</span>
                </h2>
                <?php if (!empty($companyInfo['about_us'])): ?>
                    <div class="mt-8 text-lg leading-8 text-gray-600 dark:text-gray-300 max-w-6xl mx-auto">
                        <?php echo nl2br(htmlspecialchars($companyInfo['about_us'])); ?>
                    </div>
                <?php else: ?>
                    <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                        <?php echo htmlspecialchars($companyInfo['description']); ?>
                    </p>
                <?php endif; ?>
            </div>
            
            <!-- iOS 26 Mission & Vision Cards -->
            <div class="mx-auto mt-24 max-w-6xl">
                <div class="grid grid-cols-1 gap-10 lg:grid-cols-2">
                    <!-- Mission Card -->
                    <div class="group relative glass-card-strong rounded-3xl p-10 hover:scale-105 scroll-reveal transition-all duration-500" style="animation-delay: 0.2s;">
                        <div class="relative z-10">
                            <div class="flex items-center mb-8">
                                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-500 to-purple-500 shadow-xl group-hover:shadow-2xl transition-all duration-400 group-hover:scale-110">
                                    <i class="fas fa-bullseye text-white text-2xl"></i>
                            </div>
                                <div class="ml-6">
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Our Mission</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">What drives us forward</p>
                        </div>
                            </div>
                            <p class="text-gray-700 dark:text-gray-200 leading-relaxed text-lg">
                            <?php echo htmlspecialchars($companyInfo['mission']); ?>
                        </p>
                        </div>
                    </div>
                    
                    <!-- Vision Card -->
                    <div class="group relative glass-card-strong rounded-3xl p-10 hover:scale-105 scroll-reveal transition-all duration-500" style="animation-delay: 0.4s;">
                        <div class="relative z-10">
                            <div class="flex items-center mb-8">
                                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-r from-purple-500 to-indigo-500 shadow-xl group-hover:shadow-2xl transition-all duration-400 group-hover:scale-110">
                                    <i class="fas fa-eye text-white text-2xl"></i>
                            </div>
                                <div class="ml-6">
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Our Vision</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Where we're heading</p>
                        </div>
                            </div>
                            <p class="text-gray-700 dark:text-gray-200 leading-relaxed text-lg">
                            <?php echo htmlspecialchars($companyInfo['vision']); ?>
                        </p>
                        </div>
                    </div>
                </div>
                
                <!-- iOS 26 Team Members Section -->
                <div class="mt-20 scroll-reveal" style="animation-delay: 0.5s;">
                    <div class="text-center mb-16">
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white theme-transition mb-4">Meet Our Team</h3>
                        <p class="text-lg text-gray-600 dark:text-gray-300 theme-transition">The experienced professionals behind our success</p>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                        <?php
                        // Fetch team members from database
                        if ($databaseAvailable && $contentManager) {
                            try {
                                $teamMembers = $contentManager->getAllTeamMembers();
                                // Filter only active members
                                $teamMembers = array_filter($teamMembers, function($member) {
                                    return $member['is_active'] == 1;
                                });
                            } catch (Exception $e) {
                                $teamMembers = [];
                            }
                        } else {
                            $teamMembers = [];
                        }
                        
                        if (!empty($teamMembers)):
                            foreach ($teamMembers as $index => $member): ?>
                        <div class="text-center scroll-reveal group" style="animation-delay: <?php echo 0.6 + ($index * 0.1); ?>s;">
                            <div class="glass-card rounded-3xl p-8 hover:scale-105 transition-all duration-400">
                                <div class="relative mb-8">
                                    <?php if (!empty($member['photo_url'])): ?>
                                        <img src="<?php echo htmlspecialchars($member['photo_url']); ?>" 
                                             alt="<?php echo htmlspecialchars($member['name']); ?>" 
                                             class="mx-auto h-36 w-36 rounded-full object-cover border-4 border-white/50 dark:border-gray-700/50 shadow-2xl group-hover:shadow-3xl transition-all duration-400 group-hover:scale-110">
                                    <?php else: ?>
                                        <div class="mx-auto h-36 w-36 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-center shadow-2xl group-hover:shadow-3xl transition-all duration-400 group-hover:scale-110">
                                            <i class="fas fa-user text-white text-5xl"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Hover effect overlay -->
                                    <div class="absolute inset-0 rounded-full bg-gradient-to-r from-blue-500/20 to-purple-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-400"></div>
                                </div>
                                
                                <h4 class="text-2xl font-bold text-gray-900 dark:text-white theme-transition mb-3"><?php echo htmlspecialchars($member['name']); ?></h4>
                                <p class="text-blue-600 dark:text-blue-400 font-semibold mb-4 text-lg"><?php echo htmlspecialchars($member['designation']); ?></p>
                                <p class="text-gray-600 dark:text-gray-300 text-base leading-relaxed"><?php echo htmlspecialchars($member['description']); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <div class="col-span-full text-center py-8">
                            <p class="text-gray-500 dark:text-gray-400">Team information will be available soon.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Why Choose Us -->
                <!-- iOS 26 Why Choose Us -->
                <div class="mt-20 scroll-reveal" style="animation-delay: 0.6s;">
                    <div class="text-center mb-16">
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white theme-transition mb-4">Why Choose Us?</h3>
                        <p class="text-lg text-gray-600 dark:text-gray-300 theme-transition">What sets us apart in the recruitment industry</p>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="text-center scroll-reveal glass-card rounded-2xl p-6 hover:scale-105 transition-all duration-400" style="animation-delay: 0.8s;">
                            <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-500 to-purple-500 shadow-xl hover:shadow-2xl transition-all duration-400 hover:scale-110">
                                <i class="fas fa-certificate text-white text-2xl"></i>
                            </div>
                            <h4 class="text-xl font-semibold text-gray-900 dark:text-white theme-transition mb-3">Licensed & Compliant</h4>
                            <p class="text-gray-600 dark:text-gray-300 theme-transition">Government-approved recruitment services</p>
                        </div>
                        
                        <div class="text-center scroll-reveal glass-card rounded-2xl p-6 hover:scale-105 transition-all duration-400" style="animation-delay: 1s;">
                            <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-r from-purple-500 to-indigo-500 shadow-xl hover:shadow-2xl transition-all duration-400 hover:scale-110">
                                <i class="fas fa-users text-white text-2xl"></i>
                            </div>
                            <h4 class="text-xl font-semibold text-gray-900 dark:text-white theme-transition mb-3">Expert Team</h4>
                            <p class="text-gray-600 dark:text-gray-300 theme-transition">Experienced HR professionals</p>
                        </div>
                        
                        <div class="text-center scroll-reveal glass-card rounded-2xl p-6 hover:scale-105 transition-all duration-400" style="animation-delay: 1.2s;">
                            <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-r from-indigo-500 to-blue-500 shadow-xl hover:shadow-2xl transition-all duration-400 hover:scale-110">
                                <i class="fas fa-clock text-white text-2xl"></i>
                            </div>
                            <h4 class="text-xl font-semibold text-gray-900 dark:text-white theme-transition mb-3">Fast Processing</h4>
                            <p class="text-gray-600 dark:text-gray-300 theme-transition">Quick visa and permit processing</p>
                        </div>
                        
                        <div class="text-center scroll-reveal glass-card rounded-2xl p-6 hover:scale-105 transition-all duration-400" style="animation-delay: 1.4s;">
                            <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-500 to-purple-500 shadow-xl hover:shadow-2xl transition-all duration-400 hover:scale-110">
                                <i class="fas fa-handshake text-white text-2xl"></i>
                            </div>
                            <h4 class="text-xl font-semibold text-gray-900 dark:text-white theme-transition mb-3">Trusted Partners</h4>
                            <p class="text-gray-600 dark:text-gray-300 theme-transition">Long-term relationships with clients</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-24 sm:py-32 bg-gray-50 dark:bg-gray-800 theme-transition">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="mx-auto max-w-3xl text-center mb-20 scroll-reveal">
                <h2 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-5xl theme-transition">
                    Our <span class="gradient-text-animated">Services</span>
                </h2>
                <p class="mt-6 text-xl leading-8 text-gray-600 dark:text-gray-300 theme-transition">
                    Comprehensive HR solutions tailored to your business needs across the Maldives
                </p>
                <div class="mt-8 h-1 w-24 bg-gradient-to-r from-brand-blue to-brand-teal mx-auto rounded-full"></div>
            </div>
            
            <!-- Main Services Grid -->
            <div class="grid grid-cols-1 gap-12 lg:grid-cols-2 xl:grid-cols-2 mb-20">
                
                <!-- Recruitment Services -->
                <div class="modern-card group relative overflow-hidden rounded-3xl bg-white dark:bg-gray-900 p-8 hover-lift scroll-reveal theme-transition" style="animation-delay: 0.1s;">
                    <div class="flex items-center mb-6">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-r from-brand-blue to-brand-teal group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-user-tie text-white text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white theme-transition">Recruitment Services</h3>
                            <p class="text-brand-blue dark:text-brand-blue-light font-medium">End-to-end talent acquisition</p>
                        </div>
                    </div>
                    
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-6 theme-transition">
                        Sky Border Solution offers customized recruitment solutions for employers across Maldives. We source and screen candidates ranging from professional specialists to semi-skilled laborers, based on client needs.
                    </p>
                    
                    <div class="space-y-4">
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-check-circle text-brand-green mr-3"></i>
                            Wide database and strong international links
                        </div>
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-check-circle text-brand-green mr-3"></i>
                            Timely placements and ethical recruitment practices
                        </div>
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-check-circle text-brand-green mr-3"></i>
                            Professional specialists to semi-skilled laborers
                        </div>
                    </div>
                    
                    <!-- Hover Effect -->
                    <div class="absolute inset-0 bg-gradient-to-r from-brand-blue/5 to-brand-teal/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-3xl"></div>
                </div>

                <!-- HR Support Services -->
                <div class="modern-card group relative overflow-hidden rounded-3xl bg-white dark:bg-gray-900 p-8 hover-lift scroll-reveal theme-transition" style="animation-delay: 0.2s;">
                    <div class="flex items-center mb-6">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-r from-brand-teal to-brand-green group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-users-cog text-white text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white theme-transition">HR Support Services</h3>
                            <p class="text-brand-teal font-medium">Comprehensive post-recruitment support</p>
                        </div>
                    </div>
                    
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-6 theme-transition">
                        We deliver comprehensive post-recruitment support tailored to ensure expatriate employees are onboarded efficiently and in full compliance with Maldivian labour and immigration laws.
                    </p>
                    
                    <div class="space-y-4">
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-check-circle text-brand-teal mr-3"></i>
                            Employee Documentation & Compliance
                        </div>
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-check-circle text-brand-teal mr-3"></i>
                            Medical Clearance Coordination
                        </div>
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-check-circle text-brand-teal mr-3"></i>
                            Bank Account Opening & Orientation
                        </div>
                    </div>
                    
                    <!-- Hover Effect -->
                    <div class="absolute inset-0 bg-gradient-to-r from-brand-teal/5 to-brand-green/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-3xl"></div>
                </div>

                <!-- Permits & Visa Processing -->
                <div class="modern-card group relative overflow-hidden rounded-3xl bg-white dark:bg-gray-900 p-8 hover-lift scroll-reveal theme-transition" style="animation-delay: 0.3s;">
                    <div class="flex items-center mb-6">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-r from-brand-green to-brand-blue group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-passport text-white text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white theme-transition">Permits & Visa Processing</h3>
                            <p class="text-brand-green dark:text-brand-green-light font-medium">Government approvals & compliance</p>
                        </div>
                    </div>
                    
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-6 theme-transition">
                        We oversee the full spectrum of government approvals required for legal expatriate employment, in close coordination with the Ministry of Economic Development and Maldives Immigration.
                    </p>
                    
                    <div class="space-y-4">
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-check-circle text-brand-green mr-3"></i>
                            Employment Quotas & Work Permit Applications
                        </div>
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-check-circle text-brand-green mr-3"></i>
                            Visa Endorsement & Processing
                        </div>
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-check-circle text-brand-green mr-3"></i>
                            Renewals, Transfers & Cancellations
                        </div>
                    </div>
                    
                    <!-- Hover Effect -->
                    <div class="absolute inset-0 bg-gradient-to-r from-brand-green/5 to-brand-blue/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-3xl"></div>
                </div>

                <!-- Insurance Services -->
                <div class="modern-card group relative overflow-hidden rounded-3xl bg-white dark:bg-gray-900 p-8 hover-lift scroll-reveal theme-transition" style="animation-delay: 0.4s;">
                    <div class="flex items-center mb-6">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-r from-purple-500 to-brand-blue group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-shield-alt text-white text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white theme-transition">Insurance Services</h3>
                            <p class="text-purple-600 dark:text-purple-400 font-medium">Comprehensive coverage solutions</p>
                        </div>
                    </div>
                    
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-6 theme-transition">
                        End-to-end support in securing and managing comprehensive insurance coverage for expatriate employees, in line with mandatory requirements set forth by Maldives Immigration.
                    </p>
                    
                    <div class="space-y-4">
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-check-circle text-purple-500 mr-3"></i>
                            Work Visa Medical Insurance (Mandatory)
                        </div>
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-check-circle text-purple-500 mr-3"></i>
                            Emergency care and hospitalization
                        </div>
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-check-circle text-purple-500 mr-3"></i>
                            Work-related injury and accident protection
                        </div>
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-check-circle text-purple-500 mr-3"></i>
                            Disability and death benefits
                        </div>
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-check-circle text-purple-500 mr-3"></i>
                            Repatriation support in medical emergencies
                        </div>
                    </div>

                    <!-- Insurance Providers within Insurance Services -->
                    <div class="mt-8 pt-8 border-t border-purple-200 dark:border-purple-700">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 theme-transition">
                            Our Insurance Partners
                        </h4>
                        
                        <!-- Insurance Providers Grid from Database -->
                        <div class="grid grid-cols-2 gap-4 md:grid-cols-4 lg:grid-cols-6 mb-6">
                            <?php 
                            // Get all active insurance providers from database
                            $insuranceProviders = [];
                            if ($databaseAvailable && $contentManager) {
                                try {
                                    $allProviders = $contentManager->getInsuranceProviders();
                                    // Filter only active providers and sort by display order
                                    $insuranceProviders = array_filter($allProviders, function($p) { 
                                        return isset($p['is_active']) ? $p['is_active'] == 1 : true; 
                                    });
                                    // Sort by display order, then by featured status
                                    usort($insuranceProviders, function($a, $b) {
                                        if ($a['is_featured'] != $b['is_featured']) {
                                            return $b['is_featured'] - $a['is_featured']; // Featured first
                                        }
                                        return $a['display_order'] - $b['display_order']; // Then by display order
                                    });
                                } catch (Exception $e) {
                                    error_log("Error loading insurance providers: " . $e->getMessage());
                                    $insuranceProviders = [];
                                }
                            }
                            
                            // Show providers or fallback message
                            if (!empty($insuranceProviders)): 
                                foreach ($insuranceProviders as $provider): ?>
                            <div class="group flex flex-col items-center p-3 rounded-xl bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-300 hover:scale-105 theme-transition">
                                <!-- Logo -->
                                <div class="flex justify-center mb-3">
                                    <?php 
                                    // Handle logo path - add admin/ prefix for files stored in admin directory
                                    $logoPath = '';
                                    $hasLogo = false;
                                    if (!empty($provider['logo_url'])) {
                                        // Check if it's already a full URL or starts with admin/
                                        if (filter_var($provider['logo_url'], FILTER_VALIDATE_URL) || strpos($provider['logo_url'], 'admin/') === 0) {
                                            $logoPath = $provider['logo_url'];
                                        } else {
                                            // Add admin/ prefix for relative paths
                                            $logoPath = 'admin/' . ltrim($provider['logo_url'], '/');
                                        }
                                        $hasLogo = file_exists($logoPath);
                                    }
                                    ?>
                                    <?php if ($hasLogo): ?>
                                    <div class="h-12 w-16 bg-white dark:bg-gray-700 rounded-lg flex items-center justify-center p-2 group-hover:shadow-md transition-shadow duration-200">
                                        <img src="<?php echo htmlspecialchars($logoPath); ?>" 
                                             alt="<?php echo htmlspecialchars($provider['provider_name']); ?>" 
                                             class="h-full w-full object-contain"
                                             onerror="this.style.display='none'; this.parentNode.style.display='none'; this.parentNode.nextElementSibling.style.display='flex';">
                                </div>
                                    <div class="h-12 w-12 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center group-hover:shadow-lg transition-shadow duration-200" style="display: none;">
                                        <i class="fas fa-shield-alt text-white text-lg"></i>
                            </div>
                                    <?php else: ?>
                                    <div class="h-12 w-12 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center group-hover:shadow-lg transition-shadow duration-200">
                                        <i class="fas fa-shield-alt text-white text-lg"></i>
                                </div>
                                    <?php endif; ?>
                            </div>

                                <!-- Provider Name -->
                                <p class="text-xs font-medium text-gray-600 dark:text-gray-400 text-center line-clamp-2 mb-2" title="<?php echo htmlspecialchars($provider['provider_name']); ?>">
                                    <?php echo htmlspecialchars($provider['provider_name']); ?>
                                </p>
                                
                                <!-- Featured Badge -->
                                <?php if (isset($provider['is_featured']) && $provider['is_featured']): ?>
                                <div class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400">
                                    <i class="fas fa-star mr-1 text-xs"></i>
                                    Featured
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; 
                            else: ?>
                            <!-- No providers available message -->
                            <div class="col-span-full text-center py-8">
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                                    <i class="fas fa-shield-alt text-gray-400 text-xl"></i>
                                </div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Insurance Partners</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    <?php if ($databaseAvailable): ?>
                                        We're working on partnerships with leading insurance providers to offer comprehensive coverage.
                                    <?php else: ?>
                                        Professional insurance coverage through trusted partners.
                                    <?php endif; ?>
                                </p>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Comprehensive Coverage Areas -->
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3 mt-6">
                            <!-- Medical Treatment -->
                            <div class="flex items-start space-x-3">
                                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-gradient-to-r from-blue-500 to-cyan-500 flex-shrink-0">
                                    <i class="fas fa-hospital text-white text-xs"></i>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-900 dark:text-white theme-transition">Medical Treatment</h5>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 theme-transition">Outpatient and inpatient coverage</p>
                                </div>
                            </div>

                            <!-- Emergency Care -->
                            <div class="flex items-start space-x-3">
                                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-gradient-to-r from-red-500 to-pink-500 flex-shrink-0">
                                    <i class="fas fa-ambulance text-white text-xs"></i>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-900 dark:text-white theme-transition">Emergency Care</h5>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 theme-transition">24/7 emergency care & hospitalization</p>
                                </div>
                            </div>

                            <!-- Work Injury -->
                            <div class="flex items-start space-x-3">
                                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-gradient-to-r from-orange-500 to-yellow-500 flex-shrink-0">
                                    <i class="fas fa-hard-hat text-white text-xs"></i>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-900 dark:text-white theme-transition">Work-Related Injury</h5>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 theme-transition">Accident protection coverage</p>
                                </div>
                            </div>

                            <!-- Disability Benefits -->
                            <div class="flex items-start space-x-3">
                                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-gradient-to-r from-purple-500 to-indigo-500 flex-shrink-0">
                                    <i class="fas fa-wheelchair text-white text-xs"></i>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-900 dark:text-white theme-transition">Disability Benefits</h5>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 theme-transition">Disability coverage per policy</p>
                                </div>
                            </div>

                            <!-- Death Benefits -->
                            <div class="flex items-start space-x-3">
                                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-gradient-to-r from-gray-600 to-gray-800 flex-shrink-0">
                                    <i class="fas fa-heart text-white text-xs"></i>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-900 dark:text-white theme-transition">Death Benefits</h5>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 theme-transition">Death benefit for families</p>
                                </div>
                            </div>

                            <!-- Repatriation Support -->
                            <div class="flex items-start space-x-3">
                                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-gradient-to-r from-brand-teal to-brand-blue flex-shrink-0">
                                    <i class="fas fa-plane text-white text-xs"></i>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-900 dark:text-white theme-transition">Repatriation Support</h5>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 theme-transition">Medical emergency repatriation</p>
                                </div>
                            </div>
                        </div>

                        <!-- Insurance Support Note -->
                        <div class="mt-6 p-4 bg-gradient-to-r from-purple-50 to-blue-50 dark:from-purple-900/20 dark:to-blue-900/20 rounded-xl border border-purple-200 dark:border-purple-700">
                            <div class="flex items-start space-x-3">
                                <i class="fas fa-info-circle text-purple-600 dark:text-purple-400 mt-0.5 text-sm"></i>
                                <div>
                                    <h5 class="text-sm font-medium text-purple-900 dark:text-purple-300 mb-1">Full Insurance Lifecycle Management</h5>
                                    <p class="text-xs text-purple-800 dark:text-purple-400">
                                        Our team handles the full insurance lifecycle - from policy selection and documentation to renewals and claims coordination - ensuring uninterrupted coverage and full compliance.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hover Effect -->
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-500/5 to-brand-blue/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-3xl"></div>
                </div>
            </div>


            <!-- Industries & Positions Section -->
            <div class="mt-24 scroll-reveal" style="animation-delay: 0.6s;">
                <div class="text-center mb-16">
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-4 theme-transition">
                        Industries & <span class="gradient-text">Positions We Cover</span>
                    </h3>
                    <p class="text-lg text-gray-600 dark:text-gray-300 max-w-3xl mx-auto theme-transition">
                        From professional specialists to skilled laborers across diverse sectors
                    </p>
                </div>

                <!-- Industries Grid -->
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    
                    <!-- Construction & Engineering -->
                    <div class="modern-card bg-white dark:bg-gray-900 rounded-2xl p-6 hover-lift scroll-reveal theme-transition" style="animation-delay: 0.7s;">
                        <div class="flex items-center mb-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-amber-500 to-orange-500">
                                <i class="fas fa-hard-hat text-white"></i>
                            </div>
                            <h4 class="ml-3 text-lg font-semibold text-gray-900 dark:text-white theme-transition">Construction & Engineering</h4>
                        </div>
                        <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <div class="flex flex-wrap gap-2">
                                <span class="inline-flex items-center rounded-full bg-amber-100 dark:bg-amber-900/20 px-2.5 py-0.5 text-xs font-medium text-amber-800 dark:text-amber-400">Carpenter</span>
                                <span class="inline-flex items-center rounded-full bg-amber-100 dark:bg-amber-900/20 px-2.5 py-0.5 text-xs font-medium text-amber-800 dark:text-amber-400">Mason</span>
                                <span class="inline-flex items-center rounded-full bg-amber-100 dark:bg-amber-900/20 px-2.5 py-0.5 text-xs font-medium text-amber-800 dark:text-amber-400">Electrician</span>
                                <span class="inline-flex items-center rounded-full bg-amber-100 dark:bg-amber-900/20 px-2.5 py-0.5 text-xs font-medium text-amber-800 dark:text-amber-400">Welder</span>
                                <span class="inline-flex items-center rounded-full bg-amber-100 dark:bg-amber-900/20 px-2.5 py-0.5 text-xs font-medium text-amber-800 dark:text-amber-400">Civil Engineer</span>
                                <span class="inline-flex items-center rounded-full bg-amber-100 dark:bg-amber-900/20 px-2.5 py-0.5 text-xs font-medium text-amber-800 dark:text-amber-400">Project Manager</span>
                                <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-700 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:text-gray-400">+11 more</span>
                            </div>
                        </div>
                    </div>

                    <!-- Healthcare -->
                    <div class="modern-card bg-white dark:bg-gray-900 rounded-2xl p-6 hover-lift scroll-reveal theme-transition" style="animation-delay: 0.8s;">
                        <div class="flex items-center mb-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500">
                                <i class="fas fa-user-md text-white"></i>
                            </div>
                            <h4 class="ml-3 text-lg font-semibold text-gray-900 dark:text-white theme-transition">Healthcare</h4>
                        </div>
                        <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <div class="flex flex-wrap gap-2">
                                <span class="inline-flex items-center rounded-full bg-emerald-100 dark:bg-emerald-900/20 px-2.5 py-0.5 text-xs font-medium text-emerald-800 dark:text-emerald-400">General Practitioners</span>
                                <span class="inline-flex items-center rounded-full bg-emerald-100 dark:bg-emerald-900/20 px-2.5 py-0.5 text-xs font-medium text-emerald-800 dark:text-emerald-400">Nurses</span>
                                <span class="inline-flex items-center rounded-full bg-emerald-100 dark:bg-emerald-900/20 px-2.5 py-0.5 text-xs font-medium text-emerald-800 dark:text-emerald-400">Surgeons</span>
                                <span class="inline-flex items-center rounded-full bg-emerald-100 dark:bg-emerald-900/20 px-2.5 py-0.5 text-xs font-medium text-emerald-800 dark:text-emerald-400">Pharmacists</span>
                                <span class="inline-flex items-center rounded-full bg-emerald-100 dark:bg-emerald-900/20 px-2.5 py-0.5 text-xs font-medium text-emerald-800 dark:text-emerald-400">Physiotherapists</span>
                                <span class="inline-flex items-center rounded-full bg-emerald-100 dark:bg-emerald-900/20 px-2.5 py-0.5 text-xs font-medium text-emerald-800 dark:text-emerald-400">Radiologists</span>
                                <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-700 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:text-gray-400">+12 more</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tourism & Hospitality -->
                    <div class="modern-card bg-white dark:bg-gray-900 rounded-2xl p-6 hover-lift scroll-reveal theme-transition" style="animation-delay: 0.9s;">
                        <div class="flex items-center mb-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-cyan-500">
                                <i class="fas fa-concierge-bell text-white"></i>
                            </div>
                            <h4 class="ml-3 text-lg font-semibold text-gray-900 dark:text-white theme-transition">Tourism & Hospitality</h4>
                        </div>
                        <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <div class="flex flex-wrap gap-2">
                                <span class="inline-flex items-center rounded-full bg-blue-100 dark:bg-blue-900/20 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:text-blue-400">Head Chefs</span>
                                <span class="inline-flex items-center rounded-full bg-blue-100 dark:bg-blue-900/20 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:text-blue-400">Spa Therapists</span>
                                <span class="inline-flex items-center rounded-full bg-blue-100 dark:bg-blue-900/20 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:text-blue-400">Bartenders</span>
                                <span class="inline-flex items-center rounded-full bg-blue-100 dark:bg-blue-900/20 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:text-blue-400">Resort Managers</span>
                                <span class="inline-flex items-center rounded-full bg-blue-100 dark:bg-blue-900/20 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:text-blue-400">Diving Instructors</span>
                                <span class="inline-flex items-center rounded-full bg-blue-100 dark:bg-blue-900/20 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:text-blue-400">Boat Captains</span>
                                <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-700 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:text-gray-400">+15 more</span>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Industries -->
                    <div class="modern-card bg-white dark:bg-gray-900 rounded-2xl p-6 hover-lift scroll-reveal theme-transition" style="animation-delay: 1.0s;">
                        <div class="flex items-center mb-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-violet-500 to-purple-500">
                                <i class="fas fa-briefcase text-white"></i>
                            </div>
                            <h4 class="ml-3 text-lg font-semibold text-gray-900 dark:text-white theme-transition">Administration & Office</h4>
                        </div>
                        <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <div class="flex flex-wrap gap-2">
                                <span class="inline-flex items-center rounded-full bg-violet-100 dark:bg-violet-900/20 px-2.5 py-0.5 text-xs font-medium text-violet-800 dark:text-violet-400">HR Assistants</span>
                                <span class="inline-flex items-center rounded-full bg-violet-100 dark:bg-violet-900/20 px-2.5 py-0.5 text-xs font-medium text-violet-800 dark:text-violet-400">Secretaries</span>
                                <span class="inline-flex items-center rounded-full bg-violet-100 dark:bg-violet-900/20 px-2.5 py-0.5 text-xs font-medium text-violet-800 dark:text-violet-400">Admin Officers</span>
                                <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-700 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:text-gray-400">+6 more</span>
                            </div>
                        </div>
                    </div>

                    <div class="modern-card bg-white dark:bg-gray-900 rounded-2xl p-6 hover-lift scroll-reveal theme-transition" style="animation-delay: 1.1s;">
                        <div class="flex items-center mb-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-rose-500 to-pink-500">
                                <i class="fas fa-truck text-white"></i>
                            </div>
                            <h4 class="ml-3 text-lg font-semibold text-gray-900 dark:text-white theme-transition">Transport & Logistics</h4>
                        </div>
                        <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <div class="flex flex-wrap gap-2">
                                <span class="inline-flex items-center rounded-full bg-rose-100 dark:bg-rose-900/20 px-2.5 py-0.5 text-xs font-medium text-rose-800 dark:text-rose-400">Heavy Vehicle Drivers</span>
                                <span class="inline-flex items-center rounded-full bg-rose-100 dark:bg-rose-900/20 px-2.5 py-0.5 text-xs font-medium text-rose-800 dark:text-rose-400">Warehouse Assistants</span>
                                <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-700 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:text-gray-400">+6 more</span>
                            </div>
                        </div>
                    </div>

                    <div class="modern-card bg-white dark:bg-gray-900 rounded-2xl p-6 hover-lift scroll-reveal theme-transition" style="animation-delay: 1.2s;">
                        <div class="flex items-center mb-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-indigo-500 to-blue-500">
                                <i class="fas fa-graduation-cap text-white"></i>
                            </div>
                            <h4 class="ml-3 text-lg font-semibold text-gray-900 dark:text-white theme-transition">Education & More</h4>
                        </div>
                        <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <div class="flex flex-wrap gap-2">
                                <span class="inline-flex items-center rounded-full bg-indigo-100 dark:bg-indigo-900/20 px-2.5 py-0.5 text-xs font-medium text-indigo-800 dark:text-indigo-400">Teachers</span>
                                <span class="inline-flex items-center rounded-full bg-indigo-100 dark:bg-indigo-900/20 px-2.5 py-0.5 text-xs font-medium text-indigo-800 dark:text-indigo-400">Retail & Customer Service</span>
                                <span class="inline-flex items-center rounded-full bg-indigo-100 dark:bg-indigo-900/20 px-2.5 py-0.5 text-xs font-medium text-indigo-800 dark:text-indigo-400">Facility Management</span>
                                <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-700 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:text-gray-400">Many more</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Roles Notice -->
                <div class="mt-12 text-center scroll-reveal" style="animation-delay: 1.3s;">
                    <div class="modern-card bg-gradient-to-r from-brand-blue/10 to-brand-teal/10 dark:from-brand-blue/20 dark:to-brand-teal/20 rounded-2xl p-6 max-w-2xl mx-auto">
                        <p class="text-gray-700 dark:text-gray-300 theme-transition">
                            <i class="fas fa-info-circle text-brand-blue mr-2"></i>
                            <strong>Additional roles available upon request</strong>, including both skilled and unskilled labour tailored to specific project or business needs.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Call to Action -->
            <div class="mt-20 text-center scroll-reveal" style="animation-delay: 1.4s;">
                <div class="modern-card bg-white dark:bg-gray-900 rounded-3xl p-8 max-w-4xl mx-auto">
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-4 theme-transition">Ready to Build Your Team?</h3>
                    <p class="text-xl text-gray-600 dark:text-gray-300 mb-8 theme-transition">Let's discuss how we can help you find the perfect talent for your organization</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="#contact" class="btn-primary inline-flex items-center rounded-lg px-8 py-4 text-sm font-semibold text-white shadow-xl hover-lift">
                            <i class="fas fa-phone mr-2"></i>
                            Get in Touch
                        </a>
                        <a href="https://skybordersolutions.com/profile.pdf" target="_blank" class="modern-card inline-flex items-center rounded-lg px-8 py-4 text-sm font-semibold text-gray-900 dark:text-white hover-lift">
                            <i class="fas fa-download mr-2"></i>
                            Download Company Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Portfolio Section -->
    <section id="portfolio" class="py-24 sm:py-32 bg-white dark:bg-gray-900 theme-transition">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="mx-auto max-w-2xl text-center mb-16 scroll-reveal">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl theme-transition">
                    Our <span class="gradient-text">Portfolio</span>
                </h2>
                <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-300 theme-transition">
                    Successful placements across diverse industries in the Maldives
                </p>
            </div>
            
            <!-- Portfolio Grid -->
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                <?php foreach ($portfolioCategories as $index => $category): ?>
                <div class="modern-card group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 p-8 text-center hover-lift scroll-reveal theme-transition" style="animation-delay: <?php echo $index * 0.2; ?>s;">
                    <!-- Category Icon -->
                    <div class="mb-6">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-r from-brand-blue to-brand-teal group-hover:scale-110 transition-transform duration-300 animate-scale">
                            <i class="<?php echo htmlspecialchars($category['icon_class']); ?> text-white text-2xl"></i>
                        </div>
                    </div>
                    
                    <!-- Category Content -->
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3 theme-transition">
                        <?php echo htmlspecialchars($category['category_name']); ?>
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 theme-transition">
                        <?php echo htmlspecialchars($category['description']); ?>
                    </p>
                    
                    <!-- Placement Count -->
                    <div class="mt-6">
                        <div class="gradient-border p-1 rounded-lg">
                            <div class="gradient-border-inner px-4 py-2 rounded">
                                <span class="text-2xl font-bold gradient-text"><?php echo htmlspecialchars($category['total_placements']); ?>+</span>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Successful Placements</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hover Effect -->
                    <div class="absolute inset-0 bg-gradient-to-r from-brand-blue/5 to-brand-teal/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-2xl"></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Clients Section -->
    <section id="clients" class="py-24 sm:py-32 bg-gray-50 dark:bg-gray-800 theme-transition">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="mx-auto max-w-2xl text-center mb-16 scroll-reveal">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl theme-transition">
                    Our <span class="gradient-text">Clients</span>
                </h2>
                <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-300 theme-transition">
                    Trusted by leading organizations across the Maldives
                </p>
            </div>

            <!-- Client Categories -->
            <?php 
            // Group clients by category (using real database structure)
            $groupedClients = [];
            foreach ($clients as $client) {
                $categoryName = $client['category_name'] ?? 'Other';
                if (!isset($groupedClients[$categoryName])) {
                    $groupedClients[$categoryName] = [];
                }
                $groupedClients[$categoryName][] = $client;
            }
            ?>

            <?php if (!empty($groupedClients)): ?>
            <div class="space-y-12">
                <?php foreach ($groupedClients as $categoryName => $categoryClients): ?>
                <div class="scroll-reveal" style="animation-delay: <?php echo array_search($categoryName, array_keys($groupedClients)) * 0.2; ?>s;">
                    <!-- Category Header -->
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center theme-transition">
                            <?php 
                            $categoryIcon = 'fas fa-building';
                            switch (strtolower($categoryName)) {
                                case 'construction':
                                case 'engineering':
                                case 'construction & engineering':
                                    $categoryIcon = 'fas fa-building';
                                    $categoryColor = 'text-brand-blue dark:text-brand-blue-light';
                                    break;
                                case 'tourism':
                                case 'hospitality':
                                case 'tourism & hospitality':
                                    $categoryIcon = 'fas fa-hotel';
                                    $categoryColor = 'text-brand-teal';
                                    break;
                                case 'investment':
                                case 'trading':
                                case 'services':
                                case 'investments, services & trading':
                                    $categoryIcon = 'fas fa-chart-line';
                                    $categoryColor = 'text-brand-green dark:text-brand-green-light';
                                    break;
                                default:
                                    $categoryIcon = 'fas fa-briefcase';
                                    $categoryColor = 'text-gray-600 dark:text-gray-400';
                            }
                            ?>
                            <i class="<?php echo $categoryIcon; ?> mr-3 <?php echo $categoryColor; ?>"></i>
                            <?php echo htmlspecialchars($categoryName); ?>
                        </h3>
                    </div>

                    <!-- Clients Grid -->
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                        <?php foreach ($categoryClients as $clientIndex => $client): ?>
                        <div class="modern-card group relative overflow-hidden bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 rounded-lg hover-lift theme-transition scroll-reveal" style="animation-delay: <?php echo $clientIndex * 0.1; ?>s;">
                            <div class="p-4">
                                <?php if (!empty($client['logo_url'])): ?>
                                <!-- Company Logo -->
                                <div class="flex h-16 w-full items-center justify-center mb-3 bg-gray-50 dark:bg-gray-800 rounded-md theme-transition">
                                    <?php 
                                    // Handle both uploaded files and external URLs
                                    $logoSrc = $client['logo_url'];
                                    if (!filter_var($logoSrc, FILTER_VALIDATE_URL)) {
                                        // It's a local file path, prepend admin directory
                                        $logoSrc = 'admin/' . ltrim($logoSrc, '/');
                                    }
                                    ?>
                                    <img src="<?php echo htmlspecialchars($logoSrc); ?>" 
                                         alt="<?php echo htmlspecialchars($client['client_name']); ?>" 
                                         class="h-12 w-auto object-contain"
                                         onerror="this.style.display='none'; this.parentNode.innerHTML='<i class=&quot;fas fa-building text-2xl text-gray-400 dark:text-gray-500&quot;></i>';">
                                </div>
                                <?php else: ?>
                                <!-- Placeholder for logo -->
                                <div class="flex h-16 w-full items-center justify-center mb-3 bg-gray-100 dark:bg-gray-700 rounded-md theme-transition">
                                    <i class="fas fa-building text-2xl text-gray-400 dark:text-gray-500"></i>
                                </div>
                                <?php endif; ?>

                                <!-- Company Name -->
                                <div class="text-center">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-brand-blue dark:group-hover:text-brand-blue-light transition-colors theme-transition">
                                        <?php echo htmlspecialchars($client['client_name']); ?>
                                    </h4>
                                    <?php if (!empty($client['services'])): ?>
                                    <p class="text-xs text-brand-blue dark:text-brand-blue-light font-medium mt-1">
                                        <?php echo htmlspecialchars($client['services']); ?>
                                    </p>
                                    <?php endif; ?>
                                    
                                    <!-- Service Duration Display -->
                                    <?php if (isset($client['service_duration_type'])): ?>
                                        <?php if ($client['service_duration_type'] === 'ongoing'): ?>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 mt-2">
                                                <i class="fas fa-check-circle w-3 h-3 mr-1"></i>
                                                Currently Ongoing
                                            </span>
                                        <?php elseif ($client['service_duration_type'] === 'date_range' && $client['service_start_date'] && $client['service_end_date']): ?>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 mt-2">
                                                <i class="fas fa-calendar-alt w-3 h-3 mr-1"></i>
                                                <?php echo date('M Y', strtotime($client['service_start_date'])); ?> - <?php echo date('M Y', strtotime($client['service_end_date'])); ?>
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Hover Effect -->
                            <div class="absolute inset-0 bg-gradient-to-r from-brand-blue/5 to-brand-teal/5 opacity-0 group-hover:opacity-100 transition-opacity duration-200 rounded-lg"></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <!-- Fallback when no clients in database -->
            <div class="text-center py-12 scroll-reveal">
                <div class="mx-auto max-w-md">
                    <i class="fas fa-users text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2 theme-transition">Our Growing Network</h3>
                    <p class="text-gray-600 dark:text-gray-400 theme-transition">We work with leading companies across various industries in the Maldives.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-24 sm:py-32 bg-white dark:bg-gray-900 theme-transition">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="mx-auto max-w-2xl text-center mb-16 scroll-reveal">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl theme-transition">
                    Get in <span class="gradient-text">Touch</span>
                </h2>
                <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-300 theme-transition">
                    Ready to find the perfect talent for your organization? Let's discuss your needs
                </p>
            </div>
            
            <div class="grid grid-cols-1 gap-x-8 gap-y-16 lg:grid-cols-2">
                <!-- Contact Information -->
                <div class="scroll-reveal" style="animation-delay: 0.2s;">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-8 theme-transition">Contact Information</h3>
                    
                    <div class="space-y-6">
                        <!-- Phone -->
                        <div class="flex items-center">
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-r from-brand-blue to-brand-teal">
                                <i class="fas fa-phone text-white"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900 dark:text-white theme-transition">Phone</p>
                                <p class="text-sm text-gray-600 dark:text-gray-300 theme-transition"><?php echo htmlspecialchars($companyInfo['phone']); ?></p>
                            </div>
                        </div>
                        
                        <!-- Hotlines -->
                        <div class="flex items-center">
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-r from-brand-teal to-brand-green">
                                <i class="fas fa-mobile-alt text-white"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900 dark:text-white theme-transition">Hotlines</p>
                                <p class="text-sm text-gray-600 dark:text-gray-300 theme-transition">
                                    <?php echo htmlspecialchars($companyInfo['hotline1']); ?> • <?php echo htmlspecialchars($companyInfo['hotline2']); ?>
                                </p>
                            </div>
                        </div>
                        
                        <!-- Email -->
                        <div class="flex items-center">
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-r from-brand-green to-brand-blue">
                                <i class="fas fa-envelope text-white"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900 dark:text-white theme-transition">Email</p>
                                <p class="text-sm text-gray-600 dark:text-gray-300 theme-transition"><?php echo htmlspecialchars($companyInfo['email']); ?></p>
                            </div>
                        </div>
                        
                        <!-- Address -->
                        <div class="flex items-start">
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-r from-brand-blue to-brand-green">
                                <i class="fas fa-map-marker-alt text-white"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900 dark:text-white theme-transition">Address</p>
                                <p class="text-sm text-gray-600 dark:text-gray-300 theme-transition"><?php echo htmlspecialchars($companyInfo['address']); ?></p>
                            </div>
                        </div>
                        
                        <!-- Business Hours -->
                        <div class="flex items-start">
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-r from-brand-teal to-brand-blue">
                                <i class="fas fa-clock text-white"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900 dark:text-white theme-transition">Business Hours</p>
                                <p class="text-sm text-gray-600 dark:text-gray-300 theme-transition whitespace-pre-line"><?php echo htmlspecialchars($companyInfo['business_hours']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Form -->
                <div class="scroll-reveal" style="animation-delay: 0.4s;">
                    <div class="modern-card rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-xl">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 theme-transition">Send us a Message</h3>
                        
                        <!-- Success/Error Messages -->
                        <?php if ($contactMessage): ?>
                        <div class="mb-6 bg-brand-green/10 dark:bg-brand-green/20 border border-brand-green/20 dark:border-brand-green/30 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-brand-green dark:text-brand-green-light"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-brand-green-dark dark:text-brand-green-light"><?php echo htmlspecialchars($contactMessage); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($contactError): ?>
                        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-red-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700 dark:text-red-400"><?php echo htmlspecialchars($contactError); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="space-y-6">
                            <input type="hidden" name="contact_form" value="1">
                            
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-900 dark:text-white theme-transition">Name *</label>
                                <input type="text" name="name" id="name" required 
                                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
                                       class="mt-2 block w-full rounded-md border-0 px-3.5 py-2 text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 theme-transition">
                            </div>
                            
                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-900 dark:text-white theme-transition">Email *</label>
                                <input type="email" name="email" id="email" required
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                       class="mt-2 block w-full rounded-md border-0 px-3.5 py-2 text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 theme-transition">
                            </div>
                            
                            <!-- Company -->
                            <div>
                                <label for="company" class="block text-sm font-medium text-gray-900 dark:text-white theme-transition">Company</label>
                                <input type="text" name="company" id="company"
                                       value="<?php echo isset($_POST['company']) ? htmlspecialchars($_POST['company']) : ''; ?>"
                                       class="mt-2 block w-full rounded-md border-0 px-3.5 py-2 text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 theme-transition">
                            </div>
                            
                            <!-- Subject -->
                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-900 dark:text-white theme-transition">Subject</label>
                                <input type="text" name="subject" id="subject"
                                       value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>"
                                       class="mt-2 block w-full rounded-md border-0 px-3.5 py-2 text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 theme-transition">
                            </div>
                            
                            <!-- Message -->
                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-900 dark:text-white theme-transition">Message *</label>
                                <textarea name="message" id="message" rows="4" required
                                          class="mt-2 block w-full rounded-md border-0 px-3.5 py-2 text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 theme-transition"><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                            </div>
                            
                            <!-- Submit Button -->
                            <div>
                                <button type="submit" class="btn-primary w-full rounded-md px-3.5 py-2.5 text-center text-sm font-semibold text-white shadow-xl hover-lift focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-blue">
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    Send Message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 dark:bg-black theme-transition">
        <div class="mx-auto max-w-7xl px-6 py-20 sm:py-24 lg:px-8 lg:py-32">
            <div class="xl:grid xl:grid-cols-3 xl:gap-8">
                <!-- Company Info -->
                <div class="space-y-8">
                    <div>
                        <div class="flex items-center">
                            <img src="images/wlogo.png" alt="Sky Border Solutions" class="h-8 w-auto">
                        </div>
                        <p class="mt-4 text-sm leading-6 text-gray-300">
                            <?php echo htmlspecialchars($companyInfo['tagline']); ?>
                        </p>
                        <p class="mt-2 text-sm leading-6 text-gray-400">
                            
                        </p>
                    </div>
                    
                    <!-- Social Links -->
                    <div class="flex space-x-6">
                        <a href="#" class="text-gray-400 hover:text-brand-blue transition-colors">
                            <span class="sr-only">Facebook</span>
                            <i class="fab fa-facebook-f text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-brand-teal transition-colors">
                            <span class="sr-only">LinkedIn</span>
                            <i class="fab fa-linkedin-in text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-brand-green transition-colors">
                            <span class="sr-only">Instagram</span>
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-brand-blue transition-colors">
                            <span class="sr-only">Twitter</span>
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="mt-16 grid grid-cols-2 gap-8 xl:col-span-2 xl:mt-0">
                    <div class="md:grid md:grid-cols-2 md:gap-8">
                        <div>
                            <h3 class="text-sm font-semibold leading-6 text-white">Navigation</h3>
                            <ul role="list" class="mt-6 space-y-4">
                                <li><a href="#home" class="text-sm leading-6 text-gray-300 hover:text-white transition-colors">Home</a></li>
                                <li><a href="#about" class="text-sm leading-6 text-gray-300 hover:text-white transition-colors">About</a></li>
                                <li><a href="#services" class="text-sm leading-6 text-gray-300 hover:text-white transition-colors">Services</a></li>
                                <li><a href="#portfolio" class="text-sm leading-6 text-gray-300 hover:text-white transition-colors">Portfolio</a></li>
                                <li><a href="#clients" class="text-sm leading-6 text-gray-300 hover:text-white transition-colors">Clients</a></li>
                                <li><a href="#contact" class="text-sm leading-6 text-gray-300 hover:text-white transition-colors">Contact</a></li>
                            </ul>
                        </div>
                        <div class="mt-10 md:mt-0">
                            <h3 class="text-sm font-semibold leading-6 text-white">Services</h3>
                            <ul role="list" class="mt-6 space-y-4">
                                <?php foreach (array_slice($services, 0, 4) as $service): ?>
                                <li><span class="text-sm leading-6 text-gray-300"><?php echo htmlspecialchars($service['category_name']); ?></span></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="md:grid md:grid-cols-1 md:gap-8">
                        <div>
                            <h3 class="text-sm font-semibold leading-6 text-white">Contact Info</h3>
                            <ul role="list" class="mt-6 space-y-4">
                                <li class="text-sm leading-6 text-gray-300">
                                    <i class="fas fa-phone mr-2 text-brand-blue"></i>
                                    <?php echo htmlspecialchars($companyInfo['phone']); ?>
                                </li>
                                <li class="text-sm leading-6 text-gray-300">
                                    <i class="fas fa-envelope mr-2 text-brand-teal"></i>
                                    <?php echo htmlspecialchars($companyInfo['email']); ?>
                                </li>
                                <li class="text-sm leading-6 text-gray-300">
                                    <i class="fas fa-map-marker-alt mr-2 text-brand-green"></i>
                                    Malé, Maldives
                                </li>
                            </ul>
                        </div>
                      
                    </div>
                </div>
            </div>
            
            <!-- Bottom Section -->
            <div class="mt-16 border-t border-gray-800 pt-8 sm:mt-20 lg:mt-24">
                <div class="flex flex-col items-center justify-between sm:flex-row">
                    <p class="text-xs leading-5 text-gray-400">
                        &copy; <span id="year"><?php echo date('Y'); ?></span> <?php echo htmlspecialchars($companyInfo['company_name']); ?>. All rights reserved.
                    </p>
                    <p class="mt-4 text-xs leading-5 text-gray-400 sm:mt-0">
                        Republic of Maldives
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Dark mode functionality
        function initTheme() {
            const savedTheme = localStorage.getItem('darkMode');
            const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            const darkMode = savedTheme === 'true' || (savedTheme === null && prefersDark);
            
            if (darkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            
            // Update theme icons
            updateThemeIcons();
        }
        
        function toggleTheme() {
            const isDark = document.documentElement.classList.contains('dark');
            const newDarkMode = !isDark;
            
            // Save preference
            localStorage.setItem('darkMode', newDarkMode.toString());
            
            // Toggle dark class
            if (newDarkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            
            // Update theme icons immediately
            updateThemeIcons();
        }
        
        function updateThemeIcons() {
            const isDark = document.documentElement.classList.contains('dark');
            const moonIcon = document.getElementById('theme-icon');
            const sunIcon = document.getElementById('theme-icon-dark');
            
            if (moonIcon && sunIcon) {
                if (isDark) {
                    // Dark mode: show sun icon (to switch to light)
                    moonIcon.style.display = 'none';
                    sunIcon.style.display = 'block';
                } else {
                    // Light mode: show moon icon (to switch to dark)
                    moonIcon.style.display = 'block';
                    sunIcon.style.display = 'none';
                }
            } else {
                console.warn('Sky Border: Theme icons not found', {
                    moonIcon: !!moonIcon,
                    sunIcon: !!sunIcon
                });
            }
        }
        
        // Debug function for theme state
        function getThemeDebugInfo() {
            return {
                isDark: document.documentElement.classList.contains('dark'),
                localStorage: localStorage.getItem('darkMode'),
                systemPreference: window.matchMedia('(prefers-color-scheme: dark)').matches,
                moonIcon: !!document.getElementById('theme-icon'),
                sunIcon: !!document.getElementById('theme-icon-dark'),
                toggleButton: !!document.getElementById('theme-toggle')
            };
        }
        
        // Initialize theme on page load (early to prevent flash)
        initTheme();
        
        // Also initialize on window load to ensure everything is ready
        window.addEventListener('load', function() {
            initTheme();
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            // Force theme initialization
            setTimeout(initTheme, 100);
            
            // Theme toggle button
            const themeToggle = document.getElementById('theme-toggle');
            if (themeToggle) {
                themeToggle.addEventListener('click', toggleTheme);
            }
            
            // Listen for system theme changes
            if (window.matchMedia) {
                const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
                mediaQuery.addListener(function() {
                    // Only update if user hasn't manually set a preference
                    if (!localStorage.getItem('darkMode')) {
                        initTheme();
                    }
                });
            }
            
            // Update footer year
            const yearElement = document.getElementById('year');
            if (yearElement) {
                yearElement.textContent = new Date().getFullYear();
            }

            // Mobile menu toggle
            const mobileMenuButton = document.querySelector('.mobile-menu-button');
            const mobileMenu = document.querySelector('.mobile-menu');
            
            if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
            }

            // Navigation functionality
            document.querySelectorAll('.nav-link, .mobile-nav-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    if (href && href.startsWith('#')) {
                    e.preventDefault();
                        const targetId = href.substring(1);
                    const targetElement = document.getElementById(targetId);
                    
                    if (targetElement) {
                        const headerOffset = 80; // Account for fixed header
                        const elementPosition = targetElement.getBoundingClientRect().top;
                        const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                        
                        window.scrollTo({
                            top: offsetPosition,
                            behavior: 'smooth'
                        });
                        
                        // Close mobile menu after clicking
                            if (mobileMenu) {
                        mobileMenu.classList.add('hidden');
                            }
                        }
                    }
                });
            });





            // Scroll animations with fallback
            if ('IntersectionObserver' in window) {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                            entry.target.classList.add('revealed');
                            // Unobserve once revealed to prevent multiple triggers
                            observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            // Observe all scroll-reveal elements
            document.querySelectorAll('.scroll-reveal').forEach(el => {
                observer.observe(el);
            });
            } else {
                // Fallback: immediately show all scroll-reveal elements
                document.querySelectorAll('.scroll-reveal').forEach(el => {
                    el.classList.add('revealed');
                });
            }
        });
    </script>

    <!--Start of Tawk.to Script-->
    <script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
    var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
    s1.async=true;
    s1.src='https://embed.tawk.to/6897281c2547a719265fe29a/1j2779cm1';
    s1.charset='UTF-8';
    s1.setAttribute('crossorigin','*');
    s0.parentNode.insertBefore(s1,s0);
    })();
    </script>
    <!--End of Tawk.to Script-->
</body>
</html>
