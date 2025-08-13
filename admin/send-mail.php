<?php
require_once 'config/database.php';
require_once 'classes/Auth.php';
require_once 'classes/ContentManager.php';
require_once 'classes/Mailer.php';

// Initialize authentication
$auth = new Auth();
if (!$auth->isLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Initialize database and classes
try {
    $database = new Database();
    $conn = $database->getConnection();
    $contentManager = new ContentManager();
    $mailer = new Mailer($conn);
} catch (Exception $e) {
    error_log("Failed to initialize classes: " . $e->getMessage());
    die("Failed to initialize system. Please check error logs.");
}

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'send_mail') {
        $selectedContacts = $_POST['contact_ids'] ?? [];
        $emailSubject = $_POST['email_subject'] ?? '';
        
        if (empty($selectedContacts)) {
            $error = 'Please select at least one contact to send email to.';
        } elseif (empty($emailSubject)) {
            $error = 'Please enter an email subject.';
        } else {
            try {
                // Render the front site as email template
                $renderedHtml = $mailer->renderWebsiteAsEmail('/');
                
                // Send emails to selected contacts
                $successCount = 0;
                $errorCount = 0;
                $errors = [];
                
                foreach ($selectedContacts as $contactId) {
                    $contact = $contentManager->getContact($contactId);
                    if ($contact) {
                        $result = $mailer->sendTestEmail($contact['email'], $renderedHtml, $emailSubject);
                        if ($result['success']) {
                            $successCount++;
                        } else {
                            $errorCount++;
                            $errors[] = "Failed to send to {$contact['email']}: " . $result['error'];
                        }
                    }
                }
                
                if ($successCount > 0) {
                    $message = "Successfully sent emails to {$successCount} contacts!";
                    if ($errorCount > 0) {
                        $message .= " {$errorCount} emails failed to send.";
                    }
                } else {
                    $error = "Failed to send any emails. Please check your SMTP configuration.";
                }
                
                if (!empty($errors)) {
                    error_log("Email sending errors: " . implode('; ', $errors));
                }
                
            } catch (Exception $e) {
                $error = 'Error sending emails: ' . $e->getMessage();
            }
        }
    }
}

// Get all contacts and contact lists
$contacts = $contentManager->getAllContacts();
$contactLists = $contentManager->getContactLists();

// Set content file for admin layout
$contentFile = 'views/send-mail-content.php';

// Include admin layout
include 'layouts/admin.php';
?>
