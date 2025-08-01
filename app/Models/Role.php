<?php

namespace App\Models;

use App\Core\Model;

/**
 * Role Model
 * 
 * Represents a role in the database.
 */
class Role extends Model
{
    /**
     * @var string The table name
     */
    protected string $table = 'roles';
    
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
     * Get all roles
     * 
     * @return array The roles
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
        
        $roles = [];
        foreach ($results as $result) {
            $roles[] = new static($result);
        }
        
        return $roles;
    }
    
    /**
     * Find a role by name
     * 
     * @param string $name The role name
     * @return static|null The role or null if not found
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
    
    /**
     * Get the permissions for this role
     * 
     * @return array The permissions
     */
    public function permissions(): array
    {
        $query = "
            SELECT p.*
            FROM permissions p
            JOIN role_permissions rp ON p.id = rp.permission_id
            WHERE rp.role_id = :role_id
            ORDER BY p.name ASC
        ";
        
        $results = $this->db->fetchAll($query, [
            ':role_id' => $this->id
        ]);
        
        $permissions = [];
        foreach ($results as $result) {
            $permissions[] = new Permission($result);
        }
        
        return $permissions;
    }
    
    /**
     * Check if the role has a specific permission
     * 
     * @param string $permissionName The permission name
     * @return bool True if the role has the permission
     */
    public function hasPermission(string $permissionName): bool
    {
        $query = "
            SELECT COUNT(*) as count
            FROM role_permissions rp
            JOIN permissions p ON rp.permission_id = p.id
            WHERE rp.role_id = :role_id
            AND p.name = :permission_name
        ";
        
        $result = $this->db->fetch($query, [
            ':role_id' => $this->id,
            ':permission_name' => $permissionName
        ]);
        
        return $result && $result['count'] > 0;
    }
    
    /**
     * Add a permission to this role
     * 
     * @param int $permissionId The permission ID
     * @return bool True if the permission was added
     */
    public function addPermission(int $permissionId): bool
    {
        $query = "
            INSERT INTO role_permissions (role_id, permission_id)
            VALUES (:role_id, :permission_id)
            ON CONFLICT (role_id, permission_id) DO NOTHING
        ";
        
        return $this->db->execute($query, [
            ':role_id' => $this->id,
            ':permission_id' => $permissionId
        ]);
    }
    
    /**
     * Remove a permission from this role
     * 
     * @param int $permissionId The permission ID
     * @return bool True if the permission was removed
     */
    public function removePermission(int $permissionId): bool
    {
        $query = "
            DELETE FROM role_permissions
            WHERE role_id = :role_id
            AND permission_id = :permission_id
        ";
        
        return $this->db->execute($query, [
            ':role_id' => $this->id,
            ':permission_id' => $permissionId
        ]);
    }
}