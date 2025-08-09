<?php
/**
 * Setup Insurance Providers Table
 * Run this script once to create the insurance providers table
 */

require_once 'config/database.php';

try {
    $pdo = new PDO($dsn, $username, $password, $options);
    
    // Read and execute the SQL file
    $sql = file_get_contents(__DIR__ . '/database/insurance_providers.sql');
    
    if ($sql === false) {
        throw new Exception('Could not read SQL file');
    }
    
    // Execute the SQL
    $pdo->exec($sql);
    
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Insurance Setup Complete</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
            .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .success { color: #059669; background: #d1fae5; padding: 15px; border-radius: 6px; margin-bottom: 20px; }
            .btn { display: inline-block; background: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 20px; }
            .btn:hover { background: #1d4ed8; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h1>Setup Complete!</h1>
            <div class='success'>
                ✅ Insurance providers table has been created successfully!
            </div>
            <p>The following has been set up:</p>
            <ul>
                <li>Created <code>insurance_providers</code> table</li>
                <li>Added sample insurance providers</li>
                <li>Set up proper indexes</li>
            </ul>
            <p>You can now access the insurance providers management from your admin panel.</p>
            <a href='insurance.php' class='btn'>Go to Insurance Providers</a>
            <a href='dashboard.php' class='btn' style='background: #6b7280; margin-left: 10px;'>Back to Dashboard</a>
        </div>
    </body>
    </html>";
    
} catch (Exception $e) {
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Setup Error</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
            .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .error { color: #dc2626; background: #fee2e2; padding: 15px; border-radius: 6px; margin-bottom: 20px; }
            .btn { display: inline-block; background: #6b7280; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h1>Setup Error</h1>
            <div class='error'>
                ❌ Error creating insurance providers table: " . htmlspecialchars($e->getMessage()) . "
            </div>
            <p>Please check your database configuration and try again.</p>
            <a href='dashboard.php' class='btn'>Back to Dashboard</a>
        </div>
    </body>
    </html>";
}
?>
