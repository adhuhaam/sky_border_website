<?php
/**
 * Job Positions Management Content View
 * Sky Border Solutions CMS
 */

// Group positions by industry for display
$groupedPositions = [];
foreach ($positions as $position) {
    $industryName = $position['industry_name'] ?? 'Unknown';
    if (!isset($groupedPositions[$industryName])) {
        $groupedPositions[$industryName] = [];
    }
    $groupedPositions[$industryName][] = $position;
}
?>

<?php if ($action === 'list'): ?>

<!-- Industry Filter -->
<?php if (!empty($industries)): ?>
<div class="mb-6">
    <div class="flex flex-wrap gap-2">
        <a href="positions.php" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo !$industryFilter ? 'bg-brand-blue text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'; ?> theme-transition">
            All Industries
        </a>
        <?php foreach ($industries as $industry): ?>
        <a href="?industry_id=<?php echo $industry['id']; ?>" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo $industryFilter == $industry['id'] ? 'bg-brand-blue text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'; ?> theme-transition">
            <i class="<?php echo htmlspecialchars($industry['icon_class']); ?> mr-1"></i>
            <?php echo htmlspecialchars($industry['industry_name']); ?>
        </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Positions List -->
<div class="modern-card bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg theme-transition">
    <?php if (empty($positions)): ?>
    <div class="text-center py-12">
        <i class="fas fa-user-tie text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2 theme-transition">No positions yet</h3>
        <p class="text-gray-600 dark:text-gray-400 theme-transition">
            <?php if ($industryFilter): ?>
                No positions found for the selected industry.
            <?php else: ?>
                Get started by adding your first job position.
            <?php endif; ?>
        </p>
        <div class="mt-6">
            <a href="?action=add" class="btn-primary inline-flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Add Position
            </a>
        </div>
    </div>
    <?php else: ?>
    <div class="divide-y divide-gray-200 dark:divide-gray-700">
        <?php foreach ($groupedPositions as $industryName => $industryPositions): ?>
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 theme-transition">
                <?php echo htmlspecialchars($industryName); ?>
                <span class="text-sm text-gray-500 dark:text-gray-400 font-normal ml-2">(<?php echo count($industryPositions); ?> positions)</span>
            </h3>
            
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($industryPositions as $position): ?>
                <div class="modern-card rounded-lg p-4 hover:shadow-lg transition-shadow">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white theme-transition">
                                    <?php echo htmlspecialchars($position['position_name']); ?>
                                </h4>
                                <?php if ($position['is_featured']): ?>
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-star mr-1"></i>
                                    Featured
                                </span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($position['position_description']): ?>
                            <p class="mt-1 text-xs text-gray-600 dark:text-gray-300 theme-transition">
                                <?php echo htmlspecialchars(substr($position['position_description'], 0, 80)); ?>
                                <?php if (strlen($position['position_description']) > 80): ?>...<?php endif; ?>
                            </p>
                            <?php endif; ?>
                            
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Order: <?php echo $position['display_order']; ?></p>
                        </div>
                    </div>
                    
                    <div class="mt-3 flex justify-end space-x-2">
                        <a href="?action=edit&id=<?php echo $position['id']; ?>" class="text-indigo-600 hover:text-indigo-900 text-xs font-medium">
                            <i class="fas fa-edit mr-1"></i>
                            Edit
                        </a>
                        <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this position?');">
                            <input type="hidden" name="position_id" value="<?php echo $position['id']; ?>">
                            <button type="submit" name="delete_position" class="text-red-600 hover:text-red-900 text-xs font-medium">
                                <i class="fas fa-trash mr-1"></i>
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
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
            <input type="hidden" name="position_id" value="<?php echo $editPosition['id']; ?>">
            <?php endif; ?>
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="position_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Position Name *</label>
                    <input type="text" name="position_name" id="position_name" required 
                           value="<?php echo $editPosition ? htmlspecialchars($editPosition['position_name']) : ''; ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                </div>
                
                <div>
                    <label for="industry_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Industry *</label>
                    <select name="industry_id" id="industry_id" required 
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                        <option value="">Select Industry</option>
                        <?php foreach ($industries as $industry): ?>
                        <option value="<?php echo $industry['id']; ?>" 
                                <?php echo ($editPosition && $editPosition['industry_id'] == $industry['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($industry['industry_name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div>
                <label for="position_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Description</label>
                <textarea name="position_description" id="position_description" rows="3"
                          class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition"><?php echo $editPosition ? htmlspecialchars($editPosition['position_description']) : ''; ?></textarea>
            </div>
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="display_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Display Order</label>
                    <input type="number" name="display_order" id="display_order" min="0"
                           value="<?php echo $editPosition ? $editPosition['display_order'] : '0'; ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                </div>
                
                <div class="flex items-center h-full">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1"
                               <?php echo ($editPosition && $editPosition['is_featured']) ? 'checked' : ''; ?>
                               class="h-4 w-4 text-brand-blue focus:ring-brand-blue border-gray-300 rounded">
                        <label for="is_featured" class="ml-2 block text-sm text-gray-700 dark:text-gray-300 theme-transition">
                            Featured Position
                            <span class="text-xs text-gray-500 dark:text-gray-400 block">Show on website featured positions</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3">
                <a href="positions.php" class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md py-2 px-4 inline-flex justify-center text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 theme-transition">
                    Cancel
                </a>
                <button type="submit" name="<?php echo $action === 'edit' ? 'update_position' : 'add_position'; ?>" class="btn-primary inline-flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    <?php echo $action === 'edit' ? 'Update Position' : 'Add Position'; ?>
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
