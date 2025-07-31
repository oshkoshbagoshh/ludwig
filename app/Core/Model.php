<?php

namespace App\Core;

use App\Core\Database;
use DateTime;
use Exception;

/**
 * Base Model class
 * 
 * Provides common functionality for all models.
 */
abstract class Model
{
    /**
     * @var Database The database instance
     */
    protected Database $db;
    
    /**
     * @var string The table name
     */
    protected string $table;
    
    /**
     * @var string The primary key column name
     */
    protected string $primaryKey = 'id';
    
    /**
     * @var array The model attributes
     */
    protected array $attributes = [];
    
    /**
     * @var array The original attributes (for tracking changes)
     */
    protected array $original = [];
    
    /**
     * @var array The fillable attributes
     */
    protected array $fillable = [];
    
    /**
     * @var array The guarded attributes
     */
    protected array $guarded = ['id', 'created_at', 'updated_at'];
    
    /**
     * @var array The date attributes
     */
    protected array $dates = ['created_at', 'updated_at'];
    
    /**
     * Constructor
     * 
     * @param array $attributes The model attributes
     * @param Database|null $db Optional database instance
     */
    public function __construct(array $attributes = [], ?Database $db = null)
    {
        $this->db = $db ?? new Database();
        
        $this->fill($attributes);
        $this->original = $this->attributes;
    }
    
    /**
     * Fill the model with attributes
     * 
     * @param array $attributes The attributes to fill
     * @return self
     */
    public function fill(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            if ($this->isFillable($key)) {
                $this->setAttribute($key, $value);
            }
        }
        
        return $this;
    }
    
    /**
     * Check if an attribute is fillable
     * 
     * @param string $key The attribute key
     * @return bool True if the attribute is fillable
     */
    protected function isFillable(string $key): bool
    {
        if (in_array($key, $this->guarded)) {
            return false;
        }
        
        return empty($this->fillable) || in_array($key, $this->fillable);
    }
    
    /**
     * Set an attribute
     * 
     * @param string $key The attribute key
     * @param mixed $value The attribute value
     * @return void
     */
    public function setAttribute(string $key, $value): void
    {
        // Convert date strings to DateTime objects
        if (in_array($key, $this->dates) && is_string($value)) {
            $value = new DateTime($value);
        }
        
        $this->attributes[$key] = $value;
    }
    
    /**
     * Get an attribute
     * 
     * @param string $key The attribute key
     * @return mixed The attribute value or null if not found
     */
    public function getAttribute(string $key)
    {
        return $this->attributes[$key] ?? null;
    }
    
    /**
     * Get all attributes
     * 
     * @return array The model attributes
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
    
    /**
     * Magic getter
     * 
     * @param string $key The attribute key
     * @return mixed The attribute value
     */
    public function __get(string $key)
    {
        return $this->getAttribute($key);
    }
    
    /**
     * Magic setter
     * 
     * @param string $key The attribute key
     * @param mixed $value The attribute value
     * @return void
     */
    public function __set(string $key, $value): void
    {
        $this->setAttribute($key, $value);
    }
    
    /**
     * Magic isset
     * 
     * @param string $key The attribute key
     * @return bool True if the attribute exists
     */
    public function __isset(string $key): bool
    {
        return isset($this->attributes[$key]);
    }
    
    /**
     * Find a model by its primary key
     * 
     * @param int|string $id The primary key value
     * @return static|null The model instance or null if not found
     */
    public static function find($id): ?self
    {
        $instance = new static();
        $query = "SELECT * FROM {$instance->table} WHERE {$instance->primaryKey} = :id";
        $result = $instance->db->fetch($query, [':id' => $id]);
        
        if ($result) {
            return new static($result);
        }
        
        return null;
    }
    
    /**
     * Get all models
     * 
     * @return array The model instances
     */
    public static function all(): array
    {
        $instance = new static();
        $query = "SELECT * FROM {$instance->table}";
        $results = $instance->db->fetchAll($query);
        
        $models = [];
        foreach ($results as $result) {
            $models[] = new static($result);
        }
        
        return $models;
    }
    
    /**
     * Create a new model
     * 
     * @param array $attributes The model attributes
     * @return static The new model instance
     */
    public static function create(array $attributes): self
    {
        $instance = new static($attributes);
        $instance->save();
        
        return $instance;
    }
    
    /**
     * Save the model
     * 
     * @return bool True on success
     * @throws Exception If the save fails
     */
    public function save(): bool
    {
        $now = new DateTime();
        
        if (empty($this->attributes[$this->primaryKey])) {
            // Insert
            if (in_array('created_at', $this->dates)) {
                $this->attributes['created_at'] = $now;
            }
            
            if (in_array('updated_at', $this->dates)) {
                $this->attributes['updated_at'] = $now;
            }
            
            $attributes = $this->attributes;
            unset($attributes[$this->primaryKey]); // Remove primary key for insert
            
            $columns = array_keys($attributes);
            $placeholders = array_map(fn($col) => ":{$col}", $columns);
            
            $query = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
            
            $params = [];
            foreach ($attributes as $key => $value) {
                $params[":{$key}"] = $value instanceof DateTime ? $value->format('Y-m-d H:i:s') : $value;
            }
            
            $id = $this->db->insert($query, $params);
            $this->attributes[$this->primaryKey] = $id;
        } else {
            // Update
            if (in_array('updated_at', $this->dates)) {
                $this->attributes['updated_at'] = $now;
            }
            
            $attributes = $this->attributes;
            $id = $attributes[$this->primaryKey];
            unset($attributes[$this->primaryKey]); // Remove primary key for update
            
            $setClause = array_map(fn($col) => "{$col} = :{$col}", array_keys($attributes));
            
            $query = "UPDATE {$this->table} SET " . implode(', ', $setClause) . " WHERE {$this->primaryKey} = :id";
            
            $params = [':id' => $id];
            foreach ($attributes as $key => $value) {
                $params[":{$key}"] = $value instanceof DateTime ? $value->format('Y-m-d H:i:s') : $value;
            }
            
            $this->db->execute($query, $params);
        }
        
        $this->original = $this->attributes;
        
        return true;
    }
    
    /**
     * Delete the model
     * 
     * @return bool True on success
     * @throws Exception If the delete fails
     */
    public function delete(): bool
    {
        if (empty($this->attributes[$this->primaryKey])) {
            throw new Exception("Cannot delete a model with no ID");
        }
        
        $query = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $this->db->execute($query, [':id' => $this->attributes[$this->primaryKey]]);
        
        return true;
    }
    
    /**
     * Get models by a where clause
     * 
     * @param string $column The column name
     * @param string $operator The operator
     * @param mixed $value The value
     * @return array The model instances
     */
    public static function where(string $column, string $operator, $value): array
    {
        $instance = new static();
        $query = "SELECT * FROM {$instance->table} WHERE {$column} {$operator} :{$column}";
        $results = $instance->db->fetchAll($query, [":{$column}" => $value]);
        
        $models = [];
        foreach ($results as $result) {
            $models[] = new static($result);
        }
        
        return $models;
    }
    
    /**
     * Get the first model by a where clause
     * 
     * @param string $column The column name
     * @param string $operator The operator
     * @param mixed $value The value
     * @return static|null The model instance or null if not found
     */
    public static function firstWhere(string $column, string $operator, $value): ?self
    {
        $models = static::where($column, $operator, $value);
        
        return !empty($models) ? $models[0] : null;
    }
}