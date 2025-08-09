<?php
/**
 * Clients Management (New Layout System)
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
$clientId = $_GET['id'] ?? null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_client'])) {
        $client_name = trim($_POST['client_name'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);
        $logo_url = trim($_POST['logo_url'] ?? '');
        $display_order = (int)($_POST['display_order'] ?? 0);
        
        if (!empty($client_name)) {
            if ($contentManager->addClient($client_name, $category_id, $logo_url, $display_order)) {
                $success = 'Client added successfully!';
                $action = 'list';
            } else {
                $error = 'Failed to add client. Please try again.';
            }
        } else {
            $error = 'Client name is required.';
        }
    }
    
    if (isset($_POST['update_client'])) {
        $id = (int)($_POST['client_id'] ?? 0);
        $client_name = trim($_POST['client_name'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);
        $logo_url = trim($_POST['logo_url'] ?? '');
        $display_order = (int)($_POST['display_order'] ?? 0);
        
        if (!empty($client_name) && $id > 0) {
            if ($contentManager->updateClient($id, $client_name, $category_id, $logo_url, $display_order)) {
                $success = 'Client updated successfully!';
                $action = 'list';
            } else {
                $error = 'Failed to update client. Please try again.';
            }
        } else {
            $error = 'Client name is required.';
        }
    }
    
    if (isset($_POST['delete_client'])) {
        $id = (int)($_POST['client_id'] ?? 0);
        if ($id > 0) {
            if ($contentManager->deleteClient($id)) {
                $success = 'Client deleted successfully!';
                $action = 'list';
            } else {
                $error = 'Failed to delete client. Please try again.';
            }
        }
    }
}

// Get data for the page
$clients = $contentManager->getClients();
$clientCategories = $contentManager->getClientCategories();
$editClient = null;

if ($action === 'edit' && $clientId) {
    $editClient = $contentManager->getClient($clientId);
    if (!$editClient) {
        $error = 'Client not found.';
        $action = 'list';
    }
}

// Determine page title and actions
$pageTitle = 'Clients Management';
$pageDescription = 'Manage your client portfolio displayed on the website';
$pageActions = '';

if ($action === 'add') {
    $pageTitle = 'Add New Client';
    $pageDescription = 'Add a new client to your portfolio';
} elseif ($action === 'edit') {
    $pageTitle = 'Edit Client';
    $pageDescription = 'Update client information and category';
} else {
    $pageActions = createPageActions([
        [
            'url' => '?action=add',
            'label' => 'Add Client',
            'icon' => 'fas fa-plus',
            'class' => 'btn-primary'
        ]
    ]);
}

// Prepare data for the layout
$layoutData = [
    'pageTitle' => $pageTitle,
    'pageDescription' => $pageDescription,
    'pageActions' => $pageActions,
    'currentUser' => $currentUser,
    'success' => $success,
    'error' => $error,
    'contentFile' => __DIR__ . '/views/clients-content.php',
    
    // Data for the content view
    'action' => $action,
    'clients' => $clients,
    'clientCategories' => $clientCategories,
    'editClient' => $editClient
];

// Render the page with layout
renderAdminPage($layoutData['contentFile'], $layoutData);
?>
