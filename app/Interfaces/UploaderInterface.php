<?php

namespace App\Interfaces;

/**
 * Uploader Interface
 * 
 * Defines the contract for file uploader classes.
 */
interface UploaderInterface
{
    /**
     * Upload a file
     * 
     * @param array $file The uploaded file ($_FILES array item)
     * @param string $type The file type (image, audio, document)
     * @param string|null $customName Custom filename (without extension)
     * @return string The path to the uploaded file
     * @throws \Exception If the file upload fails
     */
    public function upload(array $file, string $type, ?string $customName = null): string;
    
    /**
     * Validate a file
     * 
     * @param array $file The uploaded file ($_FILES array item)
     * @param string $type The file type (image, audio, document)
     * @return bool True if the file is valid, false otherwise
     */
    public function validate(array $file, string $type): bool;
    
    /**
     * Delete a file
     * 
     * @param string $path The file path
     * @return bool True if the file was deleted, false otherwise
     */
    public function delete(string $path): bool;
    
    /**
     * Get the URL for a file
     * 
     * @param string $path The file path
     * @return string The file URL
     */
    public function getUrl(string $path): string;
    
    /**
     * Set the upload directory
     * 
     * @param string $directory The upload directory
     * @return self
     */
    public function setUploadDirectory(string $directory): self;
    
    /**
     * Get the upload directory
     * 
     * @return string The upload directory
     */
    public function getUploadDirectory(): string;
    
    /**
     * Set the allowed file types
     * 
     * @param array $types The allowed file types
     * @return self
     */
    public function setAllowedTypes(array $types): self;
    
    /**
     * Get the allowed file types
     * 
     * @return array The allowed file types
     */
    public function getAllowedTypes(): array;
    
    /**
     * Set the maximum file size
     * 
     * @param int $size The maximum file size in bytes
     * @return self
     */
    public function setMaxFileSize(int $size): self;
    
    /**
     * Get the maximum file size
     * 
     * @return int The maximum file size in bytes
     */
    public function getMaxFileSize(): int;
}