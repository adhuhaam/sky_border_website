<?php
/**
 * Setup Contact Messages Table
 * Sky Border Solutions CMS
 */

require_once 'config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    echo "<h1>Setting up Contact Messages Table</h1>";
    
    // Read and execute the SQL file
    $sqlFile = 'database/add-contact-messages.sql';
    
    if (file_exists($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        
        // Split SQL into individual statements
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($statements as $statement) {
            if (empty($statement)) continue;
            
            try {
                $stmt = $conn->prepare($statement);
                $stmt->execute();
                $successCount++;
                echo "<p style='color: green;'>✓ Executed: " . substr($statement, 0, 50) . "...</p>";
            } catch (Exception $e) {
                $errorCount++;
                echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
            }
        }
        
        echo "<h2>Setup Complete</h2>";
        echo "<p>Successfully executed: <strong>$successCount</strong> statements</p>";
        if ($errorCount > 0) {
            echo "<p>Errors encountered: <strong>$errorCount</strong></p>";
        }
        
    } else {
        echo "<p style='color: red;'>SQL file not found: $sqlFile</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Database connection error: " . $e->getMessage() . "</p>";
}

echo "<p><a href='dashboard.php'>← Back to Dashboard</a></p>";
?>
