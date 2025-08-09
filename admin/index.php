<?php
/**
 * Admin Login Page
 * Sky Border Solutions CMS
 */

require_once 'classes/Auth.php';

$auth = new Auth();

// If already logged in, redirect to dashboard
if ($auth->isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';

// Handle logout message
if (isset($_GET['logged_out'])) {
    $success = 'You have been successfully logged out.';
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        if ($auth->login($username, $password)) {
            // Redirect to dashboard or requested page
            $redirect = $_GET['redirect'] ?? 'dashboard.php';
            header('Location: ' . $redirect);
            exit();
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Sky Border Solutions</title>
    
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
        .btn-primary {
            background: linear-gradient(135deg, #2E86AB 0%, #4ECDC4 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(46, 134, 171, 0.4);
        }
        .login-card {
            background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
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
<body class="h-full">
    <!-- Dark Mode Toggle -->
    <div class="fixed top-4 right-4 z-50">
        <button id="theme-toggle" class="p-2 rounded-lg bg-white dark:bg-gray-800 shadow-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 theme-transition">
            <i id="theme-icon" class="fas fa-moon dark:hidden"></i>
            <i id="theme-icon-dark" class="fas fa-sun hidden dark:block"></i>
        </button>
    </div>

    <div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <!-- Logo and Title -->
            <div class="text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-r from-brand-blue to-brand-teal">
                    <img src="../images/logo.svg" alt="Sky Border Solutions" class="h-8 w-8">
                </div>
                <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900 dark:text-white theme-transition">
                    Admin Portal
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400 theme-transition">
                    Sky Border Solutions CMS
                </p>
            </div>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="login-card dark:bg-gray-800 px-4 py-8 shadow sm:rounded-lg sm:px-10 theme-transition">
                <!-- Success Message -->
                <?php if ($success): ?>
                <div class="mb-4 rounded-md bg-green-50 dark:bg-green-900/20 p-4">
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

                <!-- Error Message -->
                <?php if ($error): ?>
                <div class="mb-4 rounded-md bg-red-50 dark:bg-red-900/20 p-4">
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

                <!-- Login Form -->
                <form method="POST" class="space-y-6">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-900 dark:text-white theme-transition">
                            Username
                        </label>
                        <div class="mt-1">
                            <input id="username" name="username" type="text" required 
                                   value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                                   class="block w-full appearance-none rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-brand-blue focus:outline-none focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-900 dark:text-white theme-transition">
                            Password
                        </label>
                        <div class="mt-1">
                            <input id="password" name="password" type="password" required
                                   class="block w-full appearance-none rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-brand-blue focus:outline-none focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="btn-primary group relative flex w-full justify-center rounded-md px-3 py-2 text-sm font-semibold text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-blue">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fas fa-sign-in-alt text-white group-hover:text-gray-200"></i>
                            </span>
                            Sign in
                        </button>
                    </div>
                </form>

                <!-- Footer -->
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300 dark:border-gray-600" />
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="bg-white dark:bg-gray-800 px-2 text-gray-500 dark:text-gray-400 theme-transition">
                                Secure Admin Access
                            </span>
                        </div>
                    </div>
                    
                    <div class="mt-6 text-center">
                        <a href="../index.php" class="text-sm text-brand-blue hover:text-brand-blue-dark theme-transition">
                            <i class="fas fa-home mr-1"></i>
                            Back to Website
                        </a>
                    </div>
                </div>
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

        // Auto-focus username field
        document.getElementById('username').focus();
    </script>
</body>
</html>