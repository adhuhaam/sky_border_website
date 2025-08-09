<?php
/**
 * Clients Management
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
$clientId = $_GET['id'] ?? null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_client'])) {
        // Add new client
        $client_name = trim($_POST['client_name'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);
        $logo_url = trim($_POST['logo_url'] ?? '');
        $display_order = (int)($_POST['display_order'] ?? 0);
        
        if (empty($client_name)) {
            $error = 'Client name is required.';
        } else {
            if ($contentManager->addClient($client_name, $category_id, $logo_url, $display_order)) {
                $success = 'Client added successfully!';
                $action = 'list'; // Redirect to list view
            } else {
                $error = 'Failed to add client. Please try again.';
            }
        }
    } elseif (isset($_POST['update_client'])) {
        // Update existing client
        $client_id = (int)($_POST['client_id'] ?? 0);
        $client_name = trim($_POST['client_name'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);
        $logo_url = trim($_POST['logo_url'] ?? '');
        $display_order = (int)($_POST['display_order'] ?? 0);
        
        if (empty($client_name)) {
            $error = 'Client name is required.';
        } else {
            if ($contentManager->updateClient($client_id, $client_name, $category_id, $logo_url, $display_order)) {
                $success = 'Client updated successfully!';
                $action = 'list'; // Redirect to list view
            } else {
                $error = 'Failed to update client. Please try again.';
            }
        }
    }
}

// Handle delete action
if ($action === 'delete' && $clientId) {
    if ($contentManager->deleteClient($clientId)) {
        $success = 'Client deleted successfully!';
    } else {
        $error = 'Failed to delete client.';
    }
    $action = 'list'; // Redirect to list view
}

// Get data for the page
$clients = $contentManager->getClients();
$clientCategories = $contentManager->getClientCategories();

// Get specific client for editing
$editClient = null;
if ($action === 'edit' && $clientId) {
    foreach ($clients as $client) {
        if ($client['id'] == $clientId) {
            $editClient = $client;
            break;
        }
    }
    if (!$editClient) {
        $error = 'Client not found.';
        $action = 'list';
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients Management - Sky Border Solutions CMS</title>
    
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
                            <a href="clients.php" class="border-brand-blue text-gray-900 dark:text-white inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium theme-transition">
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
                                        Add New Client
                                    <?php elseif ($action === 'edit'): ?>
                                        Edit Client
                                    <?php else: ?>
                                        Clients Management
                                    <?php endif; ?>
                                </h1>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 theme-transition">
                                    <?php if ($action === 'list'): ?>
                                        Manage your client portfolio displayed on the website
                                    <?php else: ?>
                                        Update client information and category
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <?php if ($action === 'list'): ?>
                        <div>
                            <a href="?action=add" class="bg-gradient-to-r from-brand-blue to-brand-teal border border-transparent rounded-md py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:from-brand-blue-dark hover:to-brand-blue transition-all">
                                <i class="fas fa-plus mr-2"></i>
                                Add Client
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
                        <!-- Clients List -->
                        <div class="modern-card bg-white dark:bg-gray-800 shadow overflow-hidden theme-transition">
                            <div class="px-4 py-5 sm:p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white theme-transition">
                                        All Clients (<?php echo count($clients); ?>)
                                    </h3>
                                    <a href="../index.php#clients" target="_blank" class="text-sm text-brand-blue hover:text-brand-blue-dark theme-transition">
                                        <i class="fas fa-external-link-alt mr-1"></i>
                                        View on Website
                                    </a>
                                </div>
                                
                                <?php if (empty($clients)): ?>
                                <div class="text-center py-8">
                                    <i class="fas fa-users text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2 theme-transition">No clients yet</h3>
                                    <p class="text-gray-600 dark:text-gray-400 mb-4 theme-transition">Start building your client portfolio by adding your first client.</p>
                                    <a href="?action=add" class="bg-gradient-to-r from-brand-blue to-brand-teal text-white px-4 py-2 rounded-md text-sm font-medium hover:from-brand-blue-dark hover:to-brand-blue transition-all">
                                        <i class="fas fa-plus mr-2"></i>
                                        Add First Client
                                    </a>
                                </div>
                                <?php else: ?>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Client</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Order</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            <?php foreach ($clients as $client): ?>
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 theme-transition">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-10 w-10">
                                                            <?php if (!empty($client['logo_url'])): ?>
                                                            <img class="h-10 w-10 rounded-lg object-cover" src="<?php echo htmlspecialchars($client['logo_url']); ?>" alt="<?php echo htmlspecialchars($client['client_name']); ?>">
                                                            <?php else: ?>
                                                            <div class="h-10 w-10 rounded-lg bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                                                <i class="fas fa-building text-gray-400"></i>
                                                            </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900 dark:text-white theme-transition">
                                                                <?php echo htmlspecialchars($client['client_name']); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                                        <?php echo htmlspecialchars($client['category_name'] ?? 'Uncategorized'); ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white theme-transition">
                                                    <?php echo (int)$client['display_order']; ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                                        Active
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <div class="flex items-center justify-end space-x-2">
                                                        <a href="?action=edit&id=<?php echo $client['id']; ?>" class="text-brand-blue hover:text-brand-blue-dark">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="?action=delete&id=<?php echo $client['id']; ?>" 
                                                           onclick="return confirm('Are you sure you want to delete this client?')" 
                                                           class="text-red-600 hover:text-red-900">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php elseif ($action === 'add' || $action === 'edit'): ?>
                        <!-- Add/Edit Form -->
                        <div class="modern-card bg-white dark:bg-gray-800 shadow theme-transition">
                            <form method="POST" class="space-y-6 p-6">
                                <?php if ($action === 'edit'): ?>
                                <input type="hidden" name="client_id" value="<?php echo $editClient['id']; ?>">
                                <?php endif; ?>
                                
                                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                    <div>
                                        <label for="client_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Client Name *</label>
                                        <input type="text" name="client_name" id="client_name" required
                                               value="<?php echo $editClient ? htmlspecialchars($editClient['client_name']) : ''; ?>"
                                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                                    </div>

                                    <div>
                                        <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Category</label>
                                        <select name="category_id" id="category_id"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                                            <option value="">Select Category</option>
                                            <?php foreach ($clientCategories as $category): ?>
                                            <option value="<?php echo $category['id']; ?>" 
                                                    <?php echo ($editClient && $editClient['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($category['category_name']); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="logo_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Logo URL</label>
                                        <input type="url" name="logo_url" id="logo_url"
                                               value="<?php echo $editClient ? htmlspecialchars($editClient['logo_url']) : ''; ?>"
                                               placeholder="https://example.com/logo.png"
                                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Optional: Direct URL to the client's logo image</p>
                                    </div>

                                    <div>
                                        <label for="display_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Display Order</label>
                                        <input type="number" name="display_order" id="display_order" min="0"
                                               value="<?php echo $editClient ? (int)$editClient['display_order'] : 0; ?>"
                                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Lower numbers appear first</p>
                                    </div>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                                    <div class="flex justify-end space-x-3">
                                        <a href="clients.php" class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md py-2 px-4 inline-flex justify-center text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 theme-transition">
                                            Cancel
                                        </a>
                                        <button type="submit" name="<?php echo $action === 'edit' ? 'update_client' : 'add_client'; ?>" class="bg-gradient-to-r from-brand-blue to-brand-teal border border-transparent rounded-md py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:from-brand-blue-dark hover:to-brand-blue focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-blue">
                                            <i class="fas fa-save mr-2"></i>
                                            <?php echo $action === 'edit' ? 'Update Client' : 'Add Client'; ?>
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