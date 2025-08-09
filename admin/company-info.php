<?php
require_once 'classes/Auth.php';
require_once 'classes/ContentManager.php';

$auth = new Auth();
$auth->requireLogin();

$contentManager = new ContentManager();
$user = $auth->getCurrentUser();

$success = '';
$error = '';

// Handle form submission
if ($_POST) {
    $data = [
        'company_name' => $_POST['company_name'] ?? '',
        'tagline' => $_POST['tagline'] ?? '',
        'description' => $_POST['description'] ?? '',
        'mission' => $_POST['mission'] ?? '',
        'vision' => $_POST['vision'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'hotline1' => $_POST['hotline1'] ?? '',
        'hotline2' => $_POST['hotline2'] ?? '',
        'email' => $_POST['email'] ?? '',
        'address' => $_POST['address'] ?? '',
        'business_hours' => $_POST['business_hours'] ?? ''
    ];
    
    if ($contentManager->updateCompanyInfo($data)) {
        $success = 'Company information updated successfully!';
    } else {
        $error = 'Failed to update company information.';
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
    <title>Company Information - Sky Border Solutions Admin</title>
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
                        <div class="flex items-center space-x-3">
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($user['name']); ?></p>
                                <p class="text-xs text-gray-500"><?php echo htmlspecialchars($user['role']); ?></p>
                            </div>
                            <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                <i class="fas fa-user text-indigo-600"></i>
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
                            <a href="dashboard.php" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <i class="fas fa-tachometer-alt text-gray-400 mr-3"></i>
                                Dashboard
                            </a>
                            
                            <a href="company-info.php" class="bg-indigo-50 text-indigo-700 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <i class="fas fa-building text-indigo-500 mr-3"></i>
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
                            <!-- Header -->
                            <div class="mb-8">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h1 class="text-2xl font-bold text-gray-900">Company Information</h1>
                                        <p class="mt-1 text-sm text-gray-600">Manage your company details and contact information</p>
                                    </div>
                                    <a href="../index.html" target="_blank" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                        <i class="fas fa-external-link-alt mr-2"></i>
                                        View Website
                                    </a>
                                </div>
                            </div>

                            <!-- Success/Error Messages -->
                            <?php if ($success): ?>
                            <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-check-circle text-green-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-green-800"><?php echo htmlspecialchars($success); ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if ($error): ?>
                            <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-circle text-red-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-red-800"><?php echo htmlspecialchars($error); ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Company Information Form -->
                            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                                <form method="POST" class="space-y-6 p-6">
                                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                        <!-- Company Name -->
                                        <div class="sm:col-span-2">
                                            <label for="company_name" class="block text-sm font-medium text-gray-700">Company Name</label>
                                            <input type="text" id="company_name" name="company_name" required
                                                   value="<?php echo htmlspecialchars($companyInfo['company_name'] ?? ''); ?>"
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>

                                        <!-- Tagline -->
                                        <div class="sm:col-span-2">
                                            <label for="tagline" class="block text-sm font-medium text-gray-700">Tagline</label>
                                            <input type="text" id="tagline" name="tagline"
                                                   value="<?php echo htmlspecialchars($companyInfo['tagline'] ?? ''); ?>"
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>

                                        <!-- Description -->
                                        <div class="sm:col-span-2">
                                            <label for="description" class="block text-sm font-medium text-gray-700">Company Description</label>
                                            <textarea id="description" name="description" rows="3"
                                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"><?php echo htmlspecialchars($companyInfo['description'] ?? ''); ?></textarea>
                                        </div>

                                        <!-- Mission -->
                                        <div class="sm:col-span-2">
                                            <label for="mission" class="block text-sm font-medium text-gray-700">Mission Statement</label>
                                            <textarea id="mission" name="mission" rows="3"
                                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"><?php echo htmlspecialchars($companyInfo['mission'] ?? ''); ?></textarea>
                                        </div>

                                        <!-- Vision -->
                                        <div class="sm:col-span-2">
                                            <label for="vision" class="block text-sm font-medium text-gray-700">Vision Statement</label>
                                            <textarea id="vision" name="vision" rows="3"
                                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"><?php echo htmlspecialchars($companyInfo['vision'] ?? ''); ?></textarea>
                                        </div>

                                        <!-- Phone -->
                                        <div>
                                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                                            <input type="text" id="phone" name="phone"
                                                   value="<?php echo htmlspecialchars($companyInfo['phone'] ?? ''); ?>"
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>

                                        <!-- Email -->
                                        <div>
                                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                            <input type="email" id="email" name="email"
                                                   value="<?php echo htmlspecialchars($companyInfo['email'] ?? ''); ?>"
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>

                                        <!-- Hotline 1 -->
                                        <div>
                                            <label for="hotline1" class="block text-sm font-medium text-gray-700">Hotline 1</label>
                                            <input type="text" id="hotline1" name="hotline1"
                                                   value="<?php echo htmlspecialchars($companyInfo['hotline1'] ?? ''); ?>"
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>

                                        <!-- Hotline 2 -->
                                        <div>
                                            <label for="hotline2" class="block text-sm font-medium text-gray-700">Hotline 2</label>
                                            <input type="text" id="hotline2" name="hotline2"
                                                   value="<?php echo htmlspecialchars($companyInfo['hotline2'] ?? ''); ?>"
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>

                                        <!-- Address -->
                                        <div class="sm:col-span-2">
                                            <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                                            <textarea id="address" name="address" rows="2"
                                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"><?php echo htmlspecialchars($companyInfo['address'] ?? ''); ?></textarea>
                                        </div>

                                        <!-- Business Hours -->
                                        <div class="sm:col-span-2">
                                            <label for="business_hours" class="block text-sm font-medium text-gray-700">Business Hours</label>
                                            <textarea id="business_hours" name="business_hours" rows="3"
                                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"><?php echo htmlspecialchars($companyInfo['business_hours'] ?? ''); ?></textarea>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="flex justify-end">
                                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <i class="fas fa-save mr-2"></i>
                                            Save Changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <script>
        // Auto-save form data to localStorage
        const form = document.querySelector('form');
        const inputs = form.querySelectorAll('input, textarea');
        
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                localStorage.setItem('companyInfo_' + this.name, this.value);
            });
        });
        
        // Form validation
        form.addEventListener('submit', function(e) {
            const companyName = document.getElementById('company_name').value.trim();
            
            if (!companyName) {
                e.preventDefault();
                alert('Company name is required');
                return;
            }
            
            // Show loading state
            const button = form.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
            button.disabled = true;
            
            // Clear localStorage on successful save
            setTimeout(() => {
                inputs.forEach(input => {
                    localStorage.removeItem('companyInfo_' + input.name);
                });
            }, 1000);
        });
    </script>
</body>
</html>
