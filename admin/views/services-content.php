<?php
/**
 * Services Management Content View
 * Sky Border Solutions CMS
 */

// Icon options for services
$iconOptions = [
    'fas fa-briefcase' => 'Business',
    'fas fa-users' => 'Human Resources',
    'fas fa-file-alt' => 'Documentation',
    'fas fa-shield-alt' => 'Insurance',
    'fas fa-plane' => 'Travel',
    'fas fa-handshake' => 'Consulting',
    'fas fa-cogs' => 'Services',
    'fas fa-chart-line' => 'Analytics',
    'fas fa-globe' => 'Global',
    'fas fa-star' => 'Premium'
];

// Color theme options
$colorOptions = [
    'blue' => 'Blue',
    'green' => 'Green',
    'purple' => 'Purple',
    'red' => 'Red',
    'yellow' => 'Yellow',
    'pink' => 'Pink',
    'indigo' => 'Indigo',
    'gray' => 'Gray'
];
?>

<?php if ($action === 'list'): ?>
<!-- Services List -->
<div class="modern-card bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg theme-transition">
    <?php if (empty($services)): ?>
    <div class="text-center py-12">
        <i class="fas fa-cogs text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2 theme-transition">No services yet</h3>
        <p class="text-gray-600 dark:text-gray-400 theme-transition">Get started by adding your first service category.</p>
        <div class="mt-6">
            <a href="?action=add" class="btn-primary inline-flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Add Service
            </a>
        </div>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 p-6">
        <?php foreach ($services as $service): ?>
        <div class="modern-card rounded-lg p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-<?php echo htmlspecialchars($service['color_theme']); ?>-500 to-<?php echo htmlspecialchars($service['color_theme']); ?>-600">
                        <i class="<?php echo htmlspecialchars($service['icon_class']); ?> text-white"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white theme-transition"><?php echo htmlspecialchars($service['category_name']); ?></h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Order: <?php echo $service['display_order']; ?></p>
                    </div>
                </div>
            </div>
            
            <?php if ($service['category_description']): ?>
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4 theme-transition">
                <?php echo htmlspecialchars(substr($service['category_description'], 0, 120)); ?>
                <?php if (strlen($service['category_description']) > 120): ?>...<?php endif; ?>
            </p>
            <?php endif; ?>
            
            <div class="flex justify-end space-x-2">
                <a href="?action=edit&id=<?php echo $service['id']; ?>" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                    <i class="fas fa-edit mr-1"></i>
                    Edit
                </a>
                <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this service?');">
                    <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                    <button type="submit" name="delete_service" class="text-red-600 hover:text-red-900 text-sm font-medium">
                        <i class="fas fa-trash mr-1"></i>
                        Delete
                    </button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php elseif ($action === 'add' || $action === 'edit'): ?>
<!-- Add/Edit Form -->
<div class="modern-card bg-white dark:bg-gray-800 shadow sm:rounded-lg theme-transition">
    <div class="px-4 py-5 sm:p-6">
        <form method="POST" class="space-y-6">
            <?php if ($action === 'edit'): ?>
            <input type="hidden" name="service_id" value="<?php echo $editService['id']; ?>">
            <?php endif; ?>
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="category_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Service Name *</label>
                    <input type="text" name="category_name" id="category_name" required 
                           value="<?php echo $editService ? htmlspecialchars($editService['category_name']) : ''; ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                </div>
                
                <div>
                    <label for="display_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Display Order</label>
                    <input type="number" name="display_order" id="display_order" min="0"
                           value="<?php echo $editService ? $editService['display_order'] : '0'; ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                </div>
            </div>
            
            <div>
                <label for="category_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Description</label>
                <textarea name="category_description" id="category_description" rows="3"
                          class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition"><?php echo $editService ? htmlspecialchars($editService['category_description']) : ''; ?></textarea>
            </div>
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="icon_class" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Icon</label>
                    <select name="icon_class" id="icon_class" 
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                        <?php foreach ($iconOptions as $icon => $label): ?>
                        <option value="<?php echo htmlspecialchars($icon); ?>" 
                                <?php echo ($editService && $editService['icon_class'] === $icon) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($label); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label for="color_theme" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Color Theme</label>
                    <select name="color_theme" id="color_theme" 
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                        <?php foreach ($colorOptions as $color => $label): ?>
                        <option value="<?php echo htmlspecialchars($color); ?>" 
                                <?php echo ($editService && $editService['color_theme'] === $color) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($label); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3">
                <a href="services.php" class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md py-2 px-4 inline-flex justify-center text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 theme-transition">
                    Cancel
                </a>
                <button type="submit" name="<?php echo $action === 'edit' ? 'update_service' : 'add_service'; ?>" class="btn-primary inline-flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    <?php echo $action === 'edit' ? 'Update Service' : 'Add Service'; ?>
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
