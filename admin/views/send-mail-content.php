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

    <!-- Recent Activity -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Email Activity</h3>
        <div class="text-center text-gray-500 dark:text-gray-400 py-8">
            <i class="fas fa-chart-line text-4xl mb-4"></i>
            <p>Email activity tracking will be displayed here.</p>
        </div>
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
    
    // For now, just show a message. In a full implementation, you'd filter contacts by list
    alert('List selection feature will be implemented to filter contacts by list.');
}

// Add event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Update count when checkboxes change
    const checkboxes = document.querySelectorAll('input[name="contact_ids[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });
    
    // Initial count
    updateSelectedCount();
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const selectedContacts = document.querySelectorAll('input[name="contact_ids[]"]:checked');
    const subject = document.querySelector('input[name="email_subject"]').value.trim();
    
    if (selectedContacts.length === 0) {
        e.preventDefault();
        alert('Please select at least one contact to send email to.');
        return;
    }
    
    if (!subject) {
        e.preventDefault();
        alert('Please enter an email subject.');
        return;
    }
    
    if (confirm(`Send email to ${selectedContacts.length} contact(s)?`)) {
        // Show loading state
        const submitBtn = document.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';
        submitBtn.disabled = true;
    } else {
        e.preventDefault();
    }
});
</script>
