<?php
/**
 * Company Information Management (New Layout System)
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

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $section = $_POST['section'] ?? '';
    
    if ($section === 'basic' && isset($_POST['update_basic'])) {
        $data = [
            'company_name' => trim($_POST['company_name'] ?? ''),
            'business_type' => trim($_POST['business_type'] ?? ''),
            'registration_number' => trim($_POST['registration_number'] ?? ''),
            'license_number' => trim($_POST['license_number'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'about_us' => trim($_POST['about_us'] ?? '')
        ];
        
        if (empty($data['company_name'])) {
            $error = 'Company name is required.';
        } else {
            if ($contentManager->updateCompanyInfo($data)) {
                $success = 'Basic information updated successfully!';
            } else {
                $error = 'Failed to update basic information.';
            }
        }
    }
    
    if ($section === 'contact' && isset($_POST['update_contact'])) {
        $data = [
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'website' => trim($_POST['website'] ?? ''),
            'established_year' => (int)($_POST['established_year'] ?? 0)
        ];
        
        if ($contentManager->updateCompanyInfo($data)) {
            $success = 'Contact information updated successfully!';
        } else {
            $error = 'Failed to update contact information.';
        }
    }
    
    if ($section === 'mission' && isset($_POST['update_mission'])) {
        $data = [
            'mission' => trim($_POST['mission'] ?? ''),
            'vision' => trim($_POST['vision'] ?? '')
        ];
        
        if ($contentManager->updateCompanyInfo($data)) {
            $success = 'Mission & vision updated successfully!';
        } else {
            $error = 'Failed to update mission & vision.';
        }
    }
    
    if ($section === 'social' && isset($_POST['update_social'])) {
        $data = [
            'facebook_url' => trim($_POST['facebook_url'] ?? ''),
            'linkedin_url' => trim($_POST['linkedin_url'] ?? ''),
            'twitter_url' => trim($_POST['twitter_url'] ?? ''),
            'instagram_url' => trim($_POST['instagram_url'] ?? '')
        ];
        
        if ($contentManager->updateCompanyInfo($data)) {
            $success = 'Social media information updated successfully!';
        } else {
            $error = 'Failed to update social media information.';
        }
    }
}

// Get current company information
$companyInfo = $contentManager->getCompanyInfo() ?: [];

// Prepare data for the layout
$layoutData = [
    'pageTitle' => 'Company Information',
    'pageDescription' => 'Manage your company details, contact information, and online presence',
    'currentUser' => $currentUser,
    'success' => $success,
    'error' => $error,
    'contentFile' => __DIR__ . '/views/company-info-content.php',
    
    // Data for the content view
    'companyInfo' => $companyInfo,
    
    // Additional CSS for better form styling
    'additionalCSS' => '
        .form-section:not(:last-child) {
            margin-bottom: 2rem;
            border-bottom: 1px solid rgb(229 231 235);
            padding-bottom: 2rem;
        }
        .dark .form-section:not(:last-child) {
            border-bottom-color: rgb(55 65 81);
        }
    '
];

// Render the page with layout
renderAdminPage('views/company-info-content.php', $layoutData);
?>
