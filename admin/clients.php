<?php
/**
 * Clients Management (New Layout System)
 * Sky Border Solutions CMS
 */

require_once 'classes/Auth.php';
require_once 'classes/ContentManager.php';
require_once 'classes/FileUploader.php';
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
        $display_order = (int)($_POST['display_order'] ?? 0);
        $services = '';
        
        // Handle multiple services selection
        if (isset($_POST['services']) && is_array($_POST['services'])) {
            $services = implode(', ', array_filter($_POST['services']));
        }
        
        $logo_url = '';
        
        if (!empty($client_name)) {
            try {
                // Handle logo upload if file is provided
                if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
                    $uploader = new FileUploader();
                    $logo_url = $uploader->upload('logo_file', 'client_');
                }
                
                if ($contentManager->addClient($client_name, $category_id, $logo_url, $display_order, $services)) {
                    $success = 'Client added successfully!';
                    $action = 'list';
                } else {
                    // If database insert failed and file was uploaded, clean up the file
                    if (!empty($logo_url)) {
                        $uploader = new FileUploader();
                        $uploader->delete($logo_url);
                    }
                    $error = 'Failed to add client. Please try again.';
                }
            } catch (Exception $e) {
                $error = 'Upload error: ' . $e->getMessage();
            }
        } else {
            $error = 'Client name is required.';
        }
    }
    
    if (isset($_POST['update_client'])) {
        $id = (int)($_POST['client_id'] ?? 0);
        $client_name = trim($_POST['client_name'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);
        $display_order = (int)($_POST['display_order'] ?? 0);
        $services = '';
        
        // Handle multiple services selection
        if (isset($_POST['services']) && is_array($_POST['services'])) {
            $services = implode(', ', array_filter($_POST['services']));
        }
        
        if (!empty($client_name) && $id > 0) {
            try {
                // Get current client data to preserve existing logo if no new upload
                $currentClient = $contentManager->getClient($id);
                $logo_url = $currentClient['logo_url'] ?? '';
                $old_logo_url = $logo_url;
                
                // Handle logo upload if new file is provided
                if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
                    $uploader = new FileUploader();
                    $new_logo_url = $uploader->upload('logo_file', 'client_');
                    
                    // If upload successful, update logo_url and mark old file for deletion
                    $logo_url = $new_logo_url;
                }
                
                if ($contentManager->updateClient($id, $client_name, $category_id, $logo_url, $display_order, $services)) {
                    // If update successful and we uploaded a new logo, delete the old one
                    if (isset($new_logo_url) && !empty($old_logo_url) && $old_logo_url !== $logo_url) {
                        $uploader = new FileUploader();
                        $uploader->delete($old_logo_url);
                    }
                    
                    $success = 'Client updated successfully!';
                    $action = 'list';
                } else {
                    // If database update failed and we uploaded a new file, clean up the new file
                    if (isset($new_logo_url)) {
                        $uploader = new FileUploader();
                        $uploader->delete($new_logo_url);
                    }
                    $error = 'Failed to update client. Please try again.';
                }
            } catch (Exception $e) {
                $error = 'Upload error: ' . $e->getMessage();
            }
        } else {
            $error = 'Client name is required.';
        }
    }
    
    if (isset($_POST['delete_client'])) {
        $id = (int)($_POST['client_id'] ?? 0);
        if ($id > 0) {
            try {
                // Get client data to check for logo file
                $clientData = $contentManager->getClient($id);
                
                if ($contentManager->deleteClient($id)) {
                    // If deletion successful and client had a logo, delete the file
                    if (!empty($clientData['logo_url'])) {
                        $uploader = new FileUploader();
                        $uploader->delete($clientData['logo_url']);
                    }
                    
                    $success = 'Client deleted successfully!';
                    $action = 'list';
                } else {
                    $error = 'Failed to delete client. Please try again.';
                }
            } catch (Exception $e) {
                $error = 'Error deleting client: ' . $e->getMessage();
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
