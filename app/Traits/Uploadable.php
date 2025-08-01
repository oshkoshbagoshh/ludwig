<?php

namespace App\Traits;

use Exception;

/**
 * Uploadable Trait
 * 
 * Provides file upload functionality for models.
 */
trait Uploadable
{
    /**
     * @var string The upload directory
     */
    protected string $uploadDir = 'storage/uploads';
    
    /**
     * @var array Allowed file types
     */
    protected array $allowedTypes = [
        'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'audio' => ['mp3', 'wav', 'ogg', 'flac', 'm4a'],
        'document' => ['pdf', 'doc', 'docx', 'txt'],
    ];
    
    /**
     * @var int Maximum file size in bytes (default: 10MB)
     */
    protected int $maxFileSize = 10485760; // 10MB
    
    /**
     * Upload a file
     * 
     * @param array $file The uploaded file ($_FILES array item)
     * @param string $type The file type (image, audio, document)
     * @param string|null $customName Custom filename (without extension)
     * @return string The path to the uploaded file
     * @throws Exception If the file upload fails
     */
    public function uploadFile(array $file, string $type, ?string $customName = null): string
    {
        // Validate file
        $this->validateFile($file, $type);
        
        // Generate filename
        $filename = $this->generateFilename($file, $customName);
        
        // Create upload directory if it doesn't exist
        $uploadPath = $this->getUploadPath($type);
        if (!is_dir($uploadPath) && !mkdir($uploadPath, 0755, true) && !is_dir($uploadPath)) {
            throw new Exception("Failed to create upload directory: {$uploadPath}");
        }
        
        // Move uploaded file
        $destination = $uploadPath . '/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception("Failed to upload file");
        }
        
        return $type . '/' . $filename;
    }
    
    /**
     * Validate a file
     * 
     * @param array $file The uploaded file ($_FILES array item)
     * @param string $type The file type (image, audio, document)
     * @return void
     * @throws Exception If the file is invalid
     */
    protected function validateFile(array $file, string $type): void
    {
        // Check if file was uploaded
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            throw new Exception("No file was uploaded");
        }
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors = [
                UPLOAD_ERR_INI_SIZE => "The uploaded file exceeds the upload_max_filesize directive in php.ini",
                UPLOAD_ERR_FORM_SIZE => "The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form",
                UPLOAD_ERR_PARTIAL => "The uploaded file was only partially uploaded",
                UPLOAD_ERR_NO_FILE => "No file was uploaded",
                UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder",
                UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk",
                UPLOAD_ERR_EXTENSION => "A PHP extension stopped the file upload",
            ];
            
            $errorMessage = $errors[$file['error']] ?? "Unknown upload error";
            throw new Exception($errorMessage);
        }
        
        // Check file size
        if ($file['size'] > $this->maxFileSize) {
            $maxSizeMB = $this->maxFileSize / 1048576; // Convert to MB
            throw new Exception("File size exceeds the maximum allowed size ({$maxSizeMB}MB)");
        }
        
        // Check file type
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!isset($this->allowedTypes[$type]) || !in_array($extension, $this->allowedTypes[$type])) {
            $allowedExtensions = implode(', ', $this->allowedTypes[$type] ?? []);
            throw new Exception("Invalid file type. Allowed types: {$allowedExtensions}");
        }
    }
    
    /**
     * Generate a unique filename
     * 
     * @param array $file The uploaded file ($_FILES array item)
     * @param string|null $customName Custom filename (without extension)
     * @return string The generated filename
     */
    protected function generateFilename(array $file, ?string $customName = null): string
    {
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if ($customName) {
            // Sanitize custom name
            $sanitized = preg_replace('/[^a-z0-9]+/', '-', strtolower($customName));
            $sanitized = trim($sanitized, '-');
            return $sanitized . '-' . uniqid('', true) . '.' . $extension;
        }
        
        // Generate a unique name based on the original filename
        $basename = pathinfo($file['name'], PATHINFO_FILENAME);
        $sanitized = preg_replace('/[^a-z0-9]+/', '-', strtolower($basename));
        $sanitized = trim($sanitized, '-');
        
        return $sanitized . '-' . uniqid('', true) . '.' . $extension;
    }
    
    /**
     * Get the upload path for a file type
     * 
     * @param string $type The file type (image, audio, document)
     * @return string The upload path
     */
    protected function getUploadPath(string $type): string
    {
        return $this->uploadDir . '/' . $type;
    }
    
    /**
     * Delete a file
     * 
     * @param string $path The file path relative to the upload directory
     * @return bool True if the file was deleted, false otherwise
     */
    public function deleteFile(string $path): bool
    {
        $fullPath = $this->uploadDir . '/' . $path;
        
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        
        return false;
    }
    
    /**
     * Get the URL for a file
     * 
     * @param string $path The file path relative to the upload directory
     * @return string The file URL
     */
    public function getFileUrl(string $path): string
    {
        // This assumes the upload directory is accessible via the web server
        // Adjust as needed based on your application's URL structure
        return '/uploads/' . $path;
    }
    
    /**
     * Set the upload directory
     * 
     * @param string $uploadDir The upload directory
     * @return self
     */
    public function setUploadDir(string $uploadDir): self
    {
        $this->uploadDir = rtrim($uploadDir, '/');
        return $this;
    }
    
    /**
     * Set the maximum file size
     * 
     * @param int $maxFileSize The maximum file size in bytes
     * @return self
     */
    public function setMaxFileSize(int $maxFileSize): self
    {
        $this->maxFileSize = $maxFileSize;
        return $this;
    }
    
    /**
     * Set the allowed file types
     * 
     * @param array $allowedTypes The allowed file types
     * @return self
     */
    public function setAllowedTypes(array $allowedTypes): self
    {
        $this->allowedTypes = $allowedTypes;
        return $this;
    }
    
    /**
     * Add allowed file types for a specific type
     * 
     * @param string $type The file type (image, audio, document)
     * @param array $extensions The allowed extensions
     * @return self
     */
    public function addAllowedTypes(string $type, array $extensions): self
    {
        if (!isset($this->allowedTypes[$type])) {
            $this->allowedTypes[$type] = [];
        }
        
        $this->allowedTypes[$type] = array_merge($this->allowedTypes[$type], $extensions);
        $this->allowedTypes[$type] = array_unique($this->allowedTypes[$type]);
        
        return $this;
    }
}