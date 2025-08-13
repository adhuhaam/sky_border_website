<div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Campaign Management</h1>
        <p class="text-gray-600 dark:text-gray-400">Create and manage email campaigns, send website content as HTML emails, and track performance.</p>
    </div>

    <!-- Messages -->
    <?php if ($message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <!-- Create Campaign -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Create New Campaign</h3>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="action" value="create_campaign">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Campaign Name *</label>
                    <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Subject *</label>
                    <input type="text" name="subject" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Template Source</label>
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-md p-3">
                        <p class="text-sm text-blue-800 dark:text-blue-200 mb-2">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Front Site Template</strong>
                        </p>
                        <p class="text-xs text-blue-700 dark:text-blue-300">
                            This campaign will automatically use your main website (index.php) as the email template with all styling, content, and branding preserved.
                        </p>
                    </div>
                    <input type="hidden" name="url_to_render" value="/" />
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SMTP Configuration</label>
                    <select name="smtp_config_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <?php foreach ($smtpConfigs as $config): ?>
                            <option value="<?php echo $config['id']; ?>"><?php echo htmlspecialchars($config['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="draft">Draft</option>
                        <option value="scheduled">Scheduled</option>
                        <option value="sending">Sending</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Schedule Date (Optional)</label>
                    <input type="datetime-local" name="scheduled_at" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Recipients</label>
                <div class="space-y-2">
                    <div>
                        <label class="inline-flex items-center">
                            <input type="radio" name="recipient_type" value="contacts" checked class="text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Select Individual Contacts</span>
                        </label>
                    </div>
                    <div>
                        <label class="inline-flex items-center">
                            <input type="radio" name="recipient_type" value="list" class="text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Select Contact List</span>
                        </label>
                    </div>
                </div>
                
                <div id="contacts-selection" class="mt-3">
                    <select name="contact_ids[]" multiple class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" size="6">
                        <?php foreach ($contacts as $contact): ?>
                            <option value="<?php echo $contact['id']; ?>"><?php echo htmlspecialchars($contact['name'] . ' (' . $contact['email'] . ')'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div id="list-selection" class="mt-3 hidden">
                    <select name="list_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Select a contact list</option>
                        <?php foreach ($contactLists as $list): ?>
                            <option value="<?php echo $list['id']; ?>"><?php echo htmlspecialchars($list['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="flex space-x-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors">
                    Create Campaign
                </button>
                <button type="button" onclick="previewNewCampaign()" class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 transition-colors">
                    Preview
                </button>
            </div>
        </form>
    </div>

    <!-- Campaigns List -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Campaigns</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Recipients</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Performance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <?php if (empty($campaigns)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                No campaigns found. Create your first campaign above.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($campaigns as $campaign): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        <?php echo htmlspecialchars($campaign['name']); ?>
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        Created: <?php echo date('M j, Y', strtotime($campaign['created_at'])); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        <?php echo htmlspecialchars($campaign['subject']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        <?php echo $campaign['status'] === 'draft' ? 'bg-gray-100 text-gray-800' : 
                                              ($campaign['status'] === 'scheduled' ? 'bg-blue-100 text-blue-800' : 
                                              ($campaign['status'] === 'sending' ? 'bg-yellow-100 text-yellow-800' : 
                                              ($campaign['status'] === 'sent' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'))); ?>">
                                        <?php echo ucfirst($campaign['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        <?php echo $campaign['total_recipients'] ?: 0; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        <div>Sent: <?php echo $campaign['sent_count'] ?: 0; ?></div>
                                        <div>Opened: <?php echo $campaign['opened_count'] ?: 0; ?></div>
                                        <div>Clicked: <?php echo $campaign['clicked_count'] ?: 0; ?></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <?php if ($campaign['status'] === 'draft'): ?>
                                            <button onclick="editCampaign(<?php echo htmlspecialchars(json_encode($campaign)); ?>)" class="text-blue-600 hover:text-blue-900 dark:hover:text-blue-400">
                                                Edit
                                            </button>
                                        <?php endif; ?>
                                        
                                        <?php if ($campaign['status'] === 'draft' || $campaign['status'] === 'scheduled'): ?>
                                            <button onclick="sendCampaign(<?php echo $campaign['id']; ?>)" class="text-green-600 hover:text-green-900 dark:hover:text-green-400">
                                                Send
                                            </button>
                                        <?php endif; ?>
                                        
                                        <button onclick="previewCampaign(<?php echo $campaign['id']; ?>)" class="text-blue-600 hover:text-blue-900 dark:hover:text-blue-400">
                                            Preview
                                        </button>
                                        
                                        <button onclick="testCampaign(<?php echo $campaign['id']; ?>)" class="text-purple-600 hover:text-purple-900 dark:hover:text-purple-400">
                                            Test
                                        </button>
                                        
                                        <a href="campaign-analytics.php?id=<?php echo $campaign['id']; ?>" class="text-green-600 hover:text-green-900 dark:hover:text-green-400">
                                            Analytics
                                        </a>
                                        
                                        <button onclick="deleteCampaign(<?php echo $campaign['id']; ?>)" class="text-red-600 hover:text-red-900 dark:hover:text-red-400">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Campaign Modal -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Edit Campaign</h3>
            <form id="editForm" method="POST" class="space-y-4">
                <input type="hidden" name="action" value="update_campaign">
                <input type="hidden" name="campaign_id" id="edit_campaign_id">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Campaign Name *</label>
                        <input type="text" name="name" id="edit_name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Subject *</label>
                        <input type="text" name="subject" id="edit_subject" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Template Source</label>
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-md p-3">
                            <p class="text-sm text-blue-800 dark:text-blue-200">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Front Site Template</strong>
                            </p>
                            <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">
                                Uses your main website as the email template
                            </p>
                        </div>
                        <input type="hidden" name="url_to_render" id="edit_url_to_render" value="/" />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SMTP Configuration</label>
                        <select name="smtp_config_id" id="edit_smtp_config_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-600">
                            <?php foreach ($smtpConfigs as $config): ?>
                                <option value="<?php echo $config['id']; ?>"><?php echo htmlspecialchars($config['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <select name="status" id="edit_status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="draft">Draft</option>
                            <option value="scheduled">Scheduled</option>
                            <option value="sending">Sending</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Schedule Date (Optional)</label>
                        <input type="datetime-local" name="scheduled_at" id="edit_scheduled_at" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
                
                <div class="flex space-x-3">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors">
                        Update Campaign
                    </button>
                    <button type="button" onclick="closeEditModal()" class="bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700 transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Recipient type selection
document.querySelectorAll('input[name="recipient_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const contactsSelection = document.getElementById('contacts-selection');
        const listSelection = document.getElementById('list-selection');
        
        if (this.value === 'contacts') {
            contactsSelection.classList.remove('hidden');
            listSelection.classList.add('hidden');
        } else {
            contactsSelection.classList.add('hidden');
            listSelection.classList.remove('hidden');
        }
    });
});

// Edit campaign modal
function editCampaign(campaign) {
    document.getElementById('edit_campaign_id').value = campaign.id;
    document.getElementById('edit_name').value = campaign.name;
    document.getElementById('edit_subject').value = campaign.subject;
    document.getElementById('edit_url_to_render').value = '/'; // Always use front site
    document.getElementById('edit_smtp_config_id').value = campaign.smtp_config_id;
    document.getElementById('edit_status').value = campaign.status;
    
    if (campaign.scheduled_at) {
        const scheduledDate = new Date(campaign.scheduled_at);
        const localDateTime = new Date(scheduledDate.getTime() - scheduledDate.getTimezoneOffset() * 60000);
        document.getElementById('edit_scheduled_at').value = localDateTime.toISOString().slice(0, 16);
    }
    
    document.getElementById('editModal').classList.remove('hidden');
}

// Test campaign functionality
function testCampaign(campaignId) {
    if (confirm('Send a test email of this campaign to verify it looks correct?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="test_campaign">
            <input type="hidden" name="campaign_id" value="${campaignId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

// Send campaign
function sendCampaign(campaignId) {
    if (confirm('Are you sure you want to send this campaign? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="send_campaign">
            <input type="hidden" name="campaign_id" value="${campaignId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Delete campaign
function deleteCampaign(campaignId) {
    if (confirm('Are you sure you want to delete this campaign? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete_campaign">
            <input type="hidden" name="campaign_id" value="${campaignId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Preview new campaign (before creation)
function previewNewCampaign() {
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type="hidden" name="action" value="preview_campaign">
    `;
    document.body.appendChild(form);
    form.submit();
}

// Preview existing campaign
function previewCampaign(campaignId) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type="hidden" name="action" value="preview_campaign">
        <input type="hidden" name="campaign_id" value="${campaignId}">
    `;
    document.body.appendChild(form);
    form.submit();
}

// Close modals when clicking outside
window.addEventListener('click', function(event) {
    if (event.target.classList.contains('fixed')) {
        event.target.classList.add('hidden');
    }
});
</script>
