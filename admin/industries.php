<?php
/**
 * Industries Management
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
$industryId = $_GET['id'] ?? null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_industry'])) {
        $industry_name = trim($_POST['industry_name'] ?? '');
        $industry_description = trim($_POST['industry_description'] ?? '');
        $icon_class = trim($_POST['icon_class'] ?? 'fas fa-briefcase');
        $color_theme = trim($_POST['color_theme'] ?? 'blue');
        $display_order = (int)($_POST['display_order'] ?? 0);
        
        if (!empty($industry_name)) {
            if ($contentManager->addIndustry($industry_name, $industry_description, $icon_class, $color_theme, $display_order)) {
                $success = 'Industry added successfully!';
                $action = 'list';
            } else {
                $error = 'Failed to add industry. Please try again.';
            }
        } else {
            $error = 'Industry name is required.';
        }
    }
    
    if (isset($_POST['update_industry'])) {
        $id = (int)($_POST['industry_id'] ?? 0);
        $industry_name = trim($_POST['industry_name'] ?? '');
        $industry_description = trim($_POST['industry_description'] ?? '');
        $icon_class = trim($_POST['icon_class'] ?? 'fas fa-briefcase');
        $color_theme = trim($_POST['color_theme'] ?? 'blue');
        $display_order = (int)($_POST['display_order'] ?? 0);
        
        if (!empty($industry_name) && $id > 0) {
            if ($contentManager->updateIndustry($id, $industry_name, $industry_description, $icon_class, $color_theme, $display_order)) {
                $success = 'Industry updated successfully!';
                $action = 'list';
            } else {
                $error = 'Failed to update industry. Please try again.';
            }
        } else {
            $error = 'Industry name is required.';
        }
    }
    
    if (isset($_POST['delete_industry'])) {
        $id = (int)($_POST['industry_id'] ?? 0);
        if ($id > 0) {
            if ($contentManager->deleteIndustry($id)) {
                $success = 'Industry deleted successfully!';
                $action = 'list';
            } else {
                $error = 'Failed to delete industry. Please try again.';
            }
        }
    }
}

// Get data for the page
$industries = $contentManager->getIndustries();
$editIndustry = null;

if ($action === 'edit' && $industryId) {
    $editIndustry = $contentManager->getIndustry($industryId);
    if (!$editIndustry) {
        $error = 'Industry not found.';
        $action = 'list';
    }
}

// Icon options
$iconOptions = [
    'fas fa-hard-hat' => 'Construction',
    'fas fa-user-md' => 'Healthcare',
    'fas fa-concierge-bell' => 'Hospitality',
    'fas fa-briefcase' => 'Business',
    'fas fa-truck' => 'Transport',
    'fas fa-graduation-cap' => 'Education',
    'fas fa-shopping-cart' => 'Retail',
    'fas fa-tools' => 'Maintenance',
    'fas fa-laptop-code' => 'Technology',
    'fas fa-chart-line' => 'Finance',
    'fas fa-building' => 'Real Estate',
    'fas fa-leaf' => 'Agriculture'
];

// Color theme options
$colorOptions = [
    'amber' => 'Amber',
    'emerald' => 'Emerald', 
    'blue' => 'Blue',
    'violet' => 'Violet',
    'rose' => 'Rose',
    'indigo' => 'Indigo',
    'green' => 'Green',
    'purple' => 'Purple',
    'red' => 'Red',
    'yellow' => 'Yellow',
    'pink' => 'Pink',
    'gray' => 'Gray'
];
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Industries Management - Sky Border Solutions CMS</title>
    
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
                            <div class="mb-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white theme-transition">
                                            <?php if ($action === 'add'): ?>
                                                Add New Industry
                                            <?php elseif ($action === 'edit'): ?>
                                                Edit Industry
                                            <?php else: ?>
                                                Industries Management
                                            <?php endif; ?>
                                        </h1>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 theme-transition">
                                            <?php if ($action === 'list'): ?>
                                                Manage industry categories and their details
                                            <?php else: ?>
                                                Update industry information and settings
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <?php if ($action === 'list'): ?>
                                    <div class="flex items-center space-x-4">
                                        <a href="?action=add" class="bg-gradient-to-r from-brand-blue to-brand-teal border border-transparent rounded-md py-2 px-4 inline-flex items-center text-sm font-medium text-white hover:from-brand-blue-dark hover:to-brand-blue focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-blue">
                                            <i class="fas fa-plus mr-2"></i>
                                            Add Industry
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>

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
                        <!-- Industries List -->
                        <div class="modern-card bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg theme-transition">
                            <?php if (empty($industries)): ?>
                            <div class="text-center py-12">
                                <i class="fas fa-industry text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2 theme-transition">No industries yet</h3>
                                <p class="text-gray-600 dark:text-gray-400 theme-transition">Get started by adding your first industry category.</p>
                                <div class="mt-6">
                                    <a href="?action=add" class="bg-gradient-to-r from-brand-blue to-brand-teal border border-transparent rounded-md py-2 px-4 inline-flex items-center text-sm font-medium text-white hover:from-brand-blue-dark hover:to-brand-blue">
                                        <i class="fas fa-plus mr-2"></i>
                                        Add Industry
                                    </a>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 p-6">
                                <?php foreach ($industries as $industry): ?>
                                <div class="modern-card rounded-lg p-6 hover:shadow-lg transition-shadow">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-<?php echo htmlspecialchars($industry['color_theme']); ?>-500 to-<?php echo htmlspecialchars($industry['color_theme']); ?>-600">
                                                <i class="<?php echo htmlspecialchars($industry['icon_class']); ?> text-white"></i>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-lg font-medium text-gray-900 dark:text-white theme-transition"><?php echo htmlspecialchars($industry['industry_name']); ?></h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Order: <?php echo $industry['display_order']; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <?php if ($industry['industry_description']): ?>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-4 theme-transition">
                                        <?php echo htmlspecialchars(substr($industry['industry_description'], 0, 120)); ?>
                                        <?php if (strlen($industry['industry_description']) > 120): ?>...<?php endif; ?>
                                    </p>
                                    <?php endif; ?>
                                    
                                    <div class="flex justify-end space-x-2">
                                        <a href="positions.php?industry_id=<?php echo $industry['id']; ?>" class="text-brand-blue hover:text-brand-blue-dark text-sm font-medium">
                                            <i class="fas fa-user-tie mr-1"></i>
                                            Positions
                                        </a>
                                        <a href="?action=edit&id=<?php echo $industry['id']; ?>" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                            <i class="fas fa-edit mr-1"></i>
                                            Edit
                                        </a>
                                        <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this industry?');">
                                            <input type="hidden" name="industry_id" value="<?php echo $industry['id']; ?>">
                                            <button type="submit" name="delete_industry" class="text-red-600 hover:text-red-900 text-sm font-medium">
                                                <i class="fas fa-trash mr-1"></i>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <?php elseif ($action === 'add' || $action === 'edit'): ?>
                        <!-- Add/Edit Form -->
                        <div class="modern-card bg-white dark:bg-gray-800 shadow sm:rounded-lg theme-transition">
                            <div class="px-4 py-5 sm:p-6">
                                <form method="POST" class="space-y-6">
                                    <?php if ($action === 'edit'): ?>
                                    <input type="hidden" name="industry_id" value="<?php echo $editIndustry['id']; ?>">
                                    <?php endif; ?>
                                    
                                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                        <div>
                                            <label for="industry_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Industry Name *</label>
                                            <input type="text" name="industry_name" id="industry_name" required 
                                                   value="<?php echo $editIndustry ? htmlspecialchars($editIndustry['industry_name']) : ''; ?>"
                                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                                        </div>
                                        
                                        <div>
                                            <label for="display_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Display Order</label>
                                            <input type="number" name="display_order" id="display_order" min="0"
                                                   value="<?php echo $editIndustry ? $editIndustry['display_order'] : '0'; ?>"
                                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label for="industry_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Description</label>
                                        <textarea name="industry_description" id="industry_description" rows="3"
                                                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition"><?php echo $editIndustry ? htmlspecialchars($editIndustry['industry_description']) : ''; ?></textarea>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                        <div>
                                            <label for="icon_class" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Icon</label>
                                            <select name="icon_class" id="icon_class" 
                                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                                                <?php foreach ($iconOptions as $icon => $label): ?>
                                                <option value="<?php echo htmlspecialchars($icon); ?>" 
                                                        <?php echo ($editIndustry && $editIndustry['icon_class'] === $icon) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($label); ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label for="color_theme" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Color Theme</label>
                                            <select name="color_theme" id="color_theme" 
                                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                                                <?php foreach ($colorOptions as $color => $label): ?>
                                                <option value="<?php echo htmlspecialchars($color); ?>" 
                                                        <?php echo ($editIndustry && $editIndustry['color_theme'] === $color) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($label); ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="flex justify-end space-x-3">
                                        <a href="industries.php" class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md py-2 px-4 inline-flex justify-center text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 theme-transition">
                                            Cancel
                                        </a>
                                        <button type="submit" name="<?php echo $action === 'edit' ? 'update_industry' : 'add_industry'; ?>" class="bg-gradient-to-r from-brand-blue to-brand-teal border border-transparent rounded-md py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:from-brand-blue-dark hover:to-brand-blue focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-blue">
                                            <i class="fas fa-save mr-2"></i>
                                            <?php echo $action === 'edit' ? 'Update Industry' : 'Add Industry'; ?>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php endif; ?>
                        </div>
                    </div>
                </main>
            </div>
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
        
        // Icon preview
        document.getElementById('icon_class').addEventListener('change', function() {
            const icon = this.value;
            // You could add icon preview functionality here
        });
    </script>
</body>
</html>
