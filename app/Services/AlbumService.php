<?php

namespace App\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Album;
use App\Core\Database;
use Exception;

/**
 * Album Service
 * 
 * Handles business logic related to albums
 */
class AlbumService implements ServiceInterface
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
     * Get all albums
     * 
     * @param array $params Optional parameters for filtering, sorting, etc.
     * @return array Collection of albums
     */
    public function getAll(array $params = []): array
    {
        $limit = $params['limit'] ?? 20;
        $offset = $params['offset'] ?? 0;
        $orderBy = $params['order_by'] ?? 'created_at';
        $orderDir = $params['order_dir'] ?? 'DESC';
        
        $query = "SELECT * FROM albums ORDER BY {$orderBy} {$orderDir} LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $albums = [];
        while ($row = $result->fetch_assoc()) {
            $album = new Album();
            $album->fill($row);
            $albums[] = $album;
        }
        
        return $albums;
    }
    
    /**
     * Get an album by ID
     * 
     * @param int $id Album ID
     * @return Album|null The album or null if not found
     */
    public function getById(int $id): ?Album
    {
        $query = "SELECT * FROM albums WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $album = new Album();
            $album->fill($row);
            return $album;
        }
        
        return null;
    }
    
    /**
     * Create a new album
     * 
     * @param array $data Album data
     * @return Album|null The created album
     */
    public function create(array $data): ?Album
    {
        $album = new Album();
        $album->fill($data);
        
        if ($album->save()) {
            return $album;
        }
        
        return null;
    }
    
    /**
     * Update an existing album
     * 
     * @param int $id Album ID
     * @param array $data Updated album data
     * @return Album|null The updated album
     */
    public function update(int $id, array $data): ?Album
    {
        $album = $this->getById($id);
        
        if (!$album) {
            return null;
        }
        
        // Update attributes
        $album->fill($data);
        
        // Save changes
        $album->save();
        
        return $album;
    }
    
    /**
     * Delete an album
     * 
     * @param int $id Album ID
     * @return bool True if successful, false otherwise
     */
    public function delete(int $id): bool
    {
        $album = $this->getById($id);
        
        if (!$album) {
            return false;
        }
        
        return $album->delete();
    }
    
    /**
     * Search for albums
     * 
     * @param string $search Search term
     * @param int $limit Maximum number of results
     * @return array Matching albums
     */
    public function search(string $search, int $limit = 20): array
    {
        return Album::search($search, $limit);
    }
    
    /**
     * Get recent albums
     * 
     * @param int $limit Maximum number of albums to return
     * @return array Recent albums
     */
    public function getRecent(int $limit = 10): array
    {
        return Album::recent($limit);
    }
    
    /**
     * Get albums by artist
     * 
     * @param int $artistId Artist ID
     * @param int $limit Maximum number of albums to return
     * @param int $offset Offset for pagination
     * @return array Albums by the specified artist
     */
    public function getByArtist(int $artistId, int $limit = 20, int $offset = 0): array
    {
        $query = "SELECT * FROM albums WHERE artist_id = ? ORDER BY release_date DESC LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('iii', $artistId, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $albums = [];
        while ($row = $result->fetch_assoc()) {
            $album = new Album();
            $album->fill($row);
            $albums[] = $album;
        }
        
        return $albums;
    }
    
    /**
     * Get albums by year
     * 
     * @param int $year Release year
     * @param int $limit Maximum number of albums to return
     * @param int $offset Offset for pagination
     * @return array Albums released in the specified year
     */
    public function getByYear(int $year, int $limit = 20, int $offset = 0): array
    {
        $startDate = "{$year}-01-01";
        $endDate = "{$year}-12-31";
        
        $query = "SELECT * FROM albums WHERE release_date BETWEEN ? AND ? ORDER BY release_date DESC LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ssii', $startDate, $endDate, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $albums = [];
        while ($row = $result->fetch_assoc()) {
            $album = new Album();
            $album->fill($row);
            $albums[] = $album;
        }
        
        return $albums;
    }
    
    /**
     * Get album tracks
     * 
     * @param int $albumId Album ID
     * @return array Tracks in the album
     */
    public function getAlbumTracks(int $albumId): array
    {
        $album = $this->getById($albumId);
        
        if (!$album) {
            return [];
        }
        
        return $album->tracks();
    }
    
    /**
     * Get album track count
     * 
     * @param int $albumId Album ID
     * @return int Number of tracks in the album
     */
    public function getAlbumTrackCount(int $albumId): int
    {
        $album = $this->getById($albumId);
        
        if (!$album) {
            return 0;
        }
        
        return $album->trackCount();
    }
    
    /**
     * Get album total duration
     * 
     * @param int $albumId Album ID
     * @return int Total duration in seconds
     */
    public function getAlbumTotalDuration(int $albumId): int
    {
        $album = $this->getById($albumId);
        
        if (!$album) {
            return 0;
        }
        
        return $album->totalDuration();
    }
    
    /**
     * Get album formatted duration
     * 
     * @param int $albumId Album ID
     * @return string Formatted duration (e.g., "1:23:45")
     */
    public function getAlbumFormattedDuration(int $albumId): string
    {
        $album = $this->getById($albumId);
        
        if (!$album) {
            return "0:00";
        }
        
        return $album->formattedDuration();
    }
}