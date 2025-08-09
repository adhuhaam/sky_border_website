<?php
/**
 * Industries Management Content View
 * Sky Border Solutions CMS
 */

// Icon options for industries
$iconOptions = [
    'fas fa-hard-hat' => 'Construction',
    'fas fa-user-md' => 'Healthcare',
    'fas fa-concierge-bell' => 'Hospitality',
    'fas fa-briefcase' => 'Business',
    'fas fa-truck' => 'Transport',
    'fas fa-graduation-cap' => 'Education',
    'fas fa-shopping-cart' => 'Retail',
    'fas fa-tools' => 'Maintenance',
    'fas fa-laptop-code' => 'Technology',
    'fas fa-chart-line' => 'Finance',
    'fas fa-building' => 'Real Estate',
    'fas fa-leaf' => 'Agriculture'
];

// Color theme options
$colorOptions = [
    'amber' => 'Amber',
    'emerald' => 'Emerald', 
    'blue' => 'Blue',
    'violet' => 'Violet',
    'rose' => 'Rose',
    'indigo' => 'Indigo',
    'green' => 'Green',
    'purple' => 'Purple',
    'red' => 'Red',
    'yellow' => 'Yellow',
    'pink' => 'Pink',
    'gray' => 'Gray'
];
?>

<?php if ($action === 'list'): ?>
<!-- Industries List -->
<div class="modern-card bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg theme-transition">
    <?php if (empty($industries)): ?>
    <div class="text-center py-12">
        <i class="fas fa-industry text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2 theme-transition">No industries yet</h3>
        <p class="text-gray-600 dark:text-gray-400 theme-transition">Get started by adding your first industry category.</p>
        <div class="mt-6">
            <a href="?action=add" class="btn-primary inline-flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Add Industry
            </a>
        </div>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 p-6">
        <?php foreach ($industries as $industry): ?>
        <div class="modern-card rounded-lg p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-<?php echo htmlspecialchars($industry['color_theme']); ?>-500 to-<?php echo htmlspecialchars($industry['color_theme']); ?>-600">
                        <i class="<?php echo htmlspecialchars($industry['icon_class']); ?> text-white"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white theme-transition"><?php echo htmlspecialchars($industry['industry_name']); ?></h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Order: <?php echo $industry['display_order']; ?></p>
                    </div>
                </div>
            </div>
            
            <?php if ($industry['industry_description']): ?>
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4 theme-transition">
                <?php echo htmlspecialchars(substr($industry['industry_description'], 0, 120)); ?>
                <?php if (strlen($industry['industry_description']) > 120): ?>...<?php endif; ?>
            </p>
            <?php endif; ?>
            
            <div class="flex justify-end space-x-2">
                <a href="positions.php?industry_id=<?php echo $industry['id']; ?>" class="text-brand-blue hover:text-brand-blue-dark text-sm font-medium">
                    <i class="fas fa-user-tie mr-1"></i>
                    Positions
                </a>
                <a href="?action=edit&id=<?php echo $industry['id']; ?>" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                    <i class="fas fa-edit mr-1"></i>
                    Edit
                </a>
                <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this industry?');">
                    <input type="hidden" name="industry_id" value="<?php echo $industry['id']; ?>">
                    <button type="submit" name="delete_industry" class="text-red-600 hover:text-red-900 text-sm font-medium">
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
            <input type="hidden" name="industry_id" value="<?php echo $editIndustry['id']; ?>">
            <?php endif; ?>
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="industry_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Industry Name *</label>
                    <input type="text" name="industry_name" id="industry_name" required 
                           value="<?php echo $editIndustry ? htmlspecialchars($editIndustry['industry_name']) : ''; ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                </div>
                
                <div>
                    <label for="display_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Display Order</label>
                    <input type="number" name="display_order" id="display_order" min="0"
                           value="<?php echo $editIndustry ? $editIndustry['display_order'] : '0'; ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                </div>
            </div>
            
            <div>
                <label for="industry_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Description</label>
                <textarea name="industry_description" id="industry_description" rows="3"
                          class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition"><?php echo $editIndustry ? htmlspecialchars($editIndustry['industry_description']) : ''; ?></textarea>
            </div>
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="icon_class" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Icon</label>
                    <select name="icon_class" id="icon_class" 
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                        <?php foreach ($iconOptions as $icon => $label): ?>
                        <option value="<?php echo htmlspecialchars($icon); ?>" 
                                <?php echo ($editIndustry && $editIndustry['icon_class'] === $icon) ? 'selected' : ''; ?>>
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
                                <?php echo ($editIndustry && $editIndustry['color_theme'] === $color) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($label); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3">
                <a href="industries.php" class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md py-2 px-4 inline-flex justify-center text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 theme-transition">
                    Cancel
                </a>
                <button type="submit" name="<?php echo $action === 'edit' ? 'update_industry' : 'add_industry'; ?>" class="btn-primary inline-flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    <?php echo $action === 'edit' ? 'Update Industry' : 'Add Industry'; ?>
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
