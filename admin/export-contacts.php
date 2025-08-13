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

$format = $_GET['format'] ?? 'csv';
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';

// Get all contacts based on filters
$contacts = $contentManager->getContacts($search, $status, 10000, 0); // Large limit for export

if ($format === 'csv') {
    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="contacts_' . date('Y-m-d') . '.csv"');
    
    // Create output stream
    $output = fopen('php://output', 'w');
    
    // Add CSV headers
    fputcsv($output, ['Name', 'Email', 'Phone', 'Company', 'Status', 'Created At']);
    
    // Add data rows
    foreach ($contacts as $contact) {
        fputcsv($output, [
            $contact['name'],
            $contact['email'],
            $contact['phone'] ?? '',
            $contact['company'] ?? '',
            $contact['status'],
            $contact['created_at']
        ]);
    }
    
    fclose($output);
    
} elseif ($format === 'json') {
    // Set headers for JSON download
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="contacts_' . date('Y-m-d') . '.json"');
    
    // Output JSON data
    echo json_encode($contacts, JSON_PRETTY_PRINT);
    
} else {
    // Invalid format
    header('Location: contacts.php?error=Invalid export format');
    exit;
}
?>
