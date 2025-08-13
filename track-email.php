<?php
/**
 * Email Tracking Script
 * Handles tracking pixel requests for email opens and click tracking
 */

// Set content type to 1x1 transparent GIF
header('Content-Type: image/gif');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Get tracking parameters
$type = $_GET['type'] ?? '';
$email = $_GET['email'] ?? '';
$campaign = $_GET['campaign'] ?? '';

if ($type && $email && $campaign) {
    try {
        // Initialize database connection
        require_once 'admin/config/database.php';
        require_once 'admin/classes/Mailer.php';
        
        $database = new Database();
        $conn = $database->getConnection();
        
        if ($conn) {
            $mailer = new Mailer($conn);
            
            // Track the event based on type
            if ($type === 'open') {
                $mailer->trackEmailOpen($campaign . '_' . md5($email) . '_' . time());
            } elseif ($type === 'click') {
                $mailer->trackEmailClick($campaign . '_' . md5($email) . '_' . time());
            }
        }
    } catch (Exception $e) {
        // Log error silently to avoid breaking email display
        error_log("Email tracking error: " . $e->getMessage());
    }
}

// Output 1x1 transparent GIF
echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
?>
