<?php
/**
 * Update Admin Pages with Sidebar Layout
 * This script will update all admin pages to use the new sidebar layout
 */

$adminPages = [
    'company-info.php',
    'services.php', 
    'clients.php',
    'messages.php'
];

foreach ($adminPages as $page) {
    if (file_exists($page)) {
        echo "Updating $page...\n";
        
        $content = file_get_contents($page);
        
        // Remove old navigation and dark mode toggle
        $content = preg_replace('/<!-- Dark Mode Toggle -->.*?<\/div>/s', '', $content);
        $content = preg_replace('/<nav class="bg-white.*?<\/nav>/s', '', $content);
        
        // Replace body content structure
        $content = str_replace(
            '<body class="h-full bg-gray-50 dark:bg-gray-900 theme-transition">',
            '<body class="h-full bg-gray-50 dark:bg-gray-900 theme-transition">
    <div class="flex h-screen">
        <!-- Include Sidebar -->
        <?php include \'includes/sidebar.php\'; ?>

        <!-- Main content area -->
        <div class="flex-1 md:pl-64">
            <div class="flex flex-col h-full">
                <!-- Main content -->
                <main class="flex-1 relative overflow-y-auto focus:outline-none">',
            $content
        );
        
        // Update page structure
        $content = str_replace(
            '<div class="min-h-full">',
            '',
            $content
        );
        
        // Close the new structure properly
        $content = str_replace(
            '</body>',
            '                </main>
            </div>
        </div>
    </div>
</body>',
            $content
        );
        
        // Update main content wrapper
        $content = str_replace(
            '<div class="py-10">',
            '<div class="py-6">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">',
            $content
        );
        
        // Close content wrapper
        $content = str_replace(
            '</div>
    </div>',
            '</div>',
            $content
        );
        
        file_put_contents($page, $content);
        echo "Updated $page successfully!\n";
    }
}

echo "All admin pages updated with sidebar layout!\n";
?>
