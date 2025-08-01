<?php

namespace App\Traits;

use App\Core\Database;

/**
 * Searchable Trait
 * 
 * Provides search functionality for models.
 */
trait Searchable
{
    /**
     * @var array Fields to search in
     */
    protected array $searchableFields = [];
    
    /**
     * @var Database|null Database instance
     */
    protected ?Database $db = null;
    
    /**
     * @var string The table name
     */
    protected string $tableName = '';
    
    /**
     * Search for records
     * 
     * @param string $query The search query
     * @param array|null $fields The fields to search in (defaults to $searchableFields)
     * @param int $limit The maximum number of results to return
     * @param int $offset The offset to start from
     * @return array The search results
     */
    public function search(string $query, ?array $fields = null, int $limit = 20, int $offset = 0): array
    {
        if (empty($query)) {
            return [];
        }
        
        $db = $this->getDatabase();
        $tableName = $this->getTableName();
        $searchFields = $fields ?? $this->searchableFields;
        
        if (empty($searchFields)) {
            throw new \Exception("No searchable fields defined");
        }
        
        // Build the WHERE clause for each searchable field
        $whereClauses = [];
        $params = [];
        
        foreach ($searchFields as $field) {
            $whereClauses[] = "{$field} ILIKE ?";
            $params[] = "%{$query}%";
        }
        
        $whereClause = implode(' OR ', $whereClauses);
        
        // Build the complete query
        $sql = "SELECT * FROM {$tableName} WHERE {$whereClause} LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return $db->fetchAll($sql, $params);
    }
    
    /**
     * Count search results
     * 
     * @param string $query The search query
     * @param array|null $fields The fields to search in (defaults to $searchableFields)
     * @return int The number of results
     */
    public function countSearchResults(string $query, ?array $fields = null): int
    {
        if (empty($query)) {
            return 0;
        }
        
        $db = $this->getDatabase();
        $tableName = $this->getTableName();
        $searchFields = $fields ?? $this->searchableFields;
        
        if (empty($searchFields)) {
            throw new \Exception("No searchable fields defined");
        }
        
        // Build the WHERE clause for each searchable field
        $whereClauses = [];
        $params = [];
        
        foreach ($searchFields as $field) {
            $whereClauses[] = "{$field} ILIKE ?";
            $params[] = "%{$query}%";
        }
        
        $whereClause = implode(' OR ', $whereClauses);
        
        // Build the complete query
        $sql = "SELECT COUNT(*) as count FROM {$tableName} WHERE {$whereClause}";
        
        $result = $db->fetch($sql, $params);
        return (int)($result['count'] ?? 0);
    }
    
    /**
     * Advanced search with multiple criteria
     * 
     * @param array $criteria The search criteria (field => value pairs)
     * @param string $operator The operator to use between criteria (AND, OR)
     * @param int $limit The maximum number of results to return
     * @param int $offset The offset to start from
     * @return array The search results
     */
    public function advancedSearch(array $criteria, string $operator = 'AND', int $limit = 20, int $offset = 0): array
    {
        if (empty($criteria)) {
            return [];
        }
        
        $db = $this->getDatabase();
        $tableName = $this->getTableName();
        
        // Build the WHERE clause for each criterion
        $whereClauses = [];
        $params = [];
        
        foreach ($criteria as $field => $value) {
            if (is_array($value)) {
                // Handle array values (IN operator)
                $placeholders = implode(', ', array_fill(0, count($value), '?'));
                $whereClauses[] = "{$field} IN ({$placeholders})";
                // Avoid array_merge in a loop by using array push
                foreach ($value as $val) {
                    $params[] = $val;
                }
            } else {
                // Handle scalar values
                $whereClauses[] = "{$field} = ?";
                $params[] = $value;
            }
        }
        
        $whereClause = implode(" {$operator} ", $whereClauses);
        
        // Build the complete query
        $sql = "SELECT * FROM {$tableName} WHERE {$whereClause} LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return $db->fetchAll($sql, $params);
    }
    
    /**
     * Get the database instance
     * 
     * @return Database The database instance
     */
    protected function getDatabase(): Database
    {
        if ($this->db === null) {
            $this->db = new Database();
        }
        
        return $this->db;
    }
    
    /**
     * Set the database instance
     * 
     * @param Database $db The database instance
     * @return self
     */
    public function setDatabase(Database $db): self
    {
        $this->db = $db;
        return $this;
    }
    
    /**
     * Get the table name
     * 
     * @return string The table name
     */
    protected function getTableName(): string
    {
        if (empty($this->tableName)) {
            // Try to guess the table name from the class name
            $className = (new \ReflectionClass($this))->getShortName();
            $this->tableName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className)) . 's';
        }
        
        return $this->tableName;
    }
    
    /**
     * Set the table name
     * 
     * @param string $tableName The table name
     * @return self
     */
    public function setTableName(string $tableName): self
    {
        $this->tableName = $tableName;
        return $this;
    }
    
    /**
     * Set the searchable fields
     * 
     * @param array $fields The searchable fields
     * @return self
     */
    public function setSearchableFields(array $fields): self
    {
        $this->searchableFields = $fields;
        return $this;
    }
    
    /**
     * Add a searchable field
     * 
     * @param string $field The field to add
     * @return self
     */
    public function addSearchableField(string $field): self
    {
        if (!in_array($field, $this->searchableFields)) {
            $this->searchableFields[] = $field;
        }
        
        return $this;
    }
}