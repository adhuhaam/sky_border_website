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

    <!-- Page Header -->
    <div class="mb-8">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Insurance Providers</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Manage insurance provider companies and their logos</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <button type="button" onclick="openAddModal()" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i>
                    Add Provider
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
            <div class="modern-card bg-white dark:bg-gray-800 rounded-xl p-6 hover-lift">
                <!-- Logo Section -->
                <div class="flex justify-center mb-4">
                    <?php if (!empty($provider['logo_url']) && file_exists($provider['logo_url'])): ?>
                    <img src="<?php echo htmlspecialchars($provider['logo_url']); ?>" 
                         alt="<?php echo htmlspecialchars($provider['provider_name']); ?>" 
                         class="h-16 w-auto object-contain">
                    <?php else: ?>
                    <div class="h-16 w-16 bg-gradient-to-r from-brand-blue to-brand-teal rounded-lg flex items-center justify-center">
                        <i class="fas fa-shield-alt text-white text-xl"></i>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Provider Info -->
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                        <?php echo htmlspecialchars($provider['provider_name']); ?>
                    </h3>
                    
                    <?php if ($provider['is_featured']): ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 mb-3">
                        <i class="fas fa-star mr-1"></i>
                        Featured
                    </span>
                    <?php endif; ?>
                    
                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        Order: <?php echo $provider['display_order']; ?>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex justify-center space-x-2">
                    <button type="button" onclick="editProvider(<?php echo htmlspecialchars(json_encode($provider)); ?>)" 
                            class="btn-secondary text-sm">
                        <i class="fas fa-edit mr-1"></i>
                        Edit
                    </button>
                    <button type="button" onclick="deleteProvider(<?php echo $provider['id']; ?>, '<?php echo htmlspecialchars($provider['provider_name']); ?>')" 
                            class="btn-danger text-sm">
                        <i class="fas fa-trash mr-1"></i>
                        Delete
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Add/Edit Provider Modal -->
<div id="providerModal" class="modal hidden">
    <div class="modal-overlay"></div>
    <div class="modal-content max-w-md">
        <div class="modal-header">
            <h3 id="modalTitle" class="text-lg font-medium">Add Insurance Provider</h3>
            <button type="button" onclick="closeModal()" class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="providerForm" method="POST" enctype="multipart/form-data" class="modal-body">
            <input type="hidden" name="action" id="formAction" value="add">
            <input type="hidden" name="id" id="providerId" value="">
            
            <!-- Provider Name -->
            <div class="form-group">
                <label for="provider_name" class="form-label">Provider Name *</label>
                <input type="text" id="provider_name" name="provider_name" class="form-input" required>
            </div>
            
            <!-- Logo Upload -->
            <div class="form-group">
                <label for="logo" class="form-label">Logo</label>
                <div class="logo-upload-area">
                    <input type="file" id="logo" name="logo" accept="image/*" class="form-input-file">
                    <div id="logoPreview" class="logo-preview hidden">
                        <img id="logoImage" src="" alt="Logo preview" class="h-20 w-auto object-contain mx-auto">
                    </div>
                    <div class="text-sm text-gray-500 mt-2">
                        Recommended: PNG, JPG, SVG files under 2MB
                    </div>
                </div>
            </div>
            
            <!-- Featured -->
            <div class="form-group">
                <label class="flex items-center">
                    <input type="checkbox" id="is_featured" name="is_featured" class="form-checkbox">
                    <span class="ml-2">Featured Provider</span>
                </label>
            </div>
            
            <!-- Display Order -->
            <div class="form-group">
                <label for="display_order" class="form-label">Display Order</label>
                <input type="number" id="display_order" name="display_order" class="form-input" value="0" min="0">
                <p class="text-sm text-gray-500 mt-1">Lower numbers appear first</p>
            </div>
            
            <div class="modal-footer">
                <button type="button" onclick="closeModal()" class="btn-secondary mr-3">Cancel</button>
                <button type="submit" class="btn-primary">
                    <i id="submitIcon" class="fas fa-plus mr-2"></i>
                    <span id="submitText">Add Provider</span>
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
// Modal functions
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Add Insurance Provider';
    document.getElementById('formAction').value = 'add';
    document.getElementById('providerId').value = '';
    document.getElementById('submitIcon').className = 'fas fa-plus mr-2';
    document.getElementById('submitText').textContent = 'Add Provider';
    document.getElementById('providerForm').reset();
    document.getElementById('logoPreview').classList.add('hidden');
    document.getElementById('providerModal').classList.remove('hidden');
}

function editProvider(provider) {
    document.getElementById('modalTitle').textContent = 'Edit Insurance Provider';
    document.getElementById('formAction').value = 'update';
    document.getElementById('providerId').value = provider.id;
    document.getElementById('provider_name').value = provider.provider_name;
    document.getElementById('is_featured').checked = provider.is_featured == 1;
    document.getElementById('display_order').value = provider.display_order;
    document.getElementById('submitIcon').className = 'fas fa-save mr-2';
    document.getElementById('submitText').textContent = 'Update Provider';
    
    // Show existing logo if available
    if (provider.logo_url) {
        document.getElementById('logoImage').src = provider.logo_url;
        document.getElementById('logoPreview').classList.remove('hidden');
    } else {
        document.getElementById('logoPreview').classList.add('hidden');
    }
    
    document.getElementById('providerModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('providerModal').classList.add('hidden');
}

function deleteProvider(id, name) {
    document.getElementById('deleteProviderId').value = id;
    document.getElementById('deleteProviderName').textContent = name;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Logo preview
document.getElementById('logo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('logoImage').src = e.target.result;
            document.getElementById('logoPreview').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    } else {
        document.getElementById('logoPreview').classList.add('hidden');
    }
});

// Close modals when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-overlay')) {
        closeModal();
        closeDeleteModal();
    }
});
</script>

<style>
.logo-upload-area {
    border: 2px dashed #e5e7eb;
    border-radius: 0.5rem;
    padding: 1rem;
    text-align: center;
    transition: border-color 0.2s;
}

.logo-upload-area:hover {
    border-color: #6b7280;
}

.logo-preview {
    margin-top: 1rem;
    padding: 1rem;
    background-color: #f9fafb;
    border-radius: 0.5rem;
}

.dark .logo-preview {
    background-color: #374151;
}
</style>
