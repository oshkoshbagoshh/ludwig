<?php

namespace App\Services;

use App\Models\User;
use App\Core\Database;
use Exception;

/**
 * Auth Service
 * 
 * Handles authentication and authorization related functionality
 */
class AuthService
{
    /**
     * @var Database
     */
    private Database $db;
    
    /**
     * @var UserService
     */
    private UserService $userService;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->userService = new UserService();
    }
    
    /**
     * Authenticate a user
     * 
     * @param string $email User email
     * @param string $password User password
     * @return User|null The authenticated user or null if authentication fails
     */
    public function authenticate(string $email, string $password): ?User
    {
        return User::authenticate($email, $password);
    }
    
    /**
     * Register a new user
     * 
     * @param string $email User email
     * @param string $password User password
     * @param array $attributes Additional user attributes
     * @return User|null The registered user or null if registration fails
     */
    public function register(string $email, string $password, array $attributes = []): ?User
    {
        return User::register($email, $password, $attributes);
    }
    
    /**
     * Check if a user has a specific role
     * 
     * @param int $userId User ID
     * @param string $role Role name
     * @return bool True if the user has the role, false otherwise
     */
    public function hasRole(int $userId, string $role): bool
    {
        $user = $this->userService->getById($userId);
        
        if (!$user) {
            return false;
        }
        
        return $user->hasRole($role);
    }
    
    /**
     * Check if a user has a specific permission
     * 
     * @param int $userId User ID
     * @param string $permission Permission name
     * @return bool True if the user has the permission, false otherwise
     */
    public function hasPermission(int $userId, string $permission): bool
    {
        $user = $this->userService->getById($userId);
        
        if (!$user) {
            return false;
        }
        
        return $user->hasPermission($permission);
    }
    
    /**
     * Check if a user is an admin
     * 
     * @param int $userId User ID
     * @return bool True if the user is an admin, false otherwise
     */
    public function isAdmin(int $userId): bool
    {
        $user = $this->userService->getById($userId);
        
        if (!$user) {
            return false;
        }
        
        return $user->isAdmin();
    }
    
    /**
     * Update a user's password
     * 
     * @param int $userId User ID
     * @param string $currentPassword Current password
     * @param string $newPassword New password
     * @return bool True if the password was updated, false otherwise
     */
    public function updatePassword(int $userId, string $currentPassword, string $newPassword): bool
    {
        $user = $this->userService->getById($userId);
        
        if (!$user) {
            return false;
        }
        
        // Verify current password
        if (!$user->verifyPassword($currentPassword)) {
            return false;
        }
        
        // Set new password
        $user->setPassword($newPassword);
        
        // Save changes
        return $user->save();
    }
    
    /**
     * Reset a user's password
     * 
     * @param string $email User email
     * @param string $token Reset token
     * @param string $newPassword New password
     * @return bool True if the password was reset, false otherwise
     */
    public function resetPassword(string $email, string $token, string $newPassword): bool
    {
        $user = $this->userService->getByEmail($email);
        
        if (!$user) {
            return false;
        }
        
        // Verify reset token
        // This is a simplified implementation. In a real application, you would
        // store reset tokens in the database with expiration times.
        if (!isset($_SESSION['reset_token']) || $_SESSION['reset_token'] !== $token || !isset($_SESSION['reset_email']) || $_SESSION['reset_email'] !== $email) {
            return false;
        }
        
        // Set new password
        $user->setPassword($newPassword);
        
        // Save changes
        $result = $user->save();
        
        // Clear reset token
        unset($_SESSION['reset_token']);
        unset($_SESSION['reset_email']);
        
        return $result;
    }
    
    /**
     * Generate a password reset token for a user
     * 
     * @param string $email User email
     * @return string|null The reset token or null if the user was not found
     */
    public function generateResetToken(string $email): ?string
    {
        $user = $this->userService->getByEmail($email);
        
        if (!$user) {
            return null;
        }
        
        // Generate a random token
        $token = bin2hex(random_bytes(32));
        
        // Store the token in the session
        // This is a simplified implementation. In a real application, you would
        // store reset tokens in the database with expiration times.
        $_SESSION['reset_token'] = $token;
        $_SESSION['reset_email'] = $email;
        
        return $token;
    }
    
    /**
     * Update a user's last login time
     * 
     * @param int $userId User ID
     * @return bool True if the last login time was updated, false otherwise
     */
    public function updateLastLogin(int $userId): bool
    {
        $user = $this->userService->getById($userId);
        
        if (!$user) {
            return false;
        }
        
        $user->updateLastLogin();
        
        return true;
    }
}