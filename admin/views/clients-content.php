<?php
/**
 * Clients Management Content View
 * Sky Border Solutions CMS
 */
?>

<?php if ($action === 'list'): ?>
<!-- Clients List -->
<div class="modern-card bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg theme-transition">
    <?php if (empty($clients)): ?>
    <div class="text-center py-12">
        <i class="fas fa-users text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2 theme-transition">No clients yet</h3>
        <p class="text-gray-600 dark:text-gray-400 theme-transition">Get started by adding your first client.</p>
        <div class="mt-6">
            <a href="?action=add" class="btn-primary inline-flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Add Client
            </a>
        </div>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 p-6">
        <?php foreach ($clients as $client): ?>
        <div class="modern-card rounded-lg p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <?php if (!empty($client['logo_url'])): ?>
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg overflow-hidden">
                        <img src="<?php echo htmlspecialchars($client['logo_url']); ?>" alt="<?php echo htmlspecialchars($client['client_name']); ?>" class="h-full w-full object-contain">
                    </div>
                    <?php else: ?>
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-gray-400 to-gray-500">
                        <i class="fas fa-building text-white"></i>
                    </div>
                    <?php endif; ?>
                    <div class="ml-3">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white theme-transition"><?php echo htmlspecialchars($client['client_name']); ?></h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($client['category_name'] ?? 'Other'); ?></p>
                        <p class="text-xs text-gray-400">Order: <?php echo $client['display_order']; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-2">
                <a href="?action=edit&id=<?php echo $client['id']; ?>" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                    <i class="fas fa-edit mr-1"></i>
                    Edit
                </a>
                <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this client?');">
                    <input type="hidden" name="client_id" value="<?php echo $client['id']; ?>">
                    <button type="submit" name="delete_client" class="text-red-600 hover:text-red-900 text-sm font-medium">
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
            <input type="hidden" name="client_id" value="<?php echo $editClient['id']; ?>">
            <?php endif; ?>
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="client_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Client Name *</label>
                    <input type="text" name="client_name" id="client_name" required 
                           value="<?php echo $editClient ? htmlspecialchars($editClient['client_name']) : ''; ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                </div>
                
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Category</label>
                    <select name="category_id" id="category_id" 
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                        <option value="">Select Category</option>
                        <?php foreach ($clientCategories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" 
                                <?php echo ($editClient && $editClient['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['category_name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div>
                <label for="logo_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Logo URL</label>
                <input type="url" name="logo_url" id="logo_url" 
                       value="<?php echo $editClient ? htmlspecialchars($editClient['logo_url']) : ''; ?>"
                       placeholder="https://example.com/logo.png"
                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Enter the URL of the client's logo image</p>
            </div>
            
            <div>
                <label for="display_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Display Order</label>
                <input type="number" name="display_order" id="display_order" min="0"
                       value="<?php echo $editClient ? $editClient['display_order'] : '0'; ?>"
                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Lower numbers appear first</p>
            </div>
            
            <div class="flex justify-end space-x-3">
                <a href="clients.php" class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md py-2 px-4 inline-flex justify-center text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 theme-transition">
                    Cancel
                </a>
                <button type="submit" name="<?php echo $action === 'edit' ? 'update_client' : 'add_client'; ?>" class="btn-primary inline-flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    <?php echo $action === 'edit' ? 'Update Client' : 'Add Client'; ?>
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
