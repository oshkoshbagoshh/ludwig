<?php

namespace App\Interfaces;

/**
 * Cacheable Interface
 * 
 * Defines the contract for cacheable data classes.
 */
interface CacheableInterface
{
    /**
     * Get a cached item
     * 
     * @param string $key The cache key
     * @param mixed $default The default value if the key doesn't exist
     * @return mixed The cached value or default
     */
    public function get(string $key, mixed $default = null): mixed;
    
    /**
     * Set a cached item
     * 
     * @param string $key The cache key
     * @param mixed $value The value to cache
     * @param int|null $ttl Time to live in seconds (null for forever)
     * @return bool True if the item was set, false otherwise
     */
    public function set(string $key, mixed $value, ?int $ttl = null): bool;
    
    /**
     * Check if a cached item exists
     * 
     * @param string $key The cache key
     * @return bool True if the key exists, false otherwise
     */
    public function has(string $key): bool;
    
    /**
     * Delete a cached item
     * 
     * @param string $key The cache key
     * @return bool True if the item was deleted, false otherwise
     */
    public function delete(string $key): bool;
    
    /**
     * Clear all cached items
     * 
     * @return bool True if the cache was cleared, false otherwise
     */
    public function clear(): bool;
    
    /**
     * Get multiple cached items
     * 
     * @param array $keys The cache keys
     * @param mixed $default The default value if a key doesn't exist
     * @return array The cached values
     */
    public function getMultiple(array $keys, mixed $default = null): array;
    
    /**
     * Set multiple cached items
     * 
     * @param array $values The values to cache (key => value pairs)
     * @param int|null $ttl Time to live in seconds (null for forever)
     * @return bool True if the items were set, false otherwise
     */
    public function setMultiple(array $values, ?int $ttl = null): bool;
    
    /**
     * Delete multiple cached items
     * 
     * @param array $keys The cache keys
     * @return bool True if the items were deleted, false otherwise
     */
    public function deleteMultiple(array $keys): bool;
    
    /**
     * Get or set a cached item
     * 
     * @param string $key The cache key
     * @param callable $callback The callback to generate the value if not cached
     * @param int|null $ttl Time to live in seconds (null for forever)
     * @return mixed The cached value
     */
    public function remember(string $key, callable $callback, ?int $ttl = null): mixed;
    
    /**
     * Get and delete a cached item
     * 
     * @param string $key The cache key
     * @param mixed $default The default value if the key doesn't exist
     * @return mixed The cached value or default
     */
    public function pull(string $key, mixed $default = null): mixed;
    
    /**
     * Increment a cached value
     * 
     * @param string $key The cache key
     * @param int $value The value to increment by
     * @return int|bool The new value or false on failure
     */
    public function increment(string $key, int $value = 1): int|bool;
    
    /**
     * Decrement a cached value
     * 
     * @param string $key The cache key
     * @param int $value The value to decrement by
     * @return int|bool The new value or false on failure
     */
    public function decrement(string $key, int $value = 1): int|bool;
}