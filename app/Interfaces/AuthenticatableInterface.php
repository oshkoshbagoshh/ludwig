<?php

namespace App\Interfaces;

use App\Enums\UserRole;

/**
 * Authenticatable Interface
 * 
 * Defines the contract for authenticatable user classes.
 */
interface AuthenticatableInterface
{
    /**
     * Get the user ID
     * 
     * @return int|null The user ID
     */
    public function getId(): ?int;
    
    /**
     * Get the user email
     * 
     * @return string The user email
     */
    public function getEmail(): string;
    
    /**
     * Get the user password hash
     * 
     * @return string The user password hash
     */
    public function getPasswordHash(): string;
    
    /**
     * Set the user password
     * 
     * @param string $password The plain text password
     * @return self
     */
    public function setPassword(string $password): self;
    
    /**
     * Verify a password against the user's password hash
     * 
     * @param string $password The plain text password to verify
     * @return bool True if the password is correct, false otherwise
     */
    public function verifyPassword(string $password): bool;
    
    /**
     * Get the user role
     * 
     * @return UserRole The user role
     */
    public function getRole(): UserRole;
    
    /**
     * Set the user role
     * 
     * @param UserRole $role The user role
     * @return self
     */
    public function setRole(UserRole $role): self;
    
    /**
     * Check if the user has a specific role
     * 
     * @param UserRole $role The role to check
     * @return bool True if the user has the role, false otherwise
     */
    public function hasRole(UserRole $role): bool;
    
    /**
     * Check if the user has a permission
     * 
     * @param string $permission The permission to check
     * @return bool True if the user has the permission, false otherwise
     */
    public function hasPermission(string $permission): bool;
    
    /**
     * Get the user's remember token
     * 
     * @return string|null The remember token
     */
    public function getRememberToken(): ?string;
    
    /**
     * Set the user's remember token
     * 
     * @param string|null $token The remember token
     * @return self
     */
    public function setRememberToken(?string $token): self;
    
    /**
     * Get the user's last login timestamp
     * 
     * @return string|null The last login timestamp
     */
    public function getLastLogin(): ?string;
    
    /**
     * Set the user's last login timestamp
     * 
     * @param string|null $timestamp The last login timestamp
     * @return self
     */
    public function setLastLogin(?string $timestamp): self;
    
    /**
     * Get the user's first name
     * 
     * @return string|null The first name
     */
    public function getFirstName(): ?string;
    
    /**
     * Get the user's last name
     * 
     * @return string|null The last name
     */
    public function getLastName(): ?string;
    
    /**
     * Get the user's full name
     * 
     * @return string The full name
     */
    public function getFullName(): string;
}