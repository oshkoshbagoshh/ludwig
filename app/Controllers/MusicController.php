<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use PDO;

/**
 * Music Controller
 * 
 * Handles music-related operations like listing songs, getting song details,
 * and managing playlists.
 */
class MusicController extends Controller
{
    /**
     * Constructor
     * 
     * @param PDO|Database|null $db Optional database instance or PDO connection
     */
    public function __construct($db = null)
    {
        // If $db is a PDO instance, wrap it in a Database instance
        if ($db instanceof PDO) {
            $dbInstance = new Database();
            $dbInstance->setPDO($db);
            parent::__construct($dbInstance);
        } else {
            parent::__construct($db);
        }
    }
    
    /**
     * List all songs
     * 
     * @param array $filters Optional filters for the song list
     * @return array The list of songs
     */
    public function listSongs(array $filters = []): array
    {
        $query = "
            SELECT t.*, a.name as artist_name, al.title as album_title
            FROM tracks t
            LEFT JOIN artists a ON t.artist_id = a.id
            LEFT JOIN albums al ON t.album_id = al.id
        ";
        
        $params = [];
        $whereConditions = [];
        
        // Apply filters
        if (!empty($filters['artist_id'])) {
            $whereConditions[] = "t.artist_id = :artist_id";
            $params[':artist_id'] = $filters['artist_id'];
        }
        
        if (!empty($filters['album_id'])) {
            $whereConditions[] = "t.album_id = :album_id";
            $params[':album_id'] = $filters['album_id'];
        }
        
        if (!empty($filters['genre_id'])) {
            $query .= " JOIN track_genres tg ON t.id = tg.track_id";
            $whereConditions[] = "tg.genre_id = :genre_id";
            $params[':genre_id'] = $filters['genre_id'];
        }
        
        if (!empty($filters['mood_id'])) {
            $query .= " JOIN track_moods tm ON t.id = tm.track_id";
            $whereConditions[] = "tm.mood_id = :mood_id";
            $params[':mood_id'] = $filters['mood_id'];
        }
        
        if (!empty($filters['search'])) {
            $whereConditions[] = "(t.title LIKE :search OR a.name LIKE :search OR al.title LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        // Add WHERE clause if there are conditions
        if (!empty($whereConditions)) {
            $query .= " WHERE " . implode(" AND ", $whereConditions);
        }
        
        // Add ORDER BY clause
        $query .= " ORDER BY " . ($filters['order_by'] ?? "t.created_at DESC");
        
        // Add LIMIT and OFFSET for pagination
        if (!empty($filters['limit'])) {
            $query .= " LIMIT :limit";
            $params[':limit'] = (int) $filters['limit'];
            
            if (!empty($filters['offset'])) {
                $query .= " OFFSET :offset";
                $params[':offset'] = (int) $filters['offset'];
            }
        }
        
        // Execute query and return results
        return $this->db->fetchAll($query, $params);
    }
    
    /**
     * Get a song by ID
     * 
     * @param int $id The song ID
     * @return array|false The song details or false if not found
     */
    public function getSong(int $id): array|false
    {
        $query = "
            SELECT t.*, a.name as artist_name, al.title as album_title
            FROM tracks t
            LEFT JOIN artists a ON t.artist_id = a.id
            LEFT JOIN albums al ON t.album_id = al.id
            WHERE t.id = :id
        ";
        
        $song = $this->db->fetch($query, [':id' => $id]);
        
        if ($song) {
            // Get genres
            $genresQuery = "
                SELECT g.id, g.name
                FROM genres g
                JOIN track_genres tg ON g.id = tg.genre_id
                WHERE tg.track_id = :track_id
            ";
            $song['genres'] = $this->db->fetchAll($genresQuery, [':track_id' => $id]);
            
            // Get moods
            $moodsQuery = "
                SELECT m.id, m.name
                FROM moods m
                JOIN track_moods tm ON m.id = tm.mood_id
                WHERE tm.track_id = :track_id
            ";
            $song['moods'] = $this->db->fetchAll($moodsQuery, [':track_id' => $id]);
            
            // Get instruments
            $instrumentsQuery = "
                SELECT i.id, i.name
                FROM instruments i
                JOIN track_instruments ti ON i.id = ti.instrument_id
                WHERE ti.track_id = :track_id
            ";
            $song['instruments'] = $this->db->fetchAll($instrumentsQuery, [':track_id' => $id]);
        }
        
        return $song;
    }
    
    /**
     * Get all artists
     * 
     * @return array The list of artists
     */
    public function getArtists(): array
    {
        $query = "SELECT * FROM artists ORDER BY name";
        return $this->db->fetchAll($query);
    }
    
    /**
     * Get all genres
     * 
     * @return array The list of genres
     */
    public function getGenres(): array
    {
        $query = "SELECT * FROM genres ORDER BY name";
        return $this->db->fetchAll($query);
    }
    
    /**
     * Get all moods
     * 
     * @return array The list of moods
     */
    public function getMoods(): array
    {
        $query = "SELECT * FROM moods ORDER BY name";
        return $this->db->fetchAll($query);
    }
    
    /**
     * Get featured tracks for the homepage
     * 
     * @param int $limit The maximum number of tracks to return
     * @return array The list of featured tracks
     */
    public function getFeaturedTracks(int $limit = 6): array
    {
        $query = "
            SELECT t.*, a.name as artist_name
            FROM tracks t
            JOIN artists a ON t.artist_id = a.id
            WHERE t.featured = 1
            ORDER BY t.created_at DESC
            LIMIT :limit
        ";
        
        return $this->db->fetchAll($query, [':limit' => $limit]);
    }
    
    /**
     * Get the preview duration for a track based on user authentication
     * 
     * @param bool $isAuthenticated Whether the user is authenticated
     * @return int The preview duration in seconds
     */
    public function getPreviewDuration(bool $isAuthenticated = false): int
    {
        if ($isAuthenticated) {
            // Authenticated users can listen to the full track
            return 0; // 0 means no limit
        } else {
            // Unauthenticated users are limited to the preview duration
            return (int) ($this->config['audio']['preview_duration'] ?? 10);
        }
    }
    
    /**
     * Handle the songs page
     * 
     * @return void
     */
    public function songsPage(): void
    {
        $filters = [
            'artist_id' => $this->getParam('artist_id'),
            'album_id' => $this->getParam('album_id'),
            'genre_id' => $this->getParam('genre_id'),
            'mood_id' => $this->getParam('mood_id'),
            'search' => $this->getParam('search'),
            'order_by' => $this->getParam('order_by', 'title ASC'),
            'limit' => $this->getParam('limit', 20),
            'offset' => $this->getParam('offset', 0),
        ];
        
        $songs = $this->listSongs($filters);
        $artists = $this->getArtists();
        $genres = $this->getGenres();
        $moods = $this->getMoods();
        
        // If AJAX request, return JSON
        if ($this->isAjax()) {
            $this->jsonResponse([
                'songs' => $songs,
                'filters' => $filters,
            ]);
        }
        
        // Otherwise render the view
        $this->render('songs', [
            'songs' => $songs,
            'artists' => $artists,
            'genres' => $genres,
            'moods' => $moods,
            'filters' => $filters,
            'title' => 'Songs',
        ]);
    }
    
    /**
     * Handle the song detail page
     * 
     * @param int $id The song ID
     * @return void
     */
    public function songDetailPage(int $id): void
    {
        $song = $this->getSong($id);
        
        if (!$song) {
            // If song not found, redirect to songs page
            $this->redirect('/songs');
        }
        
        // If AJAX request, return JSON
        if ($this->isAjax()) {
            $this->jsonResponse($song);
        }
        
        // Otherwise render the view
        $this->render('song_detail', [
            'song' => $song,
            'title' => $song['title'],
            'previewDuration' => $this->getPreviewDuration(isset($_SESSION['user_id'])),
        ]);
    }
}