<?php
require_once 'config/database.php';
require_once 'classes/Auth.php';

// Initialize database connection
$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    die("Database connection failed");
}

echo "Setting up Mailer System Database...\n";

// Read and execute the SQL file
$sqlFile = 'database/mailer-system.sql';
if (file_exists($sqlFile)) {
    $sql = file_get_contents($sqlFile);
    
    // Split SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $result = $conn->exec($statement);
                if ($result !== false) {
                    echo "✓ Executed: " . substr($statement, 0, 50) . "...\n";
                }
            } catch (PDOException $e) {
                echo "✗ Error executing statement: " . $e->getMessage() . "\n";
                echo "Statement: " . substr($statement, 0, 100) . "...\n";
            }
        }
    }
    
    echo "\n✅ Mailer System Database setup completed!\n";
    echo "Tables created:\n";
    echo "- contacts\n";
    echo "- contact_lists\n";
    echo "- contact_list_contacts\n";
    echo "- smtp_config\n";
    echo "- campaigns\n";
    echo "- campaign_recipients\n";
    echo "- email_events\n";
    echo "- unsubscribes\n";
    echo "- bounces\n";
    echo "\nNext steps:\n";
    echo "1. Configure SMTP settings in /admin/smtp-settings.php\n";
    echo "2. Manage contacts in /admin/contacts.php\n";
    echo "3. Create campaigns in /admin/campaigns.php\n";
    
} else {
    echo "❌ SQL file not found: $sqlFile\n";
}
?>
