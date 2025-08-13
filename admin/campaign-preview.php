<?php
require_once 'config/database.php';
require_once 'classes/Auth.php';

// Initialize authentication
$auth = new Auth();
if (!$auth->isLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Start session to get preview data
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$campaignPreview = $_SESSION['campaign_preview'] ?? null;

if (!$campaignPreview) {
    header('Location: campaigns.php?error=No preview data available');
    exit;
}

// Clear the preview data from session
unset($_SESSION['campaign_preview']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campaign Preview - Sky Border Solutions</title>
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
                        <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Campaign Preview</h1>
                        <div class="flex space-x-2">
                            <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                                <i class="fas fa-print mr-2"></i>Print
                            </button>
                            <button onclick="copyToClipboard()" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                                <i class="fas fa-copy mr-2"></i>Copy HTML
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Preview Controls -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Preview Options</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Preview Mode</label>
                        <select id="preview-mode" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="desktop">Desktop Email Client</option>
                            <option value="mobile">Mobile Email Client</option>
                            <option value="plain">Plain Text</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Test Recipient</label>
                        <input type="email" id="test-email" placeholder="test@example.com" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Actions</label>
                        <button onclick="sendTestEmail()" class="w-full bg-green-700 text-white px-4 py-2 rounded-md hover:bg-green-800 transition-colors">
                            <i class="fas fa-paper-plane mr-2"></i>Send Test
                        </button>
                    </div>
                </div>
            </div>

            <!-- Email Preview -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Email Preview</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">This is how your email will appear to recipients</p>
                </div>
                
                <div class="p-6">
                    <div id="email-preview" class="max-w-2xl mx-auto border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden">
                        <?php echo $campaignPreview; ?>
                    </div>
                </div>
            </div>

            <!-- HTML Source -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow mt-8">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">HTML Source Code</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">The HTML code that will be sent in the email</p>
                </div>
                
                <div class="p-6">
                    <div class="bg-gray-900 text-gray-100 rounded-lg p-4 overflow-x-auto">
                        <pre><code id="html-source"><?php echo htmlspecialchars($campaignPreview); ?></code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden textarea for copying HTML -->
    <textarea id="html-copy" style="position: absolute; left: -9999px;"><?php echo $campaignPreview; ?></textarea>

    <script>
        // Preview mode switching
        document.getElementById('preview-mode').addEventListener('change', function() {
            const preview = document.getElementById('email-preview');
            const mode = this.value;
            
            if (mode === 'mobile') {
                preview.style.maxWidth = '375px';
                preview.style.margin = '0 auto';
            } else if (mode === 'desktop') {
                preview.style.maxWidth = '600px';
                preview.style.margin = '0 auto';
            } else if (mode === 'plain') {
                // Convert to plain text (simplified)
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = preview.innerHTML;
                const plainText = tempDiv.textContent || tempDiv.innerText || '';
                preview.innerHTML = `<div style="font-family: monospace; white-space: pre-wrap; padding: 20px;">${plainText}</div>`;
                preview.style.maxWidth = '600px';
                preview.style.margin = '0 auto';
            }
        });

        // Copy HTML to clipboard
        function copyToClipboard() {
            const textarea = document.getElementById('html-copy');
            textarea.select();
            document.execCommand('copy');
            
            // Show feedback
            const button = event.target.closest('button');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check mr-2"></i>Copied!';
            button.classList.remove('bg-green-600', 'hover:bg-green-700');
            button.classList.add('bg-green-500');
            
            setTimeout(() => {
                button.innerHTML = originalText;
                button.classList.remove('bg-green-500');
                button.classList.add('bg-green-600', 'hover:bg-green-700');
            }, 2000);
        }

        // Send test email
        function sendTestEmail() {
            const email = document.getElementById('test-email').value;
            if (!email) {
                alert('Please enter a test email address');
                return;
            }
            
            if (confirm(`Send test email to ${email}?`)) {
                // Here you would implement the test email sending
                // For now, just show a success message
                alert('Test email sent successfully! (This is a demo - no actual email was sent)');
            }
        }

        // Print functionality
        function printPreview() {
            const printContent = document.getElementById('email-preview').innerHTML;
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Campaign Preview</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        .email-preview { max-width: 600px; margin: 0 auto; }
                    </style>
                </head>
                <body>
                    <div class="email-preview">${printContent}</div>
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        }
    </script>
</body>
</html>
