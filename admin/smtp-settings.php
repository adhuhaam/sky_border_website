<?php
require_once 'config/database.php';
require_once 'classes/Auth.php';
require_once 'classes/ContentManager.php';

// Initialize authentication
$auth = new Auth();
if (!$auth->isLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Initialize database and content manager
$database = new Database();
$conn = $database->getConnection();
$contentManager = new ContentManager($conn);

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_smtp':
                $smtpData = [
                    'name' => $_POST['name'],
                    'host' => $_POST['host'],
                    'port' => $_POST['port'],
                    'username' => $_POST['username'],
                    'password' => $_POST['password'],
                    'encryption' => $_POST['encryption'],
                    'from_email' => $_POST['from_email'],
                    'from_name' => $_POST['from_name'],
                    'is_active' => isset($_POST['is_active']) ? 1 : 0
                ];
                
                if ($contentManager->addSMTPConfig($smtpData)) {
                    $message = 'SMTP configuration added successfully!';
                } else {
                    $error = 'Failed to add SMTP configuration.';
                }
                break;
                
            case 'update_smtp':
                $smtpData = [
                    'id' => $_POST['smtp_id'],
                    'name' => $_POST['name'],
                    'host' => $_POST['host'],
                    'port' => $_POST['port'],
                    'username' => $_POST['username'],
                    'password' => $_POST['password'],
                    'encryption' => $_POST['encryption'],
                    'from_email' => $_POST['from_email'],
                    'from_name' => $_POST['from_name'],
                    'is_active' => isset($_POST['is_active']) ? 1 : 0
                ];
                
                if ($contentManager->updateSMTPConfig($smtpData)) {
                    $message = 'SMTP configuration updated successfully!';
                } else {
                    $error = 'Failed to update SMTP configuration.';
                }
                break;
                
            case 'delete_smtp':
                if ($contentManager->deleteSMTPConfig($_POST['smtp_id'])) {
                    $message = 'SMTP configuration deleted successfully!';
                } else {
                    $error = 'Failed to delete SMTP configuration.';
                }
                break;
                
            case 'test_smtp':
                try {
                    $result = $contentManager->testSMTPConnection($_POST['host'], $_POST['port'], $_POST['username'], $_POST['password'], $_POST['encryption']);
                    if ($result['success']) {
                        $message = 'SMTP connection test successful!';
                    } else {
                        $error = 'SMTP connection test failed: ' . $result['error'];
                    }
                } catch (Exception $e) {
                    $error = 'SMTP test error: ' . $e->getMessage();
                }
                break;
        }
    }
}

// Get SMTP configurations
$smtpConfigs = $contentManager->getSMTPConfigs();

// Set content file for admin layout
$contentFile = 'views/smtp-settings-content.php';

// Include admin layout
include 'layouts/admin.php';
?>
