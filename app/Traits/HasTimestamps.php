<?php

namespace App\Traits;

/**
 * HasTimestamps Trait
 * 
 * Provides timestamp functionality for models.
 */
trait HasTimestamps
{
    /**
     * @var string The created at timestamp
     */
    protected string $createdAt;
    
    /**
     * @var string The updated at timestamp
     */
    protected string $updatedAt;
    
    /**
     * Initialize timestamps
     * 
     * @return void
     */
    public function initializeTimestamps(): void
    {
        $now = date('Y-m-d H:i:s');
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }
    
    /**
     * Update the updated_at timestamp
     * 
     * @return void
     */
    public function touch(): void
    {
        $this->updatedAt = date('Y-m-d H:i:s');
    }
    
    /**
     * Get the created at timestamp
     * 
     * @return string The created at timestamp
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
    
    /**
     * Get the updated at timestamp
     * 
     * @return string The updated at timestamp
     */
    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }
    
    /**
     * Set the created at timestamp
     * 
     * @param string $createdAt The created at timestamp
     * @return self
     */
    public function setCreatedAt(string $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
    
    /**
     * Set the updated at timestamp
     * 
     * @param string $updatedAt The updated at timestamp
     * @return self
     */
    public function setUpdatedAt(string $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
    
    /**
     * Format a timestamp for display
     * 
     * @param string $timestamp The timestamp to format
     * @param string $format The format to use (default: Y-m-d H:i:s)
     * @return string The formatted timestamp
     */
    public function formatTimestamp(string $timestamp, string $format = 'Y-m-d H:i:s'): string
    {
        $date = new \DateTime($timestamp);
        return $date->format($format);
    }
    
    /**
     * Get the formatted created at timestamp
     * 
     * @param string $format The format to use (default: Y-m-d H:i:s)
     * @return string The formatted created at timestamp
     */
    public function getFormattedCreatedAt(string $format = 'Y-m-d H:i:s'): string
    {
        return $this->formatTimestamp($this->createdAt, $format);
    }
    
    /**
     * Get the formatted updated at timestamp
     * 
     * @param string $format The format to use (default: Y-m-d H:i:s)
     * @return string The formatted updated at timestamp
     */
    public function getFormattedUpdatedAt(string $format = 'Y-m-d H:i:s'): string
    {
        return $this->formatTimestamp($this->updatedAt, $format);
    }
    
    /**
     * Get the time elapsed since creation
     * 
     * @return string The time elapsed since creation
     */
    public function getTimeElapsedSinceCreation(): string
    {
        return $this->getTimeElapsedSince($this->createdAt);
    }
    
    /**
     * Get the time elapsed since last update
     * 
     * @return string The time elapsed since last update
     */
    public function getTimeElapsedSinceUpdate(): string
    {
        return $this->getTimeElapsedSince($this->updatedAt);
    }
    
    /**
     * Get the time elapsed since a timestamp
     * 
     * @param string $timestamp The timestamp to compare against
     * @return string The time elapsed
     */
    protected function getTimeElapsedSince(string $timestamp): string
    {
        $datetime = new \DateTime($timestamp);
        $now = new \DateTime();
        $interval = $datetime->diff($now);
        
        if ($interval->y > 0) {
            return $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' ago';
        }
        
        if ($interval->m > 0) {
            return $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' ago';
        }
        
        if ($interval->d > 0) {
            return $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
        }
        
        if ($interval->h > 0) {
            return $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ago';
        }
        
        if ($interval->i > 0) {
            return $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') . ' ago';
        }
        
        return 'just now';
    }
}