<?php
/**
 * Services Management (New Layout System)
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
$serviceId = $_GET['id'] ?? null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_service'])) {
        $category_name = trim($_POST['category_name'] ?? '');
        $category_description = trim($_POST['category_description'] ?? '');
        $icon_class = trim($_POST['icon_class'] ?? 'fas fa-cogs');
        $color_theme = trim($_POST['color_theme'] ?? 'blue');
        $display_order = (int)($_POST['display_order'] ?? 0);
        
        if (!empty($category_name)) {
            $data = [
                ':category_name' => $category_name,
                ':category_description' => $category_description,
                ':icon_class' => $icon_class,
                ':color_theme' => $color_theme,
                ':display_order' => $display_order
            ];
            
            if ($contentManager->addServiceCategory($data)) {
                $success = 'Service added successfully!';
                $action = 'list';
            } else {
                $error = 'Failed to add service. Please try again.';
            }
        } else {
            $error = 'Service name is required.';
        }
    }
    
    if (isset($_POST['update_service'])) {
        $id = (int)($_POST['service_id'] ?? 0);
        $category_name = trim($_POST['category_name'] ?? '');
        $category_description = trim($_POST['category_description'] ?? '');
        $icon_class = trim($_POST['icon_class'] ?? 'fas fa-cogs');
        $color_theme = trim($_POST['color_theme'] ?? 'blue');
        $display_order = (int)($_POST['display_order'] ?? 0);
        
        if (!empty($category_name) && $id > 0) {
            $data = [
                ':category_name' => $category_name,
                ':category_description' => $category_description,
                ':icon_class' => $icon_class,
                ':color_theme' => $color_theme,
                ':display_order' => $display_order
            ];
            
            if ($contentManager->updateServiceCategory($id, $data)) {
                $success = 'Service updated successfully!';
                $action = 'list';
            } else {
                $error = 'Failed to update service. Please try again.';
            }
        } else {
            $error = 'Service name is required.';
        }
    }
    
    if (isset($_POST['delete_service'])) {
        $id = (int)($_POST['service_id'] ?? 0);
        if ($id > 0) {
            if ($contentManager->deleteServiceCategory($id)) {
                $success = 'Service deleted successfully!';
                $action = 'list';
            } else {
                $error = 'Failed to delete service. Please try again.';
            }
        }
    }
}

// Get data for the page
$services = $contentManager->getServiceCategories();
$editService = null;

if ($action === 'edit' && $serviceId) {
    // Find the service to edit
    foreach ($services as $service) {
        if ($service['id'] == $serviceId) {
            $editService = $service;
            break;
        }
    }
    
    if (!$editService) {
        $error = 'Service not found.';
        $action = 'list';
    }
}

// Determine page title and actions
$pageTitle = 'Services Management';
$pageDescription = 'Manage service categories and their details';
$pageActions = '';

if ($action === 'add') {
    $pageTitle = 'Add New Service';
    $pageDescription = 'Create a new service category';
} elseif ($action === 'edit') {
    $pageTitle = 'Edit Service';
    $pageDescription = 'Update service information and settings';
} else {
    $pageActions = createPageActions([
        [
            'url' => '?action=add',
            'label' => 'Add Service',
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
    'contentFile' => __DIR__ . '/views/services-content.php',
    
    // Data for the content view
    'action' => $action,
    'services' => $services,
    'editService' => $editService
];

// Render the page with layout
renderAdminPage($layoutData['contentFile'], $layoutData);
?>
