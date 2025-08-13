<?php
// Test PHPMailer installation
echo "Testing PHPMailer installation...\n";

// Include Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Test if PHPMailer classes are available
if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    echo "✅ PHPMailer class found via Composer\n";
    
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        echo "✅ PHPMailer instantiated successfully\n";
        
        // Test basic properties
        echo "✅ PHPMailer version: " . PHPMailer\PHPMailer\PHPMailer::VERSION . "\n";
        
    } catch (Exception $e) {
        echo "❌ Error instantiating PHPMailer: " . $e->getMessage() . "\n";
    }
    
} else {
    echo "❌ PHPMailer class not found\n";
}

// Test SMTP class
if (class_exists('PHPMailer\PHPMailer\SMTP')) {
    echo "✅ SMTP class found\n";
} else {
    echo "❌ SMTP class not found\n";
}

// Test Exception class
if (class_exists('PHPMailer\PHPMailer\Exception')) {
    echo "✅ Exception class found\n";
} else {
    echo "❌ Exception class not found\n";
}

echo "\nTest completed!\n";
?>
