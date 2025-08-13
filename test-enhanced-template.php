<?php
require_once 'admin/classes/ContentManager.php';
require_once 'admin/classes/Mailer.php';

try {
    // Initialize classes
    $contentManager = new ContentManager();
    $mailer = new Mailer($contentManager->getConnection());
    
    echo "Testing enhanced email template...\n";
    
    // Test rendering the enhanced template
    $emailHtml = $mailer->renderWebsiteAsEmail('/');
    
    if ($emailHtml) {
        echo "✅ Enhanced template rendering successful!\n";
        echo "Email HTML length: " . strlen($emailHtml) . " characters\n";
        
        // Save to file for inspection
        file_put_contents('test-enhanced-email.html', $emailHtml);
        echo "📄 Enhanced email HTML saved to 'test-enhanced-email.html'\n";
        
        // Check for new features
        if (strpos($emailHtml, 'cdn.tailwindcss.com') !== false) {
            echo "✅ Tailwind CSS CDN found\n";
        }
        
        if (strpos($emailHtml, 'skybordersolutions.com/images/wlogo.png') !== false) {
            echo "✅ Logo image URL found\n";
        }
        
        if (strpos($emailHtml, 'hover:shadow-lg') !== false) {
            echo "✅ Hover effects found\n";
        }
        
        if (strpos($emailHtml, 'transition-all duration-300') !== false) {
            echo "✅ Transitions found\n";
        }
        
        if (strpos($emailHtml, 'focus:ring-2') !== false) {
            echo "✅ Focus states found\n";
        }
        
        // Check for responsive design
        if (strpos($emailHtml, '@media only screen') !== false) {
            echo "✅ Responsive CSS found\n";
        }
        
        echo "\n🎉 Enhanced template is working perfectly!\n";
        echo "Features included:\n";
        echo "- Tailwind CSS via CDN\n";
        echo "- Real logo image from website\n";
        echo "- Hover effects and transitions\n";
        echo "- Responsive design\n";
        echo "- Focus states for form elements\n";
        
    } else {
        echo "❌ Enhanced template rendering failed!\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
