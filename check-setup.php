<?php
/**
 * Setup Check for Sky Border Solutions Website
 * This file helps diagnose what components are available
 */

$checks = [];

// Check if admin directory exists
$checks['admin_directory'] = file_exists('admin') && is_dir('admin');

// Check if database config exists
$checks['database_config'] = file_exists('admin/config/database.php');

// Check if ContentManager class exists
$checks['content_manager'] = file_exists('admin/classes/ContentManager.php');

// Check if Auth class exists
$checks['auth_class'] = file_exists('admin/classes/Auth.php');

// Try to connect to database if config exists
$checks['database_connection'] = false;
if ($checks['database_config']) {
    try {
        require_once 'admin/config/database.php';
        $database = new Database();
        $conn = $database->getConnection();
        if ($conn) {
            $checks['database_connection'] = true;
        }
    } catch (Exception $e) {
        $checks['database_connection'] = false;
        $checks['database_error'] = $e->getMessage();
    }
}

// Check if required tables exist
$checks['tables'] = [];
if ($checks['database_connection']) {
    $requiredTables = [
        'admin_users', 'company_info', 'statistics', 'service_categories',
        'portfolio_categories', 'client_categories', 'clients', 'contact_messages'
    ];
    
    foreach ($requiredTables as $table) {
        try {
            $stmt = $conn->prepare("SHOW TABLES LIKE '$table'");
            $stmt->execute();
            $checks['tables'][$table] = $stmt->rowCount() > 0;
        } catch (Exception $e) {
            $checks['tables'][$table] = false;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sky Border Solutions - Setup Check</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .check { padding: 10px; margin: 5px 0; border-radius: 5px; }
        .check.pass { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .check.fail { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .check.warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .status { font-weight: bold; }
        h1 { color: #2E86AB; }
        h2 { color: #4ECDC4; margin-top: 30px; }
        .recommendation { background: #e7f3ff; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <h1>🌟 Sky Border Solutions - Setup Check</h1>
    
    <h2>📁 File System Checks</h2>
    
    <div class="check <?php echo $checks['admin_directory'] ? 'pass' : 'fail'; ?>">
        <span class="status"><?php echo $checks['admin_directory'] ? '✅' : '❌'; ?></span>
        Admin Directory: <?php echo $checks['admin_directory'] ? 'Found' : 'Missing'; ?>
    </div>
    
    <div class="check <?php echo $checks['database_config'] ? 'pass' : 'fail'; ?>">
        <span class="status"><?php echo $checks['database_config'] ? '✅' : '❌'; ?></span>
        Database Config: <?php echo $checks['database_config'] ? 'Found' : 'Missing'; ?>
        <?php if (!$checks['database_config']): ?>
            <br><small>Expected at: admin/config/database.php</small>
        <?php endif; ?>
    </div>
    
    <div class="check <?php echo $checks['content_manager'] ? 'pass' : 'fail'; ?>">
        <span class="status"><?php echo $checks['content_manager'] ? '✅' : '❌'; ?></span>
        ContentManager Class: <?php echo $checks['content_manager'] ? 'Found' : 'Missing'; ?>
        <?php if (!$checks['content_manager']): ?>
            <br><small>Expected at: admin/classes/ContentManager.php</small>
        <?php endif; ?>
    </div>
    
    <div class="check <?php echo $checks['auth_class'] ? 'pass' : 'fail'; ?>">
        <span class="status"><?php echo $checks['auth_class'] ? '✅' : '❌'; ?></span>
        Auth Class: <?php echo $checks['auth_class'] ? 'Found' : 'Missing'; ?>
        <?php if (!$checks['auth_class']): ?>
            <br><small>Expected at: admin/classes/Auth.php</small>
        <?php endif; ?>
    </div>
    
    <h2>🗄️ Database Checks</h2>
    
    <div class="check <?php echo $checks['database_connection'] ? 'pass' : 'fail'; ?>">
        <span class="status"><?php echo $checks['database_connection'] ? '✅' : '❌'; ?></span>
        Database Connection: <?php echo $checks['database_connection'] ? 'Connected' : 'Failed'; ?>
        <?php if (isset($checks['database_error'])): ?>
            <br><small>Error: <?php echo htmlspecialchars($checks['database_error']); ?></small>
        <?php endif; ?>
    </div>
    
    <?php if ($checks['database_connection'] && !empty($checks['tables'])): ?>
    <h3>📊 Database Tables</h3>
    <?php foreach ($checks['tables'] as $table => $exists): ?>
    <div class="check <?php echo $exists ? 'pass' : 'fail'; ?>">
        <span class="status"><?php echo $exists ? '✅' : '❌'; ?></span>
        Table: <?php echo $table; ?> - <?php echo $exists ? 'Exists' : 'Missing'; ?>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
    
    <h2>🎯 Current Status</h2>
    
    <?php 
    $allPass = $checks['admin_directory'] && $checks['database_config'] && 
               $checks['content_manager'] && $checks['database_connection'];
    ?>
    
    <?php if ($allPass): ?>
    <div class="check pass">
        <span class="status">🎉</span>
        <strong>All systems operational!</strong> Your website should work with full database functionality.
    </div>
    <?php else: ?>
    <div class="check warning">
        <span class="status">⚠️</span>
        <strong>Fallback mode active.</strong> Your website will work with static content while admin features are unavailable.
    </div>
    <?php endif; ?>
    
    <div class="recommendation">
        <h3>📋 Recommendations</h3>
        <ul>
            <?php if (!$allPass): ?>
            <li><strong>Main Website:</strong> Will work with static content - all UI enhancements and animations are functional</li>
            <li><strong>Admin Panel:</strong> Upload admin files to enable database functionality</li>
            <li><strong>Contact Form:</strong> Currently shows success message but doesn't save to database</li>
            <?php else: ?>
            <li>✅ Everything is working perfectly!</li>
            <li>🔗 <a href="admin/">Access Admin Panel</a></li>
            <li>🏠 <a href="index.php">View Main Website</a></li>
            <?php endif; ?>
        </ul>
    </div>
    
    <div style="margin-top: 40px; padding: 20px; background: #f8f9fa; border-radius: 5px; text-align: center;">
        <p><strong>Sky Border Solutions</strong> - Professional HR Consulting & Recruitment</p>
        <p><small>Setup check completed at <?php echo date('Y-m-d H:i:s'); ?></small></p>
    </div>
</body>
</html>
