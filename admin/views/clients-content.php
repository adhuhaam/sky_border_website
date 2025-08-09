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
                        <?php 
                        // Handle both uploaded files and external URLs
                        $logoSrc = $client['logo_url'];
                        if (!filter_var($logoSrc, FILTER_VALIDATE_URL)) {
                            // It's a local file path, no need to prepend since we're already in admin directory
                            $logoSrc = ltrim($logoSrc, '/');
                        }
                        ?>
                        <img src="<?php echo htmlspecialchars($logoSrc); ?>" 
                             alt="<?php echo htmlspecialchars($client['client_name']); ?>" 
                             class="h-full w-full object-contain"
                             onerror="this.style.display='none'; this.parentNode.innerHTML='<i class=&quot;fas fa-building text-gray-400&quot;></i>';">
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
        <form method="POST" enctype="multipart/form-data" class="space-y-6">
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
                <label for="logo_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Logo Upload</label>
                
                <!-- Current logo display if exists -->
                <?php if ($editClient && !empty($editClient['logo_url'])): ?>
                <div class="mt-2 mb-3">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <?php 
                            // Handle both uploaded files and external URLs for current logo
                            $currentLogoSrc = $editClient['logo_url'];
                            if (!filter_var($currentLogoSrc, FILTER_VALIDATE_URL)) {
                                // It's a local file path, no need to prepend since we're already in admin directory
                                $currentLogoSrc = ltrim($currentLogoSrc, '/');
                            }
                            ?>
                            <img src="<?php echo htmlspecialchars($currentLogoSrc); ?>" 
                                 alt="Current logo" 
                                 class="h-12 w-12 object-contain rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="h-12 w-12 bg-gray-100 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 flex items-center justify-center" style="display: none;">
                                <i class="fas fa-image text-gray-400"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Current Logo</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Upload a new file to replace</p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md hover:border-brand-blue dark:hover:border-brand-blue-light theme-transition">
                    <div class="space-y-1 text-center">
                        <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-3"></i>
                        <div class="flex text-sm text-gray-600 dark:text-gray-300">
                            <label for="logo_file" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-brand-blue hover:text-brand-blue-dark focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-brand-blue theme-transition">
                                <span>Upload a logo</span>
                                <input type="file" name="logo_file" id="logo_file" 
                                       accept="image/jpeg,image/jpg,image/png,image/gif,image/webp,image/svg+xml"
                                       class="sr-only">
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, GIF, WebP, SVG up to 5MB</p>
                    </div>
                </div>
                
                <!-- File upload preview -->
                <div id="logo-preview" class="mt-3 hidden">
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex-shrink-0">
                            <img id="logo-preview-img" src="" alt="Logo preview" class="h-12 w-12 object-contain rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p id="logo-preview-name" class="text-sm font-medium text-gray-900 dark:text-white truncate"></p>
                            <p id="logo-preview-size" class="text-xs text-gray-500 dark:text-gray-400"></p>
                        </div>
                        <button type="button" onclick="clearLogoPreview()" class="flex-shrink-0 text-red-500 hover:text-red-700 theme-transition">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
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

<script>
// File upload handling
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('logo_file');
    const preview = document.getElementById('logo-preview');
    const previewImg = document.getElementById('logo-preview-img');
    const previewName = document.getElementById('logo-preview-name');
    const previewSize = document.getElementById('logo-preview-size');
    
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                handleFilePreview(file);
            }
        });
        
        // Drag and drop functionality
        const dropZone = fileInput.closest('.border-dashed');
        
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.add('border-brand-blue', 'bg-brand-blue', 'bg-opacity-5');
        });
        
        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('border-brand-blue', 'bg-brand-blue', 'bg-opacity-5');
        });
        
        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('border-brand-blue', 'bg-brand-blue', 'bg-opacity-5');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFilePreview(files[0]);
            }
        });
    }
    
    function handleFilePreview(file) {
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
        if (!allowedTypes.includes(file.type)) {
            alert('Please select a valid image file (PNG, JPG, GIF, WebP, SVG)');
            clearLogoPreview();
            return;
        }
        
        // Validate file size (5MB)
        const maxSize = 5 * 1024 * 1024; // 5MB in bytes
        if (file.size > maxSize) {
            alert('File size must be less than 5MB');
            clearLogoPreview();
            return;
        }
        
        // Create preview
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewName.textContent = file.name;
            previewSize.textContent = formatFileSize(file.size);
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
});

function clearLogoPreview() {
    const fileInput = document.getElementById('logo_file');
    const preview = document.getElementById('logo-preview');
    const previewImg = document.getElementById('logo-preview-img');
    
    fileInput.value = '';
    preview.classList.add('hidden');
    previewImg.src = '';
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}
</script>
