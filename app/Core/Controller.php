<?php

namespace App\Core;

use App\Core\Database;

/**
 * Base Controller class
 * 
 * Provides common functionality for all controllers.
 */
abstract class Controller
{
    /**
     * @var Database The database instance
     */
    protected Database $db;
    
    /**
     * @var array The configuration array
     */
    protected array $config;
    
    /**
     * Constructor
     * 
     * @param Database|null $db Optional database instance
     */
    public function __construct(?Database $db = null)
    {
        // Load configuration
        $this->config = require __DIR__ . '/../../config.php';
        
        // Set database instance
        $this->db = $db ?? new Database();
    }
    
    /**
     * Render a JSON response
     * 
     * @param mixed $data The data to encode as JSON
     * @param int $statusCode The HTTP status code
     * @return void
     */
    protected function jsonResponse($data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Render a view
     * 
     * @param string $view The view file to render
     * @param array $data The data to pass to the view
     * @return void
     */
    protected function render(string $view, array $data = []): void
    {
        // Extract data to make variables available in the view
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        $viewPath = __DIR__ . '/../../views/' . $view . '.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            throw new \Exception("View not found: {$view}");
        }
        
        // Get the content and clean the buffer
        $content = ob_get_clean();
        
        // Include the layout
        include __DIR__ . '/../../views/layout.php';
        exit;
    }
    
    /**
     * Redirect to another URL
     * 
     * @param string $url The URL to redirect to
     * @return void
     */
    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }
    
    /**
     * Get a request parameter
     * 
     * @param string $key The parameter key
     * @param mixed $default The default value if the parameter is not found
     * @return mixed The parameter value or default
     */
    protected function getParam(string $key, $default = null)
    {
        return $_REQUEST[$key] ?? $default;
    }
    
    /**
     * Get all request parameters
     * 
     * @return array The request parameters
     */
    protected function getParams(): array
    {
        return $_REQUEST;
    }
    
    /**
     * Check if the request is an AJAX request
     * 
     * @return bool True if the request is an AJAX request
     */
    protected function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Get the request method
     * 
     * @return string The request method (GET, POST, etc.)
     */
    protected function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }
    
    /**
     * Check if the request method is GET
     * 
     * @return bool True if the request method is GET
     */
    protected function isGet(): bool
    {
        return $this->getMethod() === 'GET';
    }
    
    /**
     * Check if the request method is POST
     * 
     * @return bool True if the request method is POST
     */
    protected function isPost(): bool
    {
        return $this->getMethod() === 'POST';
    }
    
    /**
     * Sanitize input data
     * 
     * @param string $input The input to sanitize
     * @return string The sanitized input
     */
    protected function sanitize(string $input): string
    {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }
}