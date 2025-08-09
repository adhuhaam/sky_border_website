<?php
// Include database connection for dynamic content
require_once 'admin/config/database.php';
require_once 'admin/classes/ContentManager.php';

// Initialize content manager
$contentManager = new ContentManager();

// Get dynamic content
$companyInfo = $contentManager->getCompanyInfo();
$stats = $contentManager->getStatistics();
$services = $contentManager->getServiceCategories();
$portfolioCategories = $contentManager->getPortfolioCategories();
$clients = $contentManager->getClients();

// Handle contact form submission
$contactMessage = '';
$contactError = '';

if ($_POST && isset($_POST['contact_form'])) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $company = $_POST['company'] ?? '';
    $message = $_POST['message'] ?? '';
    
    if (!empty($name) && !empty($email) && !empty($message)) {
        // Insert contact message into database
        try {
            $database = new Database();
            $conn = $database->getConnection();
            
            $query = "INSERT INTO contact_messages (full_name, email, company_name, message) VALUES (:full_name, :email, :company_name, :message)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':full_name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':company_name', $company);
            $stmt->bindParam(':message', $message);
            
            if ($stmt->execute()) {
                $contactMessage = 'Thank you for your message! We will get back to you soon.';
            } else {
                $contactError = 'Sorry, there was an error sending your message. Please try again.';
            }
        } catch (Exception $e) {
            $contactError = 'Sorry, there was an error sending your message. Please try again.';
        }
    } else {
        $contactError = 'Please fill in all required fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($companyInfo['company_name'] ?? 'Sky Border Solutions'); ?> | Professional HR Consulting & Recruitment Agency</title>
    <meta name="description" content="<?php echo htmlspecialchars($companyInfo['description'] ?? 'Leading HR consultancy and recruitment firm in Maldives. Government-licensed professional workforce solutions.'); ?>">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Inter (same as Catalyst) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .theme-transition { transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease; }
        .gradient-bg { background: linear-gradient(135deg, #2E86AB 0%, #4ECDC4 100%); }
        .gradient-text { background: linear-gradient(135deg, #2E86AB 0%, #4ECDC4 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
    </style>
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        // Sky Border Solutions Brand Colors
                        'brand': {
                            'green-light': '#9BC53D',
                            'green': '#5CB85C',
                            'green-dark': '#4A9649',
                            'blue-light': '#5CB3CC',
                            'blue': '#2E86AB',
                            'blue-dark': '#1E5F7A',
                            'teal': '#4ECDC4',
                            'gray-light': '#F8FAFC',
                            'gray': '#64748B',
                            'gray-dark': '#334155'
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="h-full bg-white dark:bg-gray-900 theme-transition">
    <!-- Dark Mode Toggle -->
    <div class="fixed top-4 right-4 z-50">
        <button id="theme-toggle" class="p-3 rounded-full bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm shadow-lg border border-gray-200/50 dark:border-gray-700/50 text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-700 theme-transition">
            <i id="theme-icon" class="fas fa-moon dark:hidden text-lg"></i>
            <i id="theme-icon-dark" class="fas fa-sun hidden dark:block text-lg"></i>
        </button>
    </div>

    <!-- Navigation -->
    <header class="sticky top-0 z-40 bg-white/95 dark:bg-gray-900/95 backdrop-blur-sm shadow-sm ring-1 ring-gray-900/10 dark:ring-white/10 theme-transition">
        <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center space-x-3">
                        <img src="images/logo.svg" alt="<?php echo htmlspecialchars($companyInfo['company_name'] ?? 'Sky Border Solutions'); ?>" class="h-10 w-auto">
                    </div>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="#home" class="nav-link text-gray-900 dark:text-white hover:text-brand-blue dark:hover:text-brand-blue-light px-3 py-2 rounded-md text-sm font-medium theme-transition">Home</a>
                        <a href="#about" class="nav-link text-gray-500 dark:text-gray-400 hover:text-brand-blue dark:hover:text-brand-blue-light px-3 py-2 rounded-md text-sm font-medium theme-transition">About</a>
                        <a href="#services" class="nav-link text-gray-500 dark:text-gray-400 hover:text-brand-blue dark:hover:text-brand-blue-light px-3 py-2 rounded-md text-sm font-medium theme-transition">Services</a>
                        <a href="#clients" class="nav-link text-gray-500 dark:text-gray-400 hover:text-brand-blue dark:hover:text-brand-blue-light px-3 py-2 rounded-md text-sm font-medium theme-transition">Clients</a>
                        <a href="#contact" class="bg-gradient-to-r from-brand-blue to-brand-teal text-white hover:from-brand-blue-dark hover:to-brand-blue px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 shadow-lg">Contact</a>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button type="button" class="mobile-menu-button bg-gray-100 dark:bg-gray-800 p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-brand-blue theme-transition">
                        <i class="fas fa-bars h-6 w-6"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Navigation Menu -->
            <div class="mobile-menu hidden md:hidden">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 theme-transition">
                    <a href="#home" class="mobile-nav-link text-gray-900 dark:text-white hover:text-brand-blue dark:hover:text-brand-blue-light block px-3 py-2 rounded-md text-base font-medium theme-transition">Home</a>
                    <a href="#about" class="mobile-nav-link text-gray-500 dark:text-gray-400 hover:text-brand-blue dark:hover:text-brand-blue-light block px-3 py-2 rounded-md text-base font-medium theme-transition">About</a>
                    <a href="#services" class="mobile-nav-link text-gray-500 dark:text-gray-400 hover:text-brand-blue dark:hover:text-brand-blue-light block px-3 py-2 rounded-md text-base font-medium theme-transition">Services</a>
                    <a href="#clients" class="mobile-nav-link text-gray-500 dark:text-gray-400 hover:text-brand-blue dark:hover:text-brand-blue-light block px-3 py-2 rounded-md text-base font-medium theme-transition">Clients</a>
                    <a href="#contact" class="mobile-nav-link bg-gradient-to-r from-brand-blue to-brand-teal text-white hover:from-brand-blue-dark hover:to-brand-blue block px-3 py-2 rounded-md text-base font-medium">Contact</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section id="home" class="relative overflow-hidden bg-white dark:bg-gray-900 theme-transition">
        <div class="mx-auto max-w-7xl px-4 py-24 sm:px-6 sm:py-32 lg:px-8">
            <div class="text-center">
                <!-- Badges -->
                <div class="mx-auto mb-6 flex justify-center space-x-4">
                    <div class="inline-flex items-center rounded-full bg-brand-green/10 dark:bg-brand-green/20 px-3 py-1 text-sm font-medium text-brand-green-dark dark:text-brand-green-light ring-1 ring-inset ring-brand-green/20 dark:ring-brand-green/30">
                        <i class="fas fa-certificate mr-2"></i>
                        Government Licensed
                    </div>
                    <div class="inline-flex items-center rounded-full bg-brand-blue/10 dark:bg-brand-blue/20 px-3 py-1 text-sm font-medium text-brand-blue-dark dark:text-brand-blue-light ring-1 ring-inset ring-brand-blue/20 dark:ring-brand-blue/30">
                        <i class="fas fa-award mr-2"></i>
                        HR Consulting & Recruitment Agency
                    </div>
                </div>

                <!-- Main Heading -->
                <h1 class="mx-auto max-w-4xl text-5xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-6xl lg:text-7xl theme-transition">
                    <?php 
                    $companyName = $companyInfo['company_name'] ?? 'Sky Border Solutions';
                    $nameParts = explode(' ', $companyName);
                    if (count($nameParts) >= 2) {
                        echo htmlspecialchars(implode(' ', array_slice($nameParts, 0, -1))) . ' ';
                        echo '<span class="gradient-text">' . htmlspecialchars(end($nameParts)) . '</span>';
                    } else {
                        echo '<span class="gradient-text">' . htmlspecialchars($companyName) . '</span>';
                    }
                    ?>
                </h1>

                <!-- Tagline -->
                <p class="mx-auto mt-6 max-w-2xl text-xl leading-8 text-gray-600 dark:text-gray-300 theme-transition">
                    <?php echo htmlspecialchars($companyInfo['tagline'] ?? 'Where compliance meets competence'); ?>
                </p>

                <!-- Description -->
                <p class="mx-auto mt-4 max-w-3xl text-lg leading-8 text-gray-500 dark:text-gray-400 theme-transition">
                    <?php echo htmlspecialchars($companyInfo['description'] ?? 'Leading HR consultancy and recruitment firm in the Republic of Maldives, providing end-to-end manpower solutions with excellence and integrity.'); ?>
                </p>

                <!-- CTA Buttons -->
                <div class="mt-10 flex items-center justify-center gap-x-6">
                    <a href="#contact" class="rounded-lg bg-gradient-to-r from-brand-blue to-brand-teal px-6 py-3 text-sm font-semibold text-white shadow-lg hover:from-brand-blue-dark hover:to-brand-blue focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-blue transition-all duration-200 transform hover:scale-105">
                        <i class="fas fa-phone mr-2"></i>
                        Get Started
                    </a>
                    <a href="#services" class="rounded-lg bg-white dark:bg-gray-800 px-6 py-3 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 theme-transition">
                        <i class="fas fa-arrow-down mr-2"></i>
                        Our Services
                    </a>
                </div>

                <!-- Stats -->
                <div class="mx-auto mt-16 max-w-5xl">
                    <div class="grid grid-cols-1 gap-px bg-gray-900/5 dark:bg-white/5 sm:grid-cols-3">
                        <?php foreach ($stats as $stat): ?>
                        <div class="bg-white dark:bg-gray-800 px-4 py-6 text-center theme-transition">
                            <p class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-white"><?php echo htmlspecialchars($stat['stat_value']); ?></p>
                            <p class="text-sm font-medium leading-6 text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($stat['stat_label']); ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Scroll Indicator -->
                <div class="mt-16">
                    <a href="#about" class="inline-block text-gray-400 dark:text-gray-500 hover:text-brand-blue dark:hover:text-brand-blue-light transition-colors">
                        <i class="fas fa-chevron-down text-2xl animate-bounce"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-24 sm:py-32 bg-gray-50 dark:bg-gray-800 theme-transition">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="mx-auto max-w-2xl text-center mb-16">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl theme-transition">About Us</h2>
                <p class="mt-4 text-lg leading-8 text-gray-600 dark:text-gray-300 theme-transition">Leading HR solutions in the Maldives with government licensing and proven expertise</p>
            </div>

            <!-- About Content -->
            <div class="mx-auto max-w-5xl">
                <!-- Company Overview Card -->
                <div class="overflow-hidden bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-lg mb-8 theme-transition">
                    <div class="px-4 py-6 sm:px-6">
                        <h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-white theme-transition">Company Overview</h3>
                        <p class="mt-1 max-w-2xl text-sm leading-6 text-gray-500 dark:text-gray-400 theme-transition">Government-licensed HR consultancy in the Republic of Maldives</p>
                    </div>
                    <div class="border-t border-gray-100 dark:border-gray-700">
                        <dl class="divide-y divide-gray-100 dark:divide-gray-700">
                            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-white theme-transition">Mission</dt>
                                <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0 theme-transition">
                                    <?php echo htmlspecialchars($companyInfo['mission'] ?? 'To foster enduring partnerships with organizations by delivering superior recruitment solutions that align with their strategic goals.'); ?>
                                </dd>
                            </div>
                            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-white theme-transition">Vision</dt>
                                <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0 theme-transition">
                                    <?php echo htmlspecialchars($companyInfo['vision'] ?? 'To be the most trusted and recognized recruitment company in the Maldives, known for our professionalism, excellence and ability to deliver outstanding outcomes.'); ?>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Company Profile Download -->
                <div class="text-center">
                    <div class="overflow-hidden bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-lg theme-transition">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white mb-2 theme-transition">Company Profile</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 theme-transition">Download our comprehensive company profile to learn more about our services and expertise.</p>
                            <a href="http://skybordersolutions.com/profile.pdf" target="_blank" class="inline-flex items-center rounded-lg bg-gradient-to-r from-brand-green to-brand-green-light px-4 py-2 text-sm font-semibold text-white shadow-sm hover:from-brand-green-dark hover:to-brand-green transition-all duration-200 transform hover:scale-105">
                                <i class="fas fa-download mr-2"></i>
                                Download Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Clients Section -->
    <section id="clients" class="py-24 sm:py-32 bg-gray-50 dark:bg-gray-800 theme-transition">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="mx-auto max-w-2xl text-center mb-16">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl theme-transition">Our Clients</h2>
                <p class="mt-4 text-lg leading-8 text-gray-600 dark:text-gray-300 theme-transition">Trusted by leading organizations across the Maldives</p>
            </div>

            <!-- Client Categories -->
            <?php 
            $clientCategories = [];
            foreach ($clients as $client) {
                $category = $client['category'];
                if (!isset($clientCategories[$category])) {
                    $clientCategories[$category] = [];
                }
                $clientCategories[$category][] = $client;
            }
            ?>

            <?php if (!empty($clientCategories)): ?>
            <div class="space-y-12">
                <?php foreach ($clientCategories as $categoryName => $categoryClients): ?>
                <div>
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
                        <?php foreach ($categoryClients as $client): ?>
                        <div class="group relative overflow-hidden bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 rounded-lg hover:shadow-md hover:ring-2 hover:ring-brand-blue/20 dark:hover:ring-brand-blue-light/20 transition-all duration-200 theme-transition">
                            <div class="p-4">
                                <?php if (!empty($client['logo_url'])): ?>
                                <!-- Company Logo -->
                                <div class="flex h-16 w-full items-center justify-center mb-3 bg-gray-50 dark:bg-gray-800 rounded-md theme-transition">
                                    <img src="<?php echo htmlspecialchars($client['logo_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($client['name']); ?>" 
                                         class="h-12 w-auto object-contain">
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
                                        <?php echo htmlspecialchars($client['name']); ?>
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
            <div class="text-center py-12">
                <div class="mx-auto max-w-md">
                    <i class="fas fa-users text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2 theme-transition">No Clients Yet</h3>
                    <p class="text-gray-600 dark:text-gray-400 theme-transition">Client information will be displayed here once added through the admin panel.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-24 sm:py-32 bg-white dark:bg-gray-900 theme-transition">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="mx-auto max-w-2xl text-center mb-16">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl theme-transition">Contact Us</h2>
                <p class="mt-4 text-lg leading-8 text-gray-600 dark:text-gray-300 theme-transition">Ready to partner with us? Get in touch today and let's discuss how we can support your workforce needs.</p>
            </div>

            <!-- Contact Content -->
            <div class="mx-auto max-w-5xl grid grid-cols-1 gap-12 lg:grid-cols-2">
                <!-- Contact Information -->
                <div class="overflow-hidden bg-gray-50 dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-lg theme-transition">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-8 theme-transition">Get in Touch</h3>
                        
                        <div class="space-y-6">
                            <!-- Phone -->
                            <div class="flex items-start space-x-4">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-blue/10 dark:bg-brand-blue/20">
                                    <i class="fas fa-phone text-brand-blue dark:text-brand-blue-light"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white theme-transition">Phone</h4>
                                    <a href="tel:<?php echo str_replace([' ', '-'], '', $companyInfo['phone'] ?? '+9604000444'); ?>" class="text-brand-blue dark:text-brand-blue-light hover:text-brand-blue-dark dark:hover:text-brand-blue theme-transition"><?php echo htmlspecialchars($companyInfo['phone'] ?? '+960 4000-444'); ?></a>
                                </div>
                            </div>

                            <!-- Hotlines -->
                            <?php if (!empty($companyInfo['hotline1']) || !empty($companyInfo['hotline2'])): ?>
                            <div class="flex items-start space-x-4">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-teal/10 dark:bg-brand-teal/20">
                                    <i class="fas fa-mobile-alt text-brand-teal"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white theme-transition">Hotline</h4>
                                    <div class="text-brand-teal">
                                        <?php if (!empty($companyInfo['hotline1'])): ?>
                                        <a href="tel:<?php echo str_replace([' ', '-'], '', $companyInfo['hotline1']); ?>" class="hover:text-brand-blue theme-transition"><?php echo htmlspecialchars($companyInfo['hotline1']); ?></a>
                                        <?php endif; ?>
                                        <?php if (!empty($companyInfo['hotline1']) && !empty($companyInfo['hotline2'])): ?> / <?php endif; ?>
                                        <?php if (!empty($companyInfo['hotline2'])): ?>
                                        <a href="tel:<?php echo str_replace([' ', '-'], '', $companyInfo['hotline2']); ?>" class="hover:text-brand-blue theme-transition"><?php echo htmlspecialchars($companyInfo['hotline2']); ?></a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Email -->
                            <div class="flex items-start space-x-4">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-green/10 dark:bg-brand-green/20">
                                    <i class="fas fa-envelope text-brand-green dark:text-brand-green-light"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white theme-transition">Email</h4>
                                    <a href="mailto:<?php echo htmlspecialchars($companyInfo['email'] ?? 'info@skybordersolutions.com'); ?>" class="text-brand-green dark:text-brand-green-light hover:text-brand-green-dark dark:hover:text-brand-green theme-transition"><?php echo htmlspecialchars($companyInfo['email'] ?? 'info@skybordersolutions.com'); ?></a>
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="flex items-start space-x-4">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-100 dark:bg-purple-900/20">
                                    <i class="fas fa-map-marker-alt text-purple-600 dark:text-purple-400"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white theme-transition">Address</h4>
                                    <p class="text-gray-600 dark:text-gray-400 theme-transition"><?php echo nl2br(htmlspecialchars($companyInfo['address'] ?? 'H. Dhoorihaa (5A), Kalaafaanu Hingun\nMale\' City, Republic of Maldives')); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Business Hours -->
                        <?php if (!empty($companyInfo['business_hours'])): ?>
                        <div class="mt-8 rounded-xl bg-gray-100 dark:bg-gray-700 p-4 theme-transition">
                            <h4 class="flex items-center space-x-2 font-medium text-gray-900 dark:text-white mb-3 theme-transition">
                                <i class="fas fa-clock text-brand-blue dark:text-brand-blue-light"></i>
                                <span>Business Hours</span>
                            </h4>
                            <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1 theme-transition">
                                <?php echo nl2br(htmlspecialchars($companyInfo['business_hours'])); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="overflow-hidden bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-lg theme-transition">
                    <div class="px-4 py-5 sm:p-6">
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
                                    <i class="fas fa-exclamation-circle text-red-400 dark:text-red-300"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-800 dark:text-red-300"><?php echo htmlspecialchars($contactError); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <form class="contact-form space-y-6" method="POST">
                            <input type="hidden" name="contact_form" value="1">
                            
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 theme-transition">Full Name</label>
                                <input type="text" id="name" name="name" required 
                                       class="w-full rounded-lg border-0 py-1.5 text-gray-900 dark:text-white bg-white dark:bg-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-2 focus:ring-inset focus:ring-brand-blue sm:text-sm sm:leading-6 theme-transition"
                                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 theme-transition">Email Address</label>
                                <input type="email" id="email" name="email" required 
                                       class="w-full rounded-lg border-0 py-1.5 text-gray-900 dark:text-white bg-white dark:bg-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-2 focus:ring-inset focus:ring-brand-blue sm:text-sm sm:leading-6 theme-transition"
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            </div>
                            
                            <div>
                                <label for="company" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 theme-transition">Company Name</label>
                                <input type="text" id="company" name="company" 
                                       class="w-full rounded-lg border-0 py-1.5 text-gray-900 dark:text-white bg-white dark:bg-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-2 focus:ring-inset focus:ring-brand-blue sm:text-sm sm:leading-6 theme-transition"
                                       value="<?php echo htmlspecialchars($_POST['company'] ?? ''); ?>">
                            </div>
                            
                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 theme-transition">Message</label>
                                <textarea id="message" name="message" rows="4" required 
                                          class="w-full rounded-lg border-0 py-1.5 text-gray-900 dark:text-white bg-white dark:bg-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-2 focus:ring-inset focus:ring-brand-blue sm:text-sm sm:leading-6 theme-transition"><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                            </div>
                            
                            <button type="submit" class="w-full rounded-lg bg-gradient-to-r from-brand-blue to-brand-teal px-6 py-3 text-sm font-semibold text-white hover:from-brand-blue-dark hover:to-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-blue focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all duration-200 transform hover:scale-105 shadow-lg">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 dark:bg-gray-950 theme-transition">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">
                <!-- Company Info -->
                <div class="lg:col-span-2">
                    <div class="flex items-center space-x-3 mb-4">
                        <img src="images/logo.svg" alt="<?php echo htmlspecialchars($companyInfo['company_name'] ?? 'Sky Border Solutions'); ?>" class="h-10 w-auto filter brightness-0 invert">
                    </div>
                    <p class="text-gray-400 mb-6">
                        <?php echo htmlspecialchars($companyInfo['description'] ?? 'Government-licensed HR consultancy and recruitment firm providing comprehensive workforce solutions across the Maldives.'); ?>
                    </p>
                    <div class="inline-flex items-center space-x-2 rounded-full bg-brand-green/10 px-3 py-1 text-sm font-medium text-brand-green-light">
                        <i class="fas fa-certificate"></i>
                        <span>Licensed & Compliant</span>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-400 mb-4">Quick Links</h3>
                    <div class="space-y-2">
                        <a href="#home" class="block text-gray-300 hover:text-brand-blue-light transition-colors">Home</a>
                        <a href="#about" class="block text-gray-300 hover:text-brand-blue-light transition-colors">About Us</a>
                        <a href="#services" class="block text-gray-300 hover:text-brand-blue-light transition-colors">Services</a>
                        <a href="#clients" class="block text-gray-300 hover:text-brand-blue-light transition-colors">Clients</a>
                        <a href="#contact" class="block text-gray-300 hover:text-brand-blue-light transition-colors">Contact</a>
                        <a href="admin/" class="block text-gray-300 hover:text-brand-teal transition-colors">
                            <i class="fas fa-lock mr-1"></i>Admin
                        </a>
                    </div>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-400 mb-4">Contact</h3>
                    <div class="space-y-2 text-gray-300">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-phone text-brand-blue-light w-4"></i>
                            <span><?php echo htmlspecialchars($companyInfo['phone'] ?? '+960 4000-444'); ?></span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-envelope text-brand-teal w-4"></i>
                            <span><?php echo htmlspecialchars($companyInfo['email'] ?? 'info@skybordersolutions.com'); ?></span>
                        </div>
                        <div class="flex items-start space-x-2">
                            <i class="fas fa-map-marker-alt text-brand-green-light w-4 mt-1"></i>
                            <span class="text-sm">Male' City, Maldives</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Footer -->
            <div class="mt-12 border-t border-gray-800 pt-8 flex flex-col sm:flex-row items-center justify-between">
                <p class="text-gray-400 text-sm">
                    &copy; <span id="year"></span> <?php echo htmlspecialchars($companyInfo['company_name'] ?? 'Sky Border Solutions'); ?>. All rights reserved.
                </p>
                <div class="mt-4 sm:mt-0 flex space-x-6">
                    <a href="#" class="text-gray-400 hover:text-brand-blue-light text-sm transition-colors">Privacy Policy</a>
                    <a href="#" class="text-gray-400 hover:text-brand-blue-light text-sm transition-colors">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Dark mode functionality
        function initTheme() {
            const darkMode = localStorage.getItem('darkMode') === 'true' || 
                           (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches);
            
            if (darkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }

        function toggleTheme() {
            const isDark = document.documentElement.classList.contains('dark');
            
            if (isDark) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('darkMode', 'false');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('darkMode', 'true');
            }
        }

        // Initialize theme and interactions
        document.addEventListener('DOMContentLoaded', function() {
            initTheme();
            
            // Theme toggle button
            document.getElementById('theme-toggle').addEventListener('click', toggleTheme);
            
            // Update footer year
            document.getElementById('year').textContent = new Date().getFullYear();

            // Mobile menu toggle
            const mobileMenuButton = document.querySelector('.mobile-menu-button');
            const mobileMenu = document.querySelector('.mobile-menu');
            
            mobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });

            // Navigation functionality
            document.querySelectorAll('.nav-link, .mobile-nav-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href').substring(1);
                    const targetSection = document.getElementById(targetId);
                    
                    if (targetSection) {
                        targetSection.scrollIntoView({ behavior: 'smooth' });
                        
                        // Close mobile menu if open
                        mobileMenu.classList.add('hidden');
                    }
                });
            });
        });
        
        // Listen for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
            if (!localStorage.getItem('darkMode')) {
                if (e.matches) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            }
        });

        // Smooth scroll behavior for all internal links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
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
