<?php

namespace App\Core;

use App\Models\User;

/**
 * Access Control Middleware
 * 
 * Provides methods for checking user permissions and roles.
 */
class AccessControl
{
    /**
     * @var User|null The current user
     */
    private ?User $user;
    
    /**
     * Constructor
     * 
     * @param User|null $user The user to check permissions for
     */
    public function __construct(?User $user = null)
    {
        $this->user = $user;
    }
    
    /**
     * Check if the user has a specific permission
     * 
     * @param string $permission The permission to check
     * @return bool True if the user has the permission
     */
    public function hasPermission(string $permission): bool
    {
        if (!$this->user) {
            return false;
        }
        
        return $this->user->hasPermission($permission);
    }
    
    /**
     * Check if the user has a specific role
     * 
     * @param string $role The role to check
     * @return bool True if the user has the role
     */
    public function hasRole(string $role): bool
    {
        if (!$this->user) {
            return false;
        }
        
        return $this->user->hasRole($role);
    }
    
    /**
     * Check if the user is an admin
     * 
     * @return bool True if the user is an admin
     */
    public function isAdmin(): bool
    {
        if (!$this->user) {
            return false;
        }
        
        return $this->user->isAdmin();
    }
    
    /**
     * Check if the user is authenticated
     * 
     * @return bool True if the user is authenticated
     */
    public function isAuthenticated(): bool
    {
        return $this->user !== null;
    }
    
    /**
     * Get the current user from the session
     * 
     * @return User|null The current user or null if not authenticated
     */
    public static function getCurrentUser(): ?User
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        
        return User::findById($_SESSION['user_id']);
    }
    
    /**
     * Create an instance for the current user
     * 
     * @return self The access control instance
     */
    public static function forCurrentUser(): self
    {
        return new self(self::getCurrentUser());
    }
    
    /**
     * Require a specific permission to access a resource
     * 
     * @param string $permission The required permission
     * @param string $redirectUrl The URL to redirect to if the user doesn't have the permission
     * @return bool True if the user has the permission
     */
    public static function requirePermission(string $permission, string $redirectUrl = '/auth/login'): bool
    {
        $ac = self::forCurrentUser();
        
        if (!$ac->hasPermission($permission)) {
            header("Location: {$redirectUrl}");
            exit;
        }
        
        return true;
    }
    
    /**
     * Require a specific role to access a resource
     * 
     * @param string $role The required role
     * @param string $redirectUrl The URL to redirect to if the user doesn't have the role
     * @return bool True if the user has the role
     */
    public static function requireRole(string $role, string $redirectUrl = '/auth/login'): bool
    {
        $ac = self::forCurrentUser();
        
        if (!$ac->hasRole($role)) {
            header("Location: {$redirectUrl}");
            exit;
        }
        
        return true;
    }
    
    /**
     * Require authentication to access a resource
     * 
     * @param string $redirectUrl The URL to redirect to if the user is not authenticated
     * @return bool True if the user is authenticated
     */
    public static function requireAuth(string $redirectUrl = '/auth/login'): bool
    {
        $ac = self::forCurrentUser();
        
        if (!$ac->isAuthenticated()) {
            header("Location: {$redirectUrl}");
            exit;
        }
        
        return true;
    }
}