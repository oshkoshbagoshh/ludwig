<?php

namespace App\Enums;

/**
 * License Type Enumeration
 * 
 * Defines the possible license types for music tracks.
 */
enum LicenseType: string
{
    case STANDARD = 'standard';
    case PREMIUM = 'premium';
    case EXCLUSIVE = 'exclusive';
    case CUSTOM = 'custom';
    
    /**
     * Get a human-readable label for the license type
     * 
     * @return string The license type label
     */
    public function label(): string
    {
        return match($this) {
            self::STANDARD => 'Standard License',
            self::PREMIUM => 'Premium License',
            self::EXCLUSIVE => 'Exclusive License',
            self::CUSTOM => 'Custom License',
        };
    }
    
    /**
     * Get a description of the license type
     * 
     * @return string The license type description
     */
    public function description(): string
    {
        return match($this) {
            self::STANDARD => 'Basic license for personal use only',
            self::PREMIUM => 'Extended license for commercial use with limitations',
            self::EXCLUSIVE => 'Full exclusive rights transfer',
            self::CUSTOM => 'Custom negotiated license terms',
        };
    }
    
    /**
     * Get the default price for the license type
     * 
     * @return float The default price
     */
    public function defaultPrice(): float
    {
        return match($this) {
            self::STANDARD => 29.99,
            self::PREMIUM => 99.99,
            self::EXCLUSIVE => 499.99,
            self::CUSTOM => 0.00, // Custom pricing is negotiated
        };
    }
    
    /**
     * Check if the license allows commercial use
     * 
     * @return bool True if commercial use is allowed
     */
    public function allowsCommercialUse(): bool
    {
        return match($this) {
            self::STANDARD => false,
            self::PREMIUM, self::EXCLUSIVE, self::CUSTOM => true,
        };
    }
    
    /**
     * Check if the license is exclusive
     * 
     * @return bool True if the license is exclusive
     */
    public function isExclusive(): bool
    {
        return match($this) {
            self::EXCLUSIVE => true,
            self::STANDARD, self::PREMIUM, self::CUSTOM => false,
        };
    }
    
    /**
     * Get all available license types
     * 
     * @return array<LicenseType> Array of all license types
     */
    public static function all(): array
    {
        return [
            self::STANDARD,
            self::PREMIUM,
            self::EXCLUSIVE,
            self::CUSTOM,
        ];
    }
}