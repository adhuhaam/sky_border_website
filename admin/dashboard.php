<?php
/**
 * Admin Dashboard (New Layout System)
 * Sky Border Solutions CMS
 */

require_once 'classes/Auth.php';
require_once 'classes/ContentManager.php';
require_once 'includes/layout-helpers.php';

$auth = new Auth();
$auth->requireLogin();

$contentManager = new ContentManager();
$currentUser = $auth->getCurrentUser();

// Get dashboard statistics
$companyInfo = $contentManager->getCompanyInfo();
$statistics = $contentManager->getStatistics();
$recentMessages = $contentManager->getContactMessages('new', 5);
$totalClients = count($contentManager->getClients());
$totalServices = count($contentManager->getServiceCategories());

// Prepare data for the layout
$layoutData = [
    'pageTitle' => 'Dashboard',
    'pageDescription' => "Welcome back, " . htmlspecialchars($currentUser['full_name']) . "! Here's what's happening with your website.",
    'currentUser' => $currentUser,
    'contentFile' => __DIR__ . '/views/dashboard-content.php',
    
    // Data for the dashboard content
    'companyInfo' => $companyInfo,
    'statistics' => $statistics,
    'recentMessages' => $recentMessages,
    'totalClients' => $totalClients,
    'totalServices' => $totalServices
];

// Render the page with layout
renderAdminPage($layoutData['contentFile'], $layoutData);
?>
