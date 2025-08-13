<?php
/**
 * Messages Management (New Layout System)
 * Sky Border Solutions CMS
 */

require_once 'classes/Auth.php';
require_once 'classes/ContentManager.php';
require_once 'includes/layout-helpers.php';

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
            $error = 'Failed to update message status.';
        }
    }
    
    if (isset($_POST['delete_message'])) {
        $message_id = (int)($_POST['message_id'] ?? 0);
        
        if ($contentManager->deleteContactMessage($message_id)) {
            $success = 'Message deleted successfully!';
            $action = 'list';
        } else {
            $error = 'Failed to delete message.';
        }
    }
}

// Get data based on action
$messages = [];
$message = null;
$messageStats = [];

if ($action === 'view' && $messageId) {
    $message = $contentManager->getContactMessage($messageId);
    if (!$message) {
        $error = 'Message not found.';
        $action = 'list';
    }
}

if ($action === 'list' || $action === 'filter') {
    $filter = $_GET['filter'] ?? 'all';
    if ($filter === 'all') {
        $messages = $contentManager->getContactMessages();
    } else {
        $messages = $contentManager->getContactMessages($filter);
    }
    $messageStats = $contentManager->getContactMessageStats();
}

// Prepare data for the layout
$layoutData = [
    'pageTitle' => 'Contact Messages',
    'pageDescription' => 'Manage and respond to customer inquiries and contact form submissions',
    'currentUser' => $currentUser,
    'success' => $success,
    'error' => $error,
    
    // Data for the content view
    'action' => $action,
    'messages' => $messages,
    'message' => $message,
    'messageStats' => $messageStats,
    'messageId' => $messageId,
    
    // Page actions
    'pageActions' => createPageActions([
        [
            'url' => 'messages.php',
            'label' => 'All Messages',
            'icon' => 'fas fa-inbox',
            'class' => 'btn-secondary'
        ]
    ]),
    
    // Additional CSS for message management
    'additionalCSS' => '
        .message-status {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .status-new { background-color: rgb(239 246 255); color: rgb(59 130 246); }
        .status-read { background-color: rgb(240 253 244); color: rgb(34 197 94); }
        .status-replied { background-color: rgb(254 249 195); color: rgb(161 98 7); }
        .status-archived { background-color: rgb(249 250 251); color: rgb(107 114 128); }
        .dark .status-new { background-color: rgb(30 58 138); color: rgb(147 197 253); }
        .dark .status-read { background-color: rgb(20 83 45); color: rgb(134 239 172); }
        .dark .status-replied { background-color: rgb(146 64 14); color: rgb(253 224 71); }
        .dark .status-archived { background-color: rgb(55 65 81); color: rgb(156 163 175); }
        
        .message-priority-high { border-left: 4px solid rgb(239 68 68); }
        .message-priority-medium { border-left: 4px solid rgb(245 158 11); }
        .message-priority-low { border-left: 4px solid rgb(34 197 94); }
    '
];

// Render the page with layout
renderAdminPage('views/messages-content.php', $layoutData);
?>
