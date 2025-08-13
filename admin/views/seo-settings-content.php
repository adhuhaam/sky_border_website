<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">SEO Settings Management</h1>
                <p class="text-gray-600">Manage meta tags, keywords, Google Analytics, and other SEO elements for your website.</p>
            </div>
            <div class="flex space-x-4">
                <a href="generate-sitemap.php" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
                    <i class="fas fa-sitemap mr-2"></i>Generate Sitemap
                </a>
                <a href="../robots.txt" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
                    <i class="fas fa-robot mr-2"></i>View Robots.txt
                </a>
            </div>
        </div>
    </div>

    <?php if (!empty($message)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($seoSettings)): ?>
        <div class="space-y-8">
            <?php foreach ($seoSettings as $seo): ?>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-semibold text-gray-900 capitalize">
                            <?php echo htmlspecialchars($seo['page_name']); ?> Page SEO
                        </h2>
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                            <?php echo ucfirst(htmlspecialchars($seo['page_name'])); ?>
                        </span>
                    </div>

                    <form method="POST" class="space-y-6">
                        <input type="hidden" name="action" value="update_seo">
                        <input type="hidden" name="page_name" value="<?php echo htmlspecialchars($seo['page_name']); ?>">

                        <!-- Basic Meta Tags -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                                <input type="text" name="meta_title" value="<?php echo htmlspecialchars($seo['meta_title'] ?? ''); ?>" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <p class="text-xs text-gray-500 mt-1">Recommended: 50-60 characters</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                                <textarea name="meta_description" rows="3" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($seo['meta_description'] ?? ''); ?></textarea>
                                <p class="text-xs text-gray-500 mt-1">Recommended: 150-160 characters</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Meta Keywords</label>
                            <textarea name="meta_keywords" rows="2" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($seo['meta_keywords'] ?? ''); ?></textarea>
                            <p class="text-xs text-gray-500 mt-1">Separate keywords with commas</p>
                        </div>

                        <!-- Open Graph Tags -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Open Graph (Facebook) Tags</h3>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">OG Title</label>
                                    <input type="text" name="og_title" value="<?php echo htmlspecialchars($seo['og_title'] ?? ''); ?>" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">OG Description</label>
                                    <textarea name="og_description" rows="2" 
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($seo['og_description'] ?? ''); ?></textarea>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">OG Image URL</label>
                                    <input type="text" name="og_image" value="<?php echo htmlspecialchars($seo['og_image'] ?? ''); ?>" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>

                        <!-- Twitter Card Tags -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Twitter Card Tags</h3>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Twitter Title</label>
                                    <input type="text" name="twitter_title" value="<?php echo htmlspecialchars($seo['twitter_title'] ?? ''); ?>" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Twitter Description</label>
                                    <textarea name="twitter_description" rows="2" 
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($seo['twitter_description'] ?? ''); ?></textarea>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Twitter Image URL</label>
                                    <input type="text" name="twitter_image" value="<?php echo htmlspecialchars($seo['twitter_image'] ?? ''); ?>" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>

                        <!-- Technical SEO -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Technical SEO</h3>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Canonical URL</label>
                                    <input type="url" name="canonical_url" value="<?php echo htmlspecialchars($seo['canonical_url'] ?? ''); ?>" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Robots.txt Content</label>
                                    <textarea name="robots_txt" rows="4" 
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($seo['robots_txt'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Analytics & Tracking -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Analytics & Tracking</h3>
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Google Analytics ID</label>
                                    <input type="text" name="google_analytics_id" value="<?php echo htmlspecialchars($seo['google_analytics_id'] ?? ''); ?>" 
                                           placeholder="G-XXXXXXXXXX" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Google Tag Manager ID</label>
                                    <input type="text" name="google_tag_manager_id" value="<?php echo htmlspecialchars($seo['google_tag_manager_id'] ?? ''); ?>" 
                                           placeholder="GTM-XXXXXXX" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Facebook Pixel ID</label>
                                    <input type="text" name="facebook_pixel_id" value="<?php echo htmlspecialchars($seo['facebook_pixel_id'] ?? ''); ?>" 
                                           placeholder="XXXXXXXXXX" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>

                        <!-- Advanced SEO -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Advanced SEO</h3>
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Schema Markup (JSON-LD)</label>
                                    <textarea name="schema_markup" rows="6" 
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono text-sm"><?php echo htmlspecialchars($seo['schema_markup'] ?? ''); ?></textarea>
                                    <p class="text-xs text-gray-500 mt-1">Enter valid JSON-LD schema markup</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Custom Meta Tags</label>
                                    <textarea name="custom_meta_tags" rows="4" 
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono text-sm"><?php echo htmlspecialchars($seo['custom_meta_tags'] ?? ''); ?></textarea>
                                    <p class="text-xs text-gray-500 mt-1">Enter additional meta tags (one per line)</p>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="border-t pt-6">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md transition-colors duration-200">
                                Update SEO Settings
                            </button>
                        </div>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
            No SEO settings found. Please run the setup script first.
        </div>
    <?php endif; ?>
</div>
