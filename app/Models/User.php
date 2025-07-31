<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Database;
use App\Models\BlogPost;
use App\Models\Playlist;
use DateTime;

/**
 * User Model
 * 
 * Represents a user in the database.
 */
class User extends Model
{
    /**
     * @var string The table name
     */
    protected string $table = 'users';
    
    /**
     * @var array The fillable attributes
     */
    protected array $fillable = [
        'email',
        'first_name',
        'last_name',
        'avatar',
        'bio',
        'website',
        'social_twitter',
        'social_facebook',
        'social_instagram',
        'social_youtube',
        'newsletter_subscribed',
        'role'
    ];
    
    /**
     * @var array The guarded attributes
     */
    protected array $guarded = [
        'id',
        'password_hash',
        'created_at',
        'updated_at',
        'last_login'
    ];
    
    /**
     * @var array The date attributes
     */
    protected array $dates = [
        'created_at',
        'updated_at',
        'last_login'
    ];
    
    /**
     * Get the user's full name
     * 
     * @return string The full name
     */
    public function getFullName(): string
    {
        if (!empty($this->first_name) && !empty($this->last_name)) {
            return $this->first_name . ' ' . $this->last_name;
        }
        
        if (!empty($this->first_name)) {
            return $this->first_name;
        }
        
        if (!empty($this->last_name)) {
            return $this->last_name;
        }
        
        // Return email username if no name is set
        $parts = explode('@', $this->email);
        return $parts[0] ?? 'User';
    }
    
    /**
     * Get the user's display name
     * 
     * @return string The display name
     */
    public function getDisplayName(): string
    {
        return $this->getFullName();
    }
    
    /**
     * Get the user's avatar URL
     * 
     * @param int $size The size of the avatar in pixels
     * @return string The avatar URL
     */
    public function getAvatarUrl(int $size = 80): string
    {
        if (!empty($this->avatar)) {
            return $this->avatar;
        }
        
        // Generate Gravatar URL
        $hash = md5(strtolower(trim($this->email)));
        return "https://www.gravatar.com/avatar/{$hash}?s={$size}&d=mp";
    }
    
    /**
     * Check if the user has a specific role
     * 
     * @param string $role The role to check
     * @return bool True if the user has the role
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }
    
    /**
     * Check if the user is an admin
     * 
     * @return bool True if the user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }
    
    /**
     * Set the user's password
     * 
     * @param string $password The plain text password
     * @return void
     */
    public function setPassword(string $password): void
    {
        $this->attributes['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
    }
    
    /**
     * Verify the user's password
     * 
     * @param string $password The plain text password
     * @return bool True if the password is correct
     */
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password_hash);
    }
    
    /**
     * Update the last login timestamp
     * 
     * @return void
     */
    public function updateLastLogin(): void
    {
        $this->last_login = new DateTime();
        $this->save();
    }
    
    /**
     * Get the user's blog posts
     * 
     * @param int $limit The maximum number of posts to return
     * @param int $offset The offset for pagination
     * @return array The user's blog posts
     */
    public function blogPosts(int $limit = 10, int $offset = 0): array
    {
        $query = "
            SELECT *
            FROM blog_posts
            WHERE user_id = :user_id
            ORDER BY created_at DESC
            LIMIT :limit OFFSET :offset
        ";
        
        $results = $this->db->fetchAll($query, [
            ':user_id' => $this->id,
            ':limit' => $limit,
            ':offset' => $offset
        ]);
        
        $posts = [];
        foreach ($results as $result) {
            $posts[] = new BlogPost($result);
        }
        
        return $posts;
    }
    
    /**
     * Get the user's playlists
     * 
     * @param int $limit The maximum number of playlists to return
     * @param int $offset The offset for pagination
     * @return array The user's playlists
     */
    public function playlists(int $limit = 10, int $offset = 0): array
    {
        $query = "
            SELECT *
            FROM playlists
            WHERE user_id = :user_id
            ORDER BY created_at DESC
            LIMIT :limit OFFSET :offset
        ";
        
        $results = $this->db->fetchAll($query, [
            ':user_id' => $this->id,
            ':limit' => $limit,
            ':offset' => $offset
        ]);
        
        $playlists = [];
        foreach ($results as $result) {
            $playlists[] = new Playlist($result);
        }
        
        return $playlists;
    }
    
    /**
     * Get the user's favorite tracks
     * 
     * @param int $limit The maximum number of tracks to return
     * @param int $offset The offset for pagination
     * @return array The user's favorite tracks
     */
    public function favorites(int $limit = 10, int $offset = 0): array
    {
        $query = "
            SELECT t.*
            FROM tracks t
            JOIN user_favorites uf ON t.id = uf.track_id
            WHERE uf.user_id = :user_id
            ORDER BY uf.created_at DESC
            LIMIT :limit OFFSET :offset
        ";
        
        $results = $this->db->fetchAll($query, [
            ':user_id' => $this->id,
            ':limit' => $limit,
            ':offset' => $offset
        ]);
        
        $tracks = [];
        foreach ($results as $result) {
            $tracks[] = new Track($result);
        }
        
        return $tracks;
    }
    
    /**
     * Get the user's purchased tracks
     * 
     * @param int $limit The maximum number of tracks to return
     * @param int $offset The offset for pagination
     * @return array The user's purchased tracks
     */
    public function purchasedTracks(int $limit = 10, int $offset = 0): array
    {
        $query = "
            SELECT t.*, upt.license_type, upt.download_count
            FROM tracks t
            JOIN user_purchased_tracks upt ON t.id = upt.track_id
            WHERE upt.user_id = :user_id
            ORDER BY upt.created_at DESC
            LIMIT :limit OFFSET :offset
        ";
        
        $results = $this->db->fetchAll($query, [
            ':user_id' => $this->id,
            ':limit' => $limit,
            ':offset' => $offset
        ]);
        
        $tracks = [];
        foreach ($results as $result) {
            $track = new Track($result);
            $track->license_type = $result['license_type'];
            $track->download_count = $result['download_count'];
            $tracks[] = $track;
        }
        
        return $tracks;
    }
    
    /**
     * Get the user's play history
     * 
     * @param int $limit The maximum number of tracks to return
     * @param int $offset The offset for pagination
     * @return array The user's play history
     */
    public function playHistory(int $limit = 10, int $offset = 0): array
    {
        $query = "
            SELECT t.*, uph.played_at, uph.duration
            FROM tracks t
            JOIN user_play_history uph ON t.id = uph.track_id
            WHERE uph.user_id = :user_id
            ORDER BY uph.played_at DESC
            LIMIT :limit OFFSET :offset
        ";
        
        $results = $this->db->fetchAll($query, [
            ':user_id' => $this->id,
            ':limit' => $limit,
            ':offset' => $offset
        ]);
        
        $tracks = [];
        foreach ($results as $result) {
            $track = new Track($result);
            $track->played_at = $result['played_at'];
            $track->play_duration = $result['duration'];
            $tracks[] = $track;
        }
        
        return $tracks;
    }
    
    /**
     * Find a user by email
     * 
     * @param string $email The email to search for
     * @return static|null The user or null if not found
     */
    public static function findByEmail(string $email): ?self
    {
        $instance = new static();
        $query = "
            SELECT *
            FROM {$instance->table}
            WHERE email = :email
            LIMIT 1
        ";
        
        $result = $instance->db->fetch($query, [':email' => $email]);
        
        if ($result) {
            return new static($result);
        }
        
        return null;
    }
    
    /**
     * Authenticate a user
     * 
     * @param string $email The user's email
     * @param string $password The user's password
     * @return static|null The authenticated user or null if authentication failed
     */
    public static function authenticate(string $email, string $password): ?self
    {
        $user = self::findByEmail($email);
        
        if ($user && $user->verifyPassword($password)) {
            $user->updateLastLogin();
            return $user;
        }
        
        return null;
    }
    
    /**
     * Register a new user
     * 
     * @param string $email The user's email
     * @param string $password The user's password
     * @param array $attributes Additional attributes
     * @return static|null The registered user or null if registration failed
     */
    public static function register(string $email, string $password, array $attributes = []): ?self
    {
        // Check if user already exists
        if (self::findByEmail($email)) {
            return null;
        }
        
        // Create new user
        $user = new static(array_merge($attributes, [
            'email' => $email,
            'role' => 'user'
        ]));
        
        $user->setPassword($password);
        $user->save();
        
        return $user;
    }
}