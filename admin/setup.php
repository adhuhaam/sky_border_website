<?php
// Database setup script for Sky Border Solutions Admin System

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database configuration
$host = 'localhost';
$db_name = 'skydfcaf_sky_border';
$username = 'skydfcaf_sky_border_user';
$password = 'Ompl@65482*';

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Database Setup - Sky Border Solutions</title>
    <script src='https://cdn.tailwindcss.com'></script>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class='bg-gray-50 py-12'>
    <div class='max-w-4xl mx-auto px-4'>
        <div class='text-center mb-8'>
            <img src='../images/logo.svg' alt='Sky Border Solutions' class='h-16 w-auto mx-auto mb-4'>
            <h1 class='text-3xl font-bold text-gray-900'>Database Setup</h1>
            <p class='text-gray-600'>Setting up your Sky Border Solutions admin system...</p>
        </div>
        
        <div class='bg-white rounded-lg shadow-lg p-8'>";

try {
    // Connect to MySQL
    echo "<div class='mb-6'>
            <h2 class='text-xl font-semibold text-gray-900 mb-4'>Database Connection</h2>";
    
    $pdo = new PDO("mysql:host=$host;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<div class='flex items-center text-green-600 mb-2'>
            <i class='fas fa-check-circle mr-2'></i>
            Connected to MySQL server successfully
          </div>";
    
    // Create database
    echo "<div class='flex items-center text-blue-600 mb-2'>
            <i class='fas fa-database mr-2'></i>
            Creating database: $db_name
          </div>";
    
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8 COLLATE utf8_general_ci");
    $pdo->exec("USE `$db_name`");
    
    echo "<div class='flex items-center text-green-600 mb-4'>
            <i class='fas fa-check-circle mr-2'></i>
            Database created successfully
          </div>";
    
    echo "</div>";
    
    // Read and execute schema
    echo "<div class='mb-6'>
            <h2 class='text-xl font-semibold text-gray-900 mb-4'>Creating Tables</h2>";
    
    $schema = file_get_contents('database/schema.sql');
    $statements = explode(';', $schema);
    
    $tableCount = 0;
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement) && !preg_match('/^(CREATE DATABASE|USE|--)/i', $statement)) {
            try {
                $pdo->exec($statement);
                if (preg_match('/^CREATE TABLE\s+(\w+)/i', $statement, $matches)) {
                    $tableName = $matches[1];
                    echo "<div class='flex items-center text-green-600 mb-1'>
                            <i class='fas fa-table mr-2'></i>
                            Created table: $tableName
                          </div>";
                    $tableCount++;
                }
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'already exists') === false) {
                    echo "<div class='flex items-center text-red-600 mb-1'>
                            <i class='fas fa-exclamation-circle mr-2'></i>
                            Error executing statement: " . htmlspecialchars($e->getMessage()) . "
                          </div>";
                }
            }
        }
    }
    
    echo "<div class='flex items-center text-green-600 mt-4'>
            <i class='fas fa-check-circle mr-2'></i>
            Created $tableCount tables successfully
          </div>";
    
    echo "</div>";
    
    // Insert sample data
    echo "<div class='mb-6'>
            <h2 class='text-xl font-semibold text-gray-900 mb-4'>Inserting Sample Data</h2>";
    
    // Check if admin user exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM admin_users WHERE username = 'admin'");
    $adminExists = $stmt->fetchColumn() > 0;
    
    if (!$adminExists) {
        // Create default admin user
        $hashedPassword = password_hash('password', PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO admin_users (username, email, password_hash, full_name, role) VALUES (?, ?, ?, ?, ?)")
            ->execute(['admin', 'admin@skybordersolutions.com', $hashedPassword, 'System Administrator', 'super_admin']);
        
        echo "<div class='flex items-center text-green-600 mb-2'>
                <i class='fas fa-user-plus mr-2'></i>
                Created admin user (username: admin, password: password)
              </div>";
    } else {
        echo "<div class='flex items-center text-blue-600 mb-2'>
                <i class='fas fa-info-circle mr-2'></i>
                Admin user already exists
              </div>";
    }
    
    // Check if company info exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM company_info");
    $companyExists = $stmt->fetchColumn() > 0;
    
    if (!$companyExists) {
        $pdo->exec("INSERT INTO company_info (company_name, tagline, description, mission, vision, phone, hotline1, hotline2, email, address, business_hours) VALUES 
        ('Sky Border Solutions', 
         'Where compliance meets competence',
         'Leading HR consultancy and recruitment firm in the Republic of Maldives, providing end-to-end manpower solutions with excellence and integrity.',
         'To foster enduring partnerships with organizations by delivering superior recruitment solutions that align with their strategic goals. We are committed to offering unparalleled client service, acting as a seamless extension of our clients\' human resource operations.',
         'To be the most trusted and recognized recruitment company in the Maldives, known for our professionalism, excellence and ability to deliver outstanding outcomes for both employers and candidates.',
         '+960 4000-444',
         '+960 755-9001',
         '+960 911-1409',
         'info@skybordersolutions.com',
         'H. Dhoorihaa (5A), Kalaafaanu Hingun, Male\' City, Republic of Maldives',
         'Sunday - Thursday: 8:00 AM - 5:00 PM\\nSaturday: 9:00 AM - 1:00 PM\\nFriday: Closed')");
        
        echo "<div class='flex items-center text-green-600 mb-2'>
                <i class='fas fa-building mr-2'></i>
                Inserted company information
              </div>";
    }
    
    // Insert statistics if not exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM statistics");
    $statsExist = $stmt->fetchColumn() > 0;
    
    if (!$statsExist) {
        $pdo->exec("INSERT INTO statistics (stat_name, stat_value, stat_label, display_order) VALUES
        ('placements', '1000+', 'Successful Placements', 1),
        ('partners', '50+', 'Partner Companies', 2),
        ('compliance', '100%', 'Licensed & Compliant', 3)");
        
        echo "<div class='flex items-center text-green-600 mb-2'>
                <i class='fas fa-chart-bar mr-2'></i>
                Inserted statistics data
              </div>";
    }
    
    echo "</div>";
    
    // Success message
    echo "<div class='bg-green-50 border border-green-200 rounded-lg p-6 mb-6'>
            <div class='flex'>
                <div class='flex-shrink-0'>
                    <i class='fas fa-check-circle text-green-400 text-xl'></i>
                </div>
                <div class='ml-3'>
                    <h3 class='text-lg font-medium text-green-800'>Setup Complete!</h3>
                    <div class='mt-2 text-sm text-green-700'>
                        <p>Your Sky Border Solutions admin system has been set up successfully.</p>
                        <ul class='mt-2 list-disc list-inside'>
                            <li>Database created and configured</li>
                            <li>All tables created successfully</li>
                            <li>Sample data inserted</li>
                            <li>Admin user created</li>
                        </ul>
                    </div>
                </div>
            </div>
          </div>";
    
    // Login credentials
    echo "<div class='bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6'>
            <div class='flex'>
                <div class='flex-shrink-0'>
                    <i class='fas fa-key text-blue-400 text-xl'></i>
                </div>
                <div class='ml-3'>
                    <h3 class='text-lg font-medium text-blue-800'>Default Login Credentials</h3>
                    <div class='mt-2 text-sm text-blue-700'>
                        <p><strong>Username:</strong> admin</p>
                        <p><strong>Password:</strong> password</p>
                        <p class='mt-2 text-red-600'><strong>⚠️ Important:</strong> Please change the default password after login!</p>
                    </div>
                </div>
            </div>
          </div>";
    
    // Next steps
    echo "<div class='text-center'>
            <h3 class='text-lg font-medium text-gray-900 mb-4'>Next Steps</h3>
            <div class='space-x-4'>
                <a href='login.php' class='inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700'>
                    <i class='fas fa-sign-in-alt mr-2'></i>
                    Login to Admin Panel
                </a>
                <a href='../index.html' class='inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50'>
                    <i class='fas fa-globe mr-2'></i>
                    View Website
                </a>
            </div>
            
            <div class='mt-6 text-sm text-gray-500'>
                <p><strong>Security Note:</strong> Please delete or secure this setup.php file after completion</p>
            </div>
          </div>";
    
} catch (PDOException $e) {
    echo "<div class='bg-red-50 border border-red-200 rounded-lg p-6'>
            <div class='flex'>
                <div class='flex-shrink-0'>
                    <i class='fas fa-exclamation-triangle text-red-400 text-xl'></i>
                </div>
                <div class='ml-3'>
                    <h3 class='text-lg font-medium text-red-800'>Setup Failed</h3>
                    <div class='mt-2 text-sm text-red-700'>
                        <p>Error: " . htmlspecialchars($e->getMessage()) . "</p>
                        <p class='mt-2'>Please check your database credentials and try again.</p>
                    </div>
                </div>
            </div>
          </div>";
}

echo "        </div>
    </div>
</body>
</html>";
?>
