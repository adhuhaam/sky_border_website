<?php
/**
 * File Upload Handler
 * Sky Border Solutions CMS
 */

class FileUploader {
    private $uploadDir;
    private $allowedTypes;
    private $maxFileSize;
    
    public function __construct($uploadDir = 'uploads/clients/logos/', $maxFileSize = 5242880) { // 5MB default
        $this->uploadDir = rtrim($uploadDir, '/') . '/';
        $this->maxFileSize = $maxFileSize;
        $this->allowedTypes = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg', 
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg'
        ];
        
        // Ensure upload directory exists
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }
    
    /**
     * Upload a file to a specific category directory
     */
    public function uploadFile($file, $category = 'clients', $prefix = '') {
        // Set upload directory based on category
        switch ($category) {
            case 'insurance':
                $this->uploadDir = 'uploads/insurance/';
                break;
            case 'clients':
            default:
                $this->uploadDir = 'uploads/clients/logos/';
                break;
        }
        
        // Ensure upload directory exists
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
        
        // Validate file
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'File upload error.'];
        }
        
        // Validate file type
        if (!array_key_exists($file['type'], $this->allowedTypes)) {
            return ['success' => false, 'error' => 'Invalid file type. Only images are allowed.'];
        }
        
        // Validate file size
        if ($file['size'] > $this->maxFileSize) {
            return ['success' => false, 'error' => 'File size exceeds maximum limit of ' . $this->formatBytes($this->maxFileSize)];
        }
        
        // Generate unique filename
        $extension = $this->allowedTypes[$file['type']];
        $filename = $prefix . time() . '_' . uniqid() . '.' . $extension;
        $filePath = $this->uploadDir . $filename;
        
        // Additional security: validate file content
        if (!$this->validateFileContent($file['tmp_name'], $file['type'])) {
            return ['success' => false, 'error' => 'File validation failed. File may be corrupted.'];
        }
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Set appropriate permissions
            chmod($filePath, 0644);
            return ['success' => true, 'file_path' => $filePath];
        }
        
        return ['success' => false, 'error' => 'Failed to upload file.'];
    }

    /**
     * Upload a file and return the file path or false on error
     */
    public function upload($fileInput, $prefix = '') {
        if (!isset($_FILES[$fileInput]) || $_FILES[$fileInput]['error'] !== UPLOAD_ERR_OK) {
            return false;
        }
        
        $file = $_FILES[$fileInput];
        
        // Validate file type
        if (!array_key_exists($file['type'], $this->allowedTypes)) {
            throw new Exception('Invalid file type. Only ' . implode(', ', array_keys($this->allowedTypes)) . ' are allowed.');
        }
        
        // Validate file size
        if ($file['size'] > $this->maxFileSize) {
            throw new Exception('File size exceeds maximum limit of ' . $this->formatBytes($this->maxFileSize));
        }
        
        // Generate unique filename
        $extension = $this->allowedTypes[$file['type']];
        $filename = $prefix . time() . '_' . uniqid() . '.' . $extension;
        $filePath = $this->uploadDir . $filename;
        
        // Additional security: validate file content
        if (!$this->validateFileContent($file['tmp_name'], $file['type'])) {
            throw new Exception('File validation failed. File may be corrupted or not a valid image.');
        }
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Set appropriate permissions
            chmod($filePath, 0644);
            
            // Return relative path for database storage
            return $filePath;
        }
        
        throw new Exception('Failed to upload file. Please try again.');
    }
    
    /**
     * Delete an uploaded file
     */
    public function delete($filePath) {
        if (file_exists($filePath) && strpos($filePath, $this->uploadDir) === 0) {
            return unlink($filePath);
        }
        return false;
    }
    
    /**
     * Validate file content to ensure it's actually an image
     */
    private function validateFileContent($tmpName, $mimeType) {
        // For SVG files, just check if it's well-formed XML
        if ($mimeType === 'image/svg+xml') {
            $content = file_get_contents($tmpName);
            return $content !== false && strpos($content, '<svg') !== false;
        }
        
        // For other image types, use getimagesize
        $imageInfo = @getimagesize($tmpName);
        if ($imageInfo === false) {
            return false;
        }
        
        // Verify MIME type matches
        $detectedMime = $imageInfo['mime'];
        return $detectedMime === $mimeType;
    }
    
    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    /**
     * Get upload directory path
     */
    public function getUploadDir() {
        return $this->uploadDir;
    }
    
    /**
     * Get allowed file types
     */
    public function getAllowedTypes() {
        return array_keys($this->allowedTypes);
    }
    
    /**
     * Get max file size
     */
    public function getMaxFileSize() {
        return $this->maxFileSize;
    }
}
?>
