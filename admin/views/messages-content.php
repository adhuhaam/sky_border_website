<?php
/**
 * Messages Management Content View
 * Sky Border Solutions CMS
 */

// Status options for messages
$statusOptions = [
    'new' => 'New',
    'read' => 'Read',
    'replied' => 'Replied',
    'archived' => 'Archived'
];

// Status colors
$statusColors = [
    'new' => 'bg-blue-100 text-blue-800',
    'read' => 'bg-yellow-100 text-yellow-800',
    'replied' => 'bg-green-100 text-green-800',
    'archived' => 'bg-gray-100 text-gray-800'
];
?>

<?php if ($action === 'list'): ?>
<!-- Status Filter -->
<div class="mb-6">
    <div class="flex flex-wrap gap-2">
        <?php 
        $currentFilter = $_GET['filter'] ?? 'all';
        ?>
        <a href="messages.php" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo $currentFilter === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'; ?> theme-transition">
            All Messages (<?php echo $messageStats['total'] ?? 0; ?>)
        </a>
        <?php foreach ($statusOptions as $status => $label): ?>
        <a href="?filter=<?php echo $status; ?>" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo $currentFilter === $status ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'; ?> theme-transition">
            <?php echo $label; ?>
            <span class="ml-1">(<?php echo $messageStats[$status] ?? 0; ?>)</span>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Messages List -->
<div class="modern-card bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg theme-transition">
    <?php if (empty($messages)): ?>
    <div class="text-center py-12">
        <i class="fas fa-envelope text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2 theme-transition">No messages yet</h3>
        <p class="text-gray-600 dark:text-gray-400 theme-transition">
            <?php if ($statusFilter): ?>
                No messages found with status "<?php echo htmlspecialchars($statusFilter); ?>".
            <?php else: ?>
                Contact messages will appear here when visitors submit the contact form.
            <?php endif; ?>
        </p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Message</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($messages as $message): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 theme-transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-8 w-8">
                                <div class="h-8 w-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                    <i class="fas fa-user text-gray-600 dark:text-gray-300 text-sm"></i>
                                </div>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    <?php echo htmlspecialchars($message['name'] ?? 'Unknown'); ?>
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    <?php echo htmlspecialchars($message['email'] ?? ''); ?>
                                </div>
                                <?php if (!empty($message['phone'])): ?>
                                <div class="text-xs text-gray-400">
                                    <?php echo htmlspecialchars($message['phone']); ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900 dark:text-white">
                            <?php 
                            $messageText = $message['message'] ?? '';
                            echo htmlspecialchars(strlen($messageText) > 100 ? substr($messageText, 0, 100) . '...' : $messageText); 
                            ?>
                        </div>
                        <?php if (!empty($message['subject'])): ?>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Subject: <?php echo htmlspecialchars($message['subject']); ?>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?php echo $statusColors[$message['status'] ?? 'new']; ?>">
                            <?php echo ucfirst($message['status'] ?? 'new'); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        <?php echo date('M j, Y g:i A', strtotime($message['created_at'] ?? 'now')); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="?action=view&id=<?php echo $message['id']; ?>" class="text-brand-blue hover:text-brand-blue-dark mr-3">
                            <i class="fas fa-eye mr-1"></i>
                            View
                        </a>
                        <?php if ($message['status'] !== 'archived'): ?>
                        <form method="POST" class="inline">
                            <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                            <button type="submit" name="archive_message" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                                <i class="fas fa-archive mr-1"></i>
                                Archive
                            </button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php elseif ($action === 'view'): ?>
<!-- View Message -->
<div class="modern-card bg-white dark:bg-gray-800 shadow sm:rounded-lg theme-transition">
    <div class="px-4 py-5 sm:p-6">
        <div class="space-y-6">
            <!-- Message Header -->
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            From: <?php echo htmlspecialchars($message['name'] ?? 'Unknown'); ?>
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            <?php echo htmlspecialchars($message['email'] ?? ''); ?>
                            <?php if (!empty($message['phone'])): ?>
                            | <?php echo htmlspecialchars($message['phone']); ?>
                            <?php endif; ?>
                        </p>
                        <p class="text-xs text-gray-400">
                            <?php echo date('F j, Y \a\t g:i A', strtotime($message['created_at'] ?? 'now')); ?>
                        </p>
                    </div>
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full <?php echo $statusColors[$message['status'] ?? 'new']; ?>">
                        <?php echo ucfirst($message['status'] ?? 'new'); ?>
                    </span>
                </div>
            </div>
            
            <!-- Subject -->
            <?php if (!empty($message['subject'])): ?>
            <div>
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Subject:</h4>
                <p class="mt-1 text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($message['subject']); ?></p>
            </div>
            <?php endif; ?>
            
            <!-- Message Content -->
            <div>
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Message:</h4>
                <div class="mt-1 text-sm text-gray-900 dark:text-white whitespace-pre-wrap"><?php echo htmlspecialchars($message['message'] ?? ''); ?></div>
            </div>
            
            <!-- Admin Notes -->
            <?php if (!empty($message['admin_notes'])): ?>
            <div>
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Admin Notes:</h4>
                <div class="mt-1 text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 p-3 rounded-md"><?php echo htmlspecialchars($message['admin_notes']); ?></div>
            </div>
            <?php endif; ?>
            
            <!-- Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex space-x-3">
                    <a href="mailto:<?php echo htmlspecialchars($message['email'] ?? ''); ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-reply mr-2"></i>
                        Reply via Email
                    </a>
                    
                    <!-- Status Update Form -->
                    <form method="POST" class="space-y-3">
                        <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                        <div class="flex items-center space-x-2">
                            <select name="status" class="rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                                <?php foreach ($statusOptions as $status => $label): ?>
                                <option value="<?php echo $status; ?>" <?php echo ($message['status'] === $status) ? 'selected' : ''; ?>>
                                    <?php echo $label; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" name="update_status" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Update Status
                            </button>
                        </div>
                        <div>
                            <label for="admin_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Admin Notes:</label>
                            <textarea name="admin_notes" id="admin_notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm" placeholder="Add notes about this message..."><?php echo htmlspecialchars($message['admin_notes'] ?? ''); ?></textarea>
                        </div>
                    </form>
                </div>
                
                <div class="flex items-center space-x-3">
                    <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this message? This action cannot be undone.');">
                        <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                        <button type="submit" name="delete_message" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <i class="fas fa-trash mr-1"></i>
                            Delete
                        </button>
                    </form>
                    <a href="messages.php" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back to Messages
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
