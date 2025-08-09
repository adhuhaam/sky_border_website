<?php
/**
 * Database Setup Script
 * Sky Border Solutions CMS
 * 
 * This script initializes the database with the required tables and default data.
 * Run this once to set up your CMS.
 */

require_once 'config/database.php';

// Security: Only allow access from localhost or if a setup key is provided
$setupKey = $_GET['key'] ?? '';
$isLocalhost = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1']);

if (!$isLocalhost && $setupKey !== 'sky_border_setup_2024') {
    die('Setup access denied. Please run from localhost or provide the correct setup key.');
}

$messages = [];
$errors = [];

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Read and execute the schema file
    $schemaFile = __DIR__ . '/database/schema.sql';
    
    if (!file_exists($schemaFile)) {
        throw new Exception('Schema file not found: ' . $schemaFile);
    }
    
    $sql = file_get_contents($schemaFile);
    
    // Remove comments and split by semicolon
    $sql = preg_replace('/--.*$/m', '', $sql);
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $successCount = 0;
    $totalStatements = count($statements);
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $conn->exec($statement);
                $successCount++;
            } catch (PDOException $e) {
                // Log the error but continue with other statements
                $errors[] = "Error executing statement: " . $e->getMessage();
            }
        }
    }
    
    $messages[] = "Database setup completed successfully!";
    $messages[] = "Executed {$successCount} out of {$totalStatements} SQL statements.";
    
    // Test admin user creation
    try {
        $query = "SELECT COUNT(*) as count FROM admin_users WHERE username = 'admin'";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        
        if ($result['count'] > 0) {
            $messages[] = "Default admin user already exists.";
            $messages[] = "Username: admin | Password: admin123";
        }
    } catch (Exception $e) {
        $errors[] = "Could not verify admin user: " . $e->getMessage();
    }
    
} catch (Exception $e) {
    $errors[] = "Database connection failed: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Sky Border Solutions CMS</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brand': {
                            'blue': '#2E86AB',
                            'teal': '#4ECDC4',
                            'green': '#5CB85C'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full bg-gray-50">
    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-r from-brand-blue to-brand-teal">
                    <i class="fas fa-database text-white text-2xl"></i>
                </div>
                <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900">
                    Database Setup
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Sky Border Solutions CMS
                </p>
            </div>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                
                <!-- Success Messages -->
                <?php if (!empty($messages)): ?>
                <div class="mb-4">
                    <?php foreach ($messages as $message): ?>
                    <div class="mb-2 rounded-md bg-green-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800"><?php echo htmlspecialchars($message); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Error Messages -->
                <?php if (!empty($errors)): ?>
                <div class="mb-4">
                    <?php foreach ($errors as $error): ?>
                    <div class="mb-2 rounded-md bg-red-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800"><?php echo htmlspecialchars($error); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Setup Instructions -->
                <div class="space-y-4 text-sm text-gray-600">
                    <h3 class="font-medium text-gray-900">Setup Complete!</h3>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <h4 class="font-medium text-blue-900 mb-2">Default Admin Credentials:</h4>
                        <p><strong>Username:</strong> admin</p>
                        <p><strong>Password:</strong> admin123</p>
                        <p class="text-xs text-blue-700 mt-2">⚠️ Please change these credentials after your first login!</p>
                    </div>
                    
                    <div class="space-y-2">
                        <h4 class="font-medium text-gray-900">Next Steps:</h4>
                        <ol class="list-decimal list-inside space-y-1 text-sm">
                            <li>Login to the admin panel</li>
                            <li>Change the default admin password</li>
                            <li>Update company information</li>
                            <li>Add your clients and services</li>
                            <li>Delete this setup file for security</li>
                        </ol>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 space-y-3">
                    <a href="index.php" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-brand-blue to-brand-teal hover:from-brand-blue hover:to-brand-blue focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-blue">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Go to Admin Login
                    </a>
                    
                    <a href="../index.php" class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-blue">
                        <i class="fas fa-home mr-2"></i>
                        View Website
                    </a>
                </div>

                <!-- Security Notice -->
                <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Security Notice</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>For security reasons, please delete this setup.php file after completing the setup process.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>