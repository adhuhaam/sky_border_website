<?php
require_once 'classes/Auth.php';
require_once 'classes/ContentManager.php';

$auth = new Auth();
$auth->requireLogin();

$contentManager = new ContentManager();
$user = $auth->getCurrentUser();

// Get dashboard stats
$stats = $contentManager->getStatistics();
$recentMessages = $contentManager->getContactMessages('new', 5);
$teamCount = count($contentManager->getTeamMembers());
$clientCount = count($contentManager->getClients());
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Sky Border Solutions</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="h-full">
    <div class="min-h-full">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 flex items-center space-x-3">
                            <img src="../images/logo.svg" alt="Sky Border Solutions" class="h-10 w-auto">
                            <div>
                                <p class="text-xs text-gray-500">Admin Panel</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- User Menu -->
                        <div class="relative">
                            <div class="flex items-center space-x-3">
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($user['name']); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo htmlspecialchars($user['role']); ?></p>
                                </div>
                                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <i class="fas fa-user text-indigo-600"></i>
                                </div>
                            </div>
                        </div>
                        
                        <a href="logout.php" class="text-gray-400 hover:text-gray-500 transition-colors" title="Logout">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <div class="flex">
            <!-- Sidebar -->
            <div class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0 pt-16">
                <div class="flex-1 flex flex-col min-h-0 bg-white border-r border-gray-200">
                    <div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
                        <nav class="mt-5 flex-1 px-2 space-y-1">
                            <a href="dashboard.php" class="bg-indigo-50 text-indigo-700 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <i class="fas fa-tachometer-alt text-indigo-500 mr-3"></i>
                                Dashboard
                            </a>
                            
                            <a href="company-info.php" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <i class="fas fa-building text-gray-400 mr-3"></i>
                                Company Info
                            </a>
                            
                            <a href="statistics.php" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <i class="fas fa-chart-bar text-gray-400 mr-3"></i>
                                Statistics
                            </a>
                            
                            <a href="team.php" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <i class="fas fa-users text-gray-400 mr-3"></i>
                                Team Members
                            </a>
                            
                            <a href="services.php" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <i class="fas fa-cogs text-gray-400 mr-3"></i>
                                Services
                            </a>
                            
                            <a href="portfolio.php" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <i class="fas fa-briefcase text-gray-400 mr-3"></i>
                                Portfolio
                            </a>
                            
                            <a href="clients.php" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <i class="fas fa-handshake text-gray-400 mr-3"></i>
                                Clients
                            </a>
                            
                            <a href="messages.php" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <i class="fas fa-envelope text-gray-400 mr-3"></i>
                                Messages
                                <?php if (count($recentMessages) > 0): ?>
                                <span class="ml-auto bg-red-100 text-red-800 text-xs font-medium px-2 py-0.5 rounded-full"><?php echo count($recentMessages); ?></span>
                                <?php endif; ?>
                            </a>
                            
                            <a href="settings.php" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <i class="fas fa-cog text-gray-400 mr-3"></i>
                                Settings
                            </a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="md:pl-64 flex flex-col flex-1">
                <main class="flex-1">
                    <div class="py-6">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                            <!-- Dashboard Header -->
                            <div class="mb-8">
                                <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                                <p class="mt-1 text-sm text-gray-600">Welcome back, <?php echo htmlspecialchars($user['name']); ?>! Here's what's happening with Sky Border Solutions.</p>
                            </div>

                            <!-- Stats Grid -->
                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                                <!-- Website Stats -->
                                <?php foreach ($stats as $stat): ?>
                                <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                                    <div class="p-5">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                                    <i class="fas fa-chart-line text-indigo-600"></i>
                                                </div>
                                            </div>
                                            <div class="ml-5 w-0 flex-1">
                                                <dl>
                                                    <dt class="text-sm font-medium text-gray-500 truncate"><?php echo htmlspecialchars($stat['stat_label']); ?></dt>
                                                    <dd class="text-lg font-medium text-gray-900"><?php echo htmlspecialchars($stat['stat_value']); ?></dd>
                                                </dl>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>

                                <!-- Admin Stats -->
                                <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                                    <div class="p-5">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                                    <i class="fas fa-users text-green-600"></i>
                                                </div>
                                            </div>
                                            <div class="ml-5 w-0 flex-1">
                                                <dl>
                                                    <dt class="text-sm font-medium text-gray-500 truncate">Team Members</dt>
                                                    <dd class="text-lg font-medium text-gray-900"><?php echo $teamCount; ?></dd>
                                                </dl>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Activity Grid -->
                            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                                <!-- Recent Contact Messages -->
                                <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                                    <div class="px-4 py-5 sm:p-6">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Recent Contact Messages</h3>
                                        <?php if (empty($recentMessages)): ?>
                                            <p class="text-gray-500 text-sm">No new messages</p>
                                        <?php else: ?>
                                            <div class="space-y-3">
                                                <?php foreach ($recentMessages as $message): ?>
                                                <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                                                    <div class="flex-shrink-0">
                                                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                            <i class="fas fa-envelope text-blue-600 text-sm"></i>
                                                        </div>
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($message['name']); ?></p>
                                                        <p class="text-sm text-gray-500"><?php echo htmlspecialchars($message['email']); ?></p>
                                                        <p class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars(substr($message['message'], 0, 100)) . '...'; ?></p>
                                                        <p class="text-xs text-gray-400 mt-1"><?php echo date('M j, Y g:i A', strtotime($message['created_at'])); ?></p>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <div class="mt-4">
                                                <a href="messages.php" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                                    View all messages <i class="fas fa-arrow-right ml-1"></i>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Quick Actions -->
                                <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                                    <div class="px-4 py-5 sm:p-6">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Quick Actions</h3>
                                        <div class="grid grid-cols-2 gap-4">
                                            <a href="company-info.php" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center mb-2">
                                                    <i class="fas fa-building text-indigo-600"></i>
                                                </div>
                                                <span class="text-sm font-medium text-gray-900">Company Info</span>
                                            </a>
                                            
                                            <a href="team.php" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                                <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center mb-2">
                                                    <i class="fas fa-users text-green-600"></i>
                                                </div>
                                                <span class="text-sm font-medium text-gray-900">Team</span>
                                            </a>
                                            
                                            <a href="services.php" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                                <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center mb-2">
                                                    <i class="fas fa-cogs text-purple-600"></i>
                                                </div>
                                                <span class="text-sm font-medium text-gray-900">Services</span>
                                            </a>
                                            
                                            <a href="clients.php" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                                <div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center mb-2">
                                                    <i class="fas fa-handshake text-yellow-600"></i>
                                                </div>
                                                <span class="text-sm font-medium text-gray-900">Clients</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <!-- Mobile menu button (you can add mobile menu functionality here) -->
    <script>
        // Auto-refresh dashboard every 5 minutes
        setTimeout(function() {
            window.location.reload();
        }, 300000);
        
        // Add active states and interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Add hover effects and interactions here
            console.log('Sky Border Solutions Admin Dashboard Loaded');
        });
    </script>
</body>
</html>
