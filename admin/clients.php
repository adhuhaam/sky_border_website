<?php
session_start();
require_once 'config/database.php';
require_once 'classes/Auth.php';
require_once 'classes/ContentManager.php';

// Check authentication
$auth = new Auth();
$auth->requireLogin();

// Initialize content manager
$contentManager = new ContentManager();

// Handle form submissions
$message = '';
$messageType = '';

if ($_POST) {
    if (isset($_POST['add_client'])) {
        // Add new client
        $name = $_POST['name'] ?? '';
        $category = $_POST['category'] ?? '';
        $logo_url = $_POST['logo_url'] ?? '';
        $display_order = $_POST['display_order'] ?? 0;
        
        if (!empty($name) && !empty($category)) {
            if ($contentManager->addClient($name, $category, $logo_url, $display_order)) {
                $message = 'Client added successfully!';
                $messageType = 'success';
            } else {
                $message = 'Error adding client.';
                $messageType = 'error';
            }
        } else {
            $message = 'Please fill in all required fields.';
            $messageType = 'error';
        }
    } elseif (isset($_POST['update_client'])) {
        // Update client
        $id = $_POST['id'] ?? 0;
        $name = $_POST['name'] ?? '';
        $category = $_POST['category'] ?? '';
        $logo_url = $_POST['logo_url'] ?? '';
        $display_order = $_POST['display_order'] ?? 0;
        
        if ($id && !empty($name) && !empty($category)) {
            if ($contentManager->updateClient($id, $name, $category, $logo_url, $display_order)) {
                $message = 'Client updated successfully!';
                $messageType = 'success';
            } else {
                $message = 'Error updating client.';
                $messageType = 'error';
            }
        } else {
            $message = 'Please fill in all required fields.';
            $messageType = 'error';
        }
    } elseif (isset($_POST['delete_client'])) {
        // Delete client
        $id = $_POST['id'] ?? 0;
        
        if ($id && $contentManager->deleteClient($id)) {
            $message = 'Client deleted successfully!';
            $messageType = 'success';
        } else {
            $message = 'Error deleting client.';
            $messageType = 'error';
        }
    }
}

// Get all clients
$clients = $contentManager->getClients();

// Get unique categories
$categories = [];
foreach ($clients as $client) {
    if (!in_array($client['category'], $categories)) {
        $categories[] = $client['category'];
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Clients - Sky Border Solutions Admin</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .theme-transition { transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease; }
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

<body class="h-full bg-gray-50 dark:bg-gray-900 theme-transition">
    <!-- Dark Mode Toggle -->
    <div class="fixed top-4 right-4 z-50">
        <button id="theme-toggle" class="p-2 rounded-lg bg-white dark:bg-gray-800 shadow-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 theme-transition">
            <i id="theme-icon" class="fas fa-moon dark:hidden"></i>
            <i id="theme-icon-dark" class="fas fa-sun hidden dark:block"></i>
        </button>
    </div>

    <div class="flex h-full">
        <!-- Sidebar -->
        <div class="flex w-64 flex-col">
            <div class="flex min-h-0 flex-1 flex-col bg-gray-800 dark:bg-gray-900">
                <div class="flex flex-1 flex-col overflow-y-auto pt-5 pb-4">
                    <!-- Logo -->
                    <div class="flex flex-shrink-0 items-center px-4">
                        <img class="h-8 w-auto filter brightness-0 invert" src="../images/logo.svg" alt="Sky Border Solutions">
                        <span class="ml-2 text-white font-semibold">Admin Panel</span>
                    </div>
                    
                    <!-- Navigation -->
                    <nav class="mt-5 flex-1 space-y-1 px-2">
                        <a href="dashboard.php" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md theme-transition">
                            <i class="fas fa-tachometer-alt text-gray-400 group-hover:text-gray-300 mr-3"></i>
                            Dashboard
                        </a>
                        <a href="company-info.php" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md theme-transition">
                            <i class="fas fa-building text-gray-400 group-hover:text-gray-300 mr-3"></i>
                            Company Info
                        </a>
                        <a href="clients.php" class="bg-brand-blue/10 text-brand-blue dark:bg-brand-blue-dark/20 dark:text-brand-blue-light group flex items-center px-2 py-2 text-sm font-medium rounded-md theme-transition">
                            <i class="fas fa-users text-brand-blue dark:text-brand-blue-light mr-3"></i>
                            Clients
                        </a>
                        <a href="../" target="_blank" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md theme-transition">
                            <i class="fas fa-external-link-alt text-gray-400 group-hover:text-gray-300 mr-3"></i>
                            View Website
                        </a>
                        <a href="logout.php" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md theme-transition">
                            <i class="fas fa-sign-out-alt text-gray-400 group-hover:text-gray-300 mr-3"></i>
                            Logout
                        </a>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="flex flex-1 flex-col overflow-hidden">
            <!-- Top navigation -->
            <div class="relative z-10 flex h-16 flex-shrink-0 bg-white dark:bg-gray-800 shadow theme-transition">
                <div class="flex flex-1 justify-between px-4">
                    <div class="flex flex-1">
                        <div class="flex items-center">
                            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white theme-transition">Manage Clients</h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content area -->
            <main class="flex-1 relative overflow-y-auto focus:outline-none">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                        
                        <!-- Success/Error Messages -->
                        <?php if ($message): ?>
                        <div class="mb-6 <?php echo $messageType === 'success' ? 'bg-brand-green/10 border-brand-green/20 text-brand-green-dark' : 'bg-red-50 border-red-200 text-red-800'; ?> border rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="<?php echo $messageType === 'success' ? 'fas fa-check-circle text-brand-green' : 'fas fa-exclamation-circle text-red-400'; ?>"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm"><?php echo htmlspecialchars($message); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Add New Client Form -->
                        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg mb-8 theme-transition">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4 theme-transition">Add New Client</h3>
                                <form method="POST" class="space-y-4">
                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                        <div>
                                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Company Name</label>
                                            <input type="text" name="name" id="name" required 
                                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm theme-transition">
                                        </div>
                                        <div>
                                            <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Category</label>
                                            <select name="category" id="category" required 
                                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm theme-transition">
                                                <option value="">Select Category</option>
                                                <option value="Construction & Engineering">Construction & Engineering</option>
                                                <option value="Tourism & Hospitality">Tourism & Hospitality</option>
                                                <option value="Investments, Services & Trading">Investments, Services & Trading</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="logo_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Logo URL (Optional)</label>
                                            <input type="url" name="logo_url" id="logo_url" 
                                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm theme-transition"
                                                   placeholder="https://example.com/logo.png">
                                        </div>
                                        <div>
                                            <label for="display_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Display Order</label>
                                            <input type="number" name="display_order" id="display_order" min="0" value="0" 
                                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm theme-transition">
                                        </div>
                                    </div>
                                    <div class="pt-3">
                                        <button type="submit" name="add_client" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gradient-to-r from-brand-blue to-brand-teal hover:from-brand-blue-dark hover:to-brand-blue focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-blue">
                                            <i class="fas fa-plus mr-2"></i>
                                            Add Client
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Clients List -->
                        <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-md theme-transition">
                            <div class="px-4 py-5 sm:px-6">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white theme-transition">Current Clients</h3>
                                <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400 theme-transition">Manage your client list and their information.</p>
                            </div>
                            
                            <?php if (!empty($clients)): ?>
                            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                                <?php foreach ($clients as $client): ?>
                                <li class="px-4 py-4 sm:px-6">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4">
                                            <?php if (!empty($client['logo_url'])): ?>
                                            <div class="flex-shrink-0 h-12 w-12">
                                                <img class="h-12 w-12 rounded-lg object-contain bg-gray-100 dark:bg-gray-700 p-1" src="<?php echo htmlspecialchars($client['logo_url']); ?>" alt="<?php echo htmlspecialchars($client['name']); ?>">
                                            </div>
                                            <?php else: ?>
                                            <div class="flex-shrink-0 h-12 w-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-building text-gray-400 dark:text-gray-500"></i>
                                            </div>
                                            <?php endif; ?>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white theme-transition"><?php echo htmlspecialchars($client['name']); ?></p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400 theme-transition"><?php echo htmlspecialchars($client['category']); ?></p>
                                                <?php if (!empty($client['display_order'])): ?>
                                                <p class="text-xs text-gray-400 dark:text-gray-500 theme-transition">Order: <?php echo htmlspecialchars($client['display_order']); ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <button onclick="editClient(<?php echo htmlspecialchars(json_encode($client)); ?>)" class="text-brand-blue dark:text-brand-blue-light hover:text-brand-blue-dark dark:hover:text-brand-blue">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this client?')">
                                                <input type="hidden" name="id" value="<?php echo $client['id']; ?>">
                                                <button type="submit" name="delete_client" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php else: ?>
                            <div class="text-center py-8">
                                <i class="fas fa-users text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2 theme-transition">No clients yet</h3>
                                <p class="text-gray-600 dark:text-gray-400 theme-transition">Add your first client using the form above.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Edit Client Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800 theme-transition">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 theme-transition">Edit Client</h3>
                <form id="editForm" method="POST" class="space-y-4">
                    <input type="hidden" name="id" id="edit_id">
                    <div>
                        <label for="edit_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Company Name</label>
                        <input type="text" name="name" id="edit_name" required 
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm theme-transition">
                    </div>
                    <div>
                        <label for="edit_category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Category</label>
                        <select name="category" id="edit_category" required 
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm theme-transition">
                            <option value="Construction & Engineering">Construction & Engineering</option>
                            <option value="Tourism & Hospitality">Tourism & Hospitality</option>
                            <option value="Investments, Services & Trading">Investments, Services & Trading</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label for="edit_logo_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Logo URL</label>
                        <input type="url" name="logo_url" id="edit_logo_url" 
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm theme-transition">
                    </div>
                    <div>
                        <label for="edit_display_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Display Order</label>
                        <input type="number" name="display_order" id="edit_display_order" min="0" 
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm theme-transition">
                    </div>
                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-600 hover:bg-gray-200 dark:hover:bg-gray-500 rounded-md theme-transition">
                            Cancel
                        </button>
                        <button type="submit" name="update_client" class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-brand-blue to-brand-teal hover:from-brand-blue-dark hover:to-brand-blue rounded-md">
                            Update Client
                        </button>
                    </div>
                </form>
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

        // Edit client modal functions
        function editClient(client) {
            document.getElementById('edit_id').value = client.id;
            document.getElementById('edit_name').value = client.name;
            document.getElementById('edit_category').value = client.category;
            document.getElementById('edit_logo_url').value = client.logo_url || '';
            document.getElementById('edit_display_order').value = client.display_order || 0;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            initTheme();
            document.getElementById('theme-toggle').addEventListener('click', toggleTheme);
            
            // Close modal when clicking outside
            document.getElementById('editModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeEditModal();
                }
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
    </script>
</body>
</html>
