<?php
/**
 * Test Script to Add Sample Insurance Provider Data
 * Run this once to test the insurance provider display
 */

require_once 'classes/Auth.php';
require_once 'classes/ContentManager.php';

try {
    $contentManager = new ContentManager();
    
    // Sample insurance providers data
    $sampleProviders = [
        [
            'provider_name' => 'Maldivian Health Insurance Co.',
            'logo_url' => '', // No logo for now
            'is_featured' => 1,
            'display_order' => 1
        ],
        [
            'provider_name' => 'Allied Insurance Maldives',
            'logo_url' => '',
            'is_featured' => 1,
            'display_order' => 2
        ],
        [
            'provider_name' => 'Maldives National Insurance',
            'logo_url' => '',
            'is_featured' => 0,
            'display_order' => 3
        ],
        [
            'provider_name' => 'Regional Insurance Partners',
            'logo_url' => '',
            'is_featured' => 0,
            'display_order' => 4
        ]
    ];
    
    echo "<h2>Adding Sample Insurance Providers...</h2>\n";
    
    foreach ($sampleProviders as $provider) {
        $result = $contentManager->addInsuranceProvider($provider);
        if ($result) {
            echo "<p>✅ Added: " . htmlspecialchars($provider['provider_name']) . "</p>\n";
        } else {
            echo "<p>❌ Failed to add: " . htmlspecialchars($provider['provider_name']) . "</p>\n";
        }
    }
    
    echo "<h3>Sample data added successfully!</h3>";
    echo "<p><a href='insurance.php'>Go to Insurance Management</a></p>";
    echo "<p><a href='../index.php'>View Main Website</a></p>";
    
} catch (Exception $e) {
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
