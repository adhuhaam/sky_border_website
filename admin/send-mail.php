<?php
// Include Composer autoloader for PHPMailer
require_once __DIR__ . '/../vendor/autoload.php';

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
$emailStats = null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'send_mail') {
        $selectedContacts = $_POST['contact_ids'] ?? [];
        $emailSubject = $_POST['email_subject'] ?? '';
        $emailTemplate = $_POST['email_template'] ?? 'website'; // website, custom, or template
        
        if (empty($selectedContacts)) {
            $error = 'Please select at least one contact to send email to.';
        } elseif (empty($emailSubject)) {
            $error = 'Please enter an email subject.';
        } else {
            try {
                // Check SMTP configuration first
                $smtpConfig = $mailer->getSMTPConfig();
                if (!$smtpConfig) {
                    $error = 'No SMTP configuration found. Please configure SMTP settings first.';
                } else {
                    // Prepare email content based on template choice
                    $emailContent = '';
                    switch ($emailTemplate) {
                        case 'website':
                            // Render the front site as email template
                            $emailContent = $mailer->renderWebsiteAsEmail('/');
                            break;
                        case 'custom':
                            // Use custom HTML content
                            $emailContent = $_POST['custom_html'] ?? '<p>No custom content provided.</p>';
                            break;
                        case 'template':
                            // Use predefined template
                            $emailContent = $mailer->getEmailTemplate($_POST['template_id'] ?? 1);
                            break;
                        default:
                            $emailContent = $mailer->renderWebsiteAsEmail('/');
                    }
                    
                    if (empty($emailContent)) {
                        $error = 'Failed to generate email content. Please try again.';
                    } else {
                        // Send emails to selected contacts
                        $successCount = 0;
                        $errorCount = 0;
                        $errors = [];
                        $successEmails = [];
                        
                        foreach ($selectedContacts as $contactId) {
                            $contact = $contentManager->getContact($contactId);
                            if ($contact) {
                                // Add personalization to email content
                                $personalizedContent = $mailer->personalizeEmail($emailContent, $contact);
                                
                                $result = $mailer->sendTestEmail($contact['email'], $personalizedContent, $emailSubject);
                                if ($result['success']) {
                                    $successCount++;
                                    $successEmails[] = $contact['email'];
                                    
                                    // Log successful email
                                    $mailer->logEmailActivity($contact['id'], $emailSubject, 'sent', '');
                                } else {
                                    $errorCount++;
                                    $errors[] = "Failed to send to {$contact['email']}: " . $result['error'];
                                    
                                    // Log failed email
                                    $mailer->logEmailActivity($contact['id'], $emailSubject, 'failed', $result['error']);
                                }
                            }
                        }
                        
                        // Prepare result message
                        if ($successCount > 0) {
                            $message = "✅ Successfully sent emails to {$successCount} contacts!";
                            if ($errorCount > 0) {
                                $message .= " ❌ {$errorCount} emails failed to send.";
                            }
                            
                            // Store stats for display
                            $emailStats = [
                                'total' => count($selectedContacts),
                                'success' => $successCount,
                                'failed' => $errorCount,
                                'success_emails' => $successEmails,
                                'errors' => $errors,
                                'subject' => $emailSubject,
                                'template' => $emailTemplate
                            ];
                        } else {
                            $error = "❌ Failed to send any emails. Please check your SMTP configuration.";
                        }
                        
                        if (!empty($errors)) {
                            error_log("Email sending errors: " . implode('; ', $errors));
                        }
                    }
                }
                
            } catch (Exception $e) {
                $error = 'Error sending emails: ' . $e->getMessage();
                error_log("Send mail error: " . $e->getMessage());
            }
        }
    }
    
    // Handle contact list selection
    if (isset($_POST['action']) && $_POST['action'] === 'select_list') {
        $listId = $_POST['list_id'] ?? 0;
        if ($listId > 0) {
            $listContacts = $contentManager->getContactsByList($listId);
            // This will be handled by JavaScript
        }
    }
    
    // Handle AJAX request for getting contacts by list
    if (isset($_POST['action']) && $_POST['action'] === 'get_list_contacts') {
        $listId = $_POST['list_id'] ?? 0;
        
        if ($listId > 0) {
            try {
                $contacts = $contentManager->getContactsByList($listId);
                
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'contacts' => $contacts
                ]);
                exit;
                
            } catch (Exception $e) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => $e->getMessage()
                ]);
                exit;
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Invalid list ID'
            ]);
            exit;
        }
    }
}

// Get all contacts and contact lists
try {
    $contacts = $contentManager->getAllContacts();
    $contactLists = $contentManager->getContactLists();
    
    // Get email templates if available
    $emailTemplates = $contentManager->getEmailTemplates() ?? [];
    
    // Get recent email activity
    $recentActivity = $mailer->getRecentEmailActivity(10) ?? [];
    
} catch (Exception $e) {
    error_log("Failed to load data: " . $e->getMessage());
    $contacts = [];
    $contactLists = [];
    $emailTemplates = [];
    $recentActivity = [];
}

// Set content file for admin layout
$contentFile = 'views/send-mail-content.php';

// Include admin layout
include 'layouts/admin.php';
?>
