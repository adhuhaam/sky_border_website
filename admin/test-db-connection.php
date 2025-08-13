<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Connection Test</h1>";

try {
    require_once 'config/database.php';
    $database = new Database();
    
    echo "<p>Attempting to connect to production server...</p>";
    echo "<p>Host: 162.213.255.53</p>";
    echo "<p>Database: skydfcaf_sky_border</p>";
    echo "<p>Username: skydfcaf_sky_border_user</p>";
    
    $conn = $database->getConnection();
    
    if ($conn) {
        echo "<p style='color: green;'>✅ Database connection successful!</p>";
        
        // Test if we can query the database
        $stmt = $conn->prepare("SELECT 1 as test");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            echo "<p style='color: green;'>✅ Database query successful!</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Database connection failed</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='dashboard.php'>← Back to Dashboard</a></p>";
?>
