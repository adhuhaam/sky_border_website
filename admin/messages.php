<?php
/**
 * Messages Management
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
$messageId = $_GET['id'] ?? null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $message_id = (int)($_POST['message_id'] ?? 0);
        $status = $_POST['status'] ?? 'new';
        $admin_notes = trim($_POST['admin_notes'] ?? '');
        
        if ($contentManager->updateContactMessageStatus($message_id, $status, $admin_notes)) {
            $success = 'Message status updated successfully!';
            $action = 'list';
        } else {
            $error = 'Failed to update message status. Please try again.';
        }
    }
}

// Get data for the page
$statusFilter = $_GET['status'] ?? null;
$messages = $contentManager->getContactMessages($statusFilter, 100);

// Get specific message for viewing
$viewMessage = null;
if ($action === 'view' && $messageId) {
    foreach ($messages as $message) {
        if ($message['id'] == $messageId) {
            $viewMessage = $message;
            break;
        }
    }
    if (!$viewMessage) {
        $error = 'Message not found.';
        $action = 'list';
    }
}

// Count messages by status
$statusCounts = [
    'new' => 0,
    'read' => 0,
    'replied' => 0,
    'archived' => 0
];

foreach ($messages as $message) {
    if (isset($statusCounts[$message['status']])) {
        $statusCounts[$message['status']]++;
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages Management - Sky Border Solutions CMS</title>
    
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
                            <a href="services.php" class="border-transparent text-gray-500 dark:text-gray-300 hover:border-gray-300 hover:text-gray-700 dark:hover:text-white inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium theme-transition">
                                <i class="fas fa-cogs mr-2"></i>
                                Services
                            </a>
                            <a href="clients.php" class="border-transparent text-gray-500 dark:text-gray-300 hover:border-gray-300 hover:text-gray-700 dark:hover:text-white inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium theme-transition">
                                <i class="fas fa-users mr-2"></i>
                                Clients
                            </a>
                            <a href="messages.php" class="border-brand-blue text-gray-900 dark:text-white inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium theme-transition">
                                <i class="fas fa-envelope mr-2"></i>
                                Messages
                                <?php if ($statusCounts['new'] > 0): ?>
                                <span class="ml-1 bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full"><?php echo $statusCounts['new']; ?></span>
                                <?php endif; ?>
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
                                    <?php if ($action === 'view'): ?>
                                        Message Details
                                    <?php else: ?>
                                        Contact Messages
                                    <?php endif; ?>
                                </h1>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 theme-transition">
                                    <?php if ($action === 'list'): ?>
                                        Manage messages received from your website contact form
                                    <?php else: ?>
                                        View and respond to customer inquiries
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
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
                        <!-- Status Filter Tabs -->
                        <div class="mb-6">
                            <nav class="flex space-x-8" aria-label="Tabs">
                                <a href="messages.php" class="<?php echo !$statusFilter ? 'border-brand-blue text-brand-blue' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm theme-transition">
                                    All Messages (<?php echo count($messages); ?>)
                                </a>
                                <a href="messages.php?status=new" class="<?php echo $statusFilter === 'new' ? 'border-brand-blue text-brand-blue' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm theme-transition">
                                    New (<?php echo $statusCounts['new']; ?>)
                                </a>
                                <a href="messages.php?status=read" class="<?php echo $statusFilter === 'read' ? 'border-brand-blue text-brand-blue' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm theme-transition">
                                    Read (<?php echo $statusCounts['read']; ?>)
                                </a>
                                <a href="messages.php?status=replied" class="<?php echo $statusFilter === 'replied' ? 'border-brand-blue text-brand-blue' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm theme-transition">
                                    Replied (<?php echo $statusCounts['replied']; ?>)
                                </a>
                            </nav>
                        </div>

                        <!-- Messages List -->
                        <div class="modern-card bg-white dark:bg-gray-800 shadow overflow-hidden theme-transition">
                            <?php if (empty($messages)): ?>
                            <div class="text-center py-12">
                                <i class="fas fa-envelope-open text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2 theme-transition">No messages</h3>
                                <p class="text-gray-600 dark:text-gray-400 theme-transition">
                                    <?php if ($statusFilter): ?>
                                        No messages with "<?php echo htmlspecialchars($statusFilter); ?>" status.
                                    <?php else: ?>
                                        No contact messages received yet.
                                    <?php endif; ?>
                                </p>
                            </div>
                            <?php else: ?>
                            <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                                <?php foreach ($messages as $message): ?>
                                <li class="hover:bg-gray-50 dark:hover:bg-gray-700 theme-transition">
                                    <a href="?action=view&id=<?php echo $message['id']; ?>" class="block px-6 py-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center min-w-0 flex-1">
                                                <div class="flex-shrink-0">
                                                    <div class="h-10 w-10 rounded-full bg-gradient-to-r from-brand-blue to-brand-teal flex items-center justify-center">
                                                        <i class="fas fa-user text-white text-sm"></i>
                                                    </div>
                                                </div>
                                                <div class="ml-4 min-w-0 flex-1">
                                                    <div class="flex items-center">
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate theme-transition">
                                                            <?php echo htmlspecialchars($message['name']); ?>
                                                        </p>
                                                        <?php if ($message['company']): ?>
                                                        <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">
                                                            at <?php echo htmlspecialchars($message['company']); ?>
                                                        </span>
                                                        <?php endif; ?>
                                                        <?php if ($message['status'] === 'new'): ?>
                                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            New
                                                        </span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400 theme-transition">
                                                        <?php echo htmlspecialchars($message['email']); ?>
                                                    </p>
                                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1 truncate theme-transition">
                                                        <?php echo htmlspecialchars(substr($message['message'], 0, 100)) . (strlen($message['message']) > 100 ? '...' : ''); ?>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex-shrink-0 text-right">
                                                <p class="text-sm text-gray-900 dark:text-white theme-transition">
                                                    <?php echo date('M j, Y', strtotime($message['created_at'])); ?>
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 theme-transition">
                                                    <?php echo date('g:i A', strtotime($message['created_at'])); ?>
                                                </p>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1 
                                                    <?php 
                                                    switch($message['status']) {
                                                        case 'new': echo 'bg-red-100 text-red-800'; break;
                                                        case 'read': echo 'bg-blue-100 text-blue-800'; break;
                                                        case 'replied': echo 'bg-green-100 text-green-800'; break;
                                                        case 'archived': echo 'bg-gray-100 text-gray-800'; break;
                                                        default: echo 'bg-gray-100 text-gray-800';
                                                    }
                                                    ?>">
                                                    <?php echo ucfirst($message['status']); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                        </div>

                        <?php elseif ($action === 'view' && $viewMessage): ?>
                        <!-- Message Details -->
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                            <!-- Message Content -->
                            <div class="lg:col-span-2">
                                <div class="modern-card bg-white dark:bg-gray-800 shadow theme-transition">
                                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white theme-transition">
                                            Message from <?php echo htmlspecialchars($viewMessage['name']); ?>
                                        </h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 theme-transition">
                                            Received <?php echo date('F j, Y \a\t g:i A', strtotime($viewMessage['created_at'])); ?>
                                        </p>
                                    </div>
                                    <div class="p-6">
                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Email</label>
                                                <p class="mt-1 text-sm text-gray-900 dark:text-white theme-transition">
                                                    <a href="mailto:<?php echo htmlspecialchars($viewMessage['email']); ?>" class="text-brand-blue hover:text-brand-blue-dark">
                                                        <?php echo htmlspecialchars($viewMessage['email']); ?>
                                                    </a>
                                                </p>
                                            </div>
                                            
                                            <?php if ($viewMessage['company']): ?>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Company</label>
                                                <p class="mt-1 text-sm text-gray-900 dark:text-white theme-transition"><?php echo htmlspecialchars($viewMessage['company']); ?></p>
                                            </div>
                                            <?php endif; ?>
                                            
                                            <?php if ($viewMessage['phone']): ?>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Phone</label>
                                                <p class="mt-1 text-sm text-gray-900 dark:text-white theme-transition">
                                                    <a href="tel:<?php echo htmlspecialchars($viewMessage['phone']); ?>" class="text-brand-blue hover:text-brand-blue-dark">
                                                        <?php echo htmlspecialchars($viewMessage['phone']); ?>
                                                    </a>
                                                </p>
                                            </div>
                                            <?php endif; ?>
                                            
                                            <?php if ($viewMessage['subject']): ?>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Subject</label>
                                                <p class="mt-1 text-sm text-gray-900 dark:text-white theme-transition"><?php echo htmlspecialchars($viewMessage['subject']); ?></p>
                                            </div>
                                            <?php endif; ?>
                                            
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Message</label>
                                                <div class="mt-1 p-4 bg-gray-50 dark:bg-gray-700 rounded-md">
                                                    <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap theme-transition"><?php echo htmlspecialchars($viewMessage['message']); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Actions Panel -->
                            <div>
                                <div class="modern-card bg-white dark:bg-gray-800 shadow theme-transition">
                                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white theme-transition">Actions</h3>
                                    </div>
                                    <div class="p-6">
                                        <form method="POST" class="space-y-4">
                                            <input type="hidden" name="message_id" value="<?php echo $viewMessage['id']; ?>">
                                            
                                            <div>
                                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Status</label>
                                                <select name="status" id="status" 
                                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                                                    <option value="new" <?php echo $viewMessage['status'] === 'new' ? 'selected' : ''; ?>>New</option>
                                                    <option value="read" <?php echo $viewMessage['status'] === 'read' ? 'selected' : ''; ?>>Read</option>
                                                    <option value="replied" <?php echo $viewMessage['status'] === 'replied' ? 'selected' : ''; ?>>Replied</option>
                                                    <option value="archived" <?php echo $viewMessage['status'] === 'archived' ? 'selected' : ''; ?>>Archived</option>
                                                </select>
                                            </div>
                                            
                                            <div>
                                                <label for="admin_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Admin Notes</label>
                                                <textarea name="admin_notes" id="admin_notes" rows="3"
                                                          class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition"><?php echo htmlspecialchars($viewMessage['admin_notes'] ?? ''); ?></textarea>
                                            </div>
                                            
                                            <button type="submit" name="update_status" class="w-full bg-gradient-to-r from-brand-blue to-brand-teal border border-transparent rounded-md py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:from-brand-blue-dark hover:to-brand-blue focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-blue">
                                                <i class="fas fa-save mr-2"></i>
                                                Update Status
                                            </button>
                                        </form>
                                        
                                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                            <div class="space-y-3">
                                                <a href="mailto:<?php echo htmlspecialchars($viewMessage['email']); ?>" class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md py-2 px-4 inline-flex justify-center text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 theme-transition">
                                                    <i class="fas fa-reply mr-2"></i>
                                                    Reply via Email
                                                </a>
                                                
                                                <a href="messages.php" class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md py-2 px-4 inline-flex justify-center text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 theme-transition">
                                                    <i class="fas fa-arrow-left mr-2"></i>
                                                    Back to Messages
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
