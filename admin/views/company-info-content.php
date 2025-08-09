<?php
/**
 * Company Information Content View
 * Sky Border Solutions CMS
 */
?>

<div class="space-y-6">
    <!-- Company Basic Information -->
    <div class="modern-card bg-white dark:bg-gray-800 shadow sm:rounded-lg theme-transition">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 theme-transition">
                <i class="fas fa-building text-brand-blue mr-2"></i>
                Basic Information
            </h3>
            
            <form method="POST" class="space-y-6">
                <input type="hidden" name="section" value="basic">
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="company_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Company Name *</label>
                        <input type="text" name="company_name" id="company_name" required 
                               value="<?php echo htmlspecialchars($companyInfo['company_name'] ?? ''); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                    </div>
                    
                    <div>
                        <label for="business_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Business Type</label>
                        <input type="text" name="business_type" id="business_type" 
                               value="<?php echo htmlspecialchars($companyInfo['business_type'] ?? ''); ?>"
                               placeholder="e.g., Human Resources Agency"
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="registration_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Registration Number</label>
                        <input type="text" name="registration_number" id="registration_number" 
                               value="<?php echo htmlspecialchars($companyInfo['registration_number'] ?? ''); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                    </div>
                    
                    <div>
                        <label for="license_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">License Number</label>
                        <input type="text" name="license_number" id="license_number" 
                               value="<?php echo htmlspecialchars($companyInfo['license_number'] ?? ''); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                    </div>
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Company Description</label>
                    <textarea name="description" id="description" rows="4"
                              placeholder="Brief company description..."
                              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition"><?php echo htmlspecialchars($companyInfo['description'] ?? ''); ?></textarea>
                </div>
                
                <div>
                    <label for="about_us" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">About Us</label>
                    <textarea name="about_us" id="about_us" rows="8"
                              placeholder="Detailed about us content that will be displayed on the website..."
                              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition"><?php echo htmlspecialchars($companyInfo['about_us'] ?? ''); ?></textarea>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">This comprehensive about us text will be displayed prominently on your website's About section.</p>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" name="update_basic" class="btn-primary inline-flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Update Basic Information
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Contact Information -->
    <div class="modern-card bg-white dark:bg-gray-800 shadow sm:rounded-lg theme-transition">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 theme-transition">
                <i class="fas fa-phone text-brand-teal mr-2"></i>
                Contact Information
            </h3>
            
            <form method="POST" class="space-y-6">
                <input type="hidden" name="section" value="contact">
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Phone Number</label>
                        <input type="tel" name="phone" id="phone" 
                               value="<?php echo htmlspecialchars($companyInfo['phone'] ?? ''); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Email Address</label>
                        <input type="email" name="email" id="email" 
                               value="<?php echo htmlspecialchars($companyInfo['email'] ?? ''); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                    </div>
                </div>
                
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Address</label>
                    <textarea name="address" id="address" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition"><?php echo htmlspecialchars($companyInfo['address'] ?? ''); ?></textarea>
                </div>
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="website" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Website URL</label>
                        <input type="url" name="website" id="website" 
                               value="<?php echo htmlspecialchars($companyInfo['website'] ?? ''); ?>"
                               placeholder="https://example.com"
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                    </div>
                    
                    <div>
                        <label for="established_year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Established Year</label>
                        <input type="number" name="established_year" id="established_year" min="1900" max="<?php echo date('Y'); ?>"
                               value="<?php echo htmlspecialchars($companyInfo['established_year'] ?? ''); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                    </div>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" name="update_contact" class="btn-primary inline-flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Update Contact Information
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Mission & Vision -->
    <div class="modern-card bg-white dark:bg-gray-800 shadow sm:rounded-lg theme-transition">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 theme-transition">
                <i class="fas fa-bullseye text-brand-green mr-2"></i>
                Mission & Vision
            </h3>
            
            <form method="POST" class="space-y-6">
                <input type="hidden" name="section" value="mission">
                
                <div>
                    <label for="mission" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Mission Statement</label>
                    <textarea name="mission" id="mission" rows="4"
                              placeholder="Describe your company's mission and core purpose..."
                              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition"><?php echo htmlspecialchars($companyInfo['mission'] ?? ''); ?></textarea>
                </div>
                
                <div>
                    <label for="vision" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Vision Statement</label>
                    <textarea name="vision" id="vision" rows="4"
                              placeholder="Describe your company's vision and future goals..."
                              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition"><?php echo htmlspecialchars($companyInfo['vision'] ?? ''); ?></textarea>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" name="update_mission" class="btn-primary inline-flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Update Mission & Vision
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Social Media & Online Presence -->
    <div class="modern-card bg-white dark:bg-gray-800 shadow sm:rounded-lg theme-transition">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 theme-transition">
                <i class="fas fa-share-alt text-purple-600 mr-2"></i>
                Social Media & Online Presence
            </h3>
            
            <form method="POST" class="space-y-6">
                <input type="hidden" name="section" value="social">
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="facebook_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">
                            <i class="fab fa-facebook text-blue-600 mr-1"></i>
                            Facebook URL
                        </label>
                        <input type="url" name="facebook_url" id="facebook_url" 
                               value="<?php echo htmlspecialchars($companyInfo['facebook_url'] ?? ''); ?>"
                               placeholder="https://facebook.com/yourcompany"
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                    </div>
                    
                    <div>
                        <label for="linkedin_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">
                            <i class="fab fa-linkedin text-blue-700 mr-1"></i>
                            LinkedIn URL
                        </label>
                        <input type="url" name="linkedin_url" id="linkedin_url" 
                               value="<?php echo htmlspecialchars($companyInfo['linkedin_url'] ?? ''); ?>"
                               placeholder="https://linkedin.com/company/yourcompany"
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="twitter_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">
                            <i class="fab fa-twitter text-blue-400 mr-1"></i>
                            Twitter URL
                        </label>
                        <input type="url" name="twitter_url" id="twitter_url" 
                               value="<?php echo htmlspecialchars($companyInfo['twitter_url'] ?? ''); ?>"
                               placeholder="https://twitter.com/yourcompany"
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                    </div>
                    
                    <div>
                        <label for="instagram_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">
                            <i class="fab fa-instagram text-pink-600 mr-1"></i>
                            Instagram URL
                        </label>
                        <input type="url" name="instagram_url" id="instagram_url" 
                               value="<?php echo htmlspecialchars($companyInfo['instagram_url'] ?? ''); ?>"
                               placeholder="https://instagram.com/yourcompany"
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                    </div>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" name="update_social" class="btn-primary inline-flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Update Social Media
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
