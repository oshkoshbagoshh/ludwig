<?php

namespace App\Enums;

/**
 * Payment Status Enumeration
 * 
 * Defines the possible statuses for payments in the system.
 */
enum PaymentStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';
    case CANCELLED = 'cancelled';
    
    /**
     * Get a human-readable label for the payment status
     * 
     * @return string The payment status label
     */
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::PROCESSING => 'Processing',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
            self::REFUNDED => 'Refunded',
            self::CANCELLED => 'Cancelled',
        };
    }
    
    /**
     * Get a description of the payment status
     * 
     * @return string The payment status description
     */
    public function description(): string
    {
        return match($this) {
            self::PENDING => 'Payment has been initiated but not yet processed',
            self::PROCESSING => 'Payment is currently being processed',
            self::COMPLETED => 'Payment has been successfully completed',
            self::FAILED => 'Payment processing has failed',
            self::REFUNDED => 'Payment has been refunded',
            self::CANCELLED => 'Payment has been cancelled',
        };
    }
    
    /**
     * Check if the payment is successful
     * 
     * @return bool True if the payment is successful
     */
    public function isSuccessful(): bool
    {
        return $this === self::COMPLETED;
    }
    
    /**
     * Check if the payment is in progress
     * 
     * @return bool True if the payment is in progress
     */
    public function isInProgress(): bool
    {
        return in_array($this, [self::PENDING, self::PROCESSING]);
    }
    
    /**
     * Check if the payment has failed
     * 
     * @return bool True if the payment has failed
     */
    public function hasFailed(): bool
    {
        return in_array($this, [self::FAILED, self::CANCELLED]);
    }
    
    /**
     * Check if the payment has been refunded
     * 
     * @return bool True if the payment has been refunded
     */
    public function isRefunded(): bool
    {
        return $this === self::REFUNDED;
    }
    
    /**
     * Get all available payment statuses
     * 
     * @return array<PaymentStatus> Array of all payment statuses
     */
    public static function all(): array
    {
        return [
            self::PENDING,
            self::PROCESSING,
            self::COMPLETED,
            self::FAILED,
            self::REFUNDED,
            self::CANCELLED,
        ];
    }
    
    /**
     * Get all final payment statuses (no further processing expected)
     * 
     * @return array<PaymentStatus> Array of final payment statuses
     */
    public static function final(): array
    {
        return [
            self::COMPLETED,
            self::FAILED,
            self::REFUNDED,
            self::CANCELLED,
        ];
    }
}