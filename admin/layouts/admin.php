<?php
/**
 * Main Admin Layout
 * Sky Border Solutions CMS
 * 
 * This is the main layout template for all admin pages
 * Similar to Laravel Blade layouts
 */

// Ensure we have required variables
$pageTitle = $pageTitle ?? 'Admin Panel';
$pageDescription = $pageDescription ?? 'Sky Border Solutions Content Management System';
$currentUser = $currentUser ?? ['full_name' => 'Admin User'];
$bodyClass = $bodyClass ?? '';
$additionalCSS = $additionalCSS ?? '';
$additionalJS = $additionalJS ?? '';
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Sky Border Solutions CMS</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Base Styles -->
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
        .dark .modern-card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
        }
        .btn-primary {
            background: linear-gradient(135deg, #1a5a7a 0%, #2a7a6e 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(26, 90, 122, 0.3);
        }
        <?php echo $additionalCSS; ?>
    </style>
    
    <!-- Tailwind Config -->
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
<body class="h-full bg-gray-50 dark:bg-gray-900 theme-transition <?php echo $bodyClass; ?>">
    <div class="flex h-screen">
        <!-- Include Sidebar -->
        <?php include __DIR__ . '/../includes/sidebar.php'; ?>

        <!-- Main content area -->
        <div class="flex-1 md:pl-64">
            <div class="flex flex-col h-full">
                <!-- Main content -->
                <main class="flex-1 relative overflow-y-auto focus:outline-none">
                    <div class="py-6">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                            
                            <!-- Page Header Section -->
                            <?php if (isset($showPageHeader) && $showPageHeader !== false): ?>
                            <div class="mb-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white theme-transition">
                                            <?php echo htmlspecialchars($pageTitle); ?>
                                        </h1>
                                        <?php if (!empty($pageDescription)): ?>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 theme-transition">
                                            <?php echo htmlspecialchars($pageDescription); ?>
                                        </p>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Page Actions -->
                                    <?php if (isset($pageActions)): ?>
                                    <div class="flex items-center space-x-4">
                                        <?php echo $pageActions; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Success/Error Messages -->
                            <?php if (isset($success) && $success): ?>
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

                            <?php if (isset($error) && $error): ?>
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
                            
                            <!-- Main Content Area -->
                            <div class="content-area">
                                <?php 
                                // This is where the page-specific content will be included
                                if (isset($contentFile) && file_exists($contentFile)) {
                                    include $contentFile;
                                } elseif (isset($content)) {
                                    echo $content;
                                } else {
                                    echo '<div class="text-center py-12"><p class="text-gray-500">No content specified</p></div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <!-- Base JavaScript -->
    <script>
        // Dark mode functionality (included in sidebar but repeated for safety)
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
        document.addEventListener('DOMContentLoaded', function() {
            initTheme();
        });

        // Common admin functions
        function confirmDelete(message = 'Are you sure you want to delete this item?') {
            return confirm(message);
        }

        function showSuccessMessage(message) {
            // You can implement toast notifications here
            console.log('Success:', message);
        }

        function showErrorMessage(message) {
            // You can implement toast notifications here
            console.error('Error:', message);
        }
        
        <?php echo $additionalJS; ?>
    </script>
</body>
</html>
