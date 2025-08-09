<?php
/**
 * Insurance Providers Management
 * Sky Border Solutions CMS
 */

// Start session and check authentication
session_start();
require_once 'classes/Auth.php';
require_once 'classes/ContentManager.php';
require_once 'classes/FileUploader.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$contentManager = new ContentManager();
$currentUser = $auth->getCurrentUser();

// Handle form submissions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $result = handleAddProvider($contentManager);
                $message = $result['message'];
                $messageType = $result['type'];
                break;
            
            case 'update':
                $result = handleUpdateProvider($contentManager);
                $message = $result['message'];
                $messageType = $result['type'];
                break;
            
            case 'delete':
                if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                    if ($contentManager->deleteInsuranceProvider($_POST['id'])) {
                        $message = 'Insurance provider deleted successfully!';
                        $messageType = 'success';
                    } else {
                        $message = 'Error deleting insurance provider.';
                        $messageType = 'error';
                    }
                }
                break;
        }
    }
}

function handleAddProvider($contentManager) {
    $logoUrl = '';
    
    // Handle logo upload
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $uploader = new FileUploader();
        $uploadResult = $uploader->uploadFile($_FILES['logo'], 'insurance');
        if ($uploadResult['success']) {
            $logoUrl = $uploadResult['file_path'];
        } else {
            return ['message' => 'Error uploading logo: ' . $uploadResult['error'], 'type' => 'error'];
        }
    }
    
    $data = [
        'provider_name' => trim($_POST['provider_name']),
        'logo_url' => $logoUrl,
        'provider_type' => 'general',
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'display_order' => (int)($_POST['display_order'] ?? 0)
    ];
    
    if (empty($data['provider_name'])) {
        return ['message' => 'Provider name is required.', 'type' => 'error'];
    }
    
    if ($contentManager->addInsuranceProvider($data)) {
        return ['message' => 'Insurance provider added successfully!', 'type' => 'success'];
    } else {
        return ['message' => 'Error adding insurance provider.', 'type' => 'error'];
    }
}

function handleUpdateProvider($contentManager) {
    $id = (int)$_POST['id'];
    $provider = $contentManager->getInsuranceProvider($id);
    
    if (!$provider) {
        return ['message' => 'Provider not found.', 'type' => 'error'];
    }
    
    $logoUrl = $provider['logo_url']; // Keep existing logo by default
    
    // Handle new logo upload
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $uploader = new FileUploader();
        $uploadResult = $uploader->uploadFile($_FILES['logo'], 'insurance');
        if ($uploadResult['success']) {
            // Delete old logo if it exists
            if ($provider['logo_url'] && file_exists($provider['logo_url'])) {
                unlink($provider['logo_url']);
            }
            $logoUrl = $uploadResult['file_path'];
        } else {
            return ['message' => 'Error uploading logo: ' . $uploadResult['error'], 'type' => 'error'];
        }
    }
    
    $data = [
        'provider_name' => trim($_POST['provider_name']),
        'logo_url' => $logoUrl,
        'provider_type' => 'general',
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'display_order' => (int)($_POST['display_order'] ?? 0)
    ];
    
    if (empty($data['provider_name'])) {
        return ['message' => 'Provider name is required.', 'type' => 'error'];
    }
    
    if ($contentManager->updateInsuranceProvider($id, $data)) {
        return ['message' => 'Insurance provider updated successfully!', 'type' => 'success'];
    } else {
        return ['message' => 'Error updating insurance provider.', 'type' => 'error'];
    }
}

// Get all insurance providers
$providers = $contentManager->getInsuranceProviders();

// Get provider for editing if edit ID is provided
$editProvider = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editProvider = $contentManager->getInsuranceProvider($_GET['edit']);
}

$pageTitle = 'Insurance Providers';
require_once 'layouts/admin.php';
?>

<div class="insurance-content">
    <?php if ($message): ?>
    <div class="alert alert-<?php echo $messageType; ?> mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <?php if ($messageType === 'success'): ?>
                    <i class="fas fa-check-circle text-green-400"></i>
                <?php else: ?>
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                <?php endif; ?>
            </div>
            <div class="ml-3">
                <p class="text-sm"><?php echo htmlspecialchars($message); ?></p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Enhanced Page Header -->
    <div class="mb-8">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-blue-500 to-cyan-500">
                        <i class="fas fa-shield-alt text-white text-lg"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Insurance Providers</h1>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Manage insurance provider companies and their logos</p>
                <div class="mt-2 flex items-center space-x-4 text-xs">
                    <span class="flex items-center text-green-600 dark:text-green-400">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                        Active Providers: <span id="activeCount" class="font-semibold ml-1"><?php echo count($providers); ?></span>
                    </span>
                    <span class="flex items-center text-blue-600 dark:text-blue-400">
                        <i class="fas fa-star mr-1"></i>
                        Featured: <span id="featuredCount" class="font-semibold ml-1"><?php echo count(array_filter($providers, function($p) { return $p['is_featured']; })); ?></span>
                    </span>
                </div>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <button type="button" onclick="refreshProviders()" class="btn-secondary" title="Refresh List">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Refresh
                </button>
                <button type="button" onclick="openAddModal()" class="btn-primary relative overflow-hidden">
                    <span class="relative z-10 flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        Add Provider
                    </span>
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-cyan-600 opacity-0 hover:opacity-100 transition-opacity duration-200"></div>
                </button>
            </div>
        </div>
    </div>

    <!-- Providers Grid -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        <?php if (empty($providers)): ?>
        <div class="col-span-full">
            <div class="text-center py-12">
                <i class="fas fa-shield-alt text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No insurance providers</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">Get started by adding your first insurance provider.</p>
                <button type="button" onclick="openAddModal()" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i>
                    Add Provider
                </button>
            </div>
        </div>
        <?php else: ?>
            <?php foreach ($providers as $provider): ?>
            <div class="group relative modern-card bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                <!-- Status Indicator -->
                <div class="absolute top-3 right-3">
                    <div class="w-3 h-3 bg-green-500 rounded-full shadow-sm"></div>
                </div>
                
                <!-- Featured Badge -->
                <?php if ($provider['is_featured']): ?>
                <div class="absolute top-3 left-3">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-yellow-400 to-orange-500 text-white shadow-sm">
                        <i class="fas fa-star mr-1"></i>
                        Featured
                    </span>
                </div>
                <?php endif; ?>
                
                <!-- Logo Section -->
                <div class="flex justify-center mb-4 mt-2">
                    <div class="relative group">
                        <?php if (!empty($provider['logo_url']) && file_exists($provider['logo_url'])): ?>
                        <div class="h-16 w-20 bg-gray-50 dark:bg-gray-700 rounded-lg flex items-center justify-center p-2 group-hover:bg-gray-100 dark:group-hover:bg-gray-600 transition-colors duration-200">
                            <img src="<?php echo htmlspecialchars($provider['logo_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($provider['provider_name']); ?>" 
                                 class="h-full w-full object-contain">
                        </div>
                        <?php else: ?>
                        <div class="h-16 w-16 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center shadow-md group-hover:shadow-lg transition-shadow duration-200">
                            <i class="fas fa-shield-alt text-white text-xl"></i>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Hover overlay for logo preview -->
                        <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center">
                            <i class="fas fa-search-plus text-white text-sm"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Provider Info -->
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2" title="<?php echo htmlspecialchars($provider['provider_name']); ?>">
                        <?php echo htmlspecialchars($provider['provider_name']); ?>
                    </h3>
                    
                    <div class="flex items-center justify-center space-x-3 mb-4">
                        <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                            <i class="fas fa-sort-numeric-up mr-1"></i>
                            Order: <?php echo $provider['display_order']; ?>
                        </div>
                        <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                            <i class="fas fa-calendar mr-1"></i>
                            <?php echo date('M j', strtotime($provider['created_at'] ?? 'now')); ?>
                        </div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex justify-center space-x-2">
                    <button type="button" onclick="editProvider(<?php echo htmlspecialchars(json_encode($provider)); ?>)" 
                            class="flex items-center px-3 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors duration-200">
                        <i class="fas fa-edit mr-1"></i>
                        Edit
                    </button>
                    <button type="button" onclick="deleteProvider(<?php echo $provider['id']; ?>, '<?php echo htmlspecialchars($provider['provider_name']); ?>')" 
                            class="flex items-center px-3 py-2 text-sm font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors duration-200">
                        <i class="fas fa-trash mr-1"></i>
                        Delete
                    </button>
                </div>
                
                <!-- Card Background Gradient -->
                <div class="absolute inset-0 bg-gradient-to-br from-blue-50/50 to-cyan-50/50 dark:from-blue-900/10 dark:to-cyan-900/10 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Enhanced Add/Edit Provider Modal -->
<div id="providerModal" class="modal hidden">
    <div class="modal-overlay bg-black bg-opacity-50 backdrop-blur-sm"></div>
    <div class="modal-content max-w-lg transform transition-all duration-300 scale-95 opacity-0" id="modalContainer">
        <div class="modal-header bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-t-lg">
            <div class="flex items-center space-x-3">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white bg-opacity-20">
                    <i class="fas fa-shield-alt text-white"></i>
                </div>
                <h3 id="modalTitle" class="text-lg font-semibold">Add Insurance Provider</h3>
            </div>
            <button type="button" onclick="closeModal()" class="modal-close hover:bg-white hover:bg-opacity-20 rounded-lg p-2 transition-colors duration-200">
                <i class="fas fa-times text-white"></i>
            </button>
        </div>
        
        <form id="providerForm" method="POST" enctype="multipart/form-data" class="modal-body">
            <input type="hidden" name="action" id="formAction" value="add">
            <input type="hidden" name="id" id="providerId" value="">
            
            <!-- Provider Name -->
            <div class="form-group">
                <label for="provider_name" class="form-label flex items-center">
                    <i class="fas fa-building mr-2 text-blue-500"></i>
                    Provider Name *
                </label>
                <input type="text" id="provider_name" name="provider_name" 
                       class="form-input focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                       placeholder="Enter insurance provider name" required>
                <div class="mt-1 text-xs text-gray-500">
                    This will be displayed on the website
                </div>
            </div>
            
            <!-- Enhanced Logo Upload -->
            <div class="form-group">
                <label for="logo" class="form-label flex items-center">
                    <i class="fas fa-image mr-2 text-green-500"></i>
                    Company Logo
                </label>
                <div class="logo-upload-area relative group">
                    <!-- Upload Instructions -->
                    <div id="uploadInstructions" class="text-center py-8">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4 group-hover:bg-gray-200 dark:group-hover:bg-gray-600 transition-colors duration-200">
                                <i class="fas fa-cloud-upload-alt text-2xl text-gray-400"></i>
                            </div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Drop logo here or click to browse</p>
                            <p class="text-xs text-gray-500">PNG, JPG, SVG up to 2MB</p>
                        </div>
                    </div>
                    
                    <!-- File Input -->
                    <input type="file" id="logo" name="logo" accept="image/*" 
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    
                    <!-- Logo Preview -->
                    <div id="logoPreview" class="logo-preview hidden">
                        <div class="relative">
                            <img id="logoImage" src="" alt="Logo preview" 
                                 class="h-20 w-auto object-contain mx-auto rounded border border-gray-200 dark:border-gray-600">
                            <button type="button" onclick="removeLogo()" 
                                    class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600 transition-colors duration-200">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 text-center mt-2">Click to change logo</p>
                    </div>
                </div>
            </div>
            
            <!-- Options Row -->
            <div class="grid grid-cols-2 gap-4">
                <!-- Featured -->
                <div class="form-group">
                    <label class="flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 cursor-pointer">
                        <input type="checkbox" id="is_featured" name="is_featured" class="form-checkbox text-yellow-500 focus:ring-yellow-500">
                        <div class="ml-3">
                            <div class="flex items-center">
                                <i class="fas fa-star text-yellow-500 mr-2"></i>
                                <span class="font-medium text-gray-700 dark:text-gray-300">Featured</span>
                            </div>
                            <p class="text-xs text-gray-500">Highlight this provider</p>
                        </div>
                    </label>
                </div>
                
                <!-- Display Order -->
                <div class="form-group">
                    <label for="display_order" class="form-label flex items-center">
                        <i class="fas fa-sort-numeric-up mr-2 text-purple-500"></i>
                        Display Order
                    </label>
                    <input type="number" id="display_order" name="display_order" 
                           class="form-input focus:ring-2 focus:ring-purple-500 focus:border-purple-500" 
                           value="0" min="0" placeholder="0">
                    <p class="text-xs text-gray-500 mt-1">Lower numbers appear first</p>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" onclick="closeModal()" class="btn-secondary mr-3">
                    <i class="fas fa-times mr-2"></i>
                    Cancel
                </button>
                <button type="submit" class="btn-primary relative overflow-hidden">
                    <span class="relative z-10 flex items-center">
                        <i id="submitIcon" class="fas fa-plus mr-2"></i>
                        <span id="submitText">Add Provider</span>
                    </span>
                    <div class="absolute inset-0 bg-gradient-to-r from-green-600 to-blue-600 opacity-0 hover:opacity-100 transition-opacity duration-200"></div>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal hidden">
    <div class="modal-overlay"></div>
    <div class="modal-content max-w-md">
        <div class="modal-header">
            <h3 class="text-lg font-medium text-red-600">Delete Provider</h3>
            <button type="button" onclick="closeDeleteModal()" class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-body">
            <p class="text-gray-600 dark:text-gray-300 mb-4">
                Are you sure you want to delete "<span id="deleteProviderName" class="font-semibold"></span>"? 
                This action cannot be undone.
            </p>
            
            <form id="deleteForm" method="POST">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="deleteProviderId" value="">
                
                <div class="modal-footer">
                    <button type="button" onclick="closeDeleteModal()" class="btn-secondary mr-3">Cancel</button>
                    <button type="submit" class="btn-danger">
                        <i class="fas fa-trash mr-2"></i>
                        Delete Provider
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Enhanced Modal functions with animations
function openAddModal() {
    const modal = document.getElementById('providerModal');
    const container = document.getElementById('modalContainer');
    
    // Reset form and set add mode
    document.getElementById('modalTitle').textContent = 'Add Insurance Provider';
    document.getElementById('formAction').value = 'add';
    document.getElementById('providerId').value = '';
    document.getElementById('submitIcon').className = 'fas fa-plus mr-2';
    document.getElementById('submitText').textContent = 'Add Provider';
    document.getElementById('providerForm').reset();
    
    // Reset logo area
    document.getElementById('logoPreview').classList.add('hidden');
    document.getElementById('uploadInstructions').classList.remove('hidden');
    
    // Show modal with animation
    modal.classList.remove('hidden');
    setTimeout(() => {
        container.classList.remove('scale-95', 'opacity-0');
        container.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function editProvider(provider) {
    const modal = document.getElementById('providerModal');
    const container = document.getElementById('modalContainer');
    
    // Set edit mode
    document.getElementById('modalTitle').textContent = 'Edit Insurance Provider';
    document.getElementById('formAction').value = 'update';
    document.getElementById('providerId').value = provider.id;
    document.getElementById('provider_name').value = provider.provider_name;
    document.getElementById('is_featured').checked = provider.is_featured == 1;
    document.getElementById('display_order').value = provider.display_order;
    document.getElementById('submitIcon').className = 'fas fa-save mr-2';
    document.getElementById('submitText').textContent = 'Update Provider';
    
    // Handle logo display
    if (provider.logo_url) {
        document.getElementById('logoImage').src = provider.logo_url;
        document.getElementById('logoPreview').classList.remove('hidden');
        document.getElementById('uploadInstructions').classList.add('hidden');
    } else {
        document.getElementById('logoPreview').classList.add('hidden');
        document.getElementById('uploadInstructions').classList.remove('hidden');
    }
    
    // Show modal with animation
    modal.classList.remove('hidden');
    setTimeout(() => {
        container.classList.remove('scale-95', 'opacity-0');
        container.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closeModal() {
    const modal = document.getElementById('providerModal');
    const container = document.getElementById('modalContainer');
    
    // Animate out
    container.classList.remove('scale-100', 'opacity-100');
    container.classList.add('scale-95', 'opacity-0');
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

function deleteProvider(id, name) {
    if (confirm(`Are you sure you want to delete "${name}"? This action cannot be undone.`)) {
        // Create and submit delete form
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function refreshProviders() {
    const button = event.target.closest('button');
    const icon = button.querySelector('i');
    
    // Add spinning animation
    icon.classList.add('fa-spin');
    button.disabled = true;
    
    // Simulate refresh (reload page)
    setTimeout(() => {
        window.location.reload();
    }, 500);
}

function removeLogo() {
    document.getElementById('logoPreview').classList.add('hidden');
    document.getElementById('uploadInstructions').classList.remove('hidden');
    document.getElementById('logo').value = '';
}

// Enhanced logo preview with drag & drop
function setupLogoUpload() {
    const logoInput = document.getElementById('logo');
    const uploadArea = document.querySelector('.logo-upload-area');
    const logoPreview = document.getElementById('logoPreview');
    const uploadInstructions = document.getElementById('uploadInstructions');
    
    // File input change handler
    logoInput.addEventListener('change', function(e) {
        handleFileSelect(e.target.files[0]);
    });
    
    // Drag and drop handlers
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('border-blue-400', 'bg-blue-50');
    });
    
    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('border-blue-400', 'bg-blue-50');
    });
    
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('border-blue-400', 'bg-blue-50');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFileSelect(files[0]);
        }
    });
    
    function handleFileSelect(file) {
        if (!file) {
            removeLogo();
            return;
        }
        
        // Validate file type
        if (!file.type.match('image.*')) {
            alert('Please select an image file.');
            return;
        }
        
        // Validate file size (2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('File size must be less than 2MB.');
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('logoImage').src = e.target.result;
            logoPreview.classList.remove('hidden');
            uploadInstructions.classList.add('hidden');
            
            // Update file input
            const dt = new DataTransfer();
            dt.items.add(file);
            logoInput.files = dt.files;
        };
        reader.readAsDataURL(file);
    }
}

// Update counters
function updateCounters() {
    const providers = <?php echo json_encode($providers); ?>;
    const activeCount = providers.length;
    const featuredCount = providers.filter(p => p.is_featured == 1).length;
    
    document.getElementById('activeCount').textContent = activeCount;
    document.getElementById('featuredCount').textContent = featuredCount;
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    setupLogoUpload();
    updateCounters();
    
    // Close modals when clicking outside
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-overlay')) {
            closeModal();
        }
    });
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
});

// Form submission with loading state
document.getElementById('providerForm').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    const submitText = document.getElementById('submitText');
    const submitIcon = document.getElementById('submitIcon');
    
    // Show loading state
    submitBtn.disabled = true;
    submitIcon.className = 'fas fa-spinner fa-spin mr-2';
    submitText.textContent = 'Saving...';
});
</script>

<style>
/* Enhanced Logo Upload Area */
.logo-upload-area {
    border: 2px dashed #e5e7eb;
    border-radius: 0.75rem;
    padding: 0;
    text-align: center;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: linear-gradient(145deg, #f8fafc 0%, #f1f5f9 100%);
    min-height: 120px;
    position: relative;
    overflow: hidden;
}

.logo-upload-area:hover {
    border-color: #3b82f6;
    background: linear-gradient(145deg, #eff6ff 0%, #dbeafe 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.15);
}

.logo-upload-area.border-blue-400 {
    border-color: #60a5fa;
    background: linear-gradient(145deg, #dbeafe 0%, #bfdbfe 100%);
}

.dark .logo-upload-area {
    border-color: #4b5563;
    background: linear-gradient(145deg, #374151 0%, #2d3748 100%);
}

.dark .logo-upload-area:hover {
    border-color: #60a5fa;
    background: linear-gradient(145deg, #1e3a8a 0%, #1e40af 100%);
}

.logo-preview {
    padding: 1.5rem;
    background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 0.75rem;
    border: 1px solid #e5e7eb;
    position: relative;
}

.dark .logo-preview {
    background: linear-gradient(145deg, #374151 0%, #2d3748 100%);
    border-color: #4b5563;
}

/* Modern Card Enhancements */
.modern-card {
    position: relative;
    backdrop-filter: blur(10px);
}

.modern-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.3), transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.modern-card:hover::before {
    opacity: 1;
}

/* Modal Animations */
.modal {
    backdrop-filter: blur(8px);
}

.modal-content {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

/* Button Enhancements */
.btn-primary, .btn-secondary {
    position: relative;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.btn-primary:hover, .btn-secondary:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

/* Line Clamp Utility */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Status Indicators */
@keyframes pulse-dot {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.8;
        transform: scale(1.1);
    }
}

.animate-pulse {
    animation: pulse-dot 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Drag and Drop States */
.logo-upload-area.drag-over {
    border-color: #10b981;
    background: linear-gradient(145deg, #d1fae5 0%, #a7f3d0 100%);
    transform: scale(1.02);
}

.dark .logo-upload-area.drag-over {
    background: linear-gradient(145deg, #065f46 0%, #047857 100%);
}

/* Loading States */
.loading {
    pointer-events: none;
    opacity: 0.7;
}

.loading .fas {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Enhanced Form Controls */
.form-input:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    transform: translateY(-1px);
}

.form-checkbox:checked {
    background-color: #3b82f6;
    border-color: #3b82f6;
}

/* Responsive Improvements */
@media (max-width: 640px) {
    .modern-card {
        margin-bottom: 1rem;
    }
    
    .modal-content {
        margin: 1rem;
        max-width: calc(100vw - 2rem);
    }
    
    .logo-upload-area {
        min-height: 100px;
    }
}
</style>
