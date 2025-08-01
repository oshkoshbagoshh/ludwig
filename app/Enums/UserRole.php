<?php

namespace App\Enums;

/**
 * User Role Enumeration
 * 
 * Defines the possible user roles in the system.
 */
enum UserRole: string
{
    case GUEST = 'guest';
    case USER = 'user';
    case EDITOR = 'editor';
    case ADMIN = 'admin';
    
    /**
     * Get a human-readable label for the role
     * 
     * @return string The role label
     */
    public function label(): string
    {
        return match($this) {
            self::GUEST => 'Guest',
            self::USER => 'User',
            self::EDITOR => 'Editor',
            self::ADMIN => 'Administrator',
        };
    }
    
    /**
     * Get a description of the role
     * 
     * @return string The role description
     */
    public function description(): string
    {
        return match($this) {
            self::GUEST => 'Unauthenticated user with limited access',
            self::USER => 'Standard authenticated user',
            self::EDITOR => 'Can edit content but not manage users',
            self::ADMIN => 'Full administrative access',
        };
    }
    
    /**
     * Check if the role has higher or equal privileges than another role
     * 
     * @param UserRole $role The role to compare against
     * @return bool True if this role has higher or equal privileges
     */
    public function hasPrivilegesOf(UserRole $role): bool
    {
        $hierarchy = [
            self::GUEST->value => 0,
            self::USER->value => 1,
            self::EDITOR->value => 2,
            self::ADMIN->value => 3,
        ];
        
        return $hierarchy[$this->value] >= $hierarchy[$role->value];
    }
    
    /**
     * Get all available roles
     * 
     * @return array<UserRole> Array of all roles
     */
    public static function all(): array
    {
        return [
            self::GUEST,
            self::USER,
            self::EDITOR,
            self::ADMIN,
        ];
    }
}