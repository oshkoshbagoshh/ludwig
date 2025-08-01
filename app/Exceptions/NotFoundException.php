<?php

namespace App\Exceptions;

/**
 * Not Found Exception
 * 
 * Thrown when a requested resource is not found
 */
class NotFoundException extends HttpException
{
    /**
     * Constructor
     * 
     * @param string $message Exception message
     * @param int $code Exception code
     * @param \Exception|null $previous Previous exception
     */
    public function __construct(string $message = 'Resource not found', int $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, 404, $code, $previous);
    }
}