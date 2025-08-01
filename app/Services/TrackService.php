<?php

namespace App\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Track;
use App\Core\Database;
use Exception;

/**
 * Track Service
 * 
 * Handles business logic related to tracks
 */
class TrackService implements ServiceInterface
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
     * Get all tracks
     * 
     * @param array $params Optional parameters for filtering, sorting, etc.
     * @return array Collection of tracks
     */
    public function getAll(array $params = []): array
    {
        $limit = $params['limit'] ?? 20;
        $offset = $params['offset'] ?? 0;
        $orderBy = $params['order_by'] ?? 'created_at';
        $orderDir = $params['order_dir'] ?? 'DESC';
        
        $query = "SELECT * FROM tracks ORDER BY {$orderBy} {$orderDir} LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $tracks = [];
        while ($row = $result->fetch_assoc()) {
            $track = new Track();
            $track->fill($row);
            $tracks[] = $track;
        }
        
        return $tracks;
    }
    
    /**
     * Get a track by ID
     * 
     * @param int $id Track ID
     * @return Track|null The track or null if not found
     */
    public function getById(int $id): ?Track
    {
        $query = "SELECT * FROM tracks WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $track = new Track();
            $track->fill($row);
            return $track;
        }
        
        return null;
    }
    
    /**
     * Create a new track
     * 
     * @param array $data Track data
     * @return Track|null The created track
     */
    public function create(array $data): ?Track
    {
        $track = new Track();
        $track->fill($data);
        
        if ($track->save()) {
            // Handle relationships if provided
            if (isset($data['genres']) && is_array($data['genres'])) {
                foreach ($data['genres'] as $genreId) {
                    $track->addGenre((int)$genreId);
                }
            }
            
            if (isset($data['moods']) && is_array($data['moods'])) {
                foreach ($data['moods'] as $moodId) {
                    $track->addMood((int)$moodId);
                }
            }
            
            if (isset($data['instruments']) && is_array($data['instruments'])) {
                foreach ($data['instruments'] as $instrumentId) {
                    $track->addInstrument((int)$instrumentId);
                }
            }
            
            return $track;
        }
        
        return null;
    }
    
    /**
     * Update an existing track
     * 
     * @param int $id Track ID
     * @param array $data Updated track data
     * @return Track|null The updated track
     */
    public function update(int $id, array $data): ?Track
    {
        $track = $this->getById($id);
        
        if (!$track) {
            return null;
        }
        
        // Handle relationships if provided
        if (isset($data['genres'])) {
            // Get current genres
            $currentGenres = $track->genres();
            $currentGenreIds = array_map(function($genre) {
                return $genre->id;
            }, $currentGenres);
            
            // Add new genres
            foreach ($data['genres'] as $genreId) {
                if (!in_array($genreId, $currentGenreIds)) {
                    $track->addGenre((int)$genreId);
                }
            }
            
            // Remove genres that are no longer associated
            foreach ($currentGenreIds as $genreId) {
                if (!in_array($genreId, $data['genres'])) {
                    $track->removeGenre((int)$genreId);
                }
            }
            
            unset($data['genres']);
        }
        
        if (isset($data['moods'])) {
            // Get current moods
            $currentMoods = $track->moods();
            $currentMoodIds = array_map(function($mood) {
                return $mood->id;
            }, $currentMoods);
            
            // Add new moods
            foreach ($data['moods'] as $moodId) {
                if (!in_array($moodId, $currentMoodIds)) {
                    $track->addMood((int)$moodId);
                }
            }
            
            // Remove moods that are no longer associated
            foreach ($currentMoodIds as $moodId) {
                if (!in_array($moodId, $data['moods'])) {
                    $track->removeMood((int)$moodId);
                }
            }
            
            unset($data['moods']);
        }
        
        if (isset($data['instruments'])) {
            // Get current instruments
            $currentInstruments = $track->instruments();
            $currentInstrumentIds = array_map(function($instrument) {
                return $instrument->id;
            }, $currentInstruments);
            
            // Add new instruments
            foreach ($data['instruments'] as $instrumentId) {
                if (!in_array($instrumentId, $currentInstrumentIds)) {
                    $track->addInstrument((int)$instrumentId);
                }
            }
            
            // Remove instruments that are no longer associated
            foreach ($currentInstrumentIds as $instrumentId) {
                if (!in_array($instrumentId, $data['instruments'])) {
                    $track->removeInstrument((int)$instrumentId);
                }
            }
            
            unset($data['instruments']);
        }
        
        // Update other attributes
        $track->fill($data);
        
        // Save changes
        $track->save();
        
        return $track;
    }
    
    /**
     * Delete a track
     * 
     * @param int $id Track ID
     * @return bool True if successful, false otherwise
     */
    public function delete(int $id): bool
    {
        $track = $this->getById($id);
        
        if (!$track) {
            return false;
        }
        
        return $track->delete();
    }
    
    /**
     * Search for tracks
     * 
     * @param string $search Search term
     * @param int $limit Maximum number of results
     * @return array Matching tracks
     */
    public function search(string $search, int $limit = 20): array
    {
        return Track::search($search, $limit);
    }
    
    /**
     * Get featured tracks
     * 
     * @param int $limit Maximum number of tracks to return
     * @return array Featured tracks
     */
    public function getFeatured(int $limit = 10): array
    {
        return Track::featured($limit);
    }
    
    /**
     * Get tracks by genre
     * 
     * @param int $genreId Genre ID
     * @param int $limit Maximum number of tracks to return
     * @param int $offset Offset for pagination
     * @return array Tracks in the specified genre
     */
    public function getByGenre(int $genreId, int $limit = 20, int $offset = 0): array
    {
        $query = "SELECT t.* FROM tracks t 
                  JOIN track_genres tg ON t.id = tg.track_id 
                  WHERE tg.genre_id = ? 
                  ORDER BY t.created_at DESC 
                  LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('iii', $genreId, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $tracks = [];
        while ($row = $result->fetch_assoc()) {
            $track = new Track();
            $track->fill($row);
            $tracks[] = $track;
        }
        
        return $tracks;
    }
    
    /**
     * Get tracks by mood
     * 
     * @param int $moodId Mood ID
     * @param int $limit Maximum number of tracks to return
     * @param int $offset Offset for pagination
     * @return array Tracks with the specified mood
     */
    public function getByMood(int $moodId, int $limit = 20, int $offset = 0): array
    {
        $query = "SELECT t.* FROM tracks t 
                  JOIN track_moods tm ON t.id = tm.track_id 
                  WHERE tm.mood_id = ? 
                  ORDER BY t.created_at DESC 
                  LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('iii', $moodId, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $tracks = [];
        while ($row = $result->fetch_assoc()) {
            $track = new Track();
            $track->fill($row);
            $tracks[] = $track;
        }
        
        return $tracks;
    }
    
    /**
     * Get tracks by artist
     * 
     * @param int $artistId Artist ID
     * @param int $limit Maximum number of tracks to return
     * @param int $offset Offset for pagination
     * @return array Tracks by the specified artist
     */
    public function getByArtist(int $artistId, int $limit = 20, int $offset = 0): array
    {
        $query = "SELECT * FROM tracks WHERE artist_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('iii', $artistId, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $tracks = [];
        while ($row = $result->fetch_assoc()) {
            $track = new Track();
            $track->fill($row);
            $tracks[] = $track;
        }
        
        return $tracks;
    }
    
    /**
     * Get tracks by album
     * 
     * @param int $albumId Album ID
     * @param int $limit Maximum number of tracks to return
     * @param int $offset Offset for pagination
     * @return array Tracks in the specified album
     */
    public function getByAlbum(int $albumId, int $limit = 20, int $offset = 0): array
    {
        $query = "SELECT * FROM tracks WHERE album_id = ? ORDER BY created_at ASC LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('iii', $albumId, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $tracks = [];
        while ($row = $result->fetch_assoc()) {
            $track = new Track();
            $track->fill($row);
            $tracks[] = $track;
        }
        
        return $tracks;
    }
}