<?php
/**
 * Admin Dashboard with Sidebar
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
    <div class="flex h-screen">
        <!-- Include Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main content area -->
        <div class="flex-1 md:pl-64">
            <div class="flex flex-col h-full">
                <!-- Main content -->
                <main class="flex-1 relative overflow-y-auto focus:outline-none">
                    <div class="py-6">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                            <!-- Page header -->
                            <div class="mb-8">
                                <h1 class="text-2xl font-bold text-gray-900 dark:text-white theme-transition">Dashboard</h1>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 theme-transition">
                                    Welcome back, <?php echo htmlspecialchars($currentUser['full_name']); ?>! Here's what's happening with your website.
                                </p>
                            </div>

                            <!-- Statistics Cards -->
                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                                <!-- Company Info -->
                                <div class="modern-card overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 py-5 shadow theme-transition">
                                    <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Company Status</dt>
                                    <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">
                                        <?php echo $companyInfo ? 'Active' : 'Setup Needed'; ?>
                                    </dd>
                                    <div class="mt-2 flex items-center text-sm text-gray-600 dark:text-gray-300">
                                        <i class="fas fa-building text-brand-blue mr-1"></i>
                                        Company profile
                                    </div>
                                </div>

                                <!-- Services -->
                                <div class="modern-card overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 py-5 shadow theme-transition">
                                    <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Total Services</dt>
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
                            <div class="mb-8">
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
                                </div>
                            </div>

                            <!-- Recent Messages -->
                            <?php if (!empty($recentMessages)): ?>
                            <div class="mb-8">
                                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4 theme-transition">Recent Messages</h2>
                                <div class="modern-card bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-md theme-transition">
                                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                                        <?php foreach ($recentMessages as $message): ?>
                                        <li>
                                            <a href="messages.php?id=<?php echo $message['id']; ?>" class="block hover:bg-gray-50 dark:hover:bg-gray-700 px-4 py-4 sm:px-6 theme-transition">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0">
                                                            <div class="h-8 w-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                                <i class="fas fa-user text-gray-600 dark:text-gray-300 text-sm"></i>
                                                            </div>
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900 dark:text-white theme-transition">
                                                                <?php echo htmlspecialchars($message['name'] ?? 'Unknown'); ?>
                                                            </div>
                                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                                <?php echo htmlspecialchars(substr($message['message'] ?? '', 0, 60)); ?>...
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="text-sm text-gray-400 dark:text-gray-500">
                                                        <?php echo date('M j', strtotime($message['created_at'] ?? 'now')); ?>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 text-right sm:px-6">
                                        <a href="messages.php" class="text-sm font-medium text-brand-blue hover:text-brand-blue-dark theme-transition">
                                            View all messages <i class="fas fa-arrow-right ml-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Website Statistics -->
                            <?php if ($statistics): ?>
                            <div class="mb-8">
                                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4 theme-transition">Website Statistics</h2>
                                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                                    <?php foreach ($statistics as $stat): ?>
                                    <div class="modern-card overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 py-5 shadow theme-transition">
                                        <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($stat['label'] ?? ''); ?></dt>
                                        <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">
                                            <?php echo htmlspecialchars($stat['value'] ?? '0'); ?>
                                        </dd>
                                        <?php if (isset($stat['description'])): ?>
                                        <div class="mt-2 flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <i class="fas fa-chart-line text-green-500 mr-1"></i>
                                            <?php echo htmlspecialchars($stat['description']); ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
</body>
</html>
