<?php
/**
 * Database Setup Script (New Layout System)
 * Sky Border Solutions CMS
 */

require_once 'config/database.php';
require_once 'includes/layout-helpers.php';

// Security: Only allow access from localhost or if a setup key is provided
$setupKey = $_GET['key'] ?? '';
$isLocalhost = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1']);

if (!$isLocalhost && $setupKey !== 'sky_border_setup_2024') {
    die('Setup access denied. Please run from localhost or provide the correct setup key.');
}

$messages = [];
$errors = [];
$connectionStatus = false;
$dbName = '';

// Test database connection
try {
    $database = new Database();
    $conn = $database->getConnection();
    $connectionStatus = ($conn !== null);
    
    if ($connectionStatus) {
        // Get database name
        $stmt = $conn->query("SELECT DATABASE() as db_name");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $dbName = $result['db_name'] ?? '';
    }
} catch (Exception $e) {
    $errors[] = "Database connection failed: " . $e->getMessage();
}

// Handle setup form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup']) && $connectionStatus) {
    try {
        // Read and execute the schema file
        $schemaFile = __DIR__ . '/database/schema.sql';
        
        if (!file_exists($schemaFile)) {
            throw new Exception("Schema file not found: {$schemaFile}");
        }
        
        $sql = file_get_contents($schemaFile);
        if ($sql === false) {
            throw new Exception("Failed to read schema file");
        }
        
        // Split the SQL into individual statements
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                $conn->exec($statement);
                $messages[] = "Executed: " . substr($statement, 0, 50) . "...";
            }
        }
        
        // Run additional setup scripts
        $additionalSetups = [
            'database/industries-schema.sql',
            'database/add-about-us.sql'
        ];
        
        foreach ($additionalSetups as $setupFile) {
            $fullPath = __DIR__ . '/' . $setupFile;
            if (file_exists($fullPath)) {
                $sql = file_get_contents($fullPath);
                if ($sql !== false) {
                    $statements = array_filter(array_map('trim', explode(';', $sql)));
                    foreach ($statements as $statement) {
                        if (!empty($statement)) {
                            $conn->exec($statement);
                        }
                    }
                    $messages[] = "Applied additional setup: " . basename($setupFile);
                }
            }
        }
        
        $messages[] = "Database setup completed successfully!";
        $messages[] = "Default admin user created with username 'admin' and password 'admin123'";
        $messages[] = "Please change the admin password after your first login.";
        
    } catch (Exception $e) {
        $errors[] = "Setup failed: " . $e->getMessage();
    }
}

// Prepare data for the layout
$layoutData = [
    'pageTitle' => 'Database Setup',
    'pageDescription' => 'Initialize your Sky Border Solutions CMS database',
    'currentUser' => null, // No user context for setup
    'success' => '',
    'error' => '',
    
    // Data for the content view
    'messages' => $messages,
    'errors' => $errors,
    'connectionStatus' => $connectionStatus,
    'dbName' => $dbName,
    
    // Additional CSS for setup styling
    'additionalCSS' => '
        .setup-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .setup-step {
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
        }
        .setup-step.completed {
            border-left-color: rgb(34 197 94);
            background-color: rgb(240 253 244);
        }
        .dark .setup-step.completed {
            background-color: rgb(20 83 45);
        }
        .setup-step.error {
            border-left-color: rgb(239 68 68);
            background-color: rgb(254 242 242);
        }
        .dark .setup-step.error {
            background-color: rgb(127 29 29);
        }
    '
];

// Render the page with layout
renderAdminPage('views/setup-content.php', $layoutData);
?>
