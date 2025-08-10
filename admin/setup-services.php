<?php
/**
 * Services Setup Script
 * Run this to add services functionality to clients
 */

require_once 'config/database.php';
require_once 'classes/ContentManager.php';

echo "<h2>Setting up Services for Clients</h2>";

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Check if services column exists
    $stmt = $conn->prepare("SHOW COLUMNS FROM clients LIKE 'services'");
    $stmt->execute();
    $result = $stmt->fetch();
    
    if (!$result) {
        echo "<p>Adding services column to clients table...</p>";
        
        // Add services column
        $stmt = $conn->prepare("ALTER TABLE clients ADD COLUMN services TEXT AFTER description");
        $stmt->execute();
        echo "<p>✓ Services column added successfully</p>";
        
        // Add index
        $stmt = $conn->prepare("CREATE INDEX idx_clients_services ON clients(services(100))");
        $stmt->execute();
        echo "<p>✓ Index created successfully</p>";
        
        // Update sample data
        $updates = [
            ['id' => 1, 'services' => 'Recruitment, HR Consulting'],
            ['id' => 2, 'services' => 'Recruitment, Staffing'],
            ['id' => 3, 'services' => 'HR Consulting, Training']
        ];
        
        foreach ($updates as $update) {
            $stmt = $conn->prepare("UPDATE clients SET services = :services WHERE id = :id");
            $stmt->execute([
                ':services' => $update['services'],
                ':id' => $update['id']
            ]);
        }
        echo "<p>✓ Sample services data updated</p>";
        
    } else {
        echo "<p>✓ Services column already exists</p>";
    }
    
    // Show current clients with services
    echo "<h3>Current Clients with Services:</h3>";
    $stmt = $conn->prepare("SELECT id, client_name, services FROM clients ORDER BY id");
    $stmt->execute();
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($clients) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Client Name</th><th>Services</th></tr>";
        foreach ($clients as $client) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($client['id']) . "</td>";
            echo "<td>" . htmlspecialchars($client['client_name']) . "</td>";
            echo "<td>" . htmlspecialchars($client['services'] ?? 'None') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No clients found</p>";
    }
    
    echo "<p><strong>Setup completed successfully!</strong></p>";
    echo "<p><a href='clients.php'>Go to Clients Management</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
