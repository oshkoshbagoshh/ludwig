<?php

namespace App\Services;

use App\Interfaces\ServiceInterface;
use App\Models\User;
use App\Core\Database;
use Exception;

/**
 * User Service
 * 
 * Handles business logic related to users
 */
class UserService implements ServiceInterface
{
    /**
     * @var Database
     */
    private Database $db;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get all users
     * 
     * @param array $params Optional parameters for filtering, sorting, etc.
     * @return array Collection of users
     */
    public function getAll(array $params = []): array
    {
        $limit = $params['limit'] ?? 20;
        $offset = $params['offset'] ?? 0;
        $orderBy = $params['order_by'] ?? 'created_at';
        $orderDir = $params['order_dir'] ?? 'DESC';
        
        $query = "SELECT * FROM users ORDER BY {$orderBy} {$orderDir} LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $user = new User();
            $user->fill($row);
            $users[] = $user;
        }
        
        return $users;
    }
    
    /**
     * Get a user by ID
     * 
     * @param int $id User ID
     * @return User|null The user or null if not found
     */
    public function getById(int $id): ?User
    {
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $user = new User();
            $user->fill($row);
            return $user;
        }
        
        return null;
    }
    
    /**
     * Get a user by email
     * 
     * @param string $email User email
     * @return User|null The user or null if not found
     */
    public function getByEmail(string $email): ?User
    {
        return User::findByEmail($email);
    }
    
    /**
     * Create a new user
     * 
     * @param array $data User data
     * @return User|null The created user
     */
    public function create(array $data): ?User
    {
        if (!isset($data['email']) || !isset($data['password'])) {
            throw new Exception('Email and password are required');
        }
        
        $attributes = [];
        foreach ($data as $key => $value) {
            if ($key !== 'email' && $key !== 'password') {
                $attributes[$key] = $value;
            }
        }
        
        return User::register($data['email'], $data['password'], $attributes);
    }
    
    /**
     * Update an existing user
     * 
     * @param int $id User ID
     * @param array $data Updated user data
     * @return User|null The updated user
     */
    public function update(int $id, array $data): ?User
    {
        $user = $this->getById($id);
        
        if (!$user) {
            return null;
        }
        
        // Handle password separately
        if (isset($data['password'])) {
            $user->setPassword($data['password']);
            unset($data['password']);
        }
        
        // Update other attributes
        $user->fill($data);
        
        // Save changes
        $user->save();
        
        return $user;
    }
    
    /**
     * Delete a user
     * 
     * @param int $id User ID
     * @return bool True if successful, false otherwise
     */
    public function delete(int $id): bool
    {
        $user = $this->getById($id);
        
        if (!$user) {
            return false;
        }
        
        return $user->delete();
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
     * Get user's blog posts
     * 
     * @param int $userId User ID
     * @param int $limit Maximum number of posts to return
     * @param int $offset Offset for pagination
     * @return array User's blog posts
     */
    public function getUserBlogPosts(int $userId, int $limit = 10, int $offset = 0): array
    {
        $user = $this->getById($userId);
        
        if (!$user) {
            return [];
        }
        
        return $user->blogPosts($limit, $offset);
    }
    
    /**
     * Get user's playlists
     * 
     * @param int $userId User ID
     * @param int $limit Maximum number of playlists to return
     * @param int $offset Offset for pagination
     * @return array User's playlists
     */
    public function getUserPlaylists(int $userId, int $limit = 10, int $offset = 0): array
    {
        $user = $this->getById($userId);
        
        if (!$user) {
            return [];
        }
        
        return $user->playlists($limit, $offset);
    }
    
    /**
     * Get user's favorite tracks
     * 
     * @param int $userId User ID
     * @param int $limit Maximum number of tracks to return
     * @param int $offset Offset for pagination
     * @return array User's favorite tracks
     */
    public function getUserFavorites(int $userId, int $limit = 10, int $offset = 0): array
    {
        $user = $this->getById($userId);
        
        if (!$user) {
            return [];
        }
        
        return $user->favorites($limit, $offset);
    }
    
    /**
     * Get user's purchased tracks
     * 
     * @param int $userId User ID
     * @param int $limit Maximum number of tracks to return
     * @param int $offset Offset for pagination
     * @return array User's purchased tracks
     */
    public function getUserPurchasedTracks(int $userId, int $limit = 10, int $offset = 0): array
    {
        $user = $this->getById($userId);
        
        if (!$user) {
            return [];
        }
        
        return $user->purchasedTracks($limit, $offset);
    }
    
    /**
     * Get user's play history
     * 
     * @param int $userId User ID
     * @param int $limit Maximum number of tracks to return
     * @param int $offset Offset for pagination
     * @return array User's play history
     */
    public function getUserPlayHistory(int $userId, int $limit = 10, int $offset = 0): array
    {
        $user = $this->getById($userId);
        
        if (!$user) {
            return [];
        }
        
        return $user->playHistory($limit, $offset);
    }
}