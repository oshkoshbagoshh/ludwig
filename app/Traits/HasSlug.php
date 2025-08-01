<?php

namespace App\Traits;

/**
 * HasSlug Trait
 * 
 * Provides slug functionality for models.
 */
trait HasSlug
{
    /**
     * @var string The slug
     */
    protected string $slug = '';
    
    /**
     * @var string The field to generate the slug from
     */
    protected string $slugSource = 'title';
    
    /**
     * Generate a slug from a string
     * 
     * @param string $string The string to generate the slug from
     * @return string The generated slug
     */
    public function generateSlug(string $string): string
    {
        // Convert to lowercase
        $slug = strtolower($string);
        
        // Replace non-alphanumeric characters with hyphens
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        
        // Remove leading and trailing hyphens
        $slug = trim($slug, '-');
        
        // Ensure the slug is not empty
        if (empty($slug)) {
            $slug = 'untitled-' . uniqid('', true);
        }
        
        return $slug;
    }
    
    /**
     * Set the slug
     * 
     * @param string $slug The slug
     * @return self
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $this->generateSlug($slug);
        return $this;
    }
    
    /**
     * Get the slug
     * 
     * @return string The slug
     */
    public function getSlug(): string
    {
        return $this->slug;
    }
    
    /**
     * Set the slug source field
     * 
     * @param string $field The field to generate the slug from
     * @return self
     */
    public function setSlugSource(string $field): self
    {
        $this->slugSource = $field;
        return $this;
    }
    
    /**
     * Get the slug source field
     * 
     * @return string The field to generate the slug from
     */
    public function getSlugSource(): string
    {
        return $this->slugSource;
    }
    
    /**
     * Generate a slug from the source field
     * 
     * @return self
     */
    public function generateSlugFromSource(): self
    {
        $source = $this->{$this->slugSource} ?? '';
        
        if (!empty($source)) {
            $this->setSlug($source);
        }
        
        return $this;
    }
    
    /**
     * Ensure the slug is unique
     * 
     * @param callable $checkUnique Function to check if a slug is unique
     * @return self
     */
    public function ensureUniqueSlug(callable $checkUnique): self
    {
        $originalSlug = $this->slug;
        $counter = 1;
        
        // Keep incrementing the counter until we find a unique slug
        while (!$checkUnique($this->slug)) {
            $this->slug = $originalSlug . '-' . $counter++;
        }
        
        return $this;
    }
    
    /**
     * Get the URL for the model
     * 
     * @param string $prefix The URL prefix
     * @return string The URL
     */
    public function getUrl(string $prefix = ''): string
    {
        $prefix = rtrim($prefix, '/');
        return $prefix . '/' . $this->slug;
    }
}