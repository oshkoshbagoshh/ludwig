<?php

namespace App\Interfaces;

/**
 * Searchable Interface
 * 
 * Defines the contract for searchable model classes.
 */
interface SearchableInterface
{
    /**
     * Search for records
     * 
     * @param string $query The search query
     * @param array|null $fields The fields to search in
     * @param int $limit The maximum number of results to return
     * @param int $offset The offset to start from
     * @return array The search results
     */
    public function search(string $query, ?array $fields = null, int $limit = 20, int $offset = 0): array;
    
    /**
     * Count search results
     * 
     * @param string $query The search query
     * @param array|null $fields The fields to search in
     * @return int The number of results
     */
    public function countSearchResults(string $query, ?array $fields = null): int;
    
    /**
     * Advanced search with multiple criteria
     * 
     * @param array $criteria The search criteria (field => value pairs)
     * @param string $operator The operator to use between criteria (AND, OR)
     * @param int $limit The maximum number of results to return
     * @param int $offset The offset to start from
     * @return array The search results
     */
    public function advancedSearch(array $criteria, string $operator = 'AND', int $limit = 20, int $offset = 0): array;
    
    /**
     * Set the searchable fields
     * 
     * @param array $fields The searchable fields
     * @return self
     */
    public function setSearchableFields(array $fields): self;
    
    /**
     * Get the searchable fields
     * 
     * @return array The searchable fields
     */
    public function getSearchableFields(): array;
    
    /**
     * Add a searchable field
     * 
     * @param string $field The field to add
     * @return self
     */
    public function addSearchableField(string $field): self;
}