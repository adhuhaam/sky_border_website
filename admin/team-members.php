<?php
require_once 'classes/Auth.php';
require_once 'classes/ContentManager.php';
require_once 'config/database.php';

// Initialize authentication
$auth = new Auth($pdo);
$contentManager = new ContentManager($pdo);

// Check if user is logged in
if (!$auth->isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$action = $_GET['action'] ?? 'list';
$success = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_team_member'])) {
        $name = trim($_POST['name']);
        $designation = trim($_POST['designation']);
        $description = trim($_POST['description']);
        $display_order = (int)($_POST['display_order'] ?? 0);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        if (!empty($name) && !empty($designation)) {
            // Handle photo upload
            $photo_url = '';
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/team/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $fileExtension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array($fileExtension, $allowedExtensions)) {
                    $fileName = 'team_' . time() . '_' . uniqid() . '.' . $fileExtension;
                    $uploadPath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadPath)) {
                        $photo_url = $uploadPath;
                    }
                }
            }
            
            if ($contentManager->addTeamMember($name, $designation, $description, $photo_url, $display_order, $is_active)) {
                $success = 'Team member added successfully!';
                $action = 'list';
            } else {
                $error = 'Failed to add team member.';
            }
        } else {
            $error = 'Name and designation are required.';
        }
    } elseif (isset($_POST['update_team_member'])) {
        $id = (int)$_POST['id'];
        $name = trim($_POST['name']);
        $designation = trim($_POST['designation']);
        $description = trim($_POST['description']);
        $display_order = (int)($_POST['display_order'] ?? 0);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        if (!empty($name) && !empty($designation) && $id > 0) {
            // Handle photo upload
            $photo_url = $_POST['current_photo'] ?? '';
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/team/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $fileExtension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array($fileExtension, $allowedExtensions)) {
                    $fileName = 'team_' . time() . '_' . uniqid() . '.' . $fileExtension;
                    $uploadPath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadPath)) {
                        // Delete old photo if exists
                        if (!empty($_POST['current_photo']) && file_exists($_POST['current_photo'])) {
                            unlink($_POST['current_photo']);
                        }
                        $photo_url = $uploadPath;
                    }
                }
            }
            
            if ($contentManager->updateTeamMember($id, $name, $designation, $description, $photo_url, $display_order, $is_active)) {
                $success = 'Team member updated successfully!';
                $action = 'list';
            } else {
                $error = 'Failed to update team member.';
            }
        } else {
            $error = 'Name and designation are required.';
        }
    } elseif (isset($_POST['delete_team_member'])) {
        $id = (int)$_POST['id'];
        if ($id > 0) {
            // Get photo URL before deletion
            $teamMember = $contentManager->getTeamMember($id);
            if ($teamMember && !empty($teamMember['photo_url']) && file_exists($teamMember['photo_url'])) {
                unlink($teamMember['photo_url']);
            }
            
            if ($contentManager->deleteTeamMember($id)) {
                $success = 'Team member deleted successfully!';
            } else {
                $error = 'Failed to delete team member.';
            }
        }
    }
}

// Get team member for editing
$editTeamMember = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $editTeamMember = $contentManager->getTeamMember((int)$_GET['id']);
    if (!$editTeamMember) {
        $action = 'list';
        $error = 'Team member not found.';
    }
}

// Get all team members for listing
$teamMembers = $contentManager->getAllTeamMembers();

// Include layout
include 'layouts/admin.php';
?>
