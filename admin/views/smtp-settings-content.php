<div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">SMTP Settings</h1>
        <p class="text-gray-600 dark:text-gray-400">Configure SMTP providers for sending email campaigns. You can add multiple SMTP configurations and switch between them.</p>
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

    <!-- Add SMTP Configuration -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Add New SMTP Configuration</h3>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="action" value="add_smtp">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Configuration Name *</label>
                    <input type="text" name="name" required placeholder="e.g., Gmail SMTP, Office 365" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SMTP Host *</label>
                    <input type="text" name="host" required placeholder="e.g., smtp.gmail.com" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Port *</label>
                    <input type="number" name="port" required value="587" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Encryption</label>
                    <select name="encryption" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="tls">TLS</option>
                        <option value="ssl">SSL</option>
                        <option value="none">None</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Active</label>
                    <div class="mt-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="is_active" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Set as active</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Username/Email *</label>
                    <input type="email" name="username" required placeholder="your-email@gmail.com" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password/App Password *</label>
                    <input type="password" name="password" required placeholder="Your password or app password" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From Email *</label>
                    <input type="email" name="from_email" required placeholder="noreply@yourdomain.com" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From Name *</label>
                    <input type="text" name="from_name" required placeholder="Your Company Name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>
            </div>
            
            <div class="flex space-x-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors">
                    Add SMTP Configuration
                </button>
                <button type="button" onclick="testSMTPConnection()" class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 transition-colors">
                    Test Connection
                </button>
            </div>
        </form>
    </div>

    <!-- SMTP Configurations List -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">SMTP Configurations</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Host:Port</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Username</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">From</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <?php if (empty($smtpConfigs)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                No SMTP configurations found. Add your first configuration above.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($smtpConfigs as $config): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        <?php echo htmlspecialchars($config['name']); ?>
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        <?php echo ucfirst($config['encryption']); ?> encryption
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        <?php echo htmlspecialchars($config['host']); ?>:<?php echo $config['port']; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        <?php echo htmlspecialchars($config['username']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        <?php echo htmlspecialchars($config['from_name']); ?>
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        <?php echo htmlspecialchars($config['from_email']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        <?php echo $config['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                        <?php echo $config['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick="editSMTP(<?php echo htmlspecialchars(json_encode($config)); ?>)" class="text-blue-600 hover:text-blue-900 dark:hover:text-blue-400">
                                            Edit
                                        </button>
                                        <button onclick="testSMTP(<?php echo htmlspecialchars(json_encode($config)); ?>)" class="text-green-600 hover:text-green-900 dark:hover:text-green-400">
                                            Test
                                        </button>
                                        <button onclick="deleteSMTP(<?php echo $config['id']; ?>)" class="text-red-600 hover:text-red-900 dark:hover:text-red-400">
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

    <!-- Common SMTP Providers -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mt-8">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Common SMTP Providers</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Gmail</h4>
                <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                    <div>Host: smtp.gmail.com</div>
                    <div>Port: 587</div>
                    <div>Encryption: TLS</div>
                    <div class="text-xs text-gray-500">Requires App Password</div>
                </div>
            </div>
            
            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Outlook/Hotmail</h4>
                <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                    <div>Host: smtp-mail.outlook.com</div>
                    <div>Port: 587</div>
                    <div>Encryption: TLS</div>
                </div>
            </div>
            
            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Yahoo</h4>
                <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                    <div>Host: smtp.mail.yahoo.com</div>
                    <div>Port: 587</div>
                    <div>Encryption: TLS</div>
                    <div class="text-xs text-gray-500">Requires App Password</div>
                </div>
            </div>
            
            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Office 365</h4>
                <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                    <div>Host: smtp.office365.com</div>
                    <div>Port: 587</div>
                    <div>Encryption: TLS</div>
                </div>
            </div>
            
            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 dark:text-white mb-2">SendGrid</h4>
                <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                    <div>Host: smtp.sendgrid.net</div>
                    <div>Port: 587</div>
                    <div>Encryption: TLS</div>
                    <div class="text-xs text-gray-500">Uses API Key</div>
                </div>
            </div>
            
            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Mailgun</h4>
                <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                    <div>Host: smtp.mailgun.org</div>
                    <div>Port: 587</div>
                    <div>Encryption: TLS</div>
                    <div class="text-xs text-gray-500">Uses API Key</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit SMTP Modal -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Edit SMTP Configuration</h3>
            <form id="editForm" method="POST" class="space-y-4">
                <input type="hidden" name="action" value="update_smtp">
                <input type="hidden" name="smtp_id" id="edit_smtp_id">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Configuration Name *</label>
                        <input type="text" name="name" id="edit_name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SMTP Host *</label>
                        <input type="text" name="host" id="edit_host" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Port *</label>
                        <input type="number" name="port" id="edit_port" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Encryption</label>
                        <select name="encryption" id="edit_encryption" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="tls">TLS</option>
                            <option value="ssl">SSL</option>
                            <option value="none">None</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Active</label>
                        <div class="mt-2">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_active" id="edit_is_active" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Set as active</span>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Username/Email *</label>
                        <input type="email" name="username" id="edit_username" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password/App Password *</label>
                        <input type="password" name="password" id="edit_password" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From Email *</label>
                        <input type="email" name="from_email" id="edit_from_email" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From Name *</label>
                        <input type="text" name="from_name" id="edit_from_name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
                
                <div class="flex space-x-3">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors">
                        Update Configuration
                    </button>
                    <button type="button" onclick="closeEditModal()" class="bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700 transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Test SMTP Modal -->
<div id="testModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Test SMTP Connection</h3>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="test_smtp">
                <input type="hidden" name="host" id="test_host">
                <input type="hidden" name="port" id="test_port">
                <input type="hidden" name="username" id="test_username">
                <input type="hidden" name="password" id="test_password">
                <input type="hidden" name="encryption" id="test_encryption">
                
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    <p>This will test the SMTP connection using the current configuration.</p>
                </div>
                
                <div class="flex space-x-3">
                    <button type="submit" class="flex-1 bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                        Test Connection
                    </button>
                    <button type="button" onclick="closeTestModal()" class="flex-1 bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Edit SMTP modal
function editSMTP(config) {
    document.getElementById('edit_smtp_id').value = config.id;
    document.getElementById('edit_name').value = config.name;
    document.getElementById('edit_host').value = config.host;
    document.getElementById('edit_port').value = config.port;
    document.getElementById('edit_encryption').value = config.encryption;
    document.getElementById('edit_username').value = config.username;
    document.getElementById('edit_password').value = config.password;
    document.getElementById('edit_from_email').value = config.from_email;
    document.getElementById('edit_from_name').value = config.from_name;
    document.getElementById('edit_is_active').checked = config.is_active == 1;
    
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

// Test SMTP modal
function testSMTP(config) {
    document.getElementById('test_host').value = config.host;
    document.getElementById('test_port').value = config.port;
    document.getElementById('test_username').value = config.username;
    document.getElementById('test_password').value = config.password;
    document.getElementById('test_encryption').value = config.encryption;
    
    document.getElementById('testModal').classList.remove('hidden');
}

function closeTestModal() {
    document.getElementById('testModal').classList.add('hidden');
}

// Test SMTP connection from form
function testSMTPConnection() {
    const host = document.querySelector('input[name="host"]').value;
    const port = document.querySelector('input[name="port"]').value;
    const username = document.querySelector('input[name="username"]').value;
    const password = document.querySelector('input[name="password"]').value;
    const encryption = document.querySelector('select[name="encryption"]').value;
    
    if (!host || !port || !username || !password) {
        alert('Please fill in all required fields before testing.');
        return;
    }
    
    // Create a test form
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type="hidden" name="action" value="test_smtp">
        <input type="hidden" name="host" value="${host}">
        <input type="hidden" name="port" value="${port}">
        <input type="hidden" name="username" value="${username}">
        <input type="hidden" name="password" value="${password}">
        <input type="hidden" name="encryption" value="${encryption}">
    `;
    document.body.appendChild(form);
    form.submit();
}

// Delete SMTP configuration
function deleteSMTP(configId) {
    if (confirm('Are you sure you want to delete this SMTP configuration? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete_smtp">
            <input type="hidden" name="smtp_id" value="${configId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Close modals when clicking outside
window.addEventListener('click', function(event) {
    if (event.target.classList.contains('fixed')) {
        event.target.classList.add('hidden');
    }
});
</script>
