<?php
/**
 * Insurance Providers Public Page
 * Sky Border Solutions
 */

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
    error_log("Insurance providers page: Database connection error - " . $e->getMessage());
}

// Get company info for branding
if ($databaseAvailable && $contentManager) {
    try {
        $companyInfo = $contentManager->getCompanyInfo();
        $insuranceProviders = $contentManager->getInsuranceProviders();
    } catch (Exception $e) {
        error_log("Insurance providers page: Content loading error - " . $e->getMessage());
        $databaseAvailable = false;
    }
}

// Fallback data if database is not available
if (!$databaseAvailable) {
    $companyInfo = [
        'company_name' => 'Sky Border Solutions',
        'tagline' => 'Where compliance meets competence',
        'description' => 'Leading HR consultancy and recruitment firm in the Republic of Maldives, providing end-to-end manpower solutions with excellence and integrity.',
        'phone' => '+960 4000-444',
        'email' => 'info@skybordersolutions.com'
    ];
    
    $insuranceProviders = [
        [
            'id' => 1,
            'provider_name' => 'Maldivian Health Insurance Co.',
            'logo_url' => '',
            'is_featured' => 1,
            'display_order' => 1,
            'is_active' => 1
        ],
        [
            'id' => 2,
            'provider_name' => 'Allied Insurance Maldives',
            'logo_url' => '',
            'is_featured' => 1,
            'display_order' => 2,
            'is_active' => 1
        ],
        [
            'id' => 3,
            'provider_name' => 'Maldives Travel Insurance Ltd.',
            'logo_url' => '',
            'is_featured' => 0,
            'display_order' => 3,
            'is_active' => 1
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insurance Providers | <?php echo htmlspecialchars($companyInfo['company_name']); ?></title>
    <meta name="description" content="Trusted insurance providers partnered with <?php echo htmlspecialchars($companyInfo['company_name']); ?> for comprehensive workforce coverage solutions.">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    
    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            scroll-behavior: smooth;
        }
        
        /* Enhanced Transitions */
        .theme-transition { 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
        }
        
        /* Modern Gradients */
        .gradient-bg { 
            background: linear-gradient(135deg, #1a5a7a 0%, #2a7a6e 50%, #3a6b3a 100%); 
        }
        .gradient-text { 
            background: linear-gradient(135deg, #1a5a7a 0%, #2a7a6e 100%); 
            -webkit-background-clip: text; 
            -webkit-text-fill-color: transparent; 
            background-clip: text; 
        }
        
        /* Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-float { animation: float 3s ease-in-out infinite; }
        .animate-fadeInUp { animation: fadeInUp 0.8s ease-out; }
        
        /* Hover Effects */
        .hover-lift {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .hover-lift:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
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
        
        /* Line Clamp Utility */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
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
    </style>
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
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

<body class="h-full bg-white dark:bg-gray-900 theme-transition">
    <!-- Enhanced Dark Mode Toggle -->
    <div class="fixed top-4 right-4 z-50">
        <button id="theme-toggle" class="group relative p-3 rounded-2xl bg-white/90 dark:bg-gray-800/90 backdrop-blur-md shadow-lg border border-gray-200/50 dark:border-gray-700/50 text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-700 hover:scale-105 active:scale-95 theme-transition focus:outline-none focus:ring-2 focus:ring-brand-blue focus:ring-offset-2 dark:focus:ring-offset-gray-800 sm:p-4" aria-label="Toggle dark mode" title="Toggle dark/light mode">
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-brand-blue/10 to-brand-teal/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <i id="theme-icon" class="relative z-10 fas fa-moon text-lg sm:text-xl transition-all duration-200" aria-hidden="true"></i>
            <i id="theme-icon-dark" class="relative z-10 fas fa-sun text-lg sm:text-xl hidden transition-all duration-200" aria-hidden="true"></i>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="sticky top-0 z-40 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md shadow-sm border-b border-gray-200/20 dark:border-gray-700/30 theme-transition">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="/" class="flex items-center space-x-3">
                        <img src="images/logo.svg" alt="<?php echo htmlspecialchars($companyInfo['company_name']); ?>" class="h-10 w-auto">
                        <div class="hidden sm:block">
                            <h1 class="text-lg font-bold gradient-text">Sky Border</h1>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Solutions</p>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="/" class="text-gray-600 dark:text-gray-300 hover:text-brand-blue dark:hover:text-brand-blue-light transition-colors">Home</a>
                    <a href="/#about" class="text-gray-600 dark:text-gray-300 hover:text-brand-blue dark:hover:text-brand-blue-light transition-colors">About</a>
                    <a href="/#services" class="text-gray-600 dark:text-gray-300 hover:text-brand-blue dark:hover:text-brand-blue-light transition-colors">Services</a>
                    <a href="/insurance-providers.php" class="text-brand-blue dark:text-brand-blue-light font-medium">Insurance Providers</a>
                    <a href="/#contact" class="bg-gradient-to-r from-brand-blue to-brand-teal text-white px-4 py-2 rounded-lg hover:shadow-lg transition-all duration-300">Contact</a>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button type="button" class="mobile-menu-button text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1">
        <!-- Hero Section -->
        <section class="relative overflow-hidden bg-gradient-to-b from-white via-gray-50/50 to-white dark:from-gray-900 dark:via-gray-800/30 dark:to-gray-900 theme-transition py-20 sm:py-32">
            <!-- Background Pattern -->
            <div class="absolute inset-0 -z-10">
                <div class="absolute inset-0 bg-[linear-gradient(to_right,#8882_1px,transparent_1px),linear-gradient(to_bottom,#8882_1px,transparent_1px)] bg-[size:24px_24px] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_110%)]"></div>
                <div class="absolute top-0 left-1/4 w-96 h-96 bg-gradient-to-r from-brand-blue/10 to-brand-teal/10 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-float"></div>
            </div>
            
            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <div class="inline-flex items-center rounded-full bg-blue-50 dark:bg-blue-900/30 px-4 py-2 text-sm font-medium text-blue-700 dark:text-blue-300 mb-6 scroll-reveal">
                        <i class="fas fa-shield-alt mr-2"></i>
                        Our Insurance Partners
                    </div>
                    <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-6xl scroll-reveal">
                        Trusted <span class="gradient-text">Insurance Providers</span>
                    </h1>
                    <p class="mx-auto mt-6 max-w-2xl text-xl leading-8 text-gray-600 dark:text-gray-300 scroll-reveal">
                        We partner with leading insurance companies to provide comprehensive coverage for our workforce solutions, ensuring complete protection for our clients and their employees.
                    </p>
                    <div class="mt-10 flex items-center justify-center gap-x-6 scroll-reveal">
                        <a href="#providers" class="bg-gradient-to-r from-brand-blue to-brand-teal px-6 py-3 text-sm font-semibold text-white shadow-sm hover:shadow-lg transition-all duration-300 rounded-lg">
                            View Our Partners
                        </a>
                        <a href="/#contact" class="text-sm font-semibold leading-6 text-gray-900 dark:text-white hover:text-brand-blue dark:hover:text-brand-blue-light transition-colors">
                            Get Coverage <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Insurance Providers Content -->
        <section id="providers" class="py-24 sm:py-32 bg-gray-50 dark:bg-gray-800 theme-transition">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <?php include 'admin/views/insurance-content.php'; ?>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="bg-white dark:bg-gray-900 theme-transition py-24 sm:py-32">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="modern-card rounded-3xl p-8 sm:p-12 text-center">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                        Need Insurance Coverage?
                    </h2>
                    <p class="mx-auto mt-6 max-w-xl text-lg leading-8 text-gray-600 dark:text-gray-300">
                        Let us help you find the right insurance solution for your workforce needs. Our team will guide you through the process.
                    </p>
                    <div class="mt-10 flex items-center justify-center gap-x-6">
                        <a href="/#contact" class="bg-gradient-to-r from-brand-blue to-brand-teal px-6 py-3 text-sm font-semibold text-white shadow-sm hover:shadow-lg transition-all duration-300 rounded-lg">
                            Contact Us Today
                        </a>
                        <a href="/" class="text-sm font-semibold leading-6 text-gray-900 dark:text-white hover:text-brand-blue dark:hover:text-brand-blue-light transition-colors">
                            Back to Home <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 dark:bg-black theme-transition">
        <div class="mx-auto max-w-7xl px-6 py-12 md:py-16">
            <div class="xl:grid xl:grid-cols-3 xl:gap-8">
                <!-- Company Info -->
                <div class="space-y-8">
                    <div>
                        <div class="flex items-center">
                            <img src="images/logo.svg" alt="Sky Border Solutions" class="h-8 w-auto">
                            <span class="ml-3 text-xl font-bold text-white"><?php echo htmlspecialchars($companyInfo['company_name']); ?></span>
                        </div>
                        <p class="mt-4 text-sm leading-6 text-gray-300">
                            Leading HR consultancy and recruitment firm in the Republic of Maldives.
                        </p>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="mt-16 grid grid-cols-2 gap-8 xl:col-span-2 xl:mt-0">
                    <div>
                        <h3 class="text-sm font-semibold leading-6 text-white">Navigation</h3>
                        <ul role="list" class="mt-6 space-y-4">
                            <li><a href="/" class="text-sm leading-6 text-gray-300 hover:text-white transition-colors">Home</a></li>
                            <li><a href="/#about" class="text-sm leading-6 text-gray-300 hover:text-white transition-colors">About</a></li>
                            <li><a href="/#services" class="text-sm leading-6 text-gray-300 hover:text-white transition-colors">Services</a></li>
                            <li><a href="/insurance-providers.php" class="text-sm leading-6 text-gray-300 hover:text-white transition-colors">Insurance Providers</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold leading-6 text-white">Contact</h3>
                        <ul role="list" class="mt-6 space-y-4">
                            <li class="text-sm leading-6 text-gray-300">
                                <i class="fas fa-phone mr-2 text-brand-blue"></i>
                                <?php echo htmlspecialchars($companyInfo['phone']); ?>
                            </li>
                            <li class="text-sm leading-6 text-gray-300">
                                <i class="fas fa-envelope mr-2 text-brand-teal"></i>
                                <?php echo htmlspecialchars($companyInfo['email']); ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="mt-16 border-t border-gray-800 pt-8">
                <p class="text-xs leading-5 text-gray-400 text-center">
                    &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($companyInfo['company_name']); ?>. All rights reserved.
                </p>
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
            updateThemeIcons();
        }
        
        function toggleTheme() {
            const isDark = document.documentElement.classList.contains('dark');
            const newDarkMode = !isDark;
            
            localStorage.setItem('darkMode', newDarkMode.toString());
            
            if (newDarkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            updateThemeIcons();
        }
        
        function updateThemeIcons() {
            const isDark = document.documentElement.classList.contains('dark');
            const moonIcon = document.getElementById('theme-icon');
            const sunIcon = document.getElementById('theme-icon-dark');
            
            if (moonIcon && sunIcon) {
                if (isDark) {
                    moonIcon.style.display = 'none';
                    sunIcon.style.display = 'block';
                } else {
                    moonIcon.style.display = 'block';
                    sunIcon.style.display = 'none';
                }
            }
        }
        
        // Initialize theme
        initTheme();
        
        document.addEventListener('DOMContentLoaded', function() {
            // Theme toggle
            const themeToggle = document.getElementById('theme-toggle');
            if (themeToggle) {
                themeToggle.addEventListener('click', toggleTheme);
            }
            
            // Scroll animations
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('revealed');
                            observer.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.1,
                    rootMargin: '0px 0px -50px 0px'
                });

                document.querySelectorAll('.scroll-reveal').forEach(el => {
                    observer.observe(el);
                });
            } else {
                document.querySelectorAll('.scroll-reveal').forEach(el => {
                    el.classList.add('revealed');
                });
            }
        });
    </script>
</body>
</html>
