<?php
/**
 * Database Setup Content View
 * Sky Border Solutions CMS
 */
?>

<div class="space-y-6">
    <!-- Setup Header -->
    <div class="text-center">
        <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-gradient-to-r from-brand-blue to-brand-teal mb-4">
            <i class="fas fa-database text-white text-2xl"></i>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white theme-transition">Database Setup</h1>
        <p class="mt-2 text-lg text-gray-600 dark:text-gray-300 theme-transition">Initialize your Sky Border Solutions CMS</p>
    </div>

    <!-- Setup Status -->
    <?php if (!empty($messages)): ?>
    <div class="modern-card bg-white dark:bg-gray-800 shadow sm:rounded-lg theme-transition">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 theme-transition">
                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                Setup Results
            </h3>
            <div class="space-y-2">
                <?php foreach ($messages as $message): ?>
                <div class="flex items-center text-green-700 dark:text-green-300">
                    <i class="fas fa-check mr-2 text-sm"></i>
                    <span><?php echo htmlspecialchars($message); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Setup Errors -->
    <?php if (!empty($errors)): ?>
    <div class="modern-card bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 shadow sm:rounded-lg theme-transition">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-red-800 dark:text-red-200 mb-4 theme-transition">
                <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                Setup Errors
            </h3>
            <div class="space-y-2">
                <?php foreach ($errors as $error): ?>
                <div class="flex items-center text-red-700 dark:text-red-300">
                    <i class="fas fa-times mr-2 text-sm"></i>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Setup Form -->
    <?php if (empty($messages) && empty($errors)): ?>
    <div class="modern-card bg-white dark:bg-gray-800 shadow sm:rounded-lg theme-transition">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 theme-transition">
                <i class="fas fa-play text-brand-blue mr-2"></i>
                Initialize Database
            </h3>
            <p class="text-gray-600 dark:text-gray-300 mb-6 theme-transition">
                This will create all required database tables and insert default data for your CMS.
                Make sure your database configuration is correct before proceeding.
            </p>
            
            <form method="POST" class="space-y-4">
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Warning</h3>
                            <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                                This will overwrite existing data if tables already exist. Make sure to backup your database if needed.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="confirm" id="confirm" required
                           class="h-4 w-4 text-brand-blue focus:ring-brand-blue border-gray-300 rounded">
                    <label for="confirm" class="ml-2 text-sm text-gray-700 dark:text-gray-300 theme-transition">
                        I understand the risks and want to proceed with database setup
                    </label>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <a href="index.php" class="btn-secondary inline-flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Login
                    </a>
                    <button type="submit" name="setup" class="btn-primary inline-flex items-center">
                        <i class="fas fa-database mr-2"></i>
                        Initialize Database
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Database Information -->
    <div class="modern-card bg-white dark:bg-gray-800 shadow sm:rounded-lg theme-transition">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 theme-transition">
                <i class="fas fa-info-circle text-brand-teal mr-2"></i>
                Database Information
            </h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block">Database Name</span>
                    <span class="text-gray-900 dark:text-white theme-transition"><?php echo htmlspecialchars($dbName ?? 'skydfcaf_sky_border'); ?></span>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block">Connection Status</span>
                    <span class="text-gray-900 dark:text-white theme-transition">
                        <?php echo $connectionStatus ? 'Connected' : 'Not Connected'; ?>
                        <i class="fas fa-circle text-<?php echo $connectionStatus ? 'green' : 'red'; ?>-500 ml-2"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <?php if (!empty($messages)): ?>
    <div class="modern-card bg-white dark:bg-gray-800 shadow sm:rounded-lg theme-transition">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 theme-transition">
                <i class="fas fa-rocket text-brand-green mr-2"></i>
                Next Steps
            </h3>
            <div class="space-y-3">
                <a href="index.php" class="btn-primary inline-flex items-center w-full justify-center">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Go to Admin Login
                </a>
                <p class="text-sm text-gray-600 dark:text-gray-400 text-center theme-transition">
                    Default admin credentials: <strong>admin</strong> / <strong>admin123</strong>
                    <br>
                    <em>Please change the password after first login!</em>
                </p>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
