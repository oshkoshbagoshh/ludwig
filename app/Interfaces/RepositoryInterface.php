<?php

namespace App\Interfaces;

/**
 * Repository Interface
 * 
 * Defines the contract for repository classes.
 */
interface RepositoryInterface
{
    /**
     * Find a record by ID
     * 
     * @param int $id The record ID
     * @return array|null The record or null if not found
     */
    public function find(int $id): ?array;
    
    /**
     * Find all records
     * 
     * @param int $limit The maximum number of records to return
     * @param int $offset The offset to start from
     * @return array The records
     */
    public function findAll(int $limit = 100, int $offset = 0): array;
    
    /**
     * Find records by criteria
     * 
     * @param array $criteria The criteria to match
     * @param int $limit The maximum number of records to return
     * @param int $offset The offset to start from
     * @return array The matching records
     */
    public function findBy(array $criteria, int $limit = 100, int $offset = 0): array;
    
    /**
     * Find a single record by criteria
     * 
     * @param array $criteria The criteria to match
     * @return array|null The record or null if not found
     */
    public function findOneBy(array $criteria): ?array;
    
    /**
     * Create a new record
     * 
     * @param array $data The record data
     * @return int The ID of the created record
     */
    public function create(array $data): int;
    
    /**
     * Update a record
     * 
     * @param int $id The record ID
     * @param array $data The record data
     * @return bool True if the record was updated, false otherwise
     */
    public function update(int $id, array $data): bool;
    
    /**
     * Delete a record
     * 
     * @param int $id The record ID
     * @return bool True if the record was deleted, false otherwise
     */
    public function delete(int $id): bool;
    
    /**
     * Count all records
     * 
     * @return int The number of records
     */
    public function count(): int;
    
    /**
     * Count records by criteria
     * 
     * @param array $criteria The criteria to match
     * @return int The number of matching records
     */
    public function countBy(array $criteria): int;
}