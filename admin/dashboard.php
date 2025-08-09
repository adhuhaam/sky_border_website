<?php
/**
 * Admin Dashboard
 * Sky Border Solutions CMS
 */

require_once 'classes/Auth.php';
require_once 'classes/ContentManager.php';

$auth = new Auth();
$auth->requireLogin();

$contentManager = new ContentManager();
$currentUser = $auth->getCurrentUser();

// Get dashboard statistics
$companyInfo = $contentManager->getCompanyInfo();
$statistics = $contentManager->getStatistics();
$recentMessages = $contentManager->getContactMessages('new', 5);
$totalClients = count($contentManager->getClients());
$totalServices = count($contentManager->getServiceCategories());
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sky Border Solutions CMS</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .theme-transition { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
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
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
    </style>
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
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
<body class="h-full bg-gray-50 dark:bg-gray-900 theme-transition">
    <!-- Dark Mode Toggle -->
    <div class="fixed top-4 right-4 z-50">
        <button id="theme-toggle" class="p-2 rounded-lg bg-white dark:bg-gray-800 shadow-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 theme-transition">
            <i id="theme-icon" class="fas fa-moon dark:hidden"></i>
            <i id="theme-icon-dark" class="fas fa-sun hidden dark:block"></i>
        </button>
    </div>

    <div class="min-h-full">
        <!-- Navigation -->
        <nav class="bg-white dark:bg-gray-800 shadow-sm theme-transition">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between">
                    <div class="flex">
                        <div class="flex flex-shrink-0 items-center">
                            <img src="../images/logo.svg" alt="Sky Border Solutions" class="h-8 w-auto">
                            <span class="ml-3 text-xl font-bold text-gray-900 dark:text-white theme-transition">CMS Dashboard</span>
                        </div>
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <a href="dashboard.php" class="border-brand-blue text-gray-900 dark:text-white inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium theme-transition">
                                <i class="fas fa-tachometer-alt mr-2"></i>
                                Dashboard
                            </a>
                            <a href="company-info.php" class="border-transparent text-gray-500 dark:text-gray-300 hover:border-gray-300 hover:text-gray-700 dark:hover:text-white inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium theme-transition">
                                <i class="fas fa-building mr-2"></i>
                                Company Info
                            </a>
                            <a href="services.php" class="border-transparent text-gray-500 dark:text-gray-300 hover:border-gray-300 hover:text-gray-700 dark:hover:text-white inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium theme-transition">
                                <i class="fas fa-cogs mr-2"></i>
                                Services
                            </a>
                            <a href="clients.php" class="border-transparent text-gray-500 dark:text-gray-300 hover:border-gray-300 hover:text-gray-700 dark:hover:text-white inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium theme-transition">
                                <i class="fas fa-users mr-2"></i>
                                Clients
                            </a>
                            <a href="messages.php" class="border-transparent text-gray-500 dark:text-gray-300 hover:border-gray-300 hover:text-gray-700 dark:hover:text-white inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium theme-transition">
                                <i class="fas fa-envelope mr-2"></i>
                                Messages
                                <?php if (count($recentMessages) > 0): ?>
                                <span class="ml-1 bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full"><?php echo count($recentMessages); ?></span>
                                <?php endif; ?>
                            </a>
                        </div>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:items-center">
                        <div class="relative ml-3">
                            <div class="flex items-center space-x-4">
                                <span class="text-sm text-gray-700 dark:text-gray-300 theme-transition">
                                    Welcome, <?php echo htmlspecialchars($currentUser['full_name']); ?>
                                </span>
                                <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-sign-out-alt mr-1"></i>
                                    Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="py-10">
            <header>
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <h1 class="text-3xl font-bold leading-tight tracking-tight text-gray-900 dark:text-white theme-transition">Dashboard</h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 theme-transition">
                        Manage your website content and monitor activity
                    </p>
                </div>
            </header>
            
            <main>
                <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <!-- Stats Cards -->
                    <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        <!-- Website Stats -->
                        <div class="modern-card overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 py-5 shadow theme-transition">
                            <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Total Statistics</dt>
                            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white"><?php echo count($statistics); ?></dd>
                            <div class="mt-2 flex items-center text-sm text-gray-600 dark:text-gray-300">
                                <i class="fas fa-chart-bar text-brand-blue mr-1"></i>
                                Website metrics
                            </div>
                        </div>

                        <!-- Services -->
                        <div class="modern-card overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 py-5 shadow theme-transition">
                            <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Active Services</dt>
                            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white"><?php echo $totalServices; ?></dd>
                            <div class="mt-2 flex items-center text-sm text-gray-600 dark:text-gray-300">
                                <i class="fas fa-cogs text-brand-teal mr-1"></i>
                                Service categories
                            </div>
                        </div>

                        <!-- Clients -->
                        <div class="modern-card overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 py-5 shadow theme-transition">
                            <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Total Clients</dt>
                            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white"><?php echo $totalClients; ?></dd>
                            <div class="mt-2 flex items-center text-sm text-gray-600 dark:text-gray-300">
                                <i class="fas fa-users text-brand-green mr-1"></i>
                                Active clients
                            </div>
                        </div>

                        <!-- Messages -->
                        <div class="modern-card overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 py-5 shadow theme-transition">
                            <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">New Messages</dt>
                            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white"><?php echo count($recentMessages); ?></dd>
                            <div class="mt-2 flex items-center text-sm text-gray-600 dark:text-gray-300">
                                <i class="fas fa-envelope text-orange-500 mr-1"></i>
                                Unread messages
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="mt-8">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4 theme-transition">Quick Actions</h2>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
                            <a href="company-info.php" class="modern-card group block rounded-lg bg-white dark:bg-gray-800 p-6 hover:shadow-md theme-transition">
                                <div class="flex items-center">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-brand-blue to-brand-teal">
                                        <i class="fas fa-building text-white"></i>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-brand-blue theme-transition">Edit Company Info</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Update company details</p>
                                    </div>
                                </div>
                            </a>

                            <a href="services.php" class="modern-card group block rounded-lg bg-white dark:bg-gray-800 p-6 hover:shadow-md theme-transition">
                                <div class="flex items-center">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-brand-teal to-brand-green">
                                        <i class="fas fa-cogs text-white"></i>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-brand-teal theme-transition">Manage Services</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Add or edit services</p>
                                    </div>
                                </div>
                            </a>

                            <a href="industries.php" class="modern-card group block rounded-lg bg-white dark:bg-gray-800 p-6 hover:shadow-md theme-transition">
                                <div class="flex items-center">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-amber-500 to-orange-500">
                                        <i class="fas fa-industry text-white"></i>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-amber-600 theme-transition">Manage Industries</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Industry categories</p>
                                    </div>
                                </div>
                            </a>

                            <a href="positions.php" class="modern-card group block rounded-lg bg-white dark:bg-gray-800 p-6 hover:shadow-md theme-transition">
                                <div class="flex items-center">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-indigo-500 to-purple-500">
                                        <i class="fas fa-user-tie text-white"></i>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-indigo-600 theme-transition">Job Positions</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Manage job positions</p>
                                    </div>
                                </div>
                            </a>

                            <a href="clients.php" class="modern-card group block rounded-lg bg-white dark:bg-gray-800 p-6 hover:shadow-md theme-transition">
                                <div class="flex items-center">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-brand-green to-brand-blue">
                                        <i class="fas fa-users text-white"></i>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-brand-green theme-transition">Manage Clients</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Add or edit clients</p>
                                    </div>
                                </div>
                            </a>

                            <a href="messages.php" class="modern-card group block rounded-lg bg-white dark:bg-gray-800 p-6 hover:shadow-md theme-transition">
                                <div class="flex items-center">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-orange-400 to-red-500">
                                        <i class="fas fa-envelope text-white"></i>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-orange-500 theme-transition">View Messages</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Check contact messages</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Recent Messages -->
                    <?php if (!empty($recentMessages)): ?>
                    <div class="mt-8">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4 theme-transition">Recent Messages</h2>
                        <div class="modern-card overflow-hidden bg-white dark:bg-gray-800 shadow theme-transition">
                            <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                                <?php foreach ($recentMessages as $message): ?>
                                <li class="px-6 py-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-blue/10">
                                                <i class="fas fa-user text-brand-blue"></i>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white theme-transition">
                                                    <?php echo htmlspecialchars($message['name']); ?>
                                                </p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400 theme-transition">
                                                    <?php echo htmlspecialchars($message['email']); ?>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-gray-900 dark:text-white theme-transition">
                                                <?php echo date('M j, Y', strtotime($message['created_at'])); ?>
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 theme-transition">
                                                <?php echo date('g:i A', strtotime($message['created_at'])); ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-600 dark:text-gray-300 theme-transition">
                                            <?php echo htmlspecialchars(substr($message['message'], 0, 100)) . (strlen($message['message']) > 100 ? '...' : ''); ?>
                                        </p>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-3 theme-transition">
                                <a href="messages.php" class="text-sm font-medium text-brand-blue hover:text-brand-blue-dark theme-transition">
                                    View all messages →
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- System Status -->
                    <div class="mt-8">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4 theme-transition">System Status</h2>
                        <div class="modern-card bg-white dark:bg-gray-800 p-6 theme-transition">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                <div class="text-center">
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/20">
                                        <i class="fas fa-database text-green-600 dark:text-green-400"></i>
                                    </div>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white theme-transition">Database</h3>
                                    <p class="text-xs text-green-600 dark:text-green-400">Connected</p>
                                </div>
                                
                                <div class="text-center">
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/20">
                                        <i class="fas fa-globe text-green-600 dark:text-green-400"></i>
                                    </div>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white theme-transition">Website</h3>
                                    <p class="text-xs text-green-600 dark:text-green-400">Online</p>
                                </div>
                                
                                <div class="text-center">
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/20">
                                        <i class="fas fa-shield-alt text-green-600 dark:text-green-400"></i>
                                    </div>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white theme-transition">Security</h3>
                                    <p class="text-xs text-green-600 dark:text-green-400">Protected</p>
                                </div>
                            </div>
                            
                            <div class="mt-6 text-center">
                                <a href="../check-setup.php" target="_blank" class="text-sm text-brand-blue hover:text-brand-blue-dark theme-transition">
                                    <i class="fas fa-external-link-alt mr-1"></i>
                                    View Detailed System Status
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Dark mode functionality
        function initTheme() {
            const darkMode = localStorage.getItem('darkMode') === 'true' || 
                           (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches);
            
            if (darkMode) {
                document.documentElement.classList.add('dark');
            }
        }

        function toggleTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('darkMode', isDark);
        }

        // Initialize theme on page load
        initTheme();

        // Theme toggle button
        document.getElementById('theme-toggle').addEventListener('click', toggleTheme);
    </script>
</body>
</html>