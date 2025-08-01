<?php

namespace App\Exceptions;

use App\Core\Logger;
use Exception;
use Throwable;

/**
 * Exception Handler
 * 
 * Handles application exceptions
 */
class ExceptionHandler
{
    /**
     * @var Logger
     */
    private Logger $logger;
    
    /**
     * @var bool
     */
    private bool $debug;
    
    /**
     * @var ExceptionHandler|null
     */
    private static ?ExceptionHandler $instance = null;
    
    /**
     * Get the exception handler instance
     * 
     * @return ExceptionHandler
     */
    public static function getInstance(): ExceptionHandler
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * Constructor
     * 
     * @param bool $debug Whether to show debug information
     */
    private function __construct(bool $debug = false)
    {
        $this->logger = Logger::getInstance();
        $this->debug = $debug;
        
        // Register exception handlers
        set_exception_handler([$this, 'handleException']);
        set_error_handler([$this, 'handleError']);
        register_shutdown_function([$this, 'handleShutdown']);
    }
    
    /**
     * Handle an exception
     * 
     * @param Throwable $exception The exception to handle
     * @return void
     */
    public function handleException(Throwable $exception): void
    {
        // Log the exception
        $this->logException($exception);
        
        // Display an error page
        $this->displayError($exception);
    }
    
    /**
     * Handle an error
     * 
     * @param int $level Error level
     * @param string $message Error message
     * @param string $file File where the error occurred
     * @param int $line Line where the error occurred
     * @return bool Whether the error was handled
     */
    public function handleError(int $level, string $message, string $file, int $line): bool
    {
        // Check if error reporting is disabled for this error level
        if (!(error_reporting() & $level)) {
            return false;
        }
        
        // Convert the error to an exception
        throw new \ErrorException($message, 0, $level, $file, $line);
    }
    
    /**
     * Handle a shutdown
     * 
     * @return void
     */
    public function handleShutdown(): void
    {
        // Check if a fatal error occurred
        $error = error_get_last();
        
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            // Convert the error to an exception
            $exception = new \ErrorException(
                $error['message'],
                0,
                $error['type'],
                $error['file'],
                $error['line']
            );
            
            // Handle the exception
            $this->handleException($exception);
        }
    }
    
    /**
     * Log an exception
     * 
     * @param Throwable $exception The exception to log
     * @return void
     */
    private function logException(Throwable $exception): void
    {
        $message = sprintf(
            '%s: %s in %s on line %d',
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );
        
        $context = [
            'trace' => $exception->getTraceAsString(),
            'code' => $exception->getCode()
        ];
        
        $this->logger->error($message, $context);
    }
    
    /**
     * Display an error page
     * 
     * @param Throwable $exception The exception to display
     * @return void
     */
    private function displayError(Throwable $exception): void
    {
        // Clear any output buffers
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        
        // Set the HTTP status code
        http_response_code(500);
        
        // Check if the request is an AJAX request
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        
        if ($isAjax) {
            // Return a JSON error response
            header('Content-Type: application/json');
            
            $response = [
                'error' => true,
                'message' => $this->debug ? $exception->getMessage() : 'An error occurred'
            ];
            
            if ($this->debug) {
                $response['trace'] = $exception->getTraceAsString();
                $response['file'] = $exception->getFile();
                $response['line'] = $exception->getLine();
            }
            
            echo json_encode($response);
        } else {
            // Display an HTML error page
            $title = 'Application Error';
            $message = $this->debug ? $exception->getMessage() : 'An error occurred';
            
            echo '<!DOCTYPE html>';
            echo '<html>';
            echo '<head>';
            echo '<meta charset="UTF-8">';
            echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
            echo '<title>' . $title . '</title>';
            echo '<style>';
            echo 'body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; }';
            echo '.error-container { max-width: 800px; margin: 0 auto; background: #f8f8f8; border: 1px solid #ddd; padding: 20px; border-radius: 5px; }';
            echo 'h1 { color: #e74c3c; margin-top: 0; }';
            echo '.trace { background: #f1f1f1; padding: 15px; border-radius: 3px; overflow-x: auto; font-family: monospace; font-size: 14px; }';
            echo '</style>';
            echo '</head>';
            echo '<body>';
            echo '<div class="error-container">';
            echo '<h1>' . $title . '</h1>';
            echo '<p>' . $message . '</p>';
            
            if ($this->debug) {
                echo '<h2>Details</h2>';
                echo '<p><strong>File:</strong> ' . $exception->getFile() . ' (line ' . $exception->getLine() . ')</p>';
                echo '<h2>Stack Trace</h2>';
                echo '<div class="trace">' . nl2br($exception->getTraceAsString()) . '</div>';
            }
            
            echo '</div>';
            echo '</body>';
            echo '</html>';
        }
    }
    
    /**
     * Set debug mode
     * 
     * @param bool $debug Whether to show debug information
     * @return self
     */
    public function setDebug(bool $debug): self
    {
        $this->debug = $debug;
        return $this;
    }
    
    /**
     * Get debug mode
     * 
     * @return bool Whether debug mode is enabled
     */
    public function getDebug(): bool
    {
        return $this->debug;
    }
}