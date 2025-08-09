<?php
/**
 * Company Information Management
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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'company_name' => trim($_POST['company_name'] ?? ''),
        'tagline' => trim($_POST['tagline'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'mission' => trim($_POST['mission'] ?? ''),
        'vision' => trim($_POST['vision'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'hotline1' => trim($_POST['hotline1'] ?? ''),
        'hotline2' => trim($_POST['hotline2'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
        'business_hours' => trim($_POST['business_hours'] ?? '')
    ];
    
    // Validate required fields
    if (empty($data['company_name']) || empty($data['email'])) {
        $error = 'Company name and email are required.';
    } else {
        if ($contentManager->updateCompanyInfo($data)) {
            $success = 'Company information updated successfully!';
        } else {
            $error = 'Failed to update company information. Please try again.';
        }
    }
}

// Get current company info
$companyInfo = $contentManager->getCompanyInfo();
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Information - Sky Border Solutions CMS</title>
    
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
                            <a href="company-info.php" class="border-brand-blue text-gray-900 dark:text-white inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium theme-transition">
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
                    <div class="flex items-center">
                        <a href="dashboard.php" class="text-brand-blue hover:text-brand-blue-dark mr-4">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h1 class="text-3xl font-bold leading-tight tracking-tight text-gray-900 dark:text-white theme-transition">Company Information</h1>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 theme-transition">
                                Update your company details that appear on the website
                            </p>
                        </div>
                    </div>
                </div>
            </header>
            
            <main>
                <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
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

                        <!-- Form -->
                        <div class="modern-card bg-white dark:bg-gray-800 shadow theme-transition">
                            <form method="POST" class="space-y-6 p-6">
                                <!-- Basic Information -->
                                <div>
                                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white mb-4 theme-transition">Basic Information</h3>
                                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                        <div>
                                            <label for="company_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Company Name *</label>
                                            <input type="text" name="company_name" id="company_name" required
                                                   value="<?php echo htmlspecialchars($companyInfo['company_name']); ?>"
                                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                                        </div>

                                        <div>
                                            <label for="tagline" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Tagline</label>
                                            <input type="text" name="tagline" id="tagline"
                                                   value="<?php echo htmlspecialchars($companyInfo['tagline']); ?>"
                                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                                        </div>
                                    </div>

                                    <div class="mt-6">
                                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Description</label>
                                        <textarea name="description" id="description" rows="3"
                                                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition"><?php echo htmlspecialchars($companyInfo['description']); ?></textarea>
                                    </div>
                                </div>

                                <!-- Mission & Vision -->
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white mb-4 theme-transition">Mission & Vision</h3>
                                    <div class="space-y-6">
                                        <div>
                                            <label for="mission" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Mission Statement</label>
                                            <textarea name="mission" id="mission" rows="3"
                                                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition"><?php echo htmlspecialchars($companyInfo['mission']); ?></textarea>
                                        </div>

                                        <div>
                                            <label for="vision" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Vision Statement</label>
                                            <textarea name="vision" id="vision" rows="3"
                                                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition"><?php echo htmlspecialchars($companyInfo['vision']); ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contact Information -->
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white mb-4 theme-transition">Contact Information</h3>
                                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                        <div>
                                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Main Phone</label>
                                            <input type="text" name="phone" id="phone"
                                                   value="<?php echo htmlspecialchars($companyInfo['phone']); ?>"
                                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                                        </div>

                                        <div>
                                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Email Address *</label>
                                            <input type="email" name="email" id="email" required
                                                   value="<?php echo htmlspecialchars($companyInfo['email']); ?>"
                                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                                        </div>

                                        <div>
                                            <label for="hotline1" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Hotline 1</label>
                                            <input type="text" name="hotline1" id="hotline1"
                                                   value="<?php echo htmlspecialchars($companyInfo['hotline1']); ?>"
                                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                                        </div>

                                        <div>
                                            <label for="hotline2" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Hotline 2</label>
                                            <input type="text" name="hotline2" id="hotline2"
                                                   value="<?php echo htmlspecialchars($companyInfo['hotline2']); ?>"
                                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                                        </div>
                                    </div>

                                    <div class="mt-6">
                                        <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Address</label>
                                        <textarea name="address" id="address" rows="3"
                                                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition"><?php echo htmlspecialchars($companyInfo['address']); ?></textarea>
                                    </div>

                                    <div class="mt-6">
                                        <label for="business_hours" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Business Hours</label>
                                        <textarea name="business_hours" id="business_hours" rows="3"
                                                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition"><?php echo htmlspecialchars($companyInfo['business_hours']); ?></textarea>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                                    <div class="flex justify-end space-x-3">
                                        <a href="dashboard.php" class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md py-2 px-4 inline-flex justify-center text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 theme-transition">
                                            Cancel
                                        </a>
                                        <button type="submit" class="bg-gradient-to-r from-brand-blue to-brand-teal border border-transparent rounded-md py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:from-brand-blue-dark hover:to-brand-blue focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-blue">
                                            <i class="fas fa-save mr-2"></i>
                                            Save Changes
                                        </button>
                                    </div>
                                </div>
                            </form>
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