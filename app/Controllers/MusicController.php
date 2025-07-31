<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Playlist;
use App\Models\Track;
use App\Models\User;
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
    
    /**
     * Get user playlists
     * 
     * @param int $userId The user ID
     * @param bool $includePrivate Whether to include private playlists
     * @param int $limit The maximum number of playlists to return
     * @param int $offset The offset for pagination
     * @return array The playlists
     */
    public function getUserPlaylists(int $userId, bool $includePrivate = true, int $limit = 10, int $offset = 0): array
    {
        $query = "
            SELECT p.*
            FROM playlists p
            WHERE p.user_id = :user_id
        ";
        
        if (!$includePrivate) {
            $query .= " AND p.is_public = 1";
        }
        
        $query .= "
            ORDER BY p.created_at DESC
            LIMIT :limit OFFSET :offset
        ";
        
        return $this->db->fetchAll($query, [
            ':user_id' => $userId,
            ':limit' => $limit,
            ':offset' => $offset
        ]);
    }
    
    /**
     * Get public playlists
     * 
     * @param int $limit The maximum number of playlists to return
     * @param int $offset The offset for pagination
     * @return array The playlists
     */
    public function getPublicPlaylists(int $limit = 10, int $offset = 0): array
    {
        $query = "
            SELECT p.*, u.first_name, u.last_name
            FROM playlists p
            JOIN users u ON p.user_id = u.id
            WHERE p.is_public = 1
            ORDER BY p.created_at DESC
            LIMIT :limit OFFSET :offset
        ";
        
        return $this->db->fetchAll($query, [
            ':limit' => $limit,
            ':offset' => $offset
        ]);
    }
    
    /**
     * Get a playlist by ID
     * 
     * @param int $id The playlist ID
     * @return array|false The playlist or false if not found
     */
    public function getPlaylist(int $id): array|false
    {
        $query = "
            SELECT p.*, u.first_name, u.last_name
            FROM playlists p
            JOIN users u ON p.user_id = u.id
            WHERE p.id = :id
        ";
        
        $playlist = $this->db->fetch($query, [':id' => $id]);
        
        if ($playlist) {
            // Get tracks in the playlist
            $tracksQuery = "
                SELECT t.*, a.name as artist_name, pt.sort_order
                FROM tracks t
                JOIN playlist_tracks pt ON t.id = pt.track_id
                LEFT JOIN artists a ON t.artist_id = a.id
                WHERE pt.playlist_id = :playlist_id
                ORDER BY pt.sort_order ASC, pt.created_at ASC
            ";
            
            $playlist['tracks'] = $this->db->fetchAll($tracksQuery, [':playlist_id' => $id]);
            
            // Calculate total duration
            $durationQuery = "
                SELECT SUM(t.duration) as total_duration
                FROM tracks t
                JOIN playlist_tracks pt ON t.id = pt.track_id
                WHERE pt.playlist_id = :playlist_id
            ";
            
            $duration = $this->db->fetch($durationQuery, [':playlist_id' => $id]);
            $playlist['total_duration'] = (int) ($duration['total_duration'] ?? 0);
            
            // Format the duration
            $hours = floor($playlist['total_duration'] / 3600);
            $minutes = floor(($playlist['total_duration'] % 3600) / 60);
            $seconds = $playlist['total_duration'] % 60;
            
            if ($hours > 0) {
                $playlist['formatted_duration'] = sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
            } else {
                $playlist['formatted_duration'] = sprintf('%d:%02d', $minutes, $seconds);
            }
        }
        
        return $playlist;
    }
    
    /**
     * Create a new playlist
     * 
     * @param array $data The playlist data
     * @return int|false The new playlist ID or false on failure
     */
    public function createPlaylist(array $data): int|false
    {
        // Validate required fields
        if (empty($data['user_id']) || empty($data['name'])) {
            return false;
        }
        
        // Sanitize inputs
        $name = $this->sanitize($data['name']);
        $description = !empty($data['description']) ? $this->sanitize($data['description']) : null;
        $coverImage = !empty($data['cover_image']) ? $this->sanitize($data['cover_image']) : null;
        $isPublic = isset($data['is_public']) ? (int) $data['is_public'] : 0;
        
        $query = "
            INSERT INTO playlists (user_id, name, description, cover_image, is_public, created_at, updated_at)
            VALUES (:user_id, :name, :description, :cover_image, :is_public, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
        ";
        
        return $this->db->insert($query, [
            ':user_id' => (int) $data['user_id'],
            ':name' => $name,
            ':description' => $description,
            ':cover_image' => $coverImage,
            ':is_public' => $isPublic
        ]);
    }
    
    /**
     * Update a playlist
     * 
     * @param int $id The playlist ID
     * @param array $data The playlist data
     * @return bool True on success
     */
    public function updatePlaylist(int $id, array $data): bool
    {
        // Get the current playlist
        $playlist = $this->getPlaylist($id);
        if (!$playlist) {
            return false;
        }
        
        // Sanitize inputs
        $name = isset($data['name']) ? $this->sanitize($data['name']) : $playlist['name'];
        $description = isset($data['description']) ? $this->sanitize($data['description']) : $playlist['description'];
        $coverImage = isset($data['cover_image']) ? $this->sanitize($data['cover_image']) : $playlist['cover_image'];
        $isPublic = isset($data['is_public']) ? (int) $data['is_public'] : $playlist['is_public'];
        
        $query = "
            UPDATE playlists
            SET name = :name, description = :description, cover_image = :cover_image, is_public = :is_public, updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ";
        
        $this->db->execute($query, [
            ':id' => $id,
            ':name' => $name,
            ':description' => $description,
            ':cover_image' => $coverImage,
            ':is_public' => $isPublic
        ]);
        
        return true;
    }
    
    /**
     * Delete a playlist
     * 
     * @param int $id The playlist ID
     * @return bool True on success
     */
    public function deletePlaylist(int $id): bool
    {
        // First delete all playlist tracks
        $query = "DELETE FROM playlist_tracks WHERE playlist_id = :playlist_id";
        $this->db->execute($query, [':playlist_id' => $id]);
        
        // Then delete the playlist
        $query = "DELETE FROM playlists WHERE id = :id";
        $this->db->execute($query, [':id' => $id]);
        
        return true;
    }
    
    /**
     * Add a track to a playlist
     * 
     * @param int $playlistId The playlist ID
     * @param int $trackId The track ID
     * @param int $sortOrder Optional sort order
     * @return bool True on success
     */
    public function addTrackToPlaylist(int $playlistId, int $trackId, int $sortOrder = 0): bool
    {
        // Check if the playlist exists
        $playlist = $this->getPlaylist($playlistId);
        if (!$playlist) {
            return false;
        }
        
        // Check if the track exists
        $track = $this->getSong($trackId);
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
            
            $result = $this->db->fetch($query, [':playlist_id' => $playlistId]);
            $sortOrder = (int) ($result['max_order'] ?? 0) + 1;
        }
        
        // Add the track to the playlist
        $query = "
            INSERT INTO playlist_tracks (playlist_id, track_id, sort_order, created_at)
            VALUES (:playlist_id, :track_id, :sort_order, CURRENT_TIMESTAMP)
            ON CONFLICT (playlist_id, track_id) DO UPDATE SET
                sort_order = :sort_order
        ";
        
        $this->db->execute($query, [
            ':playlist_id' => $playlistId,
            ':track_id' => $trackId,
            ':sort_order' => $sortOrder
        ]);
        
        return true;
    }
    
    /**
     * Remove a track from a playlist
     * 
     * @param int $playlistId The playlist ID
     * @param int $trackId The track ID
     * @return bool True on success
     */
    public function removeTrackFromPlaylist(int $playlistId, int $trackId): bool
    {
        $query = "
            DELETE FROM playlist_tracks
            WHERE playlist_id = :playlist_id AND track_id = :track_id
        ";
        
        $this->db->execute($query, [
            ':playlist_id' => $playlistId,
            ':track_id' => $trackId
        ]);
        
        return true;
    }
    
    /**
     * Update the sort order of a track in a playlist
     * 
     * @param int $playlistId The playlist ID
     * @param int $trackId The track ID
     * @param int $sortOrder The new sort order
     * @return bool True on success
     */
    public function updateTrackOrder(int $playlistId, int $trackId, int $sortOrder): bool
    {
        $query = "
            UPDATE playlist_tracks
            SET sort_order = :sort_order
            WHERE playlist_id = :playlist_id AND track_id = :track_id
        ";
        
        $this->db->execute($query, [
            ':playlist_id' => $playlistId,
            ':track_id' => $trackId,
            ':sort_order' => $sortOrder
        ]);
        
        return true;
    }
    
    /**
     * Handle the playlists page
     * 
     * @return void
     */
    public function playlistsPage(): void
    {
        $userId = $_SESSION['user_id'] ?? null;
        $userPlaylists = [];
        
        if ($userId) {
            $userPlaylists = $this->getUserPlaylists($userId);
        }
        
        $publicPlaylists = $this->getPublicPlaylists();
        
        // If AJAX request, return JSON
        if ($this->isAjax()) {
            $this->jsonResponse([
                'user_playlists' => $userPlaylists,
                'public_playlists' => $publicPlaylists
            ]);
        }
        
        // Otherwise render the view
        $this->render('playlists', [
            'user_playlists' => $userPlaylists,
            'public_playlists' => $publicPlaylists,
            'title' => 'Playlists',
            'is_authenticated' => !empty($userId)
        ]);
    }
    
    /**
     * Handle the playlist detail page
     * 
     * @param int $id The playlist ID
     * @return void
     */
    public function playlistDetailPage(int $id): void
    {
        $playlist = $this->getPlaylist($id);
        
        if (!$playlist) {
            // If playlist not found, redirect to playlists page
            $this->redirect('/playlists');
        }
        
        $userId = $_SESSION['user_id'] ?? null;
        $isOwner = $userId && $userId == $playlist['user_id'];
        
        // If private playlist and not the owner, redirect
        if (!$playlist['is_public'] && !$isOwner) {
            $this->redirect('/playlists');
        }
        
        // If AJAX request, return JSON
        if ($this->isAjax()) {
            $this->jsonResponse($playlist);
        }
        
        // Otherwise render the view
        $this->render('playlist_detail', [
            'playlist' => $playlist,
            'title' => $playlist['name'],
            'is_owner' => $isOwner,
            'is_authenticated' => !empty($userId),
            'previewDuration' => $this->getPreviewDuration(!empty($userId))
        ]);
    }
    
    /**
     * Handle the create playlist page
     * 
     * @return void
     */
    public function createPlaylistPage(): void
    {
        $userId = $_SESSION['user_id'] ?? null;
        
        // If not authenticated, redirect to login
        if (!$userId) {
            $this->redirect('/login');
        }
        
        // If POST request, create the playlist
        if ($this->isPost()) {
            $data = [
                'user_id' => $userId,
                'name' => $this->getParam('name'),
                'description' => $this->getParam('description'),
                'cover_image' => $this->getParam('cover_image'),
                'is_public' => $this->getParam('is_public', 0)
            ];
            
            $playlistId = $this->createPlaylist($data);
            
            if ($playlistId) {
                // If AJAX request, return JSON
                if ($this->isAjax()) {
                    $this->jsonResponse([
                        'success' => true,
                        'playlist_id' => $playlistId
                    ]);
                }
                
                // Otherwise redirect to the playlist detail page
                $this->redirect('/playlists/' . $playlistId);
            } else {
                // If AJAX request, return JSON
                if ($this->isAjax()) {
                    $this->jsonResponse([
                        'success' => false,
                        'error' => 'Failed to create playlist'
                    ], 400);
                }
                
                // Otherwise render the view with an error
                $this->render('create_playlist', [
                    'title' => 'Create Playlist',
                    'error' => 'Failed to create playlist',
                    'data' => $data
                ]);
            }
        } else {
            // Render the create playlist form
            $this->render('create_playlist', [
                'title' => 'Create Playlist'
            ]);
        }
    }
    
    /**
     * Handle the edit playlist page
     * 
     * @param int $id The playlist ID
     * @return void
     */
    public function editPlaylistPage(int $id): void
    {
        $userId = $_SESSION['user_id'] ?? null;
        
        // If not authenticated, redirect to login
        if (!$userId) {
            $this->redirect('/login');
        }
        
        $playlist = $this->getPlaylist($id);
        
        if (!$playlist) {
            // If playlist not found, redirect to playlists page
            $this->redirect('/playlists');
        }
        
        // If not the owner, redirect
        if ($userId != $playlist['user_id']) {
            $this->redirect('/playlists');
        }
        
        // If POST request, update the playlist
        if ($this->isPost()) {
            $data = [
                'name' => $this->getParam('name'),
                'description' => $this->getParam('description'),
                'cover_image' => $this->getParam('cover_image'),
                'is_public' => $this->getParam('is_public', 0)
            ];
            
            $success = $this->updatePlaylist($id, $data);
            
            if ($success) {
                // If AJAX request, return JSON
                if ($this->isAjax()) {
                    $this->jsonResponse([
                        'success' => true
                    ]);
                }
                
                // Otherwise redirect to the playlist detail page
                $this->redirect('/playlists/' . $id);
            } else {
                // If AJAX request, return JSON
                if ($this->isAjax()) {
                    $this->jsonResponse([
                        'success' => false,
                        'error' => 'Failed to update playlist'
                    ], 400);
                }
                
                // Otherwise render the view with an error
                $this->render('edit_playlist', [
                    'title' => 'Edit Playlist',
                    'playlist' => $playlist,
                    'error' => 'Failed to update playlist'
                ]);
            }
        } else {
            // Render the edit playlist form
            $this->render('edit_playlist', [
                'title' => 'Edit Playlist',
                'playlist' => $playlist
            ]);
        }
    }
}