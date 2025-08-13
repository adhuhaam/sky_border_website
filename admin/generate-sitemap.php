<?php
require_once 'classes/Auth.php';
require_once 'classes/ContentManager.php';

// Initialize authentication
$auth = new Auth();
if (!$auth->isAuthenticated()) {
    header('Location: index.php');
    exit;
}

// Initialize content manager
$contentManager = new ContentManager();

// Get SEO settings for base URL
$seoSettings = $contentManager->getSEOSettings('global');
$baseUrl = $seoSettings['canonical_url'] ?? 'https://skybordersolutions.com';

// Generate sitemap XML
$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// Add main pages
$pages = [
    '' => '1.0', // Home page
    '/about' => '0.8',
    '/services' => '0.8',
    '/contact' => '0.7'
];

foreach ($pages as $page => $priority) {
    $xml .= '  <url>' . "\n";
    $xml .= '    <loc>' . $baseUrl . $page . '</loc>' . "\n";
    $xml .= '    <lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
    $xml .= '    <changefreq>weekly</changefreq>' . "\n";
    $xml .= '    <priority>' . $priority . '</priority>' . "\n";
    $xml .= '  </url>' . "\n";
}

// Add dynamic content pages
try {
    // Add services
    $services = $contentManager->getAllServices();
    if ($services) {
        foreach ($services as $service) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . $baseUrl . '/services#' . strtolower(str_replace(' ', '-', $service['service_name'])) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
            $xml .= '    <changefreq>monthly</changefreq>' . "\n";
            $xml .= '    <priority>0.6</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }
    }
    
    // Add team members
    $teamMembers = $contentManager->getAllTeamMembers();
    if ($teamMembers) {
        foreach ($teamMembers as $member) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . $baseUrl . '/about#team</loc>' . "\n";
            $xml .= '    <lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
            $xml .= '    <changefreq>monthly</changefreq>' . "\n";
            $xml .= '    <priority>0.5</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }
    }
    
} catch (Exception $e) {
    // Continue if there's an error
}

$xml .= '</urlset>';

// Save sitemap to root directory
$sitemapPath = '../sitemap.xml';
if (file_put_contents($sitemapPath, $xml)) {
    echo "<h1>Sitemap Generated Successfully!</h1>";
    echo "<p>The sitemap has been created at: <code>$sitemapPath</code></p>";
    echo "<p>You can access it at: <a href='../sitemap.xml' target='_blank'>sitemap.xml</a></p>";
} else {
    echo "<h1>Error Generating Sitemap</h1>";
    echo "<p>Failed to create sitemap file.</p>";
}

echo "<hr>";
echo "<p><a href='dashboard.php'>← Back to Dashboard</a></p>";
?>
