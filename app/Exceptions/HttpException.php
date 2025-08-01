<?php

namespace App\Exceptions;

use Exception;

/**
 * HTTP Exception
 * 
 * Base class for HTTP exceptions
 */
class HttpException extends Exception
{
    /**
     * @var int
     */
    protected int $statusCode;
    
    /**
     * Constructor
     * 
     * @param string $message Exception message
     * @param int $statusCode HTTP status code
     * @param int $code Exception code
     * @param Exception|null $previous Previous exception
     */
    public function __construct(string $message = '', int $statusCode = 500, int $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->statusCode = $statusCode;
    }
    
    /**
     * Get the HTTP status code
     * 
     * @return int HTTP status code
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}