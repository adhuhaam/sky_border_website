<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Send Mail</h2>
        <p class="text-gray-600 dark:text-gray-400">
            Send emails to your contacts using your website as the template. Simply select contacts, add a subject, and hit send!
        </p>
    </div>

    <!-- Send Mail Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form method="POST" class="space-y-6">
            <input type="hidden" name="action" value="send_mail">
            
            <!-- Email Template Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Email Template
                </label>
                <select name="email_template" id="email_template" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="website">🌐 Use Website as Template (Recommended)</option>
                    <option value="custom">✏️ Custom HTML Content</option>
                    <option value="template">📧 Predefined Template</option>
                </select>
            </div>

            <!-- Custom HTML Content (hidden by default) -->
            <div id="custom_html_section" class="hidden">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Custom HTML Content
                </label>
                <textarea name="custom_html" rows="8" 
                          placeholder="Enter your custom HTML content here..." 
                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white font-mono text-sm"></textarea>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">You can use HTML tags and inline CSS for styling.</p>
            </div>

            <!-- Template Selection (hidden by default) -->
            <div id="template_section" class="hidden">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Select Template
                </label>
                <select name="template_id" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <?php if (empty($emailTemplates)): ?>
                        <option value="">No templates available</option>
                    <?php else: ?>
                        <?php foreach ($emailTemplates as $template): ?>
                            <option value="<?php echo $template['id']; ?>"><?php echo htmlspecialchars($template['name']); ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Email Subject -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Email Subject *
                </label>
                <input type="text" name="email_subject" required 
                       placeholder="Enter email subject..." 
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
            </div>

            <!-- Contact Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Select Contacts to Send Email To *
                </label>
                
                <!-- Quick Selection Buttons -->
                <div class="flex flex-wrap gap-2 mb-4">
                    <button type="button" onclick="selectAllContacts()" 
                            class="px-4 py-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-colors">
                        Select All
                    </button>
                    <button type="button" onclick="deselectAllContacts()" 
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors">
                        Deselect All
                    </button>
                    <button type="button" onclick="selectByList()" 
                            class="px-4 py-2 bg-green-100 text-green-700 rounded-md hover:bg-green-200 transition-colors">
                        Select by List
                    </button>
                </div>

                <!-- Contact List Selection -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                        Quick List Selection:
                    </label>
                    <select id="list-selector" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Select a contact list...</option>
                        <?php foreach ($contactLists as $list): ?>
                            <option value="<?php echo $list['id']; ?>"><?php echo htmlspecialchars($list['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Individual Contact Selection -->
                <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-4 max-h-96 overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        <?php if (empty($contacts)): ?>
                            <div class="col-span-full text-center text-gray-500 dark:text-gray-400 py-8">
                                <i class="fas fa-users text-4xl mb-4"></i>
                                <p>No contacts found. Please add contacts first.</p>
                                <a href="contacts.php" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 mt-2 inline-block">
                                    Go to Contacts
                                </a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($contacts as $contact): ?>
                                <label class="flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors">
                                    <input type="checkbox" name="contact_ids[]" value="<?php echo $contact['id']; ?>" 
                                           class="mr-3 text-blue-600 focus:ring-blue-500">
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-900 dark:text-white">
                                            <?php echo htmlspecialchars($contact['name']); ?>
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            <?php echo htmlspecialchars($contact['email']); ?>
                                        </div>
                                        <?php if (!empty($contact['company'])): ?>
                                            <div class="text-xs text-gray-400 dark:text-gray-500">
                                                <?php echo htmlspecialchars($contact['company']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    <span id="selected-count">0</span> contacts selected
                </div>
            </div>

            <!-- Template Info -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 mt-1 mr-3"></i>
                    <div>
                        <h4 class="font-medium text-blue-800 dark:text-blue-200 mb-1">Email Template</h4>
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            Your website will automatically be used as the email template. All content, styling, and branding will be preserved.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Send Button -->
            <div class="flex justify-end">
                <button type="submit" 
                        class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium flex items-center">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Send Emails
                </button>
            </div>
        </form>
    </div>

    <!-- Email Statistics (shown after sending) -->
    <?php if ($emailStats): ?>
    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-green-800 dark:text-green-200 mb-4">📊 Email Sending Results</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400"><?php echo $emailStats['total']; ?></div>
                <div class="text-sm text-green-700 dark:text-green-300">Total Contacts</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400"><?php echo $emailStats['success']; ?></div>
                <div class="text-sm text-green-700 dark:text-green-300">Successfully Sent</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-red-600 dark:text-red-400"><?php echo $emailStats['failed']; ?></div>
                <div class="text-sm text-red-700 dark:text-red-300">Failed</div>
            </div>
        </div>

        <?php if (!empty($emailStats['success_emails'])): ?>
        <div class="mb-4">
            <h4 class="font-medium text-green-800 dark:text-green-200 mb-2">✅ Successfully Sent To:</h4>
            <div class="bg-white dark:bg-gray-800 rounded p-3 max-h-32 overflow-y-auto">
                <?php foreach ($emailStats['success_emails'] as $email): ?>
                    <div class="text-sm text-green-700 dark:text-green-300">• <?php echo htmlspecialchars($email); ?></div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($emailStats['errors'])): ?>
        <div class="mb-4">
            <h4 class="font-medium text-red-800 dark:text-red-200 mb-2">❌ Failed To Send:</h4>
            <div class="bg-white dark:bg-gray-800 rounded p-3 max-h-32 overflow-y-auto">
                <?php foreach ($emailStats['errors'] as $error): ?>
                    <div class="text-sm text-red-700 dark:text-red-300">• <?php echo htmlspecialchars($error); ?></div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="text-sm text-green-700 dark:text-green-300">
            <strong>Subject:</strong> <?php echo htmlspecialchars($emailStats['subject']); ?><br>
            <strong>Template:</strong> <?php echo ucfirst($emailStats['template']); ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Recent Activity -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Email Activity</h3>
        
        <?php if (empty($recentActivity)): ?>
            <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                <i class="fas fa-chart-line text-4xl mb-4"></i>
                <p>No email activity yet. Send your first email to see activity here.</p>
            </div>
        <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($recentActivity as $activity): ?>
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <?php if ($activity['status'] === 'sent'): ?>
                                    <i class="fas fa-check-circle text-green-500"></i>
                                <?php else: ?>
                                    <i class="fas fa-exclamation-circle text-red-500"></i>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    <?php echo htmlspecialchars($activity['subject']); ?>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    <?php echo htmlspecialchars($activity['contact_email']); ?> • 
                                    <?php echo date('M j, Y g:i A', strtotime($activity['created_at'])); ?>
                                </div>
                            </div>
                        </div>
                        <div class="text-xs">
                            <span class="px-2 py-1 rounded-full <?php echo $activity['status'] === 'sent' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                <?php echo ucfirst($activity['status']); ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="mt-4 text-center">
                <a href="#" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                    View All Activity →
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Update selected count
function updateSelectedCount() {
    const checkboxes = document.querySelectorAll('input[name="contact_ids[]"]:checked');
    document.getElementById('selected-count').textContent = checkboxes.length;
}

// Select all contacts
function selectAllContacts() {
    const checkboxes = document.querySelectorAll('input[name="contact_ids[]"]');
    checkboxes.forEach(checkbox => checkbox.checked = true);
    updateSelectedCount();
}

// Deselect all contacts
function deselectAllContacts() {
    const checkboxes = document.querySelectorAll('input[name="contact_ids[]"]');
    checkboxes.forEach(checkbox => checkbox.checked = false);
    updateSelectedCount();
}

// Select contacts by list
function selectByList() {
    const listId = document.getElementById('list-selector').value;
    if (!listId) {
        alert('Please select a contact list first.');
        return;
    }
    
    // Get contacts from the selected list
    fetch('send-mail.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-encoded-form',
        },
        body: new URLSearchParams({
            'action': 'select_list',
            'list_id': listId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Select contacts from the list
            const contactIds = data.contacts.map(c => c.id);
            const checkboxes = document.querySelectorAll('input[name="contact_ids[]"]');
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = contactIds.includes(parseInt(checkbox.value));
            });
            
            updateSelectedCount();
            
            // Show success message
            showNotification(`Selected ${contactIds.length} contacts from the list.`, 'success');
        } else {
            showNotification('Failed to load contacts from list.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error loading contacts from list.', 'error');
    });
}

// Handle template selection changes
function handleTemplateChange() {
    const templateSelect = document.getElementById('email_template');
    const customSection = document.getElementById('custom_html_section');
    const templateSection = document.getElementById('template_section');
    
    // Hide all sections first
    customSection.classList.add('hidden');
    templateSection.classList.add('hidden');
    
    // Show relevant section based on selection
    switch (templateSelect.value) {
        case 'custom':
            customSection.classList.remove('hidden');
            break;
        case 'template':
            templateSection.classList.remove('hidden');
            break;
        default:
            // website template - no additional sections needed
            break;
    }
}

// Show notification
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(n => n.remove());
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 max-w-md ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-blue-500 text-white'
    }`;
    
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Add event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Update count when checkboxes change
    const checkboxes = document.querySelectorAll('input[name="contact_ids[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });
    
    // Handle template selection changes
    const templateSelect = document.getElementById('email_template');
    if (templateSelect) {
        templateSelect.addEventListener('change', handleTemplateChange);
    }
    
    // Initial count and template setup
    updateSelectedCount();
    handleTemplateChange();
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const selectedContacts = document.querySelectorAll('input[name="contact_ids[]"]:checked');
    const subject = document.querySelector('input[name="email_subject"]').value.trim();
    const template = document.getElementById('email_template').value;
    
    if (selectedContacts.length === 0) {
        e.preventDefault();
        showNotification('Please select at least one contact to send email to.', 'error');
        return;
    }
    
    if (!subject) {
        e.preventDefault();
        showNotification('Please enter an email subject.', 'error');
        return;
    }
    
    // Validate custom HTML if selected
    if (template === 'custom') {
        const customHtml = document.querySelector('textarea[name="custom_html"]').value.trim();
        if (!customHtml) {
            e.preventDefault();
            showNotification('Please enter custom HTML content.', 'error');
            return;
        }
    }
    
    // Validate template selection if template mode
    if (template === 'template') {
        const templateId = document.querySelector('select[name="template_id"]').value;
        if (!templateId) {
            e.preventDefault();
            showNotification('Please select a template.', 'error');
            return;
        }
    }
    
    if (confirm(`Send email to ${selectedContacts.length} contact(s)?\n\nSubject: ${subject}\nTemplate: ${template}`)) {
        // Show loading state
        const submitBtn = document.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';
        submitBtn.disabled = true;
        
        // Show progress notification
        showNotification(`Sending emails to ${selectedContacts.length} contacts...`, 'info');
    } else {
        e.preventDefault();
    }
});

// Enhanced contact list selection with AJAX
function loadContactsByList(listId) {
    if (!listId) return;
    
    // Show loading state
    const contactGrid = document.querySelector('.grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-3');
    if (contactGrid) {
        contactGrid.innerHTML = '<div class="col-span-full text-center py-8"><i class="fas fa-spinner fa-spin text-2xl"></i><p class="mt-2">Loading contacts...</p></div>';
    }
    
    // Load contacts from the list
    fetch('send-mail.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-encoded-form',
        },
        body: new URLSearchParams({
            'action': 'get_list_contacts',
            'list_id': listId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.contacts) {
            updateContactGrid(data.contacts);
            showNotification(`Loaded ${data.contacts.length} contacts from list.`, 'success');
        } else {
            showNotification('Failed to load contacts from list.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error loading contacts from list.', 'error');
    });
}

// Update contact grid with new contacts
function updateContactGrid(contacts) {
    const contactGrid = document.querySelector('.grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-3');
    if (!contactGrid) return;
    
    if (contacts.length === 0) {
        contactGrid.innerHTML = '<div class="col-span-full text-center text-gray-500 dark:text-gray-400 py-8"><i class="fas fa-users text-4xl mb-4"></i><p>No contacts found in this list.</p></div>';
        return;
    }
    
    let html = '';
    contacts.forEach(contact => {
        html += `
            <label class="flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors">
                <input type="checkbox" name="contact_ids[]" value="${contact.id}" class="mr-3 text-blue-600 focus:ring-blue-500">
                <div class="flex-1">
                    <div class="font-medium text-gray-900 dark:text-white">
                        ${contact.name}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        ${contact.email}
                    </div>
                    ${contact.company ? `<div class="text-xs text-gray-400 dark:text-gray-500">${contact.company}</div>` : ''}
                </div>
            </label>
        `;
    });
    
    contactGrid.innerHTML = html;
    
    // Re-attach event listeners
    const checkboxes = contactGrid.querySelectorAll('input[name="contact_ids[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });
    
    updateSelectedCount();
}
</script>
