<?php
/**
 * Industries Management (New Layout System)
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
$industryId = $_GET['id'] ?? null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_industry'])) {
        $industry_name = trim($_POST['industry_name'] ?? '');
        $industry_description = trim($_POST['industry_description'] ?? '');
        $icon_class = trim($_POST['icon_class'] ?? 'fas fa-briefcase');
        $color_theme = trim($_POST['color_theme'] ?? 'blue');
        $display_order = (int)($_POST['display_order'] ?? 0);
        
        if (!empty($industry_name)) {
            if ($contentManager->addIndustry($industry_name, $industry_description, $icon_class, $color_theme, $display_order)) {
                $success = 'Industry added successfully!';
                $action = 'list';
            } else {
                $error = 'Failed to add industry. Please try again.';
            }
        } else {
            $error = 'Industry name is required.';
        }
    }
    
    if (isset($_POST['update_industry'])) {
        $id = (int)($_POST['industry_id'] ?? 0);
        $industry_name = trim($_POST['industry_name'] ?? '');
        $industry_description = trim($_POST['industry_description'] ?? '');
        $icon_class = trim($_POST['icon_class'] ?? 'fas fa-briefcase');
        $color_theme = trim($_POST['color_theme'] ?? 'blue');
        $display_order = (int)($_POST['display_order'] ?? 0);
        
        if (!empty($industry_name) && $id > 0) {
            if ($contentManager->updateIndustry($id, $industry_name, $industry_description, $icon_class, $color_theme, $display_order)) {
                $success = 'Industry updated successfully!';
                $action = 'list';
            } else {
                $error = 'Failed to update industry. Please try again.';
            }
        } else {
            $error = 'Industry name is required.';
        }
    }
    
    if (isset($_POST['delete_industry'])) {
        $id = (int)($_POST['industry_id'] ?? 0);
        if ($id > 0) {
            if ($contentManager->deleteIndustry($id)) {
                $success = 'Industry deleted successfully!';
                $action = 'list';
            } else {
                $error = 'Failed to delete industry. Please try again.';
            }
        }
    }
}

// Get data for the page
$industries = $contentManager->getIndustries();
$editIndustry = null;

if ($action === 'edit' && $industryId) {
    $editIndustry = $contentManager->getIndustry($industryId);
    if (!$editIndustry) {
        $error = 'Industry not found.';
        $action = 'list';
    }
}

// Determine page title and actions
$pageTitle = 'Industries Management';
$pageDescription = 'Manage industry categories and their details';
$pageActions = '';

if ($action === 'add') {
    $pageTitle = 'Add New Industry';
    $pageDescription = 'Create a new industry category';
} elseif ($action === 'edit') {
    $pageTitle = 'Edit Industry';
    $pageDescription = 'Update industry information and settings';
} else {
    $pageActions = createPageActions([
        [
            'url' => '?action=add',
            'label' => 'Add Industry',
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
    'contentFile' => __DIR__ . '/views/industries-content.php',
    
    // Data for the content view
    'action' => $action,
    'industries' => $industries,
    'editIndustry' => $editIndustry
];

// Render the page with layout
renderAdminPage($layoutData['contentFile'], $layoutData);
?>
