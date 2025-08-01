<?php

namespace App\Core;

/**
 * Logger
 * 
 * Handles application logging
 */
class Logger
{
    /**
     * Log levels
     */
    const DEBUG = 'DEBUG';
    const INFO = 'INFO';
    const WARNING = 'WARNING';
    const ERROR = 'ERROR';
    const CRITICAL = 'CRITICAL';
    
    /**
     * @var string
     */
    private string $logDirectory;
    
    /**
     * @var string
     */
    private string $logFile;
    
    /**
     * @var string
     */
    private string $minLevel;
    
    /**
     * @var array
     */
    private array $levelPriority = [
        self::DEBUG => 0,
        self::INFO => 1,
        self::WARNING => 2,
        self::ERROR => 3,
        self::CRITICAL => 4
    ];
    
    /**
     * @var Logger|null
     */
    private static ?Logger $instance = null;
    
    /**
     * Get the logger instance
     * 
     * @return Logger
     */
    public static function getInstance(): Logger
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * Constructor
     * 
     * @param string $logDirectory Log directory
     * @param string $minLevel Minimum log level
     */
    private function __construct(string $logDirectory = null, string $minLevel = self::DEBUG)
    {
        $this->logDirectory = $logDirectory ?? dirname(__DIR__, 2) . '/storage/logs';
        $this->minLevel = $minLevel;
        
        // Create log directory if it doesn't exist
        if (!is_dir($this->logDirectory)) {
            if (!mkdir($this->logDirectory, 0755, true) && !is_dir($this->logDirectory)) {
                throw new \Exception('Failed to create log directory');
            }
        }
        
        // Set the log file name based on the current date
        $this->logFile = $this->logDirectory . '/' . date('Y-m-d') . '.log';
    }
    
    /**
     * Log a message
     * 
     * @param string $level Log level
     * @param string $message Log message
     * @param array $context Additional context data
     * @return bool True if the message was logged, false otherwise
     */
    public function log(string $level, string $message, array $context = []): bool
    {
        // Check if the log level is high enough
        if ($this->levelPriority[$level] < $this->levelPriority[$this->minLevel]) {
            return false;
        }
        
        // Format the log message
        $timestamp = date('Y-m-d H:i:s');
        $formattedMessage = "[$timestamp] [$level] $message";
        
        // Add context data if provided
        if (!empty($context)) {
            $formattedMessage .= ' ' . json_encode($context);
        }
        
        $formattedMessage .= PHP_EOL;
        
        // Write to the log file
        return file_put_contents($this->logFile, $formattedMessage, FILE_APPEND) !== false;
    }
    
    /**
     * Log a debug message
     * 
     * @param string $message Log message
     * @param array $context Additional context data
     * @return bool True if the message was logged, false otherwise
     */
    public function debug(string $message, array $context = []): bool
    {
        return $this->log(self::DEBUG, $message, $context);
    }
    
    /**
     * Log an info message
     * 
     * @param string $message Log message
     * @param array $context Additional context data
     * @return bool True if the message was logged, false otherwise
     */
    public function info(string $message, array $context = []): bool
    {
        return $this->log(self::INFO, $message, $context);
    }
    
    /**
     * Log a warning message
     * 
     * @param string $message Log message
     * @param array $context Additional context data
     * @return bool True if the message was logged, false otherwise
     */
    public function warning(string $message, array $context = []): bool
    {
        return $this->log(self::WARNING, $message, $context);
    }
    
    /**
     * Log an error message
     * 
     * @param string $message Log message
     * @param array $context Additional context data
     * @return bool True if the message was logged, false otherwise
     */
    public function error(string $message, array $context = []): bool
    {
        return $this->log(self::ERROR, $message, $context);
    }
    
    /**
     * Log a critical message
     * 
     * @param string $message Log message
     * @param array $context Additional context data
     * @return bool True if the message was logged, false otherwise
     */
    public function critical(string $message, array $context = []): bool
    {
        return $this->log(self::CRITICAL, $message, $context);
    }
    
    /**
     * Set the minimum log level
     * 
     * @param string $level Minimum log level
     * @return self
     */
    public function setMinLevel(string $level): self
    {
        $this->minLevel = $level;
        return $this;
    }
    
    /**
     * Get the minimum log level
     * 
     * @return string Minimum log level
     */
    public function getMinLevel(): string
    {
        return $this->minLevel;
    }
    
    /**
     * Set the log directory
     * 
     * @param string $directory Log directory
     * @return self
     */
    public function setLogDirectory(string $directory): self
    {
        $this->logDirectory = $directory;
        return $this;
    }
    
    /**
     * Get the log directory
     * 
     * @return string Log directory
     */
    public function getLogDirectory(): string
    {
        return $this->logDirectory;
    }
    
    /**
     * Get the current log file
     * 
     * @return string Log file path
     */
    public function getLogFile(): string
    {
        return $this->logFile;
    }
}