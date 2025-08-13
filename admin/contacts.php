<?php
require_once 'config/database.php';
require_once 'classes/Auth.php';
require_once 'classes/ContentManager.php';

// Initialize authentication
$auth = new Auth();
if (!$auth->isLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Initialize database and content manager
$database = new Database();
$conn = $database->getConnection();
$contentManager = new ContentManager($conn);

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_contact':
                $contactData = [
                    'name' => $_POST['name'],
                    'email' => $_POST['email'],
                    'phone' => $_POST['phone'] ?? '',
                    'company' => $_POST['company'] ?? '',
                    'status' => 'active'
                ];
                
                if ($contentManager->addContact($contactData)) {
                    $message = 'Contact added successfully!';
                } else {
                    $error = 'Failed to add contact. Email might already exist.';
                }
                break;
                
            case 'update_contact':
                $contactData = [
                    'id' => $_POST['contact_id'],
                    'name' => $_POST['name'],
                    'email' => $_POST['email'],
                    'phone' => $_POST['phone'] ?? '',
                    'company' => $_POST['company'] ?? '',
                    'status' => $_POST['status']
                ];
                
                if ($contentManager->updateContact($contactData)) {
                    $message = 'Contact updated successfully!';
                } else {
                    $error = 'Failed to update contact.';
                }
                break;
                
            case 'delete_contact':
                if ($contentManager->deleteContact($_POST['contact_id'])) {
                    $message = 'Contact deleted successfully!';
                } else {
                    $error = 'Failed to delete contact.';
                }
                break;
                
            case 'import_contacts':
                if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
                    $importResult = $contentManager->importContactsFromCSV($_FILES['csv_file']['tmp_name']);
                    if ($importResult['success']) {
                        $message = "Successfully imported {$importResult['imported']} contacts!";
                    } else {
                        $error = "Import failed: {$importResult['error']}";
                    }
                } else {
                    $error = 'Please select a valid CSV file.';
                }
                break;
                
            case 'bulk_action':
                $contactIds = $_POST['contact_ids'] ?? [];
                $bulkAction = $_POST['bulk_action_type'] ?? '';
                
                if (!empty($contactIds) && !empty($bulkAction)) {
                    $result = $contentManager->bulkActionContacts($contactIds, $bulkAction);
                    if ($result) {
                        $message = 'Bulk action completed successfully!';
                    } else {
                        $error = 'Failed to complete bulk action.';
                    }
                }
                break;
        }
    }
}

// Get contacts with pagination and search
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

$contacts = $contentManager->getContacts($search, $status, $perPage, $offset);
$totalContacts = $contentManager->getTotalContacts($search, $status);
$totalPages = ceil($totalContacts / $perPage);

// Get contact lists for assignment
$contactLists = $contentManager->getContactLists();

// Set content file for admin layout
$contentFile = 'views/contacts-content.php';

// Include admin layout
include 'layouts/admin.php';
?>
