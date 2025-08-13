<?php
require_once 'config/database.php';
require_once 'classes/ContentManager.php';
require_once 'classes/Mailer.php';

// Initialize database and mailer
$database = new Database();
$conn = $database->getConnection();
$mailer = new Mailer($conn);

$message = '';
$error = '';
$renderedHtml = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_render'])) {
    try {
        $url = $_POST['url'] ?? '/';
        $renderedHtml = $mailer->renderWebsiteAsEmail($url);
        $message = 'Email rendering successful!';
    } catch (Exception $e) {
        $error = 'Error rendering email: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Email Rendering - Sky Border Solutions</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <a href="campaigns.php" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Campaigns
                        </a>
                    </div>
                    <div class="flex items-center space-x-4">
                        <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Test Email Rendering</h1>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Form -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Test Email Rendering</h2>
                
                <?php if ($message): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Website URL to Render</label>
                        <input type="url" name="url" value="/" placeholder="https://yoursite.com or /" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Use "/" for homepage or full URL for specific page</p>
                    </div>
                    
                    <button type="submit" name="test_render" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-eye mr-2"></i>Test Render
                    </button>
                </form>
            </div>

            <!-- Rendered HTML Preview -->
            <?php if ($renderedHtml): ?>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Rendered Email HTML</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">This is how your email will appear to recipients</p>
                </div>
                
                <div class="p-6">
                    <div class="max-w-4xl mx-auto border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden">
                        <?php echo $renderedHtml; ?>
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">HTML Source Code</h4>
                    <details class="text-sm">
                        <summary class="cursor-pointer text-blue-600 hover:text-blue-800">Click to view HTML source</summary>
                        <pre class="mt-2 p-4 bg-gray-900 text-gray-100 rounded overflow-x-auto text-xs"><code><?php echo htmlspecialchars($renderedHtml); ?></code></pre>
                    </details>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
