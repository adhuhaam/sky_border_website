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
    
    // Check if duration columns exist
    $stmt = $conn->prepare("SHOW COLUMNS FROM clients LIKE 'service_duration_type'");
    $stmt->execute();
    $result = $stmt->fetch();
    
    if (!$result) {
        echo "<p>Adding duration columns to clients table...</p>";
        
        // Add duration columns
        $stmt = $conn->prepare("ALTER TABLE clients ADD COLUMN service_duration_type ENUM('ongoing', 'date_range') DEFAULT 'ongoing' AFTER services");
        $stmt->execute();
        echo "<p>✓ Service duration type column added successfully</p>";
        
        $stmt = $conn->prepare("ALTER TABLE clients ADD COLUMN service_start_date DATE NULL AFTER service_duration_type");
        $stmt->execute();
        echo "<p>✓ Service start date column added successfully</p>";
        
        $stmt = $conn->prepare("ALTER TABLE clients ADD COLUMN service_end_date DATE NULL AFTER service_start_date");
        $stmt->execute();
        echo "<p>✓ Service end date column added successfully</p>";
        
        // Add index for duration fields
        $stmt = $conn->prepare("CREATE INDEX idx_clients_duration ON clients(service_duration_type, service_start_date, service_end_date)");
        $stmt->execute();
        echo "<p>✓ Duration index created successfully</p>";
        
        // Set default duration type for existing clients
        $stmt = $conn->prepare("UPDATE clients SET service_duration_type = 'ongoing' WHERE service_duration_type IS NULL");
        $stmt->execute();
        echo "<p>✓ Default duration type set for existing clients</p>";
        
    } else {
        echo "<p>✓ Duration columns already exist</p>";
    }
    
    // Show current clients with services and duration
    echo "<h3>Current Clients with Services and Duration:</h3>";
    $stmt = $conn->prepare("SELECT id, client_name, services, service_duration_type, service_start_date, service_end_date FROM clients ORDER BY id");
    $stmt->execute();
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($clients) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Client Name</th><th>Services</th><th>Duration Type</th><th>Start Date</th><th>End Date</th></tr>";
        foreach ($clients as $client) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($client['id']) . "</td>";
            echo "<td>" . htmlspecialchars($client['client_name']) . "</td>";
            echo "<td>" . htmlspecialchars($client['services'] ?? 'None') . "</td>";
            echo "<td>" . htmlspecialchars($client['service_duration_type'] ?? 'ongoing') . "</td>";
            echo "<td>" . htmlspecialchars($client['service_start_date'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($client['service_end_date'] ?? 'N/A') . "</td>";
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
