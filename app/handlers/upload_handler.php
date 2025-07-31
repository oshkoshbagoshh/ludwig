<?php

require_once __DIR__ . '/../Utils/FtpUploader.php';

function handleFileUpload($file): array
{
    try {
        // Local upload first
        $uploadDir = __DIR__ . '/../../uploads/';
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $uploadDir));
            };
        }

        $fileName = time() . '_' . basename($file['name']);
        $localPath = $uploadDir . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $localPath)) {
            throw new Exception("Failed to move uploaded file.");
        }

        // Load FTP config
        $ftpConfig = require __DIR__ . '/../../config/ftp.php';
        
        // Upload to FTP
        $ftpUploader = new FtpUploader(
            $ftpConfig['host'],
            $ftpConfig['username'],
            $ftpConfig['password']
        );

        $ftpUploader->connect();
        $remotePath = $ftpConfig['upload_path'] . $fileName;
        $ftpUploader->uploadFile($localPath, $remotePath);
        $ftpUploader->disconnect();

        return [
            'success' => true,
            'filename' => $fileName,
            'path' => $remotePath
        ];

    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}