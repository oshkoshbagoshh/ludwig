<?php

namespace App\Services;

use App\Interfaces\UploaderInterface;
use Exception;

/**
 * File Service
 * 
 * Handles file uploads and media management
 */
class FileService implements UploaderInterface
{
    /**
     * @var string
     */
    private string $uploadDirectory;
    
    /**
     * @var array
     */
    private array $allowedTypes;
    
    /**
     * @var int
     */
    private int $maxFileSize;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->uploadDirectory = dirname(__DIR__, 2) . '/storage/uploads';
        $this->allowedTypes = [
            'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            'audio' => ['mp3', 'wav', 'ogg', 'm4a', 'flac'],
            'document' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'csv'],
            'video' => ['mp4', 'webm', 'mov', 'avi']
        ];
        $this->maxFileSize = 50 * 1024 * 1024; // 50MB
    }
    
    /**
     * Upload a file
     * 
     * @param array $file The uploaded file ($_FILES array item)
     * @param string $type The file type (image, audio, document)
     * @param string|null $customName Custom filename (without extension)
     * @return string The path to the uploaded file
     * @throws Exception If the file upload fails
     */
    public function upload(array $file, string $type, ?string $customName = null): string
    {
        // Validate the file
        if (!$this->validate($file, $type)) {
            throw new Exception('Invalid file');
        }
        
        // Create the upload directory if it doesn't exist
        $typeDirectory = $this->uploadDirectory . '/' . $type;
        if (!is_dir($typeDirectory)) {
            if (!mkdir($typeDirectory, 0755, true) && !is_dir($typeDirectory)) {
                throw new Exception('Failed to create upload directory');
            }
        }
        
        // Generate a filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $customName ? $customName . '.' . $extension : uniqid('', true) . '.' . $extension;
        
        // Upload the file
        $destination = $typeDirectory . '/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception('Failed to upload file');
        }
        
        // Return the relative path
        return $type . '/' . $filename;
    }
    
    /**
     * Validate a file
     * 
     * @param array $file The uploaded file ($_FILES array item)
     * @param string $type The file type (image, audio, document)
     * @return bool True if the file is valid, false otherwise
     */
    public function validate(array $file, string $type): bool
    {
        // Check if the file was uploaded
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return false;
        }
        
        // Check if the file type is allowed
        if (!isset($this->allowedTypes[$type])) {
            return false;
        }
        
        // Check the file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->allowedTypes[$type])) {
            return false;
        }
        
        // Check the file size
        if ($file['size'] > $this->maxFileSize) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Delete a file
     * 
     * @param string $path The file path
     * @return bool True if the file was deleted, false otherwise
     */
    public function delete(string $path): bool
    {
        $fullPath = $this->uploadDirectory . '/' . $path;
        
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        
        return false;
    }
    
    /**
     * Get the URL for a file
     * 
     * @param string $path The file path
     * @return string The file URL
     */
    public function getUrl(string $path): string
    {
        // Determine the base URL from the server variables
        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $baseUrl = $protocol . $_SERVER['HTTP_HOST'];
        
        return $baseUrl . '/storage/uploads/' . $path;
    }
    
    /**
     * Set the upload directory
     * 
     * @param string $directory The upload directory
     * @return self
     */
    public function setUploadDirectory(string $directory): self
    {
        $this->uploadDirectory = $directory;
        return $this;
    }
    
    /**
     * Get the upload directory
     * 
     * @return string The upload directory
     */
    public function getUploadDirectory(): string
    {
        return $this->uploadDirectory;
    }
    
    /**
     * Set the allowed file types
     * 
     * @param array $types The allowed file types
     * @return self
     */
    public function setAllowedTypes(array $types): self
    {
        $this->allowedTypes = $types;
        return $this;
    }
    
    /**
     * Get the allowed file types
     * 
     * @return array The allowed file types
     */
    public function getAllowedTypes(): array
    {
        return $this->allowedTypes;
    }
    
    /**
     * Set the maximum file size
     * 
     * @param int $size The maximum file size in bytes
     * @return self
     */
    public function setMaxFileSize(int $size): self
    {
        $this->maxFileSize = $size;
        return $this;
    }
    
    /**
     * Get the maximum file size
     * 
     * @return int The maximum file size in bytes
     */
    public function getMaxFileSize(): int
    {
        return $this->maxFileSize;
    }
}