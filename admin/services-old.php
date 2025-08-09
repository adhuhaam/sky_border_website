<?php
/**
 * Services Management
 * Sky Border Solutions CMS
 */

require_once 'classes/Auth.php';
require_once 'classes/ContentManager.php';

$auth = new Auth();
$auth->requireLogin();

$contentManager = new ContentManager();
$currentUser = $auth->getCurrentUser();

$success = '';
$error = '';
$action = $_GET['action'] ?? 'list';
$serviceId = $_GET['id'] ?? null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_service'])) {
        // Add new service
        $data = [
            ':category_name' => trim($_POST['category_name'] ?? ''),
            ':category_description' => trim($_POST['category_description'] ?? ''),
            ':icon_class' => trim($_POST['icon_class'] ?? ''),
            ':color_theme' => trim($_POST['color_theme'] ?? ''),
            ':display_order' => (int)($_POST['display_order'] ?? 0)
        ];
        
        if (empty($data[':category_name'])) {
            $error = 'Service name is required.';
        } else {
            if ($contentManager->addServiceCategory($data)) {
                $success = 'Service added successfully!';
                $action = 'list';
            } else {
                $error = 'Failed to add service. Please try again.';
            }
        }
    } elseif (isset($_POST['update_service'])) {
        // Update existing service
        $service_id = (int)($_POST['service_id'] ?? 0);
        $data = [
            ':category_name' => trim($_POST['category_name'] ?? ''),
            ':category_description' => trim($_POST['category_description'] ?? ''),
            ':icon_class' => trim($_POST['icon_class'] ?? ''),
            ':color_theme' => trim($_POST['color_theme'] ?? ''),
            ':display_order' => (int)($_POST['display_order'] ?? 0)
        ];
        
        if (empty($data[':category_name'])) {
            $error = 'Service name is required.';
        } else {
            if ($contentManager->updateServiceCategory($service_id, $data)) {
                $success = 'Service updated successfully!';
                $action = 'list';
            } else {
                $error = 'Failed to update service. Please try again.';
            }
        }
    }
}

// Handle delete action
if ($action === 'delete' && $serviceId) {
    if ($contentManager->deleteServiceCategory($serviceId)) {
        $success = 'Service deleted successfully!';
    } else {
        $error = 'Failed to delete service.';
    }
    $action = 'list';
}

// Get data for the page
$services = $contentManager->getServiceCategories();

// Get specific service for editing
$editService = null;
if ($action === 'edit' && $serviceId) {
    foreach ($services as $service) {
        if ($service['id'] == $serviceId) {
            $editService = $service;
            break;
        }
    }
    if (!$editService) {
        $error = 'Service not found.';
        $action = 'list';
    }
}

// Available icons for services
$iconOptions = [
    'fas fa-user-tie' => 'User Tie',
    'fas fa-users-cog' => 'Users Cog',
    'fas fa-passport' => 'Passport',
    'fas fa-shield-alt' => 'Shield',
    'fas fa-handshake' => 'Handshake',
    'fas fa-briefcase' => 'Briefcase',
    'fas fa-cogs' => 'Cogs',
    'fas fa-chart-line' => 'Chart Line',
    'fas fa-building' => 'Building',
    'fas fa-globe' => 'Globe'
];

$colorOptions = [
    'indigo' => 'Indigo',
    'blue' => 'Blue',
    'green' => 'Green',
    'purple' => 'Purple',
    'yellow' => 'Yellow',
    'red' => 'Red',
    'pink' => 'Pink',
    'gray' => 'Gray'
];
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services Management - Sky Border Solutions CMS</title>
    
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
        }
        .dark .modern-card {
            background: linear-gradient(145deg, #1f2937 0%, #111827 100%);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
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
                            <a href="dashboard.php" class="border-transparent text-gray-500 dark:text-gray-300 hover:border-gray-300 hover:text-gray-700 dark:hover:text-white inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium theme-transition">
                                <i class="fas fa-tachometer-alt mr-2"></i>
                                Dashboard
                            </a>
                            <a href="company-info.php" class="border-transparent text-gray-500 dark:text-gray-300 hover:border-gray-300 hover:text-gray-700 dark:hover:text-white inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium theme-transition">
                                <i class="fas fa-building mr-2"></i>
                                Company Info
                            </a>
                            <a href="services.php" class="border-brand-blue text-gray-900 dark:text-white inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium theme-transition">
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
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <a href="dashboard.php" class="text-brand-blue hover:text-brand-blue-dark mr-4">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                            <div>
                                <h1 class="text-3xl font-bold leading-tight tracking-tight text-gray-900 dark:text-white theme-transition">
                                    <?php if ($action === 'add'): ?>
                                        Add New Service
                                    <?php elseif ($action === 'edit'): ?>
                                        Edit Service
                                    <?php else: ?>
                                        Services Management
                                    <?php endif; ?>
                                </h1>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 theme-transition">
                                    <?php if ($action === 'list'): ?>
                                        Manage your service categories displayed on the website
                                    <?php else: ?>
                                        Update service information and display settings
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <?php if ($action === 'list'): ?>
                        <div>
                            <a href="?action=add" class="bg-gradient-to-r from-brand-blue to-brand-teal border border-transparent rounded-md py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:from-brand-blue-dark hover:to-brand-blue transition-all">
                                <i class="fas fa-plus mr-2"></i>
                                Add Service
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </header>
            
            <main>
                <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="mt-8">
                        <!-- Success/Error Messages -->
                        <?php if ($success): ?>
                        <div class="mb-6 rounded-md bg-green-50 dark:bg-green-900/20 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-green-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800 dark:text-green-400"><?php echo htmlspecialchars($success); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($error): ?>
                        <div class="mb-6 rounded-md bg-red-50 dark:bg-red-900/20 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-red-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800 dark:text-red-400"><?php echo htmlspecialchars($error); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($action === 'list'): ?>
                        <!-- Services List -->
                        <div class="modern-card bg-white dark:bg-gray-800 shadow overflow-hidden theme-transition">
                            <div class="px-4 py-5 sm:p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white theme-transition">
                                        All Services (<?php echo count($services); ?>)
                                    </h3>
                                    <a href="../index.php#services" target="_blank" class="text-sm text-brand-blue hover:text-brand-blue-dark theme-transition">
                                        <i class="fas fa-external-link-alt mr-1"></i>
                                        View on Website
                                    </a>
                                </div>
                                
                                <?php if (empty($services)): ?>
                                <div class="text-center py-8">
                                    <i class="fas fa-cogs text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2 theme-transition">No services yet</h3>
                                    <p class="text-gray-600 dark:text-gray-400 mb-4 theme-transition">Start by adding your first service category.</p>
                                    <a href="?action=add" class="bg-gradient-to-r from-brand-blue to-brand-teal text-white px-4 py-2 rounded-md text-sm font-medium hover:from-brand-blue-dark hover:to-brand-blue transition-all">
                                        <i class="fas fa-plus mr-2"></i>
                                        Add First Service
                                    </a>
                                </div>
                                <?php else: ?>
                                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                                    <?php foreach ($services as $service): ?>
                                    <div class="modern-card bg-white dark:bg-gray-800 p-6 rounded-lg hover:shadow-lg theme-transition">
                                        <div class="flex items-center mb-4">
                                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-r from-brand-blue to-brand-teal">
                                                <i class="<?php echo htmlspecialchars($service['icon_class']); ?> text-white text-xl"></i>
                                            </div>
                                            <div class="ml-4 flex-1">
                                                <h4 class="text-lg font-medium text-gray-900 dark:text-white theme-transition">
                                                    <?php echo htmlspecialchars($service['category_name']); ?>
                                                </h4>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    Order: <?php echo (int)$service['display_order']; ?>
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-4 theme-transition">
                                            <?php echo htmlspecialchars($service['category_description']); ?>
                                        </p>
                                        
                                        <div class="flex items-center justify-between">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-<?php echo $service['color_theme']; ?>-100 text-<?php echo $service['color_theme']; ?>-800">
                                                <?php echo htmlspecialchars($service['color_theme']); ?>
                                            </span>
                                            <div class="flex items-center space-x-2">
                                                <a href="?action=edit&id=<?php echo $service['id']; ?>" class="text-brand-blue hover:text-brand-blue-dark">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="?action=delete&id=<?php echo $service['id']; ?>" 
                                                   onclick="return confirm('Are you sure you want to delete this service?')" 
                                                   class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php elseif ($action === 'add' || $action === 'edit'): ?>
                        <!-- Add/Edit Form -->
                        <div class="modern-card bg-white dark:bg-gray-800 shadow theme-transition">
                            <form method="POST" class="space-y-6 p-6">
                                <?php if ($action === 'edit'): ?>
                                <input type="hidden" name="service_id" value="<?php echo $editService['id']; ?>">
                                <?php endif; ?>
                                
                                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                    <div>
                                        <label for="category_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Service Name *</label>
                                        <input type="text" name="category_name" id="category_name" required
                                               value="<?php echo $editService ? htmlspecialchars($editService['category_name']) : ''; ?>"
                                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                                    </div>

                                    <div>
                                        <label for="icon_class" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Icon</label>
                                        <select name="icon_class" id="icon_class"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                                            <?php foreach ($iconOptions as $value => $label): ?>
                                            <option value="<?php echo $value; ?>" 
                                                    <?php echo ($editService && $editService['icon_class'] == $value) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($label); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="color_theme" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Color Theme</label>
                                        <select name="color_theme" id="color_theme"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                                            <?php foreach ($colorOptions as $value => $label): ?>
                                            <option value="<?php echo $value; ?>" 
                                                    <?php echo ($editService && $editService['color_theme'] == $value) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($label); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="display_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Display Order</label>
                                        <input type="number" name="display_order" id="display_order" min="0"
                                               value="<?php echo $editService ? (int)$editService['display_order'] : 0; ?>"
                                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Lower numbers appear first</p>
                                    </div>
                                </div>

                                <div>
                                    <label for="category_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Description</label>
                                    <textarea name="category_description" id="category_description" rows="3"
                                              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition"><?php echo $editService ? htmlspecialchars($editService['category_description']) : ''; ?></textarea>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                                    <div class="flex justify-end space-x-3">
                                        <a href="services.php" class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md py-2 px-4 inline-flex justify-center text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 theme-transition">
                                            Cancel
                                        </a>
                                        <button type="submit" name="<?php echo $action === 'edit' ? 'update_service' : 'add_service'; ?>" class="bg-gradient-to-r from-brand-blue to-brand-teal border border-transparent rounded-md py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:from-brand-blue-dark hover:to-brand-blue focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-blue">
                                            <i class="fas fa-save mr-2"></i>
                                            <?php echo $action === 'edit' ? 'Update Service' : 'Add Service'; ?>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <?php endif; ?>
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
