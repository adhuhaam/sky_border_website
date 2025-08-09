<?php
/**
 * Update Company Info Table
 * Sky Border Solutions CMS
 * 
 * This script adds the about_us column and updates company information
 */

require_once '../config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception("Database connection failed");
    }
    
    echo "<h2>Updating Company Info Table...</h2>\n";
    
    // Check if about_us column exists
    $checkColumnQuery = "SHOW COLUMNS FROM company_info LIKE 'about_us'";
    $stmt = $conn->prepare($checkColumnQuery);
    $stmt->execute();
    $columnExists = $stmt->fetch();
    
    if (!$columnExists) {
        echo "<p>Adding about_us column to company_info table...</p>\n";
        
        // Add about_us column
        $alterQuery = "ALTER TABLE company_info ADD COLUMN about_us TEXT AFTER description";
        $stmt = $conn->prepare($alterQuery);
        $stmt->execute();
        
        echo "<p style='color: green;'>✓ about_us column added successfully!</p>\n";
    } else {
        echo "<p style='color: blue;'>ℹ about_us column already exists.</p>\n";
    }
    
    // Update or insert company information
    $aboutUs = "Sky Border Solution Pvt Ltd is a government-licensed HR consultancy and recruitment firm headquartered in the Republic of Maldives. Established in response to the rising demand for skilled foreign labor, we are strategically positioned to provide end-to-end manpower solutions. Our operations are driven by a long-term vision, well-defined mission, and a strong foundation of core values. With a seasoned leadership team that brings decades of recruitment expertise, we are adept at identifying, sourcing, and placing the most qualified talent to meet diverse organizational needs. Our consistent year-on-year growth, backed by a solid financial framework, reflects our commitment to service excellence, operational integrity, and client satisfaction. At Sky Border Solution, we are dedicated to bridging workforce gaps with professionalism, precision, and purpose.";
    
    // Check if company info exists
    $checkQuery = "SELECT id FROM company_info LIMIT 1";
    $stmt = $conn->prepare($checkQuery);
    $stmt->execute();
    $exists = $stmt->fetch();
    
    if ($exists) {
        echo "<p>Updating existing company information...</p>\n";
        
        // Update existing record
        $updateQuery = "UPDATE company_info SET 
                        company_name = 'Sky Border Solution Pvt Ltd',
                        about_us = :about_us,
                        description = 'Government-licensed HR consultancy and recruitment firm',
                        phone = '+960 330-5462',
                        email = 'info@skybordersolutions.com',
                        address = 'Male, Republic of Maldives',
                        website = 'https://skybordersolutions.com',
                        established_year = 2020
                        WHERE id = :id";
        
        $stmt = $conn->prepare($updateQuery);
        $stmt->execute([
            ':about_us' => $aboutUs,
            ':id' => $exists['id']
        ]);
        
        echo "<p style='color: green;'>✓ Company information updated successfully!</p>\n";
    } else {
        echo "<p>Inserting new company information...</p>\n";
        
        // Insert new record
        $insertQuery = "INSERT INTO company_info (
                        company_name, description, about_us, phone, email, address, website, established_year, is_active
                        ) VALUES (
                        'Sky Border Solution Pvt Ltd',
                        'Government-licensed HR consultancy and recruitment firm',
                        :about_us,
                        '+960 330-5462',
                        'info@skybordersolutions.com',
                        'Male, Republic of Maldives',
                        'https://skybordersolutions.com',
                        2020,
                        1
                        )";
        
        $stmt = $conn->prepare($insertQuery);
        $stmt->execute([':about_us' => $aboutUs]);
        
        echo "<p style='color: green;'>✓ Company information inserted successfully!</p>\n";
    }
    
    echo "<h3 style='color: green;'>Database update completed successfully!</h3>\n";
    echo "<p><a href='../dashboard.php'>← Back to Dashboard</a></p>\n";
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</h3>\n";
    echo "<p>Please check your database configuration and try again.</p>\n";
    echo "<p><a href='../setup.php'>← Go to Setup</a></p>\n";
}
?>
