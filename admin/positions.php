<?php
/**
 * Job Positions Management (New Layout System)
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
$positionId = $_GET['id'] ?? null;
$industryFilter = $_GET['industry_id'] ?? null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_position'])) {
        $industry_id = (int)($_POST['industry_id'] ?? 0);
        $position_name = trim($_POST['position_name'] ?? '');
        $position_description = trim($_POST['position_description'] ?? '');
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $display_order = (int)($_POST['display_order'] ?? 0);
        
        if (!empty($position_name) && $industry_id > 0) {
            if ($contentManager->addJobPosition($industry_id, $position_name, $position_description, $is_featured, $display_order)) {
                $success = 'Position added successfully!';
                $action = 'list';
            } else {
                $error = 'Failed to add position. Please try again.';
            }
        } else {
            $error = 'Position name and industry are required.';
        }
    }
    
    if (isset($_POST['update_position'])) {
        $id = (int)($_POST['position_id'] ?? 0);
        $industry_id = (int)($_POST['industry_id'] ?? 0);
        $position_name = trim($_POST['position_name'] ?? '');
        $position_description = trim($_POST['position_description'] ?? '');
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $display_order = (int)($_POST['display_order'] ?? 0);
        
        if (!empty($position_name) && $industry_id > 0 && $id > 0) {
            if ($contentManager->updateJobPosition($id, $industry_id, $position_name, $position_description, $is_featured, $display_order)) {
                $success = 'Position updated successfully!';
                $action = 'list';
            } else {
                $error = 'Failed to update position. Please try again.';
            }
        } else {
            $error = 'Position name and industry are required.';
        }
    }
    
    if (isset($_POST['delete_position'])) {
        $id = (int)($_POST['position_id'] ?? 0);
        if ($id > 0) {
            if ($contentManager->deleteJobPosition($id)) {
                $success = 'Position deleted successfully!';
                $action = 'list';
            } else {
                $error = 'Failed to delete position. Please try again.';
            }
        }
    }
}

// Get data for the page
$industries = $contentManager->getIndustries();
$positions = $contentManager->getJobPositions($industryFilter);
$editPosition = null;

if ($action === 'edit' && $positionId) {
    $editPosition = $contentManager->getJobPosition($positionId);
    if (!$editPosition) {
        $error = 'Position not found.';
        $action = 'list';
    }
}

// Determine page title and actions
$pageTitle = 'Job Positions Management';
$pageDescription = 'Manage job positions across all industries';
$pageActions = '';

if ($action === 'add') {
    $pageTitle = 'Add New Position';
    $pageDescription = 'Create a new job position';
} elseif ($action === 'edit') {
    $pageTitle = 'Edit Position';
    $pageDescription = 'Update position information and settings';
} else {
    $actions = [];
    if ($industryFilter) {
        $actions[] = [
            'url' => 'positions.php',
            'label' => 'Clear Filter',
            'icon' => 'fas fa-times',
            'class' => 'bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-md text-sm font-medium'
        ];
    }
    $actions[] = [
        'url' => '?action=add',
        'label' => 'Add Position',
        'icon' => 'fas fa-plus',
        'class' => 'btn-primary'
    ];
    $pageActions = createPageActions($actions);
}

// Prepare data for the layout
$layoutData = [
    'pageTitle' => $pageTitle,
    'pageDescription' => $pageDescription,
    'pageActions' => $pageActions,
    'currentUser' => $currentUser,
    'success' => $success,
    'error' => $error,
    'contentFile' => __DIR__ . '/views/positions-content.php',
    
    // Data for the content view
    'action' => $action,
    'industries' => $industries,
    'positions' => $positions,
    'editPosition' => $editPosition,
    'industryFilter' => $industryFilter
];

// Render the page with layout
renderAdminPage($layoutData['contentFile'], $layoutData);
?>
