<?php
/**
 * Insurance Providers Management (New Layout System)
 * Sky Border Solutions CMS
 */

require_once 'classes/Auth.php';
require_once 'classes/ContentManager.php';
require_once 'classes/FileUploader.php';
require_once 'includes/layout-helpers.php';

$auth = new Auth();
$auth->requireLogin();

$contentManager = new ContentManager();
$currentUser = $auth->getCurrentUser();

$success = '';
$error = '';
$action = $_GET['action'] ?? 'list';
$providerId = $_GET['id'] ?? null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_provider'])) {
        $provider_name = trim($_POST['provider_name'] ?? '');
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $display_order = (int)($_POST['display_order'] ?? 0);
        $logo_url = '';
        
        if (!empty($provider_name)) {
            try {
                // Handle logo upload if file is provided
                if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
                    $uploader = new FileUploader();
                    $uploadResult = $uploader->uploadFile($_FILES['logo_file'], 'insurance', 'insurance_');
                    if ($uploadResult['success']) {
                        $logo_url = $uploadResult['file_path'];
                    } else {
                        $error = 'Error uploading logo: ' . $uploadResult['error'];
                    }
                }
                
                if (empty($error)) {
                    $data = [
                        'provider_name' => $provider_name,
                        'logo_url' => $logo_url,
                        'is_featured' => $is_featured,
                        'display_order' => $display_order
                    ];
                    
                    if ($contentManager->addInsuranceProvider($data)) {
                        $success = 'Insurance provider added successfully!';
                        $action = 'list';
                    } else {
                        // If database insert failed and file was uploaded, clean up the file
                        if (!empty($logo_url)) {
                            $uploader = new FileUploader();
                            $uploader->delete($logo_url);
                        }
                        $error = 'Failed to add insurance provider. Please try again.';
                    }
                }
            } catch (Exception $e) {
                $error = 'Upload error: ' . $e->getMessage();
            }
        } else {
            $error = 'Provider name is required.';
        }
    }
    
    if (isset($_POST['update_provider'])) {
        $id = (int)($_POST['provider_id'] ?? 0);
        $provider_name = trim($_POST['provider_name'] ?? '');
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $display_order = (int)($_POST['display_order'] ?? 0);
        
        if (!empty($provider_name) && $id > 0) {
            try {
                // Get current provider data to preserve existing logo if no new upload
                $currentProvider = $contentManager->getInsuranceProvider($id);
                $logo_url = $currentProvider['logo_url'] ?? '';
                $old_logo_url = $logo_url;
                
                // Handle logo upload if new file is provided
                if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
                    $uploader = new FileUploader();
                    $uploadResult = $uploader->uploadFile($_FILES['logo_file'], 'insurance', 'insurance_');
                    
                    if ($uploadResult['success']) {
                        $logo_url = $uploadResult['file_path'];
                    } else {
                        $error = 'Error uploading logo: ' . $uploadResult['error'];
                    }
                }
                
                if (empty($error)) {
                    $data = [
                        'provider_name' => $provider_name,
                        'logo_url' => $logo_url,
                        'is_featured' => $is_featured,
                        'display_order' => $display_order
                    ];
                    
                    if ($contentManager->updateInsuranceProvider($id, $data)) {
                        // If update successful and we uploaded a new logo, delete the old one
                        if (isset($uploadResult) && !empty($old_logo_url) && $old_logo_url !== $logo_url) {
                            $uploader = new FileUploader();
                            $uploader->delete($old_logo_url);
                        }
                        
                        $success = 'Insurance provider updated successfully!';
                        $action = 'list';
                    } else {
                        // If database update failed and we uploaded a new file, clean up the new file
                        if (isset($uploadResult) && $uploadResult['success']) {
                            $uploader = new FileUploader();
                            $uploader->delete($logo_url);
                        }
                        $error = 'Failed to update insurance provider. Please try again.';
                    }
                }
            } catch (Exception $e) {
                $error = 'Upload error: ' . $e->getMessage();
            }
        } else {
            $error = 'Provider name is required.';
        }
    }
    
    if (isset($_POST['delete_provider'])) {
        $id = (int)($_POST['provider_id'] ?? 0);
        if ($id > 0) {
            try {
                // Get provider data to check for logo file
                $providerData = $contentManager->getInsuranceProvider($id);
                
                if ($contentManager->deleteInsuranceProvider($id)) {
                    // If deletion successful and provider had a logo, delete the file
                    if (!empty($providerData['logo_url'])) {
                        $uploader = new FileUploader();
                        $uploader->delete($providerData['logo_url']);
                    }
                    
                    $success = 'Insurance provider deleted successfully!';
                    $action = 'list';
                } else {
                    $error = 'Failed to delete insurance provider. Please try again.';
                }
            } catch (Exception $e) {
                $error = 'Error deleting insurance provider: ' . $e->getMessage();
            }
        }
    }
}

// Get data for the page
$providers = [];
try {
    $providers = $contentManager->getInsuranceProviders();
} catch (Exception $e) {
    $error = 'Error loading insurance providers: ' . $e->getMessage();
}

$editProvider = null;
if ($action === 'edit' && $providerId) {
    try {
        $editProvider = $contentManager->getInsuranceProvider($providerId);
        if (!$editProvider) {
            $error = 'Insurance provider not found.';
            $action = 'list';
        }
    } catch (Exception $e) {
        $error = 'Error loading insurance provider: ' . $e->getMessage();
        $action = 'list';
    }
}

// Determine page title and actions
$pageTitle = 'Insurance Providers';
$pageDescription = 'Manage insurance providers displayed on the website';
$pageActions = '';

if ($action === 'add') {
    $pageTitle = 'Add Insurance Provider';
    $pageDescription = 'Add a new insurance provider to your network';
} elseif ($action === 'edit') {
    $pageTitle = 'Edit Insurance Provider';
    $pageDescription = 'Update insurance provider information';
} else {
    $pageActions = createPageActions([
        [
            'url' => '?action=add',
            'label' => 'Add Provider',
            'icon' => 'fas fa-plus',
            'class' => 'btn-primary'
        ]
    ]);
}

// Create content view for insurance providers
$contentView = __DIR__ . '/views/insurance-providers-content.php';

// Create the insurance providers content view if it doesn't exist
if (!file_exists($contentView)) {
    $contentViewCode = '<?php
/**
 * Insurance Providers Management Content View
 * Sky Border Solutions CMS
 */
?>

<?php if ($action === \'list\'): ?>
<!-- Providers List -->
<div class="modern-card bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg theme-transition">
    <?php if (empty($providers)): ?>
    <div class="text-center py-12">
        <i class="fas fa-shield-alt text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2 theme-transition">No insurance providers yet</h3>
        <p class="text-gray-600 dark:text-gray-400 theme-transition">Get started by adding your first insurance provider.</p>
        <div class="mt-6">
            <a href="?action=add" class="btn-primary inline-flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Add Provider
            </a>
        </div>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 p-6">
        <?php foreach ($providers as $provider): ?>
        <div class="modern-card rounded-lg p-4 hover:shadow-lg transition-shadow">
            <div class="flex flex-col h-full">
                <!-- Logo Section -->
                <div class="flex justify-center mb-4">
                    <?php if (!empty($provider[\'logo_url\']) && file_exists($provider[\'logo_url\'])): ?>
                    <div class="h-16 w-20 bg-gray-50 dark:bg-gray-700 rounded-lg flex items-center justify-center p-2">
                        <img src="<?php echo htmlspecialchars($provider[\'logo_url\']); ?>" 
                             alt="<?php echo htmlspecialchars($provider[\'provider_name\']); ?>" 
                             class="h-full w-full object-contain">
                    </div>
                    <?php else: ?>
                    <div class="h-16 w-16 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shield-alt text-white text-xl"></i>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Provider Info -->
                <div class="flex-1 text-center mb-4">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white theme-transition mb-2">
                        <?php echo htmlspecialchars($provider[\'provider_name\']); ?>
                    </h3>
                    
                    <!-- Status Indicators -->
                    <div class="flex flex-wrap justify-center gap-1 mb-3">
                        <?php if ($provider[\'is_featured\']): ?>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400">
                            <i class="fas fa-star mr-1"></i>
                            Featured
                        </span>
                        <?php endif; ?>
                        
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                            <i class="fas fa-check-circle mr-1"></i>
                            Active
                        </span>
                    </div>
                    
                    <p class="text-xs text-gray-500 dark:text-gray-400">Order: <?php echo $provider[\'display_order\']; ?></p>
                </div>
                
                <!-- Actions -->
                <div class="flex justify-center space-x-2">
                    <a href="?action=edit&id=<?php echo $provider[\'id\']; ?>" class="text-indigo-600 hover:text-indigo-900 text-xs font-medium px-2 py-1 bg-indigo-50 hover:bg-indigo-100 rounded transition-colors">
                        <i class="fas fa-edit mr-1"></i>
                        Edit
                    </a>
                    <form method="POST" class="inline" onsubmit="return confirm(\'Are you sure you want to delete this provider?\');">
                        <input type="hidden" name="provider_id" value="<?php echo $provider[\'id\']; ?>">
                        <button type="submit" name="delete_provider" class="text-red-600 hover:text-red-900 text-xs font-medium px-2 py-1 bg-red-50 hover:bg-red-100 rounded transition-colors">
                            <i class="fas fa-trash mr-1"></i>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php elseif ($action === \'add\' || $action === \'edit\'): ?>
<!-- Add/Edit Form -->
<div class="modern-card bg-white dark:bg-gray-800 shadow sm:rounded-lg theme-transition">
    <div class="px-4 py-5 sm:p-6">
        <form method="POST" enctype="multipart/form-data" class="space-y-6">
            <?php if ($action === \'edit\'): ?>
            <input type="hidden" name="provider_id" value="<?php echo $editProvider[\'id\']; ?>">
            <?php endif; ?>
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="provider_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Provider Name *</label>
                    <input type="text" name="provider_name" id="provider_name" required 
                           value="<?php echo $editProvider ? htmlspecialchars($editProvider[\'provider_name\']) : \'\'; ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                </div>
                
                <div>
                    <label for="display_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Display Order</label>
                    <input type="number" name="display_order" id="display_order" min="0"
                           value="<?php echo $editProvider ? $editProvider[\'display_order\'] : \'0\'; ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition">
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Lower numbers appear first</p>
                </div>
            </div>
            
            <div>
                <label for="logo_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition">Logo Upload</label>
                
                <!-- Current logo display if exists -->
                <?php if ($editProvider && !empty($editProvider[\'logo_url\'])): ?>
                <div class="mt-2 mb-3">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <img src="<?php echo htmlspecialchars($editProvider[\'logo_url\']); ?>" 
                                 alt="Current logo" 
                                 class="h-12 w-16 object-contain rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700"
                                 onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'flex\';">
                            <div class="h-12 w-16 bg-gray-100 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 flex items-center justify-center" style="display: none;">
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
            </div>
            
            <div class="flex items-center">
                <input type="checkbox" name="is_featured" id="is_featured" value="1"
                       <?php echo ($editProvider && $editProvider[\'is_featured\']) ? \'checked\' : \'\'; ?>
                       class="h-4 w-4 text-brand-blue focus:ring-brand-blue border-gray-300 rounded">
                <label for="is_featured" class="ml-2 block text-sm text-gray-700 dark:text-gray-300 theme-transition">
                    Featured Provider
                    <span class="text-xs text-gray-500 dark:text-gray-400 block">Show prominently on website</span>
                </label>
            </div>
            
            <div class="flex justify-end space-x-3">
                <a href="insurance.php" class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md py-2 px-4 inline-flex justify-center text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 theme-transition">
                    Cancel
                </a>
                <button type="submit" name="<?php echo $action === \'edit\' ? \'update_provider\' : \'add_provider\'; ?>" class="btn-primary inline-flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    <?php echo $action === \'edit\' ? \'Update Provider\' : \'Add Provider\'; ?>
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
';
    
    file_put_contents($contentView, $contentViewCode);
}

// Prepare data for the layout
$layoutData = [
    'pageTitle' => $pageTitle,
    'pageDescription' => $pageDescription,
    'pageActions' => $pageActions,
    'currentUser' => $currentUser,
    'success' => $success,
    'error' => $error,
    'contentFile' => $contentView,
    
    // Data for the content view
    'action' => $action,
    'providers' => $providers,
    'editProvider' => $editProvider
];

// Render the page with layout
renderAdminPage($layoutData['contentFile'], $layoutData);
?>