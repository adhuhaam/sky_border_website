<?php if ($action === 'list'): ?>
    <!-- Team Members List -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Team Members</h2>
                <a href="?action=add" class="bg-brand-blue hover:bg-brand-blue-dark text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>Add Team Member
                </a>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Photo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Designation</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <?php if (empty($teamMembers)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No team members found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($teamMembers as $member): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if (!empty($member['photo_url'])): ?>
                                        <img src="<?php echo htmlspecialchars($member['photo_url']); ?>" 
                                             alt="<?php echo htmlspecialchars($member['name']); ?>" 
                                             class="h-12 w-12 rounded-full object-cover">
                                    <?php else: ?>
                                        <div class="h-12 w-12 rounded-full bg-gradient-to-r from-brand-blue to-brand-teal flex items-center justify-center">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($member['name']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($member['designation']); ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 dark:text-white max-w-xs truncate" title="<?php echo htmlspecialchars($member['description']); ?>">
                                        <?php echo htmlspecialchars($member['description']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white"><?php echo $member['display_order']; ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $member['is_active'] ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'; ?>">
                                        <?php echo $member['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="?action=edit&id=<?php echo $member['id']; ?>" class="text-brand-blue hover:text-brand-blue-dark mr-3">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this team member?');">
                                        <input type="hidden" name="id" value="<?php echo $member['id']; ?>">
                                        <button type="submit" name="delete_team_member" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php elseif ($action === 'add' || $action === 'edit'): ?>
    <!-- Add/Edit Team Member Form -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                <?php echo $action === 'add' ? 'Add New Team Member' : 'Edit Team Member'; ?>
            </h2>
        </div>
        
        <div class="px-6 py-4">
            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <?php if ($action === 'edit'): ?>
                    <input type="hidden" name="id" value="<?php echo $editTeamMember['id']; ?>">
                    <input type="hidden" name="current_photo" value="<?php echo htmlspecialchars($editTeamMember['photo_url'] ?? ''); ?>">
                <?php endif; ?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name *</label>
                        <input type="text" name="name" id="name" required
                               value="<?php echo htmlspecialchars($editTeamMember['name'] ?? ''); ?>"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-brand-blue focus:border-brand-blue dark:bg-gray-700 dark:text-white">
                    </div>
                    
                    <div>
                        <label for="designation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Designation *</label>
                        <input type="text" name="designation" id="designation" required
                               value="<?php echo htmlspecialchars($editTeamMember['designation'] ?? ''); ?>"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-brand-blue focus:border-brand-blue dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                    <textarea name="description" id="description" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-brand-blue focus:border-brand-blue dark:bg-gray-700 dark:text-white"><?php echo htmlspecialchars($editTeamMember['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="photo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Photo</label>
                        <input type="file" name="photo" id="photo" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-brand-blue focus:border-brand-blue dark:bg-gray-700 dark:text-white">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Upload a square image (recommended: 400x400px)</p>
                        
                        <?php if ($action === 'edit' && !empty($editTeamMember['photo_url'])): ?>
                            <div class="mt-2">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Current photo:</p>
                                <img src="<?php echo htmlspecialchars($editTeamMember['photo_url']); ?>" 
                                     alt="Current photo" class="h-20 w-20 rounded-full object-cover">
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="display_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Display Order</label>
                            <input type="number" name="display_order" id="display_order" min="0"
                                   value="<?php echo $editTeamMember['display_order'] ?? 0; ?>"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-brand-blue focus:border-brand-blue dark:bg-gray-700 dark:text-white">
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1"
                                   <?php echo (!isset($editTeamMember['is_active']) || $editTeamMember['is_active']) ? 'checked' : ''; ?>
                                   class="h-4 w-4 text-brand-blue focus:ring-brand-blue border-gray-300 dark:border-gray-600 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                Active (visible on website)
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="?action=list" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        Cancel
                    </a>
                    <button type="submit" name="<?php echo $action === 'add' ? 'add_team_member' : 'update_team_member'; ?>"
                            class="bg-brand-blue hover:bg-brand-blue-dark text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        <?php echo $action === 'add' ? 'Add Team Member' : 'Update Team Member'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
