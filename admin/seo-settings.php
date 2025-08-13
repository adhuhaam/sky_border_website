<?php
require_once 'classes/Auth.php';
require_once 'classes/ContentManager.php';
require_once 'includes/layout-helpers.php';

// Initialize authentication
$auth = new Auth();
if (!$auth->isAuthenticated()) {
    header('Location: index.php');
    exit;
}

// Initialize content manager
$contentManager = new ContentManager();

$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_seo') {
        $pageName = $_POST['page_name'] ?? '';
        $seoData = [
            'meta_title' => $_POST['meta_title'] ?? '',
            'meta_description' => $_POST['meta_description'] ?? '',
            'meta_keywords' => $_POST['meta_keywords'] ?? '',
            'og_title' => $_POST['og_title'] ?? '',
            'og_description' => $_POST['og_description'] ?? '',
            'og_image' => $_POST['og_image'] ?? '',
            'twitter_title' => $_POST['twitter_title'] ?? '',
            'twitter_description' => $_POST['twitter_description'] ?? '',
            'twitter_image' => $_POST['twitter_image'] ?? '',
            'canonical_url' => $_POST['canonical_url'] ?? '',
            'robots_txt' => $_POST['robots_txt'] ?? '',
            'google_analytics_id' => $_POST['google_analytics_id'] ?? '',
            'google_tag_manager_id' => $_POST['google_tag_manager_id'] ?? '',
            'facebook_pixel_id' => $_POST['facebook_pixel_id'] ?? '',
            'schema_markup' => $_POST['schema_markup'] ?? '',
            'custom_meta_tags' => $_POST['custom_meta_tags'] ?? ''
        ];
        
        if ($contentManager->updateSEOSettings($pageName, $seoData)) {
            $message = "SEO settings for '$pageName' updated successfully!";
        } else {
            $error = "Failed to update SEO settings for '$pageName'.";
        }
    }
}

// Get all SEO settings
$seoSettings = $contentManager->getAllSEOSettings();

// Prepare layout data
$layoutData = [
    'title' => 'SEO Settings',
    'contentFile' => 'views/seo-settings-content.php',
    'seoSettings' => $seoSettings,
    'message' => $message,
    'error' => $error
];

// Render the admin page
renderAdminPage($layoutData['contentFile'], $layoutData);
?>
