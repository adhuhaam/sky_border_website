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
    $message = $_POST['message'] ?? '';
    
    if (!empty($name) && !empty($email) && !empty($message)) {
        // Try to save to database if available
        $messageSaved = false;
        
        if ($databaseAvailable && $contentManager) {
            try {
                $messageSaved = $contentManager->addContactMessage($name, $email, $company, '', '', $message);
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
    <title><?php echo htmlspecialchars($companyInfo['company_name'] ?? 'Sky Border Solutions'); ?> | Professional HR Consulting & Recruitment Agency</title>
    <meta name="description" content="<?php echo htmlspecialchars($companyInfo['description'] ?? 'Leading HR consultancy and recruitment firm in Maldives. Government-licensed professional workforce solutions.'); ?>">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    
    <!-- Google Fonts - Inter (same as Catalyst) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Preload critical resources -->
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="//cdn.tailwindcss.com">
    
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            scroll-behavior: smooth;
        }
        
        /* Enhanced Transitions */
        .theme-transition { 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
        }
        
        /* Modern Gradients - Darker Shades */
        .gradient-bg { 
            background: linear-gradient(135deg, #1a5a7a 0%, #2a7a6e 50%, #3a6b3a 100%); 
        }
        .gradient-text { 
            background: linear-gradient(135deg, #1a5a7a 0%, #2a7a6e 100%); 
            -webkit-background-clip: text; 
            -webkit-text-fill-color: transparent; 
            background-clip: text; 
        }
        .gradient-text-animated {
            background: linear-gradient(45deg, #1a5a7a, #2a7a6e, #3a6b3a, #5a8a2a, #1a5a7a);
            background-size: 400% 400%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradient-flow 3s ease-in-out infinite;
        }
        .gradient-border {
            background: linear-gradient(135deg, #1a5a7a, #2a7a6e, #3a6b3a);
            border-radius: 0.75rem;
            padding: 2px;
        }
        .gradient-border-inner {
            background: white;
            border-radius: 0.625rem;
            height: 100%;
        }
        .dark .gradient-border-inner {
            background: rgb(17 24 39);
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
        @keyframes counterPulse {
            0%, 100% {
                opacity: 0;
            }
            50% {
                opacity: 0.3;
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
        .animate-counterPulse { animation: counterPulse 1s ease-in-out infinite; }
        
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
        
        /* Enhanced Hover Effects */
        .hover-lift {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .hover-lift:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        .hover-glow:hover {
            box-shadow: 0 0 30px rgba(46, 134, 171, 0.3);
        }
        
        /* Glass Morphism Effect */
        .glass {
            backdrop-filter: blur(16px) saturate(180%);
            background-color: rgba(255, 255, 255, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.125);
        }
        .dark .glass {
            background-color: rgba(17, 24, 39, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* Modern Card Styles */
        .modern-card {
            background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .dark .modern-card {
            background: linear-gradient(145deg, #1f2937 0%, #111827 100%);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .modern-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }
        .dark .modern-card:hover {
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
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
        
        /* Enhanced Button Styles - Darker Shades */
        .btn-primary {
            background: linear-gradient(135deg, #1a5a7a 0%, #2a7a6e 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        .btn-primary:hover::before {
            left: 100%;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(26, 90, 122, 0.4);
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
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #1a5a7a, #2a7a6e);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #0f3a4a, #1a5a7a);
        }
        .dark ::-webkit-scrollbar-track {
            background: #1e293b;
        }
    </style>
    
    <script>
        // Early theme initialization to prevent flash
        (function() {
            const savedTheme = localStorage.getItem('darkMode');
            const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            const darkMode = savedTheme === 'true' || (savedTheme === null && prefersDark);
            
            if (darkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
        
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        // Sky Border Solutions Brand Colors - Darker Shades
                        'brand': {
                            'green-light': '#5a8a2a',
                            'green': '#3a6b3a',
                            'green-dark': '#2a5a2a',
                            'blue-light': '#2a7a8a',
                            'blue': '#1a5a7a',
                            'blue-dark': '#0f3a4a',
                            'teal': '#2a7a6e',
                            'gray-light': '#E2E8F0',
                            'gray': '#475569',
                            'gray-dark': '#1e293b'
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="h-full bg-gray-50 dark:bg-gray-900 theme-transition">
    <!-- Enhanced Dark Mode Toggle -->
    <div class="fixed top-4 right-4 z-50">
        <button id="theme-toggle" class="group relative p-3 rounded-2xl bg-white/90 dark:bg-gray-800/90 backdrop-blur-md shadow-lg border border-gray-200/50 dark:border-gray-700/50 text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-700 hover:scale-105 active:scale-95 theme-transition focus:outline-none focus:ring-2 focus:ring-brand-blue focus:ring-offset-2 dark:focus:ring-offset-gray-800 sm:p-4" aria-label="Toggle dark mode" title="Toggle dark/light mode">
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-brand-blue/10 to-brand-teal/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <i id="theme-icon" class="relative z-10 fas fa-moon text-lg sm:text-xl transition-all duration-200" aria-hidden="true"></i>
            <i id="theme-icon-dark" class="relative z-10 fas fa-sun text-lg sm:text-xl hidden transition-all duration-200" aria-hidden="true"></i>
        </button>
    </div>



    <!-- Enhanced Hero Section with Catalyst Design -->
    <section id="home" class="relative overflow-hidden bg-gradient-to-b from-white via-gray-50/50 to-white dark:from-gray-900 dark:via-gray-800/30 dark:to-gray-900 theme-transition">
        <!-- Modern Background Pattern -->
        <div class="absolute inset-0 -z-20">
            <!-- Grid Pattern -->
            <div class="absolute inset-0 bg-[linear-gradient(to_right,#8882_1px,transparent_1px),linear-gradient(to_bottom,#8882_1px,transparent_1px)] bg-[size:24px_24px] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_110%)]"></div>
            <!-- Animated Orbs -->
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-gradient-to-r from-brand-blue/10 to-brand-teal/10 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-float"></div>
            <div class="absolute top-20 right-1/4 w-96 h-96 bg-gradient-to-l from-brand-teal/10 to-brand-green/10 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-float" style="animation-delay: 2s;"></div>
            <div class="absolute -bottom-20 left-1/2 w-96 h-96 bg-gradient-to-t from-brand-green/10 to-brand-blue/10 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-float" style="animation-delay: 4s;"></div>
        </div>
        
        <!-- Main Content -->
        <div class="relative mx-auto max-w-7xl px-4 py-20 sm:px-6 sm:py-28 lg:px-8 lg:py-32">
            <div class="text-center">
                <!-- Main Heading -->
                <div class="scroll-reveal" style="animation-delay: 0.2s;">
                    <h1 class="mx-auto max-w-5xl text-4xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-6xl lg:text-6xl">
                        <span class="block">
                            <span class="bg-gradient-to-r from-brand-blue via-brand-teal to-brand-green bg-clip-text text-transparent animate-pulse">
                        <?php echo htmlspecialchars($companyInfo['company_name'] ?? 'Sky Border Solutions'); ?>
                            </span>
                        </span>
                        <span class="mt-2 block text-2xl sm:text-3xl lg:text-4xl font-medium text-gray-600 dark:text-gray-300">
                            Professional Workforce Solutions
                    </span>
                </h1>
                </div>

                <!-- Tagline -->
                <div class="scroll-reveal" style="animation-delay: 0.4s;">
                    <p class="mx-auto mt-8 max-w-2xl text-xl leading-8 text-gray-600 dark:text-gray-300">
                        <span class="relative">
                            <span class="absolute -left-4 top-0 text-brand-blue/40 text-lg">"</span>
                    <?php echo htmlspecialchars($companyInfo['tagline'] ?? 'Where compliance meets competence'); ?>
                            <span class="absolute -right-4 bottom-0 text-brand-blue/40 text-lg">"</span>
                        </span>
                </p>
                </div>

                <!-- Description -->
                <div class="scroll-reveal" style="animation-delay: 0.6s;">
                    <p class="mx-auto mt-6 max-w-3xl text-lg leading-relaxed text-gray-500 dark:text-gray-400">
                    <?php echo htmlspecialchars($companyInfo['description'] ?? 'Leading HR consultancy and recruitment firm in the Republic of Maldives, providing end-to-end manpower solutions with excellence and integrity.'); ?>
                </p>
                </div>

                <!-- Status Badges -->
                <div class="mx-auto mt-10 mb-6 flex flex-wrap justify-center gap-3 scroll-reveal" style="animation-delay: 0.7s;">
                    <div class="group relative inline-flex items-center rounded-full bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm px-4 py-2 text-sm font-medium text-brand-green-dark dark:text-brand-green-light border border-brand-green/20 dark:border-brand-green/30 hover:border-brand-green/40 dark:hover:border-brand-green/50 transition-all duration-300 hover:scale-105">
                        <div class="absolute inset-0 rounded-full bg-gradient-to-r from-brand-green/5 to-brand-green/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <i class="fas fa-certificate mr-2 text-brand-green"></i>
                        <span class="relative z-10">Government Licensed</span>
                    </div>
                    <div class="group relative inline-flex items-center rounded-full bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm px-4 py-2 text-sm font-medium text-brand-blue-dark dark:text-brand-blue-light border border-brand-blue/20 dark:border-brand-blue/30 hover:border-brand-blue/40 dark:hover:border-brand-blue/50 transition-all duration-300 hover:scale-105" style="animation-delay: 0.1s;">
                        <div class="absolute inset-0 rounded-full bg-gradient-to-r from-brand-blue/5 to-brand-blue/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <i class="fas fa-award mr-2 text-brand-blue"></i>
                        <span class="relative z-10">HR Consulting & Recruitment</span>
                    </div>
                </div>

                <!-- Enhanced CTA Buttons -->
                <div class="mt-12 flex flex-col sm:flex-row items-center justify-center gap-4 scroll-reveal" style="animation-delay: 0.8s;">
                    <a href="#contact" class="group relative inline-flex items-center justify-center px-8 py-4 text-sm font-semibold text-white bg-gradient-to-r from-brand-blue to-brand-teal rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:ring-offset-2 dark:focus:ring-offset-gray-900 w-full sm:w-auto">
                        <span class="absolute inset-0 rounded-xl bg-gradient-to-r from-brand-blue-dark to-brand-teal opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                        <i class="fas fa-comments mr-3 relative z-10"></i>
                        <span class="relative z-10">Get Started Today</span>
                        <i class="fas fa-arrow-right ml-3 transition-transform group-hover:translate-x-1 relative z-10"></i>
                    </a>
                    <a href="#services" class="group inline-flex items-center justify-center px-8 py-4 text-sm font-semibold text-gray-900 dark:text-white bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 w-full sm:w-auto">
                        <i class="fas fa-eye mr-3 transition-transform group-hover:scale-110"></i>
                        <span>Explore Services</span>
                        <i class="fas fa-arrow-down ml-3 transition-transform group-hover:translate-y-1"></i>
                    </a>
                </div>

                <!-- Enhanced Stats with Real-time Count Animation -->
                <div class="mx-auto mt-16 max-w-7xl scroll-reveal" style="animation-delay: 1s;">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 lg:grid-cols-3 xl:grid-cols-3">
                        <?php foreach ($stats as $index => $stat): ?>
                        <div class="group relative bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm rounded-xl p-6 text-center border border-gray-200/50 dark:border-gray-700/50 hover:border-gray-300/70 dark:hover:border-gray-600/70 transition-all duration-300 hover:scale-105 hover:shadow-lg scroll-reveal" style="animation-delay: <?php echo 1.2 + ($index * 0.1); ?>s;">
                            <!-- Background Gradient -->
                            <div class="absolute inset-0 rounded-xl bg-gradient-to-br <?php 
                                switch($stat['stat_name']) {
                                    case 'placements': echo 'from-brand-blue/5 to-brand-teal/5';
                                        break;
                                    case 'partners': echo 'from-brand-teal/5 to-brand-green/5';
                                        break;
                                    case 'compliance': echo 'from-brand-green/5 to-brand-blue/5';
                                        break;
                                    default: echo 'from-gray-100/50 to-gray-200/50 dark:from-gray-700/50 dark:to-gray-600/50';
                                }
                            ?> opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            
                            <!-- Icon -->
                            <div class="relative z-10 mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r <?php 
                                switch($stat['stat_name']) {
                                    case 'placements': echo 'from-brand-blue to-brand-teal';
                                        break;
                                    case 'partners': echo 'from-brand-teal to-brand-green';
                                        break;
                                    case 'compliance': echo 'from-brand-green to-brand-blue';
                                        break;
                                    default: echo 'from-gray-400 to-gray-600';
                                }
                            ?> shadow-md group-hover:shadow-lg transition-all duration-300">
                                <i class="fas fa-<?php 
                                    switch($stat['stat_name']) {
                                        case 'placements': echo 'users';
                                            break;
                                        case 'partners': echo 'handshake';
                                            break;
                                        case 'compliance': echo 'shield-alt';
                                            break;
                                        default: echo 'chart-line';
                                    }
                                ?> text-white text-lg group-hover:scale-110 transition-transform duration-300"></i>
                            </div>
                            
                            <!-- Animated Value -->
                            <div class="relative z-10">
                                <?php 
                                // Extract numeric value for animation
                                $numericValue = preg_replace('/[^0-9]/', '', $stat['stat_value']);
                                $suffix = preg_replace('/[0-9]/', '', $stat['stat_value']);
                                ?>
                                <div class="relative overflow-hidden">
                                    <p class="text-3xl sm:text-4xl font-bold tracking-tight bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-200 bg-clip-text text-transparent">
                                        <span class="counter-value" 
                                              data-target="<?php echo $numericValue; ?>" 
                                              data-suffix="<?php echo htmlspecialchars($suffix); ?>"
                                              data-duration="2000">0<?php echo htmlspecialchars($suffix); ?></span>
                                    </p>
                                    <!-- Pulse effect during counting -->
                                    <div class="absolute inset-0 bg-gradient-to-r from-brand-blue/10 to-brand-teal/10 rounded opacity-0 counter-pulse"></div>
                                </div>
                                <p class="mt-2 text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-300 leading-tight">
                                    <?php echo htmlspecialchars($stat['stat_label']); ?>
                                </p>
                            </div>

                            <!-- Progress bar animation -->
                            <div class="relative z-10 mt-3">
                                <div class="h-1 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r <?php 
                                        switch($stat['stat_name']) {
                                            case 'placements': echo 'from-brand-blue to-brand-teal';
                                                break;
                                            case 'partners': echo 'from-brand-teal to-brand-green';
                                                break;
                                            case 'compliance': echo 'from-brand-green to-brand-blue';
                                                break;
                                            default: echo 'from-gray-400 to-gray-600';
                                        }
                                    ?> rounded-full transform -translate-x-full progress-bar transition-transform duration-2000 ease-out"></div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Live Update Indicator -->
                    <div class="text-center mt-6">
                        <div class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-medium">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                            Live Statistics
                        </div>
                    </div>
                </div>

                <!-- Enhanced Scroll Indicator -->
                <div class="mt-20">
                    <div class="flex flex-col items-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Learn more about us</p>
                        <a href="#about" class="group inline-flex items-center justify-center w-12 h-12 rounded-full bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200 dark:border-gray-700 text-gray-400 dark:text-gray-500 hover:text-brand-blue dark:hover:text-brand-blue-light hover:border-brand-blue/30 dark:hover:border-brand-blue/30 transition-all duration-300 hover:scale-105">
                            <i class="fas fa-chevron-down text-lg animate-bounce group-hover:translate-y-1 transition-transform duration-300"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Enhanced About Section -->
    <section id="about" class="relative py-24 sm:py-32 bg-gradient-to-b from-gray-50 to-white dark:from-gray-800 dark:to-gray-900 theme-transition overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-grid-gray-100/25 dark:bg-grid-gray-800/25 [mask-image:linear-gradient(0deg,transparent,black,transparent)]"></div>
        
        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="mx-auto max-w-4xl text-center scroll-reveal">
                <div class="inline-flex items-center rounded-full bg-brand-blue/10 dark:bg-brand-blue/20 px-4 py-2 text-sm font-medium text-brand-blue-dark dark:text-brand-blue-light mb-6">
                    <i class="fas fa-info-circle mr-2"></i>
                    About Our Company
                </div>
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl lg:text-5xl">
                    About <span class="bg-gradient-to-r from-brand-blue to-brand-teal bg-clip-text text-transparent">Sky Border Solutions</span>
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
            
            <!-- Mission & Vision Cards -->
            <div class="mx-auto mt-20 max-w-6xl">
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                    <!-- Mission Card -->
                    <div class="group relative bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-3xl p-8 border border-gray-200/50 dark:border-gray-700/50 hover:border-gray-300/70 dark:hover:border-gray-600/70 transition-all duration-300 hover:scale-105 scroll-reveal" style="animation-delay: 0.2s;">
                        <!-- Background Gradient -->
                        <div class="absolute inset-0 rounded-3xl bg-gradient-to-br from-brand-blue/5 to-brand-teal/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        
                        <div class="relative z-10">
                        <div class="flex items-center mb-6">
                                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-r from-brand-blue to-brand-teal shadow-lg group-hover:shadow-xl transition-shadow duration-300">
                                    <i class="fas fa-bullseye text-white text-xl group-hover:scale-110 transition-transform duration-300"></i>
                            </div>
                                <div class="ml-4">
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Our Mission</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">What drives us forward</p>
                        </div>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                            <?php echo htmlspecialchars($companyInfo['mission']); ?>
                        </p>
                        </div>
                    </div>
                    
                    <!-- Vision Card -->
                    <div class="group relative bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-3xl p-8 border border-gray-200/50 dark:border-gray-700/50 hover:border-gray-300/70 dark:hover:border-gray-600/70 transition-all duration-300 hover:scale-105 scroll-reveal" style="animation-delay: 0.4s;">
                        <!-- Background Gradient -->
                        <div class="absolute inset-0 rounded-3xl bg-gradient-to-br from-brand-teal/5 to-brand-green/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        
                        <div class="relative z-10">
                        <div class="flex items-center mb-6">
                                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-r from-brand-teal to-brand-green shadow-lg group-hover:shadow-xl transition-shadow duration-300">
                                    <i class="fas fa-eye text-white text-xl group-hover:scale-110 transition-transform duration-300"></i>
                            </div>
                                <div class="ml-4">
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Our Vision</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Where we're heading</p>
                        </div>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                            <?php echo htmlspecialchars($companyInfo['vision']); ?>
                        </p>
                        </div>
                    </div>
                </div>
                
                <!-- Why Choose Us -->
                <div class="mt-16 scroll-reveal" style="animation-delay: 0.6s;">
                    <div class="text-center mb-12">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white theme-transition">Why Choose Us?</h3>
                        <p class="mt-4 text-lg text-gray-600 dark:text-gray-300 theme-transition">What sets us apart in the recruitment industry</p>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="text-center scroll-reveal" style="animation-delay: 0.8s;">
                            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-brand-blue/10 dark:bg-brand-blue/20 hover-glow animate-scale">
                                <i class="fas fa-certificate text-brand-blue dark:text-brand-blue-light text-xl"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white theme-transition">Licensed & Compliant</h4>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 theme-transition">Government-approved recruitment services</p>
                        </div>
                        
                        <div class="text-center scroll-reveal" style="animation-delay: 1s;">
                            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-brand-teal/10 dark:bg-brand-teal/20 hover-glow animate-scale">
                                <i class="fas fa-users text-brand-teal text-xl"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white theme-transition">Expert Team</h4>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 theme-transition">Experienced HR professionals</p>
                        </div>
                        
                        <div class="text-center scroll-reveal" style="animation-delay: 1.2s;">
                            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-brand-green/10 dark:bg-brand-green/20 hover-glow animate-scale">
                                <i class="fas fa-clock text-brand-green dark:text-brand-green-light text-xl"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white theme-transition">Fast Processing</h4>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 theme-transition">Quick visa and permit processing</p>
                        </div>
                        
                        <div class="text-center scroll-reveal" style="animation-delay: 1.4s;">
                            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-r from-brand-blue to-brand-teal hover-glow animate-scale">
                                <i class="fas fa-handshake text-white text-xl"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white theme-transition">Trusted Partners</h4>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 theme-transition">Long-term relationships with clients</p>
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
                                    <?php if (!empty($provider['logo_url']) && file_exists($provider['logo_url'])): ?>
                                    <div class="h-12 w-16 bg-white dark:bg-gray-700 rounded-lg flex items-center justify-center p-2 group-hover:shadow-md transition-shadow duration-200">
                                        <img src="<?php echo htmlspecialchars($provider['logo_url']); ?>" 
                                             alt="<?php echo htmlspecialchars($provider['provider_name']); ?>" 
                                             class="h-full w-full object-contain">
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
                            <img src="images/logo.svg" alt="Sky Border Solutions" class="h-8 w-auto">
                            <span class="ml-3 text-xl font-bold text-white"><?php echo htmlspecialchars($companyInfo['company_name']); ?></span>
                        </div>
                        <p class="mt-4 text-sm leading-6 text-gray-300">
                            <?php echo htmlspecialchars($companyInfo['tagline']); ?>
                        </p>
                        <p class="mt-2 text-sm leading-6 text-gray-400">
                            Leading HR consultancy and recruitment firm in the Republic of Maldives.
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
                    <div class="md:grid md:grid-cols-2 md:gap-8">
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
                        <div class="mt-10 md:mt-0">
                            <h3 class="text-sm font-semibold leading-6 text-white">Quick Actions</h3>
                            <ul role="list" class="mt-6 space-y-4">
                                <li><a href="#contact" class="text-sm leading-6 text-gray-300 hover:text-white transition-colors">Get Quote</a></li>
                                <li><a href="admin/" class="text-sm leading-6 text-gray-300 hover:text-white transition-colors">Admin</a></li>
                                <li><a href="check-setup.php" class="text-sm leading-6 text-gray-300 hover:text-white transition-colors">System Status</a></li>
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
                        Government Licensed HR Consultancy • Licensed & Compliant
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

            // Counter Animation Function
            function animateCounter(element) {
                const target = parseInt(element.dataset.target);
                const suffix = element.dataset.suffix || '';
                const duration = parseInt(element.dataset.duration) || 2000;
                const startTime = performance.now();
                const startValue = 0;
                
                // Get associated progress bar and pulse effect
                const card = element.closest('.group');
                const progressBar = card?.querySelector('.progress-bar');
                const pulseEffect = card?.querySelector('.counter-pulse');
                
                // Start pulse animation
                if (pulseEffect) {
                    pulseEffect.style.opacity = '0.3';
                    pulseEffect.style.animation = 'pulse 0.5s ease-in-out infinite';
                }

                function updateCounter(currentTime) {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    
                    // Use easeOutQuart for smooth animation
                    const easeProgress = 1 - Math.pow(1 - progress, 4);
                    const currentValue = Math.floor(startValue + (target - startValue) * easeProgress);
                    
                    // Update counter display
                    element.textContent = currentValue.toLocaleString() + suffix;
                    
                    // Update progress bar
                    if (progressBar) {
                        progressBar.style.transform = `translateX(-${100 - (progress * 100)}%)`;
                    }
                    
                    if (progress < 1) {
                        requestAnimationFrame(updateCounter);
                    } else {
                        // Animation complete
                        element.textContent = target.toLocaleString() + suffix;
                        if (progressBar) {
                            progressBar.style.transform = 'translateX(0%)';
                        }
                        if (pulseEffect) {
                            pulseEffect.style.opacity = '0';
                            pulseEffect.style.animation = 'none';
                        }
                        
                        // Add completion effect
                        element.style.animation = 'pulse 0.3s ease-in-out';
                        setTimeout(() => {
                            element.style.animation = '';
                        }, 300);
                    }
                }
                
                requestAnimationFrame(updateCounter);
            }

            // Enhanced Stats Counter with Real-time Updates
            function initStatsCounters() {
                const counters = document.querySelectorAll('.counter-value');
                const statsObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const counter = entry.target;
                            if (!counter.dataset.animated) {
                                counter.dataset.animated = 'true';
                                // Add slight delay for staggered effect
                                const delay = Array.from(counters).indexOf(counter) * 200;
                                setTimeout(() => {
                                    animateCounter(counter);
                                }, delay);
                            }
                            statsObserver.unobserve(counter);
                        }
                    });
                }, {
                    threshold: 0.5,
                    rootMargin: '0px 0px -100px 0px'
                });

                counters.forEach(counter => {
                    statsObserver.observe(counter);
                });
            }

            // Simulate real-time updates (optional - for demo purposes)
            function simulateRealTimeUpdates() {
                const counters = document.querySelectorAll('.counter-value[data-animated="true"]');
                
                counters.forEach(counter => {
                    const currentTarget = parseInt(counter.dataset.target);
                    const suffix = counter.dataset.suffix || '';
                    
                    // Small random increment every 30-60 seconds
                    const updateInterval = Math.random() * 30000 + 30000; // 30-60 seconds
                    
                    setTimeout(() => {
                        if (Math.random() > 0.7) { // 30% chance of update
                            const increment = Math.floor(Math.random() * 3) + 1; // 1-3 increment
                            const newTarget = currentTarget + increment;
                            
                            // Update target and re-animate briefly
                            counter.dataset.target = newTarget;
                            counter.style.transition = 'all 0.5s ease';
                            counter.textContent = newTarget.toLocaleString() + suffix;
                            
                            // Pulse effect for real-time update
                            counter.style.transform = 'scale(1.1)';
                            setTimeout(() => {
                                counter.style.transform = 'scale(1)';
                            }, 200);
                        }
                        
                        // Schedule next potential update
                        simulateRealTimeUpdates();
                    }, updateInterval);
                });
            }

            // Initialize counters
            initStatsCounters();
            
            // Start real-time simulation after initial load
            setTimeout(simulateRealTimeUpdates, 5000);

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
