<?php
/**
 * Unsubscribe Page
 * Allows email recipients to unsubscribe from campaigns
 */

$email = $_GET['email'] ?? '';
$campaign = $_GET['campaign'] ?? '';
$message = '';
$error = '';

if ($email && $campaign) {
    try {
        // Initialize database connection
        require_once 'admin/config/database.php';
        require_once 'admin/classes/Mailer.php';
        
        $database = new Database();
        $conn = $database->getConnection();
        
        if ($conn) {
            $mailer = new Mailer($conn);
            
            // Mark as unsubscribed
            $stmt = $conn->prepare("
                INSERT INTO unsubscribes (email, campaign_id, reason) 
                VALUES (?, ?, 'User unsubscribed via link')
                ON DUPLICATE KEY UPDATE 
                reason = VALUES(reason),
                unsubscribed_at = NOW()
            ");
            
            if ($stmt->execute([$email, $campaign])) {
                $message = "You have been successfully unsubscribed from this campaign.";
            } else {
                $error = "Failed to unsubscribe. Please try again.";
            }
        } else {
            $error = "Database connection failed.";
        }
    } catch (Exception $e) {
        $error = "An error occurred while processing your request.";
        error_log("Unsubscribe error: " . $e->getMessage());
    }
} else {
    $error = "Invalid unsubscribe link.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unsubscribe - Sky Border Solutions</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-auto px-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8">
            <!-- Logo -->
            <div class="text-center mb-8">
                <img src="images/logo.svg" alt="Sky Border Solutions" class="h-16 w-auto mx-auto mb-4">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Unsubscribe</h1>
            </div>

            <?php if ($message): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="text-center">
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    <?php if ($message): ?>
                        You will no longer receive emails from this campaign. If you change your mind, you can contact us to resubscribe.
                    <?php elseif ($error): ?>
                        We're sorry, but there was an issue processing your unsubscribe request.
                    <?php else: ?>
                        Please use the unsubscribe link from your email to unsubscribe from campaigns.
                    <?php endif; ?>
                </p>

                <div class="space-y-3">
                    <a href="index.php" class="block w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-home mr-2"></i>Return to Website
                    </a>
                    
                    <a href="mailto:info@skybordersolutions.com" class="block w-full bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
                        <i class="fas fa-envelope mr-2"></i>Contact Support
                    </a>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Sky Border Solutions<br>
                    Professional Workforce Solutions
                </p>
            </div>
        </div>
    </div>
</body>
</html>
