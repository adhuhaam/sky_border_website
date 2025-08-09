<?php
/**
 * Admin Sidebar Navigation Component
 * Sky Border Solutions CMS
 */

// Get current page for active navigation highlighting
$currentPage = basename($_SERVER['PHP_SELF']);
$currentPath = $_SERVER['REQUEST_URI'];

// Navigation items
$navItems = [
    [
        'name' => 'Dashboard',
        'url' => 'dashboard.php',
        'icon' => 'fas fa-tachometer-alt',
        'active' => $currentPage === 'dashboard.php'
    ],
    [
        'name' => 'Company Info',
        'url' => 'company-info.php',
        'icon' => 'fas fa-building',
        'active' => $currentPage === 'company-info.php'
    ],
    [
        'name' => 'Services',
        'url' => 'services.php',
        'icon' => 'fas fa-cogs',
        'active' => $currentPage === 'services.php'
    ],
    [
        'name' => 'Industries',
        'url' => 'industries.php',
        'icon' => 'fas fa-industry',
        'active' => $currentPage === 'industries.php'
    ],
    [
        'name' => 'Job Positions',
        'url' => 'positions.php',
        'icon' => 'fas fa-user-tie',
        'active' => $currentPage === 'positions.php'
    ],
    [
        'name' => 'Clients',
        'url' => 'clients.php',
        'icon' => 'fas fa-users',
        'active' => $currentPage === 'clients.php'
    ],
    [
        'name' => 'Messages',
        'url' => 'messages.php',
        'icon' => 'fas fa-envelope',
        'active' => $currentPage === 'messages.php'
    ]
];

// Get unread messages count for badge
try {
    require_once __DIR__ . '/../classes/ContentManager.php';
    $contentManager = new ContentManager();
    $unreadCount = count($contentManager->getContactMessages('new', 50));
} catch (Exception $e) {
    $unreadCount = 0;
}
?>

<!-- Sidebar -->
<div class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0">
    <div class="flex flex-col flex-grow pt-5 bg-white dark:bg-gray-800 overflow-y-auto border-r border-gray-200 dark:border-gray-700 theme-transition">
        <!-- Logo and Brand -->
        <div class="flex items-center flex-shrink-0 px-4 pb-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center w-full sidebar-logo-container">
                <div class="flex-shrink-0">
                    <img class="h-12 w-auto drop-shadow-sm" src="../images/logo.svg" alt="Sky Border Solutions" 
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <!-- Fallback logo if image fails to load -->
                    <div class="h-12 w-12 bg-gradient-to-r from-brand-blue to-brand-teal rounded-xl flex items-center justify-center shadow-lg" style="display: none;">
                        <span class="text-white font-bold text-lg">SBS</span>
                    </div>
                </div>
                <div class="ml-4 flex-1 min-w-0">
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white theme-transition truncate">
                        Sky Border
                    </h1>
                    <p class="text-sm font-semibold gradient-text">Admin Panel</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Content Management System</p>
                </div>
            </div>
        </div>
        
        <!-- Navigation -->
        <nav class="mt-8 flex-1 px-2 space-y-1">
            <?php foreach ($navItems as $item): ?>
            <a href="<?php echo $item['url']; ?>" 
               class="<?php echo $item['active'] ? 'bg-gradient-to-r from-brand-blue to-brand-teal text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white'; ?> group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200">
                <i class="<?php echo $item['icon']; ?> <?php echo $item['active'] ? 'text-white' : 'text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300'; ?> flex-shrink-0 mr-3 text-base"></i>
                <?php echo $item['name']; ?>
                
                <?php if ($item['name'] === 'Messages' && $unreadCount > 0): ?>
                <span class="ml-auto inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                    <?php echo $unreadCount; ?>
                </span>
                <?php endif; ?>
            </a>
            <?php endforeach; ?>
        </nav>
        
        <!-- Bottom Section -->
        <div class="flex-shrink-0 flex border-t border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center w-full">
                <div class="flex-shrink-0">
                    <div class="h-8 w-8 rounded-full bg-gradient-to-r from-brand-blue to-brand-teal flex items-center justify-center">
                        <i class="fas fa-user text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-200 theme-transition">
                        <?php echo htmlspecialchars($currentUser['full_name'] ?? 'Admin User'); ?>
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Administrator</p>
                </div>
                <div class="ml-3">
                    <a href="logout.php" class="text-gray-400 hover:text-red-500 transition-colors" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile menu -->
<div class="md:hidden">
    <div class="fixed inset-0 flex z-40 hidden" id="mobile-menu">
        <div class="fixed inset-0 bg-gray-600 bg-opacity-75" id="mobile-menu-overlay"></div>
        <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white dark:bg-gray-800 theme-transition">
            <div class="absolute top-0 right-0 -mr-12 pt-2">
                <button type="button" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" id="close-mobile-menu">
                    <span class="sr-only">Close sidebar</span>
                    <i class="fas fa-times text-white text-lg"></i>
                </button>
            </div>
            
            <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                <div class="flex-shrink-0 flex items-center px-4">
                    <img class="h-8 w-auto" src="../images/logo.svg" alt="Sky Border Solutions">
                    <div class="ml-3">
                        <h1 class="text-lg font-bold text-gray-900 dark:text-white theme-transition">CMS Admin</h1>
                    </div>
                </div>
                <nav class="mt-5 px-2 space-y-1">
                    <?php foreach ($navItems as $item): ?>
                    <a href="<?php echo $item['url']; ?>" 
                       class="<?php echo $item['active'] ? 'bg-gradient-to-r from-brand-blue to-brand-teal text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'; ?> group flex items-center px-2 py-2 text-base font-medium rounded-md">
                        <i class="<?php echo $item['icon']; ?> <?php echo $item['active'] ? 'text-white' : 'text-gray-400'; ?> mr-4 flex-shrink-0 text-lg"></i>
                        <?php echo $item['name']; ?>
                        <?php if ($item['name'] === 'Messages' && $unreadCount > 0): ?>
                        <span class="ml-auto inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <?php echo $unreadCount; ?>
                        </span>
                        <?php endif; ?>
                    </a>
                    <?php endforeach; ?>
                </nav>
            </div>
            
            <div class="flex-shrink-0 flex border-t border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center w-full">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 rounded-full bg-gradient-to-r from-brand-blue to-brand-teal flex items-center justify-center">
                            <i class="fas fa-user text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200">
                            <?php echo htmlspecialchars($currentUser['full_name'] ?? 'Admin User'); ?>
                        </p>
                        <a href="logout.php" class="text-xs text-red-500 hover:text-red-700">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile top bar -->
<div class="md:hidden">
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 theme-transition">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <button type="button" class="-ml-0.5 -mt-0.5 h-12 w-12 inline-flex items-center justify-center rounded-md text-gray-500 hover:text-gray-900 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-brand-blue" id="open-mobile-menu">
                        <span class="sr-only">Open sidebar</span>
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                    <img class="ml-2 h-8 w-auto" src="../images/logo.svg" alt="Sky Border Solutions">
                    <span class="ml-2 text-lg font-bold text-gray-900 dark:text-white theme-transition">CMS</span>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Dark Mode Toggle -->
                    <button id="theme-toggle-mobile" class="p-2 rounded-lg text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 theme-transition">
                        <i id="theme-icon-mobile" class="fas fa-moon dark:hidden"></i>
                        <i id="theme-icon-dark-mobile" class="fas fa-sun hidden dark:block"></i>
                    </button>
                    <a href="logout.php" class="text-gray-500 hover:text-red-500 dark:text-gray-300 dark:hover:text-red-400">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dark Mode Toggle for Desktop (in sidebar area) -->
<div class="hidden md:block md:fixed md:top-4 md:left-72 z-50">
    <button id="theme-toggle" class="p-2 rounded-lg bg-white dark:bg-gray-800 shadow-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 theme-transition">
        <i id="theme-icon" class="fas fa-moon dark:hidden"></i>
        <i id="theme-icon-dark" class="fas fa-sun hidden dark:block"></i>
    </button>
</div>

<script>
// Mobile menu functionality
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenu = document.getElementById('mobile-menu');
    const openMobileMenu = document.getElementById('open-mobile-menu');
    const closeMobileMenu = document.getElementById('close-mobile-menu');
    const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
    
    if (openMobileMenu) {
        openMobileMenu.addEventListener('click', function() {
            mobileMenu.classList.remove('hidden');
        });
    }
    
    if (closeMobileMenu) {
        closeMobileMenu.addEventListener('click', function() {
            mobileMenu.classList.add('hidden');
        });
    }
    
    if (mobileMenuOverlay) {
        mobileMenuOverlay.addEventListener('click', function() {
            mobileMenu.classList.add('hidden');
        });
    }
    
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

    // Theme toggle buttons
    const themeToggle = document.getElementById('theme-toggle');
    const themeToggleMobile = document.getElementById('theme-toggle-mobile');
    
    if (themeToggle) {
        themeToggle.addEventListener('click', toggleTheme);
    }
    
    if (themeToggleMobile) {
        themeToggleMobile.addEventListener('click', toggleTheme);
    }
});
</script>
