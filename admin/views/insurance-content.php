<?php
/**
 * Insurance Providers Content View
 * Sky Border Solutions CMS
 */

// Get insurance providers data
try {
    $insuranceProviders = $contentManager->getInsuranceProviders();
} catch (Exception $e) {
    $insuranceProviders = [];
    error_log("Insurance providers fetch error: " . $e->getMessage());
}
?>

<div class="insurance-providers-content">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="text-center">
            <div class="inline-flex items-center rounded-full bg-blue-50 dark:bg-blue-900/30 px-4 py-2 text-sm font-medium text-blue-700 dark:text-blue-300 mb-4">
                <i class="fas fa-shield-alt mr-2"></i>
                Our Insurance Partners
            </div>
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                Trusted <span class="bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">Insurance Providers</span>
            </h2>
            <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-gray-600 dark:text-gray-300">
                We partner with leading insurance companies to provide comprehensive coverage for our workforce solutions.
            </p>
        </div>
    </div>

    <?php if (empty($insuranceProviders)): ?>
    <!-- Empty State -->
    <div class="text-center py-16">
        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 mb-6">
            <i class="fas fa-shield-alt text-gray-400 text-2xl"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Insurance Providers</h3>
        <p class="text-gray-500 dark:text-gray-400">Insurance provider information will be available soon.</p>
    </div>
    <?php else: ?>
    
    <!-- Featured Providers Section -->
    <?php 
    $featuredProviders = array_filter($insuranceProviders, function($provider) {
        return $provider['is_featured'] == 1;
    });
    ?>
    
    <?php if (!empty($featuredProviders)): ?>
    <div class="mb-12">
        <div class="text-center mb-8">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Featured Partners</h3>
            <p class="text-gray-600 dark:text-gray-400">Our premier insurance partners</p>
        </div>
        
        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($featuredProviders as $provider): ?>
            <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border border-gray-200 dark:border-gray-700">
                <!-- Featured Badge -->
                <div class="absolute top-4 right-4">
                    <span class="inline-flex items-center rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 px-3 py-1 text-xs font-medium text-white shadow-sm">
                        <i class="fas fa-star mr-1"></i>
                        Featured
                    </span>
                </div>
                
                <!-- Logo -->
                <div class="flex justify-center mb-6">
                    <?php 
                    // Handle logo path properly for web access
                    $logoPath = '';
                    $hasLogo = false;
                    if (!empty($provider['logo_url'])) {
                        // Check if it's already a full URL
                        if (filter_var($provider['logo_url'], FILTER_VALIDATE_URL)) {
                            $logoPath = $provider['logo_url'];
                            $hasLogo = true; // Assume external URLs are valid
                        } else {
                            // For local files, just use the path as stored (it's relative to admin)
                            $logoPath = '../' . ltrim($provider['logo_url'], '/');
                            $hasLogo = file_exists($logoPath);
                        }
                    }
                    ?>
                    <?php if ($hasLogo): ?>
                    <div class="h-20 w-24 bg-gray-50 dark:bg-gray-700 rounded-xl flex items-center justify-center p-3 group-hover:bg-gray-100 dark:group-hover:bg-gray-600 transition-colors duration-200">
                        <img src="<?php echo htmlspecialchars($logoPath); ?>" 
                             alt="<?php echo htmlspecialchars($provider['provider_name']); ?>" 
                             class="h-full w-full object-contain"
                             onerror="this.style.display='none'; this.parentNode.style.display='none'; this.parentNode.nextElementSibling.style.display='flex';">
                    </div>
                    <div class="h-20 w-20 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-shadow duration-200" style="display: none;">
                        <i class="fas fa-shield-alt text-white text-2xl"></i>
                    </div>
                    <?php else: ?>
                    <div class="h-20 w-20 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-shadow duration-200">
                        <i class="fas fa-shield-alt text-white text-2xl"></i>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Provider Info -->
                <div class="text-center">
                    <h4 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                        <?php echo htmlspecialchars($provider['provider_name']); ?>
                    </h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">
                        Professional Insurance Solutions
                    </p>
                </div>
                
                <!-- Hover Effect -->
                <div class="absolute inset-0 bg-gradient-to-r from-blue-50/50 to-cyan-50/50 dark:from-blue-900/20 dark:to-cyan-900/20 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- All Providers Section -->
    <?php 
    $allProviders = array_filter($insuranceProviders, function($provider) {
        return $provider['is_active'] == 1;
    });
    // Sort by display order
    usort($allProviders, function($a, $b) {
        return $a['display_order'] - $b['display_order'];
    });
    ?>
    
    <div class="mb-8">
        <div class="text-center mb-8">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Our Insurance Network</h3>
            <p class="text-gray-600 dark:text-gray-400">Comprehensive coverage through trusted partners</p>
        </div>
        
        <div class="grid grid-cols-2 gap-6 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
            <?php foreach ($allProviders as $provider): ?>
            <div class="group relative overflow-hidden rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1 border border-gray-200 dark:border-gray-700">
                <!-- Logo -->
                <div class="flex justify-center mb-4">
                    <?php 
                    // Handle logo path properly for web access
                    $logoPath = '';
                    $hasLogo = false;
                    if (!empty($provider['logo_url'])) {
                        // Check if it's already a full URL
                        if (filter_var($provider['logo_url'], FILTER_VALIDATE_URL)) {
                            $logoPath = $provider['logo_url'];
                            $hasLogo = true; // Assume external URLs are valid
                        } else {
                            // For local files, just use the path as stored (it's relative to admin)
                            $logoPath = '../' . ltrim($provider['logo_url'], '/');
                            $hasLogo = file_exists($logoPath);
                        }
                    }
                    ?>
                    <?php if ($hasLogo): ?>
                    <div class="h-12 w-16 bg-gray-50 dark:bg-gray-700 rounded-lg flex items-center justify-center p-2 group-hover:bg-gray-100 dark:group-hover:bg-gray-600 transition-colors duration-200">
                        <img src="<?php echo htmlspecialchars($logoPath); ?>" 
                             alt="<?php echo htmlspecialchars($provider['provider_name']); ?>" 
                             class="h-full w-full object-contain"
                             onerror="this.style.display='none'; this.parentNode.style.display='none'; this.parentNode.nextElementSibling.style.display='flex';">
                    </div>
                    <div class="h-12 w-12 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center shadow-sm group-hover:shadow-md transition-shadow duration-200" style="display: none;">
                        <i class="fas fa-shield-alt text-white text-lg"></i>
                    </div>
                    <?php else: ?>
                    <div class="h-12 w-12 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center shadow-sm group-hover:shadow-md transition-shadow duration-200">
                        <i class="fas fa-shield-alt text-white text-lg"></i>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Provider Name -->
                <div class="text-center">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white line-clamp-2" title="<?php echo htmlspecialchars($provider['provider_name']); ?>">
                        <?php echo htmlspecialchars($provider['provider_name']); ?>
                    </h4>
                </div>
                
                <!-- Featured Indicator -->
                <?php if ($provider['is_featured']): ?>
                <div class="absolute top-2 right-2">
                    <div class="w-2 h-2 bg-yellow-400 rounded-full shadow-sm"></div>
                </div>
                <?php endif; ?>
                
                <!-- Hover Effect -->
                <div class="absolute inset-0 bg-gradient-to-r from-blue-50/30 to-cyan-50/30 dark:from-blue-900/10 dark:to-cyan-900/10 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Trust Indicators -->
    <div class="bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 rounded-2xl p-8 text-center">
        <div class="max-w-3xl mx-auto">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Why Choose Our Insurance Partners?</h3>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                <div class="flex flex-col items-center">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30 mb-3">
                        <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Licensed & Regulated</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">All partners are fully licensed and regulated by authorities</p>
                </div>
                <div class="flex flex-col items-center">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30 mb-3">
                        <i class="fas fa-award text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Industry Leading</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Top-rated insurance providers with proven track records</p>
                </div>
                <div class="flex flex-col items-center">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/30 mb-3">
                        <i class="fas fa-handshake text-purple-600 dark:text-purple-400 text-xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Trusted Partnership</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Long-term partnerships ensuring reliable coverage</p>
                </div>
            </div>
        </div>
    </div>
    
    <?php endif; ?>
</div>

<style>
/* Line Clamp Utility */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Responsive Grid Improvements */
@media (max-width: 640px) {
    .grid {
        gap: 1rem;
    }
    
    .grid-cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 480px) {
    .grid-cols-2 {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }
}
</style>
