<?php

namespace App\Enums;

/**
 * Track Status Enumeration
 * 
 * Defines the possible statuses of a track in the system.
 */
enum TrackStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case FEATURED = 'featured';
    case ARCHIVED = 'archived';
    
    /**
     * Get a human-readable label for the status
     * 
     * @return string The status label
     */
    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::PUBLISHED => 'Published',
            self::FEATURED => 'Featured',
            self::ARCHIVED => 'Archived',
        };
    }
    
    /**
     * Get a description of the status
     * 
     * @return string The status description
     */
    public function description(): string
    {
        return match($this) {
            self::DRAFT => 'Track is in draft mode and not visible to the public',
            self::PUBLISHED => 'Track is published and visible to the public',
            self::FEATURED => 'Track is published and featured on the platform',
            self::ARCHIVED => 'Track is archived and no longer visible to the public',
        };
    }
    
    /**
     * Check if the track is visible to the public
     * 
     * @return bool True if the track is visible
     */
    public function isVisible(): bool
    {
        return match($this) {
            self::DRAFT, self::ARCHIVED => false,
            self::PUBLISHED, self::FEATURED => true,
        };
    }
    
    /**
     * Get all available statuses
     * 
     * @return array<TrackStatus> Array of all statuses
     */
    public static function all(): array
    {
        return [
            self::DRAFT,
            self::PUBLISHED,
            self::FEATURED,
            self::ARCHIVED,
        ];
    }
    
    /**
     * Get all visible statuses
     * 
     * @return array<TrackStatus> Array of visible statuses
     */
    public static function visible(): array
    {
        return [
            self::PUBLISHED,
            self::FEATURED,
        ];
    }
}