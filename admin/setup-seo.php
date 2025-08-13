<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    echo "<h1>SEO Settings Setup</h1>";
    
    // Read and execute the SQL file
    $sqlFile = 'database/add-seo-settings.sql';
    
    if (file_exists($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        
        // Split SQL into individual statements
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                try {
                    $stmt = $conn->prepare($statement);
                    $stmt->execute();
                    echo "<p style='color: green;'>✅ Executed: " . substr($statement, 0, 50) . "...</p>";
                } catch (PDOException $e) {
                    echo "<p style='color: orange;'>⚠️ Statement skipped: " . $e->getMessage() . "</p>";
                }
            }
        }
        
        echo "<p style='color: green;'>✅ SEO settings setup completed!</p>";
        
    } else {
        echo "<p style='color: red;'>❌ SQL file not found: $sqlFile</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='dashboard.php'>← Back to Dashboard</a></p>";
?>
