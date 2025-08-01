<?php

namespace App\Models;

use App\Core\Model;

/**
 * Permission Model
 * 
 * Represents a permission in the database.
 */
class Permission extends Model
{
    /**
     * @var string The table name
     */
    protected string $table = 'permissions';
    
    /**
     * @var array The fillable attributes
     */
    protected array $fillable = [
        'name',
        'description'
    ];
    
    /**
     * @var array The guarded attributes
     */
    protected array $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];
    
    /**
     * @var array The date attributes
     */
    protected array $dates = [
        'created_at',
        'updated_at'
    ];
    
    /**
     * Get all permissions
     * 
     * @return array The permissions
     */
    public static function getAll(): array
    {
        $instance = new static();
        $query = "
            SELECT *
            FROM {$instance->table}
            ORDER BY name ASC
        ";
        
        $results = $instance->db->fetchAll($query);
        
        $permissions = [];
        foreach ($results as $result) {
            $permissions[] = new static($result);
        }
        
        return $permissions;
    }
    
    /**
     * Find a permission by name
     * 
     * @param string $name The permission name
     * @return static|null The permission or null if not found
     */
    public static function findByName(string $name): ?self
    {
        $instance = new static();
        $query = "
            SELECT *
            FROM {$instance->table}
            WHERE name = :name
            LIMIT 1
        ";
        
        $result = $instance->db->fetch($query, [':name' => $name]);
        
        if ($result) {
            return new static($result);
        }
        
        return null;
    }
}