<?php

namespace App\Interfaces;

/**
 * Service Interface
 * 
 * Base interface for all service classes in the application
 */
interface ServiceInterface
{
    /**
     * Get all resources
     * 
     * @param array $params Optional parameters for filtering, sorting, etc.
     * @return array Collection of resources
     */
    public function getAll(array $params = []): array;
    
    /**
     * Get a resource by ID
     * 
     * @param int $id Resource ID
     * @return mixed The resource or null if not found
     */
    public function getById(int $id);
    
    /**
     * Create a new resource
     * 
     * @param array $data Resource data
     * @return mixed The created resource
     */
    public function create(array $data);
    
    /**
     * Update an existing resource
     * 
     * @param int $id Resource ID
     * @param array $data Updated resource data
     * @return mixed The updated resource
     */
    public function update(int $id, array $data);
    
    /**
     * Delete a resource
     * 
     * @param int $id Resource ID
     * @return bool True if successful, false otherwise
     */
    public function delete(int $id): bool;
}