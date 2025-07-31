<?php

/**
 * Main configuration file for the CMS + Music Platform MVP
 * 
 * This file loads environment variables from .env file and provides
 * configuration arrays for different parts of the application.
 */

// Load environment variables
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Helper function to get environment variables with fallback
function env($key, $default = null) {
    return isset($_ENV[$key]) ? $_ENV[$key] : $default;
}

// Application configuration
$config = [];

// App settings
$config['app'] = [
    'env' => env('APP_ENV', 'production'),
    'debug' => env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost:8000'),
];

// Database configuration
$config['database'] = [
    'connection' => env('DB_CONNECTION', 'mysql'),
    'mysql' => [
        'host' => env('DB_HOST', 'localhost'),
        'port' => env('DB_PORT', 3306),
        'database' => env('DB_DATABASE', 'music_platform'),
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],
    'sqlite' => [
        'path' => env('SQLITE_PATH', 'database/music.sqlite'),
    ],
];

// Authentication settings
$config['auth'] = [
    'session_lifetime' => env('AUTH_SESSION_LIFETIME', 7200),
];

// File upload settings
$config['upload'] = [
    'max_size' => env('UPLOAD_MAX_SIZE', 10485760), // 10MB
    'allowed_types' => explode(',', env('UPLOAD_ALLOWED_TYPES', 'mp3,wav,ogg,mp4,m4a')),
    'path' => env('UPLOAD_PATH', 'storage/uploads'),
];

// Audio preview settings
$config['audio'] = [
    'preview_duration' => env('PREVIEW_DURATION', 10), // seconds for unregistered users
];

// Email configuration
$config['mail'] = [
    'driver' => env('MAIL_DRIVER', 'smtp'),
    'host' => env('MAIL_HOST', 'smtp.mailtrap.io'),
    'port' => env('MAIL_PORT', 2525),
    'username' => env('MAIL_USERNAME', null),
    'password' => env('MAIL_PASSWORD', null),
    'encryption' => env('MAIL_ENCRYPTION', null),
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Music Platform'),
    ],
];

// Slack webhook
$config['slack'] = [
    'webhook_url' => env('SLACK_WEBHOOK_URL', ''),
];

// API keys
$config['api'] = [
    'key' => env('API_KEY', ''),
];

// Logging
$config['logging'] = [
    'channel' => env('LOG_CHANNEL', 'file'),
    'level' => env('LOG_LEVEL', 'debug'),
    'path' => env('LOG_PATH', 'storage/logs'),
];

// Return the configuration array
return $config;


