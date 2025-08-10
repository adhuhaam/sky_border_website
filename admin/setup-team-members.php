<?php
/**
 * Setup Team Members Database
 * Sky Border Solutions CMS
 * 
 * This script creates the team_members table and populates it with sample data.
 * Run this script once to set up the team members functionality.
 */

require_once 'config/database.php';

echo "<h1>Team Members Database Setup</h1>";
echo "<p>Setting up team members table...</p>";

try {
    // Initialize database connection
    $database = new Database();
    $conn = $database->getConnection();
    
    // Read and execute the SQL file
    $sqlFile = 'database/add-team-members.sql';
    
    if (!file_exists($sqlFile)) {
        throw new Exception("SQL file not found: $sqlFile");
    }
    
    $sql = file_get_contents($sqlFile);
    
    if (empty($sql)) {
        throw new Exception("SQL file is empty");
    }
    
    // Split SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($statements as $statement) {
        if (empty($statement)) continue;
        
        try {
            $stmt = $conn->prepare($statement);
            $result = $stmt->execute();
            
            if ($result) {
                $successCount++;
                echo "<p style='color: green;'>✓ Executed: " . substr($statement, 0, 50) . "...</p>";
            } else {
                $errorCount++;
                echo "<p style='color: red;'>✗ Failed: " . substr($statement, 0, 50) . "...</p>";
            }
        } catch (PDOException $e) {
            // Check if it's a "table already exists" error (which is fine)
            if (strpos($e->getMessage(), 'already exists') !== false) {
                $successCount++;
                echo "<p style='color: orange;'>⚠ Table already exists (skipped)</p>";
            } else {
                $errorCount++;
                echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
            }
        }
    }
    
    echo "<hr>";
    echo "<h2>Setup Complete!</h2>";
    echo "<p><strong>Successful operations:</strong> $successCount</p>";
    echo "<p><strong>Errors:</strong> $errorCount</p>";
    
    if ($errorCount === 0) {
        echo "<p style='color: green; font-weight: bold;'>✅ Team members table setup completed successfully!</p>";
        echo "<p>You can now:</p>";
        echo "<ul>";
        echo "<li>Go to <a href='team-members.php'>Team Members</a> in the admin panel</li>";
        echo "<li>Add, edit, and manage team members</li>";
        echo "<li>View team members on the main website</li>";
        echo "</ul>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>❌ Setup completed with errors. Please check the error messages above.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red; font-weight: bold;'>❌ Setup failed: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='dashboard.php'>← Back to Dashboard</a></p>";
?>
