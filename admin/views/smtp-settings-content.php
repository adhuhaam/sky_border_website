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
        
        <!-- Setup Instructions -->
        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg">
            <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">📋 Setup Instructions for Sky Border Solutions</h4>
            <div class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                <p><strong>Step 1:</strong> Click "Use Sky Border SMTP" button below to auto-fill the form</p>
                <p><strong>Step 2:</strong> Enter your email password: <code class="bg-gray-200 dark:bg-gray-600 px-1 rounded">Ompl@655482*</code></p>
                <p><strong>Step 3:</strong> Click "Add SMTP Configuration" to save</p>
                <p><strong>Step 4:</strong> Test the connection using "Test Connection" button</p>
                <div class="mt-2 p-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded">
                    <p class="text-blue-700 dark:text-blue-300"><strong>📧 Email Configuration:</strong></p>
                    <p class="text-blue-600 dark:text-blue-400">• Host: skybordersolutions.com</p>
                    <p class="text-blue-600 dark:text-blue-400">• Port: 465 (SSL) or 587 (TLS)</p>
                    <p class="text-blue-600 dark:text-blue-400">• Username: hello@skybordersolutions.com</p>
                    <p class="text-blue-600 dark:text-blue-400">• Authentication: Required for all protocols</p>
                </div>
            </div>
        </div>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="action" value="add_smtp">
            
            <!-- Pre-filled Sky Border Solutions SMTP Configuration -->
            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                <h4 class="text-sm font-semibold text-blue-800 dark:text-blue-200 mb-2">💡 Quick Setup: Sky Border Solutions SMTP</h4>
                <p class="text-xs text-blue-700 dark:text-blue-300 mb-3">Click "Use Sky Border SMTP" to pre-fill the form with your company's email settings</p>
                <button type="button" onclick="fillSkyBorderSMTP()" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700 transition-colors">
                    Use Sky Border SMTP
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Configuration Name *</label>
                    <input type="text" name="name" id="smtp_name" required placeholder="e.g., Sky Border SMTP, Gmail SMTP" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SMTP Host *</label>
                    <input type="text" name="host" id="smtp_host" required placeholder="e.g., skybordersolutions.com" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Port *</label>
                    <input type="number" name="port" id="smtp_port" required value="465" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Encryption</label>
                    <select name="encryption" id="smtp_encryption" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="ssl" selected>SSL</option>
                        <option value="tls">TLS</option>
                        <option value="none">None</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Active</label>
                    <div class="mt-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="is_active" id="smtp_is_active" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" checked>
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Set as active</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Username/Email *</label>
                    <input type="email" name="username" id="smtp_username" required placeholder="hello@skybordersolutions.com" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password/App Password *</label>
                    <input type="password" name="password" id="smtp_password" required placeholder="Your email password" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">For Sky Border Solutions: Use your email account password</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From Email *</label>
                    <input type="email" name="from_email" id="smtp_from_email" required placeholder="hello@skybordersolutions.com" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From Name *</label>
                    <input type="text" name="from_name" id="smtp_from_name" required placeholder="Sky Border Solutions" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>
            </div>
            
            <div class="flex space-x-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors">
                    Add SMTP Configuration
                </button>
                <button type="button" onclick="testSMTPConnection()" class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 transition-colors">
                    Test Connection
                </button>
                <button type="button" onclick="testEmailSend()" class="bg-purple-600 text-white px-6 py-2 rounded-md hover:bg-purple-700 transition-colors">
                    Test Email Send
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
            <!-- Sky Border Solutions SMTP -->
            <div class="border-2 border-blue-300 dark:border-blue-600 rounded-lg p-4 bg-blue-50 dark:bg-blue-900/20">
                <h4 class="font-semibold text-blue-800 dark:text-blue-200 mb-2">🏢 Sky Border Solutions</h4>
                <div class="text-sm text-blue-700 dark:text-blue-300 space-y-1">
                    <div>Host: skybordersolutions.com</div>
                    <div>Port: 465</div>
                    <div>Encryption: SSL</div>
                    <div class="text-xs text-blue-600 dark:text-blue-400 font-medium">✅ Recommended for your company</div>
                </div>
            </div>
            
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

// Test email send functionality
function testEmailSend() {
    const host = document.querySelector('input[name="host"]').value;
    const port = document.querySelector('input[name="port"]').value;
    const username = document.querySelector('input[name="username"]').value;
    const password = document.querySelector('input[name="password"]').value;
    const encryption = document.querySelector('select[name="encryption"]').value;
    const fromEmail = document.querySelector('input[name="from_email"]').value;
    const fromName = document.querySelector('input[name="from_name"]').value;
    
    if (!host || !port || !username || !password || !fromEmail || !fromName) {
        alert('Please fill in all required fields before testing email send.');
        return;
    }
    
    // Create a test form
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type="hidden" name="action" value="test_email_send">
        <input type="hidden" name="host" value="${host}">
        <input type="hidden" name="port" value="${port}">
        <input type="hidden" name="username" value="${username}">
        <input type="hidden" name="password" value="${password}">
        <input type="hidden" name="encryption" value="${encryption}">
        <input type="hidden" name="from_email" value="${fromEmail}">
        <input type="hidden" name="from_name" value="${fromName}">
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

// Pre-fill Sky Border Solutions SMTP configuration
function fillSkyBorderSMTP() {
    document.getElementById('smtp_name').value = 'Sky Border Solutions SMTP';
    document.getElementById('smtp_host').value = 'skybordersolutions.com';
    document.getElementById('smtp_port').value = '465';
    document.getElementById('smtp_encryption').value = 'ssl';
    document.getElementById('smtp_username').value = 'hello@skybordersolutions.com';
    document.getElementById('smtp_from_email').value = 'hello@skybordersolutions.com';
    document.getElementById('smtp_from_name').value = 'Sky Border Solutions';
    document.getElementById('smtp_is_active').checked = true;
    
    // Show success message
    const successDiv = document.createElement('div');
    successDiv.className = 'mt-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded';
    successDiv.innerHTML = '✅ Sky Border Solutions SMTP settings pre-filled!<br><strong>Password:</strong> <code>Ompl@655482*</code><br>Enter this password and click "Add SMTP Configuration".';
    
    const form = document.querySelector('form');
    form.insertBefore(successDiv, form.firstChild);
    
    // Remove the message after 8 seconds
    setTimeout(() => {
        successDiv.remove();
    }, 8000);
}

// Close modals when clicking outside
window.addEventListener('click', function(event) {
    if (event.target.classList.contains('fixed')) {
        event.target.classList.add('hidden');
    }
});
</script>
