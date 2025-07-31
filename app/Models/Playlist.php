<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Database;
use App\Models\User;
use App\Models\Track;
use DateTime;

/**
 * Playlist Model
 * 
 * Represents a user's playlist in the database.
 */
class Playlist extends Model
{
    /**
     * @var string The table name
     */
    protected string $table = 'playlists';
    
    /**
     * @var array The fillable attributes
     */
    protected array $fillable = [
        'user_id',
        'name',
        'description',
        'cover_image',
        'is_public'
    ];
    
    /**
     * @var array The date attributes
     */
    protected array $dates = [
        'created_at',
        'updated_at'
    ];
    
    /**
     * Get the user who owns this playlist
     * 
     * @return User|null The user or null if not found
     */
    public function user(): ?User
    {
        if (empty($this->user_id)) {
            return null;
        }
        
        return User::find($this->user_id);
    }
    
    /**
     * Get the tracks in this playlist
     * 
     * @return array The tracks
     */
    public function tracks(): array
    {
        $query = "
            SELECT t.*, pt.sort_order
            FROM tracks t
            JOIN playlist_tracks pt ON t.id = pt.track_id
            WHERE pt.playlist_id = :playlist_id
            ORDER BY pt.sort_order ASC, pt.created_at ASC
        ";
        
        $results = $this->db->fetchAll($query, [':playlist_id' => $this->id]);
        
        $tracks = [];
        foreach ($results as $result) {
            $track = new Track($result);
            $track->sort_order = $result['sort_order'];
            $tracks[] = $track;
        }
        
        return $tracks;
    }
    
    /**
     * Add a track to this playlist
     * 
     * @param int $trackId The track ID
     * @param int $sortOrder Optional sort order
     * @return bool True on success
     */
    public function addTrack(int $trackId, int $sortOrder = 0): bool
    {
        // Check if track exists
        $track = Track::find($trackId);
        if (!$track) {
            return false;
        }
        
        // Get the highest sort order if not specified
        if ($sortOrder === 0) {
            $query = "
                SELECT MAX(sort_order) as max_order
                FROM playlist_tracks
                WHERE playlist_id = :playlist_id
            ";
            
            $result = $this->db->fetch($query, [':playlist_id' => $this->id]);
            $sortOrder = (int)($result['max_order'] ?? 0) + 1;
        }
        
        // Add the track to the playlist
        $query = "
            INSERT INTO playlist_tracks (playlist_id, track_id, sort_order, created_at)
            VALUES (:playlist_id, :track_id, :sort_order, :created_at)
            ON CONFLICT (playlist_id, track_id) DO UPDATE SET
                sort_order = :sort_order
        ";
        
        $this->db->execute($query, [
            ':playlist_id' => $this->id,
            ':track_id' => $trackId,
            ':sort_order' => $sortOrder,
            ':created_at' => (new DateTime())->format('Y-m-d H:i:s')
        ]);
        
        return true;
    }
    
    /**
     * Remove a track from this playlist
     * 
     * @param int $trackId The track ID
     * @return bool True on success
     */
    public function removeTrack(int $trackId): bool
    {
        $query = "
            DELETE FROM playlist_tracks
            WHERE playlist_id = :playlist_id AND track_id = :track_id
        ";
        
        $this->db->execute($query, [
            ':playlist_id' => $this->id,
            ':track_id' => $trackId
        ]);
        
        return true;
    }
    
    /**
     * Update the sort order of a track in this playlist
     * 
     * @param int $trackId The track ID
     * @param int $sortOrder The new sort order
     * @return bool True on success
     */
    public function updateTrackOrder(int $trackId, int $sortOrder): bool
    {
        $query = "
            UPDATE playlist_tracks
            SET sort_order = :sort_order
            WHERE playlist_id = :playlist_id AND track_id = :track_id
        ";
        
        $this->db->execute($query, [
            ':playlist_id' => $this->id,
            ':track_id' => $trackId,
            ':sort_order' => $sortOrder
        ]);
        
        return true;
    }
    
    /**
     * Clear all tracks from this playlist
     * 
     * @return bool True on success
     */
    public function clearTracks(): bool
    {
        $query = "
            DELETE FROM playlist_tracks
            WHERE playlist_id = :playlist_id
        ";
        
        $this->db->execute($query, [':playlist_id' => $this->id]);
        
        return true;
    }
    
    /**
     * Get the number of tracks in this playlist
     * 
     * @return int The number of tracks
     */
    public function trackCount(): int
    {
        $query = "
            SELECT COUNT(*) as count
            FROM playlist_tracks
            WHERE playlist_id = :playlist_id
        ";
        
        $result = $this->db->fetch($query, [':playlist_id' => $this->id]);
        
        return (int) ($result['count'] ?? 0);
    }
    
    /**
     * Get the total duration of all tracks in this playlist
     * 
     * @return int The total duration in seconds
     */
    public function totalDuration(): int
    {
        $query = "
            SELECT SUM(t.duration) as total_duration
            FROM tracks t
            JOIN playlist_tracks pt ON t.id = pt.track_id
            WHERE pt.playlist_id = :playlist_id
        ";
        
        $result = $this->db->fetch($query, [':playlist_id' => $this->id]);
        
        return (int) ($result['total_duration'] ?? 0);
    }
    
    /**
     * Format the total duration as a string (HH:MM:SS)
     * 
     * @return string The formatted duration
     */
    public function formattedDuration(): string
    {
        $duration = $this->totalDuration();
        $hours = floor($duration / 3600);
        $minutes = floor(($duration % 3600) / 60);
        $seconds = $duration % 60;
        
        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
        } else {
            return sprintf('%d:%02d', $minutes, $seconds);
        }
    }
    
    /**
     * Get public playlists
     * 
     * @param int $limit The maximum number of playlists to return
     * @param int $offset The offset for pagination
     * @return array The public playlists
     */
    public static function getPublic(int $limit = 10, int $offset = 0): array
    {
        $instance = new static();
        $query = "
            SELECT p.*
            FROM {$instance->table} p
            WHERE p.is_public = 1
            ORDER BY p.created_at DESC
            LIMIT :limit OFFSET :offset
        ";
        
        $results = $instance->db->fetchAll($query, [
            ':limit' => $limit,
            ':offset' => $offset
        ]);
        
        $playlists = [];
        foreach ($results as $result) {
            $playlists[] = new static($result);
        }
        
        return $playlists;
    }
    
    /**
     * Search for playlists
     * 
     * @param string $search The search term
     * @param bool $publicOnly Whether to only search public playlists
     * @param int $limit The maximum number of playlists to return
     * @return array The matching playlists
     */
    public static function search(string $search, bool $publicOnly = true, int $limit = 20): array
    {
        $instance = new static();
        $query = "
            SELECT p.*
            FROM {$instance->table} p
            LEFT JOIN users u ON p.user_id = u.id
            WHERE (p.name LIKE :search OR p.description LIKE :search OR u.first_name LIKE :search OR u.last_name LIKE :search)
        ";
        
        if ($publicOnly) {
            $query .= " AND p.is_public = 1";
        }
        
        $query .= "
            ORDER BY p.name ASC
            LIMIT :limit
        ";
        
        $results = $instance->db->fetchAll($query, [
            ':search' => '%' . $search . '%',
            ':limit' => $limit
        ]);
        
        $playlists = [];
        foreach ($results as $result) {
            $playlists[] = new static($result);
        }
        
        return $playlists;
    }
}