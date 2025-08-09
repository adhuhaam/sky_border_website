<?php
/**
 * Job Positions Management
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
$positionId = $_GET['id'] ?? null;
$industryFilter = $_GET['industry_id'] ?? null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_position'])) {
        $industry_id = (int)($_POST['industry_id'] ?? 0);
        $position_name = trim($_POST['position_name'] ?? '');
        $position_description = trim($_POST['position_description'] ?? '');
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $display_order = (int)($_POST['display_order'] ?? 0);
        
        if (!empty($position_name) && $industry_id > 0) {
            if ($contentManager->addJobPosition($industry_id, $position_name, $position_description, $is_featured, $display_order)) {
                $success = 'Position added successfully!';
                $action = 'list';
            } else {
                $error = 'Failed to add position. Please try again.';
            }
        } else {
            $error = 'Position name and industry are required.';
        }
    }
    
    if (isset($_POST['update_position'])) {
        $id = (int)($_POST['position_id'] ?? 0);
        $industry_id = (int)($_POST['industry_id'] ?? 0);
        $position_name = trim($_POST['position_name'] ?? '');
        $position_description = trim($_POST['position_description'] ?? '');
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $display_order = (int)($_POST['display_order'] ?? 0);
        
        if (!empty($position_name) && $industry_id > 0 && $id > 0) {
            if ($contentManager->updateJobPosition($id, $industry_id, $position_name, $position_description, $is_featured, $display_order)) {
                $success = 'Position updated successfully!';
                $action = 'list';
            } else {
                $error = 'Failed to update position. Please try again.';
            }
        } else {
            $error = 'Position name and industry are required.';
        }
    }
    
    if (isset($_POST['delete_position'])) {
        $id = (int)($_POST['position_id'] ?? 0);
        if ($id > 0) {
            if ($contentManager->deleteJobPosition($id)) {
                $success = 'Position deleted successfully!';
                $action = 'list';
            } else {
                $error = 'Failed to delete position. Please try again.';
            }
        }
    }
}

// Get data for the page
$industries = $contentManager->getIndustries();
$positions = $contentManager->getJobPositions($industryFilter);
$editPosition = null;

if ($action === 'edit' && $positionId) {
    $editPosition = $contentManager->getJobPosition($positionId);
    if (!$editPosition) {
        $error = 'Position not found.';
        $action = 'list';
    }
}

// Group positions by industry for display
$groupedPositions = [];
foreach ($positions as $position) {
    $industryName = $position['industry_name'] ?? 'Unknown';
    if (!isset($groupedPositions[$industryName])) {
        $groupedPositions[$industryName] = [];
    }
    $groupedPositions[$industryName][] = $position;
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Positions Management - Sky Border Solutions CMS</title>
    
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
                            <a href="services.php" class="border-transparent text-gray-500 dark:text-gray-300 hover:border-gray-300 hover:text-gray-700 dark:hover:text-white inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium theme-transition">
                                <i class="fas fa-cogs mr-2"></i>
                                Services
                            </a>
                            <a href="industries.php" class="border-transparent text-gray-500 dark:text-gray-300 hover:border-gray-300 hover:text-gray-700 dark:hover:text-white inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium theme-transition">
                                <i class="fas fa-industry mr-2"></i>
                                Industries
                            </a>
                            <a href="positions.php" class="border-brand-blue text-gray-900 dark:text-white inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium theme-transition">
                                <i class="fas fa-user-tie mr-2"></i>
                                Positions
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
                                        Add New Position
                                    <?php elseif ($action === 'edit'): ?>
                                        Edit Position
                                    <?php else: ?>
                                        Job Positions Management
                                    <?php endif; ?>
                                </h1>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 theme-transition">
                                    <?php if ($action === 'list'): ?>
                                        Manage job positions across all industries
                                    <?php else: ?>
                                        Update position information and settings
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <?php if ($action === 'list'): ?>
                        <div class="flex items-center space-x-4">
                            <?php if ($industryFilter): ?>
                            <a href="positions.php" class="text-brand-blue hover:text-brand-blue-dark text-sm font-medium">
                                <i class="fas fa-times mr-1"></i>
                                Clear Filter
                            </a>
                            <?php endif; ?>
                            <a href="?action=add" class="bg-gradient-to-r from-brand-blue to-brand-teal border border-transparent rounded-md py-2 px-4 inline-flex items-center text-sm font-medium text-white hover:from-brand-blue-dark hover:to-brand-blue focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-blue">
                                <i class="fas fa-plus mr-2"></i>
                                Add Position
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
                        
                        <!-- Industry Filter -->
                        <?php if (!empty($industries)): ?>
                        <div class="mb-6">
                            <div class="flex flex-wrap gap-2">
                                <a href="positions.php" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo !$industryFilter ? 'bg-brand-blue text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'; ?> theme-transition">
                                    All Industries
                                </a>
                                <?php foreach ($industries as $industry): ?>
                                <a href="?industry_id=<?php echo $industry['id']; ?>" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo $industryFilter == $industry['id'] ? 'bg-brand-blue text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'; ?> theme-transition">
                                    <i class="<?php echo htmlspecialchars($industry['icon_class']); ?> mr-1"></i>
                                    <?php echo htmlspecialchars($industry['industry_name']); ?>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Positions List -->
                        <div class="modern-card bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg theme-transition">
                            <?php if (empty($positions)): ?>
                            <div class="text-center py-12">
                                <i class="fas fa-user-tie text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2 theme-transition">No positions yet</h3>
                                <p class="text-gray-600 dark:text-gray-400 theme-transition">
                                    <?php if ($industryFilter): ?>
                                        No positions found for the selected industry.
                                    <?php else: ?>
                                        Get started by adding your first job position.
                                    <?php endif; ?>
                                </p>
                                <div class="mt-6">
                                    <a href="?action=add" class="bg-gradient-to-r from-brand-blue to-brand-teal border border-transparent rounded-md py-2 px-4 inline-flex items-center text-sm font-medium text-white hover:from-brand-blue-dark hover:to-brand-blue">
                                        <i class="fas fa-plus mr-2"></i>
                                        Add Position
                                    </a>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                <?php foreach ($groupedPositions as $industryName => $industryPositions): ?>
                                <div class="p-6">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 theme-transition">
                                        <?php echo htmlspecialchars($industryName); ?>
                                        <span class="text-sm text-gray-500 dark:text-gray-400 font-normal ml-2">(<?php echo count($industryPositions); ?> positions)</span>
                                    </h3>
                                    
                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                        <?php foreach ($industryPositions as $position): ?>
                                        <div class="modern-card rounded-lg p-4 hover:shadow-lg transition-shadow">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center">
                                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white theme-transition">
                                                            <?php echo htmlspecialchars($position['position_name']); ?>
                                                        </h4>
                                                        <?php if ($position['is_featured']): ?>
                                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            <i class="fas fa-star mr-1"></i>
                                                            Featured
                                                        </span>
                                                        <?php endif; ?>
                                                    </div>
                                                    
                                                    <?php if ($position['position_description']): ?>
                                                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-300 theme-transition">
                                                        <?php echo htmlspecialchars(substr($position['position_description'], 0, 80)); ?>
                                                        <?php if (strlen($position['position_description']) > 80): ?>...<?php endif; ?>
                                                    </p>
                                                    <?php endif; ?>
                                                    
                                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Order: <?php echo $position['display_order']; ?></p>
                                                </div>
                                            </div>
                                            
                                            <div class="mt-3 flex justify-end space-x-2">
                                                <a href="?action=edit&id=<?php echo $position['id']; ?>" class="text-indigo-600 hover:text-indigo-900 text-xs font-medium">
                                                    <i class="fas fa-edit mr-1"></i>
                                                    Edit
                                                </a>
                                                <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this position?');">
                                                    <input type="hidden" name="position_id" value="<?php echo $position['id']; ?>">
                                                    <button type="submit" name="delete_position" class="text-red-600 hover:text-red-900 text-xs font-medium">
                                                        <i class="fas fa-trash mr-1"></i>
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
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
                                    <input type="hidden" name="position_id" value="<?php echo $editPosition['id']; ?>">
                                    <?php endif; ?>
                                    
                                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                        <div>
                                            <label for="position_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Position Name *</label>
                                            <input type="text" name="position_name" id="position_name" required 
                                                   value="<?php echo $editPosition ? htmlspecialchars($editPosition['position_name']) : ''; ?>"
                                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                                        </div>
                                        
                                        <div>
                                            <label for="industry_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Industry *</label>
                                            <select name="industry_id" id="industry_id" required 
                                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                                                <option value="">Select Industry</option>
                                                <?php foreach ($industries as $industry): ?>
                                                <option value="<?php echo $industry['id']; ?>" 
                                                        <?php echo ($editPosition && $editPosition['industry_id'] == $industry['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($industry['industry_name']); ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label for="position_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Description</label>
                                        <textarea name="position_description" id="position_description" rows="3"
                                                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition"><?php echo $editPosition ? htmlspecialchars($editPosition['position_description']) : ''; ?></textarea>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                        <div>
                                            <label for="display_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Display Order</label>
                                            <input type="number" name="display_order" id="display_order" min="0"
                                                   value="<?php echo $editPosition ? $editPosition['display_order'] : '0'; ?>"
                                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                                        </div>
                                        
                                        <div class="flex items-center h-full">
                                            <div class="flex items-center">
                                                <input type="checkbox" name="is_featured" id="is_featured" value="1"
                                                       <?php echo ($editPosition && $editPosition['is_featured']) ? 'checked' : ''; ?>
                                                       class="h-4 w-4 text-brand-blue focus:ring-brand-blue border-gray-300 rounded">
                                                <label for="is_featured" class="ml-2 block text-sm text-gray-700 dark:text-gray-300 theme-transition">
                                                    Featured Position
                                                    <span class="text-xs text-gray-500 dark:text-gray-400 block">Show on website featured positions</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex justify-end space-x-3">
                                        <a href="positions.php" class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md py-2 px-4 inline-flex justify-center text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 theme-transition">
                                            Cancel
                                        </a>
                                        <button type="submit" name="<?php echo $action === 'edit' ? 'update_position' : 'add_position'; ?>" class="bg-gradient-to-r from-brand-blue to-brand-teal border border-transparent rounded-md py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:from-brand-blue-dark hover:to-brand-blue focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-blue">
                                            <i class="fas fa-save mr-2"></i>
                                            <?php echo $action === 'edit' ? 'Update Position' : 'Add Position'; ?>
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
