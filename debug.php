<?php
// Debug script to test database connection and content loading
// Sky Border Solutions - Debug Tool

echo "<h1>Sky Border Solutions - Debug Information</h1>";
echo "<hr>";

// Test 1: Check if files exist
echo "<h2>1. File Existence Check</h2>";
$requiredFiles = [
    'admin/config/database.php',
    'admin/classes/ContentManager.php'
];

foreach ($requiredFiles as $file) {
    $exists = file_exists($file);
    echo "<p>{$file}: " . ($exists ? "✅ EXISTS" : "❌ MISSING") . "</p>";
}

echo "<hr>";

// Test 2: Test database connection
echo "<h2>2. Database Connection Test</h2>";
try {
    if (file_exists('admin/config/database.php')) {
        require_once 'admin/config/database.php';
        $database = new Database();
        $connection = $database->testConnection();
        echo "<p>Database connection: " . ($connection ? "✅ SUCCESS" : "❌ FAILED") . "</p>";
        
        if ($connection) {
            $conn = $database->getConnection();
            echo "<p>Connection object: " . (is_object($conn) ? "✅ Valid PDO object" : "❌ Invalid") . "</p>";
        }
    } else {
        echo "<p>❌ Database config file not found</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Database connection error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";

// Test 3: Test ContentManager
echo "<h2>3. ContentManager Test</h2>";
try {
    if (file_exists('admin/classes/ContentManager.php')) {
        require_once 'admin/classes/ContentManager.php';
        $contentManager = new ContentManager();
        echo "<p>ContentManager instantiation: ✅ SUCCESS</p>";
        
        // Test methods
        $methods = ['getCompanyInfo', 'getServiceCategories', 'getClients'];
        foreach ($methods as $method) {
            try {
                $result = $contentManager->$method();
                echo "<p>{$method}(): " . (is_array($result) && !empty($result) ? "✅ SUCCESS (" . count($result) . " items)" : "⚠️ EMPTY OR FAILED") . "</p>";
            } catch (Exception $e) {
                echo "<p>{$method}(): ❌ ERROR - " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        }
    } else {
        echo "<p>❌ ContentManager class file not found</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ ContentManager error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";

// Test 4: Check tables exist
echo "<h2>4. Database Tables Check</h2>";
try {
    if (isset($conn)) {
        $tables = ['company_info', 'service_categories', 'clients', 'client_categories'];
        foreach ($tables as $table) {
            try {
                $stmt = $conn->prepare("SELECT COUNT(*) FROM {$table}");
                $stmt->execute();
                $count = $stmt->fetchColumn();
                echo "<p>Table '{$table}': ✅ EXISTS ({$count} rows)</p>";
            } catch (Exception $e) {
                echo "<p>Table '{$table}': ❌ ERROR - " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        }
    } else {
        echo "<p>❌ No database connection available</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Tables check error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";

// Test 5: Test individual data queries
echo "<h2>5. Individual Data Queries</h2>";
try {
    if (isset($contentManager)) {
        // Test company info
        echo "<h3>Company Info:</h3>";
        $companyInfo = $contentManager->getCompanyInfo();
        if ($companyInfo) {
            echo "<pre>" . htmlspecialchars(print_r($companyInfo, true)) . "</pre>";
        } else {
            echo "<p>❌ No company info found</p>";
        }
        
        // Test clients
        echo "<h3>Clients (first 3):</h3>";
        $clients = $contentManager->getClients();
        if ($clients) {
            $firstThree = array_slice($clients, 0, 3);
            echo "<pre>" . htmlspecialchars(print_r($firstThree, true)) . "</pre>";
        } else {
            echo "<p>❌ No clients found</p>";
        }
    }
} catch (Exception $e) {
    echo "<p>❌ Data queries error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<h2>Debug Complete</h2>";
echo "<p>Please check the results above to identify any issues.</p>";
?>
