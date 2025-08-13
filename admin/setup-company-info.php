<?php
/**
 * Company Information Database Setup
 * Sky Border Solutions CMS
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Company Information Database Setup</h1>";
echo "<p>Setting up company info table...</p>";

try {
    // Initialize database connection
    require_once 'config/database.php';
    $database = new Database();
    $conn = $database->getConnection();
    
    // Read and execute the SQL file
    $sqlFile = 'database/add-company-info.sql';
    
    if (!file_exists($sqlFile)) {
        throw new Exception("SQL file not found: {$sqlFile}");
    }
    
    $sqlContent = file_get_contents($sqlFile);
    if (empty($sqlContent)) {
        throw new Exception("SQL file is empty: {$sqlFile}");
    }
    
    // Split SQL into individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $sqlContent)),
        function($stmt) { return !empty($stmt) && !preg_match('/^(--|\/\*)/', $stmt); }
    );
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($statements as $statement) {
        if (empty(trim($statement))) continue;
        
        try {
            $stmt = $conn->prepare($statement);
            $result = $stmt->execute();
            
            if ($result) {
                echo "<p style='color: green;'>✓ Executed: " . substr($statement, 0, 50) . "...</p>";
                $successCount++;
            } else {
                echo "<p style='color: red;'>✗ Failed: " . substr($statement, 0, 50) . "...</p>";
                $errorCount++;
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
            $errorCount++;
        }
    }
    
    echo "<hr>";
    echo "<h2>Setup Complete!</h2>";
    echo "<p><strong>Successful operations:</strong> {$successCount}</p>";
    echo "<p><strong>Errors:</strong> {$errorCount}</p>";
    
    if ($errorCount === 0) {
        echo "<p style='color: green; font-weight: bold;'>✅ Company information table setup completed successfully!</p>";
        echo "<p>You can now:</p>";
        echo "<ul>";
        echo "<li>Go to <a href='company-info.php'>Company Information</a> in the admin panel</li>";
        echo "<li>Update your company name, tagline, contact details, and more</li>";
        echo "<li>All changes will be reflected on your main website</li>";
        echo "</ul>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>❌ Setup completed with errors. Please check the error messages above.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red; font-weight: bold;'>❌ Setup failed: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='dashboard.php'>← Back to Dashboard</a></p>";
?>
