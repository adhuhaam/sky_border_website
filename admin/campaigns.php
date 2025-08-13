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
$database = new Database();
$conn = $database->getConnection();
$contentManager = new ContentManager($conn);
$mailer = new Mailer($conn);

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create_campaign':
                try {
                    // Render website as HTML email
                    $renderedHtml = $mailer->renderWebsiteAsEmail($_POST['url_to_render']);
                    
                    $campaignData = [
                        'name' => $_POST['name'],
                        'subject' => $_POST['subject'],
                        'url_to_render' => $_POST['url_to_render'],
                        'status' => $_POST['status'],
                        'smtp_config_id' => $_POST['smtp_config_id'],
                        'scheduled_at' => $_POST['scheduled_at'] ?: null,
                        'rendered_html' => $renderedHtml
                    ];
                    
                    $campaignId = $mailer->createCampaign($campaignData);
                    
                    if ($campaignId) {
                        // Add recipients if specified
                        if (!empty($_POST['contact_ids'])) {
                            $mailer->addCampaignRecipients($campaignId, $_POST['contact_ids'], $_POST['list_id'] ?? null);
                        }
                        
                        $message = 'Campaign created successfully!';
                    } else {
                        $error = 'Failed to create campaign.';
                    }
                } catch (Exception $e) {
                    $error = 'Error creating campaign: ' . $e->getMessage();
                }
                break;
                
            case 'update_campaign':
                try {
                    $renderedHtml = $mailer->renderWebsiteAsEmail($_POST['url_to_render']);
                    
                    $campaignData = [
                        'name' => $_POST['name'],
                        'subject' => $_POST['subject'],
                        'url_to_render' => $_POST['url_to_render'],
                        'status' => $_POST['status'],
                        'smtp_config_id' => $_POST['smtp_config_id'],
                        'scheduled_at' => $_POST['scheduled_at'] ?: null,
                        'rendered_html' => $renderedHtml
                    ];
                    
                    if ($mailer->updateCampaign($_POST['campaign_id'], $campaignData)) {
                        $message = 'Campaign updated successfully!';
                    } else {
                        $error = 'Failed to update campaign.';
                    }
                } catch (Exception $e) {
                    $error = 'Error updating campaign: ' . $e->getMessage();
                }
                break;
                
            case 'delete_campaign':
                if ($contentManager->deleteCampaign($_POST['campaign_id'])) {
                    $message = 'Campaign deleted successfully!';
                } else {
                    $error = 'Failed to delete campaign.';
                }
                break;
                
            case 'send_campaign':
                try {
                    $result = $mailer->sendCampaign($_POST['campaign_id']);
                    $message = "Campaign sent successfully! {$result['sent']} emails sent.";
                    if (!empty($result['errors'])) {
                        $error = 'Some emails failed to send: ' . implode(', ', $result['errors']);
                    }
                } catch (Exception $e) {
                    $error = 'Error sending campaign: ' . $e->getMessage();
                }
                break;
                
            case 'preview_campaign':
                try {
                    $renderedHtml = $mailer->renderWebsiteAsEmail($_POST['url_to_render']);
                    // Store in session for preview
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }
                    $_SESSION['campaign_preview'] = $renderedHtml;
                    header('Location: campaign-preview.php');
                    exit;
                } catch (Exception $e) {
                    $error = 'Error previewing campaign: ' . $e->getMessage();
                }
                break;
        }
    }
}

// Get campaigns
$campaigns = $mailer->getAllCampaigns();

// Get SMTP configurations
$smtpConfigs = $contentManager->getSMTPConfigs();

// Get contacts and lists for recipient selection
$contacts = $contentManager->getAllContacts();
$contactLists = $contentManager->getContactLists();

// Set content file for admin layout
$contentFile = 'views/campaigns-content.php';

// Include admin layout
include 'layouts/admin.php';
?>
