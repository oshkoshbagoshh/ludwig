<?php

namespace App\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Playlist;
use App\Core\Database;
use Exception;

/**
 * Playlist Service
 * 
 * Handles business logic related to playlists
 */
class PlaylistService implements ServiceInterface
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
     * Get all playlists
     * 
     * @param array $params Optional parameters for filtering, sorting, etc.
     * @return array Collection of playlists
     */
    public function getAll(array $params = []): array
    {
        $limit = $params['limit'] ?? 20;
        $offset = $params['offset'] ?? 0;
        $orderBy = $params['order_by'] ?? 'created_at';
        $orderDir = $params['order_dir'] ?? 'DESC';
        $publicOnly = $params['public_only'] ?? false;
        
        $query = "SELECT * FROM playlists";
        
        if ($publicOnly) {
            $query .= " WHERE is_public = 1";
        }
        
        $query .= " ORDER BY {$orderBy} {$orderDir} LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $playlists = [];
        while ($row = $result->fetch_assoc()) {
            $playlist = new Playlist();
            $playlist->fill($row);
            $playlists[] = $playlist;
        }
        
        return $playlists;
    }
    
    /**
     * Get a playlist by ID
     * 
     * @param int $id Playlist ID
     * @return Playlist|null The playlist or null if not found
     */
    public function getById(int $id): ?Playlist
    {
        $query = "SELECT * FROM playlists WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $playlist = new Playlist();
            $playlist->fill($row);
            return $playlist;
        }
        
        return null;
    }
    
    /**
     * Create a new playlist
     * 
     * @param array $data Playlist data
     * @return Playlist|null The created playlist
     */
    public function create(array $data): ?Playlist
    {
        if (!isset($data['user_id'])) {
            throw new Exception('User ID is required');
        }
        
        $playlist = new Playlist();
        $playlist->fill($data);
        
        if ($playlist->save()) {
            // Add tracks if provided
            if (isset($data['tracks']) && is_array($data['tracks'])) {
                foreach ($data['tracks'] as $index => $trackId) {
                    $sortOrder = $index + 1;
                    $playlist->addTrack((int)$trackId, $sortOrder);
                }
            }
            
            return $playlist;
        }
        
        return null;
    }
    
    /**
     * Update an existing playlist
     * 
     * @param int $id Playlist ID
     * @param array $data Updated playlist data
     * @return Playlist|null The updated playlist
     */
    public function update(int $id, array $data): ?Playlist
    {
        $playlist = $this->getById($id);
        
        if (!$playlist) {
            return null;
        }
        
        // Handle tracks if provided
        if (isset($data['tracks']) && is_array($data['tracks'])) {
            // Clear existing tracks
            $playlist->clearTracks();
            
            // Add new tracks
            foreach ($data['tracks'] as $index => $trackId) {
                $sortOrder = $index + 1;
                $playlist->addTrack((int)$trackId, $sortOrder);
            }
            
            unset($data['tracks']);
        }
        
        // Update other attributes
        $playlist->fill($data);
        
        // Save changes
        $playlist->save();
        
        return $playlist;
    }
    
    /**
     * Delete a playlist
     * 
     * @param int $id Playlist ID
     * @return bool True if successful, false otherwise
     */
    public function delete(int $id): bool
    {
        $playlist = $this->getById($id);
        
        if (!$playlist) {
            return false;
        }
        
        return $playlist->delete();
    }
    
    /**
     * Search for playlists
     * 
     * @param string $search Search term
     * @param bool $publicOnly Whether to search only public playlists
     * @param int $limit Maximum number of results
     * @return array Matching playlists
     */
    public function search(string $search, bool $publicOnly = true, int $limit = 20): array
    {
        return Playlist::search($search, $publicOnly, $limit);
    }
    
    /**
     * Get public playlists
     * 
     * @param int $limit Maximum number of playlists to return
     * @param int $offset Offset for pagination
     * @return array Public playlists
     */
    public function getPublic(int $limit = 20, int $offset = 0): array
    {
        return Playlist::getPublic($limit, $offset);
    }
    
    /**
     * Get playlists by user
     * 
     * @param int $userId User ID
     * @param int $limit Maximum number of playlists to return
     * @param int $offset Offset for pagination
     * @return array Playlists created by the specified user
     */
    public function getByUser(int $userId, int $limit = 20, int $offset = 0): array
    {
        $query = "SELECT * FROM playlists WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('iii', $userId, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $playlists = [];
        while ($row = $result->fetch_assoc()) {
            $playlist = new Playlist();
            $playlist->fill($row);
            $playlists[] = $playlist;
        }
        
        return $playlists;
    }
    
    /**
     * Add a track to a playlist
     * 
     * @param int $playlistId Playlist ID
     * @param int $trackId Track ID
     * @param int|null $sortOrder Optional sort order (if null, adds to the end)
     * @return bool True if successful, false otherwise
     */
    public function addTrack(int $playlistId, int $trackId, ?int $sortOrder = null): bool
    {
        $playlist = $this->getById($playlistId);
        
        if (!$playlist) {
            return false;
        }
        
        if ($sortOrder === null) {
            // Get the current track count and add 1 for the new track's position
            $sortOrder = $playlist->trackCount() + 1;
        }
        
        return $playlist->addTrack($trackId, $sortOrder);
    }
    
    /**
     * Remove a track from a playlist
     * 
     * @param int $playlistId Playlist ID
     * @param int $trackId Track ID
     * @return bool True if successful, false otherwise
     */
    public function removeTrack(int $playlistId, int $trackId): bool
    {
        $playlist = $this->getById($playlistId);
        
        if (!$playlist) {
            return false;
        }
        
        return $playlist->removeTrack($trackId);
    }
    
    /**
     * Update a track's order in a playlist
     * 
     * @param int $playlistId Playlist ID
     * @param int $trackId Track ID
     * @param int $sortOrder New sort order
     * @return bool True if successful, false otherwise
     */
    public function updateTrackOrder(int $playlistId, int $trackId, int $sortOrder): bool
    {
        $playlist = $this->getById($playlistId);
        
        if (!$playlist) {
            return false;
        }
        
        return $playlist->updateTrackOrder($trackId, $sortOrder);
    }
    
    /**
     * Clear all tracks from a playlist
     * 
     * @param int $playlistId Playlist ID
     * @return bool True if successful, false otherwise
     */
    public function clearTracks(int $playlistId): bool
    {
        $playlist = $this->getById($playlistId);
        
        if (!$playlist) {
            return false;
        }
        
        return $playlist->clearTracks();
    }
    
    /**
     * Get playlist tracks
     * 
     * @param int $playlistId Playlist ID
     * @return array Tracks in the playlist
     */
    public function getPlaylistTracks(int $playlistId): array
    {
        $playlist = $this->getById($playlistId);
        
        if (!$playlist) {
            return [];
        }
        
        return $playlist->tracks();
    }
    
    /**
     * Get playlist track count
     * 
     * @param int $playlistId Playlist ID
     * @return int Number of tracks in the playlist
     */
    public function getPlaylistTrackCount(int $playlistId): int
    {
        $playlist = $this->getById($playlistId);
        
        if (!$playlist) {
            return 0;
        }
        
        return $playlist->trackCount();
    }
    
    /**
     * Get playlist total duration
     * 
     * @param int $playlistId Playlist ID
     * @return int Total duration in seconds
     */
    public function getPlaylistTotalDuration(int $playlistId): int
    {
        $playlist = $this->getById($playlistId);
        
        if (!$playlist) {
            return 0;
        }
        
        return $playlist->totalDuration();
    }
    
    /**
     * Get playlist formatted duration
     * 
     * @param int $playlistId Playlist ID
     * @return string Formatted duration (e.g., "1:23:45")
     */
    public function getPlaylistFormattedDuration(int $playlistId): string
    {
        $playlist = $this->getById($playlistId);
        
        if (!$playlist) {
            return "0:00";
        }
        
        return $playlist->formattedDuration();
    }
}