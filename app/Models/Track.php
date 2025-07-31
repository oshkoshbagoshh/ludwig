<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Database;
use App\Models\Artist;
use App\Models\Album;
use App\Models\Genre;
use App\Models\Mood;
use App\Models\Instrument;

/**
 * Track Model
 * 
 * Represents a music track in the database.
 */
class Track extends Model
{
    /**
     * @var string The table name
     */
    protected string $table = 'tracks';
    
    /**
     * @var array The fillable attributes
     */
    protected array $fillable = [
        'artist_id',
        'album_id',
        'title',
        'artwork',
        'file_path',
        'bpm',
        'key_signature',
        'featured',
        'preview_duration'
    ];
    
    /**
     * Get the artist associated with this track
     * 
     * @return Artist|null The artist or null if not found
     */
    public function artist(): ?Artist
    {
        if (empty($this->artist_id)) {
            return null;
        }
        
        return Artist::find($this->artist_id);
    }
    
    /**
     * Get the album associated with this track
     * 
     * @return Album|null The album or null if not found
     */
    public function album(): ?Album
    {
        if (empty($this->album_id)) {
            return null;
        }
        
        return Album::find($this->album_id);
    }
    
    /**
     * Get the genres associated with this track
     * 
     * @return array The genres
     */
    public function genres(): array
    {
        $query = "
            SELECT g.*
            FROM genres g
            JOIN track_genres tg ON g.id = tg.genre_id
            WHERE tg.track_id = :track_id
        ";
        
        $results = $this->db->fetchAll($query, [':track_id' => $this->id]);
        
        $genres = [];
        foreach ($results as $result) {
            $genres[] = new Genre($result);
        }
        
        return $genres;
    }
    
    /**
     * Get the moods associated with this track
     * 
     * @return array The moods
     */
    public function moods(): array
    {
        $query = "
            SELECT m.*
            FROM moods m
            JOIN track_moods tm ON m.id = tm.mood_id
            WHERE tm.track_id = :track_id
        ";
        
        $results = $this->db->fetchAll($query, [':track_id' => $this->id]);
        
        $moods = [];
        foreach ($results as $result) {
            $moods[] = new Mood($result);
        }
        
        return $moods;
    }
    
    /**
     * Get the instruments associated with this track
     * 
     * @return array The instruments
     */
    public function instruments(): array
    {
        $query = "
            SELECT i.*
            FROM instruments i
            JOIN track_instruments ti ON i.id = ti.instrument_id
            WHERE ti.track_id = :track_id
        ";
        
        $results = $this->db->fetchAll($query, [':track_id' => $this->id]);
        
        $instruments = [];
        foreach ($results as $result) {
            $instruments[] = new Instrument($result);
        }
        
        return $instruments;
    }
    
    /**
     * Add a genre to this track
     * 
     * @param int $genreId The genre ID
     * @return bool True on success
     */
    public function addGenre(int $genreId): bool
    {
        $query = "
            INSERT INTO track_genres (track_id, genre_id)
            VALUES (:track_id, :genre_id)
            ON CONFLICT (track_id, genre_id) DO NOTHING
        ";
        
        $this->db->execute($query, [
            ':track_id' => $this->id,
            ':genre_id' => $genreId
        ]);
        
        return true;
    }
    
    /**
     * Remove a genre from this track
     * 
     * @param int $genreId The genre ID
     * @return bool True on success
     */
    public function removeGenre(int $genreId): bool
    {
        $query = "
            DELETE FROM track_genres
            WHERE track_id = :track_id AND genre_id = :genre_id
        ";
        
        $this->db->execute($query, [
            ':track_id' => $this->id,
            ':genre_id' => $genreId
        ]);
        
        return true;
    }
    
    /**
     * Add a mood to this track
     * 
     * @param int $moodId The mood ID
     * @return bool True on success
     */
    public function addMood(int $moodId): bool
    {
        $query = "
            INSERT INTO track_moods (track_id, mood_id)
            VALUES (:track_id, :mood_id)
            ON CONFLICT (track_id, mood_id) DO NOTHING
        ";
        
        $this->db->execute($query, [
            ':track_id' => $this->id,
            ':mood_id' => $moodId
        ]);
        
        return true;
    }
    
    /**
     * Remove a mood from this track
     * 
     * @param int $moodId The mood ID
     * @return bool True on success
     */
    public function removeMood(int $moodId): bool
    {
        $query = "
            DELETE FROM track_moods
            WHERE track_id = :track_id AND mood_id = :mood_id
        ";
        
        $this->db->execute($query, [
            ':track_id' => $this->id,
            ':mood_id' => $moodId
        ]);
        
        return true;
    }
    
    /**
     * Add an instrument to this track
     * 
     * @param int $instrumentId The instrument ID
     * @return bool True on success
     */
    public function addInstrument(int $instrumentId): bool
    {
        $query = "
            INSERT INTO track_instruments (track_id, instrument_id)
            VALUES (:track_id, :instrument_id)
            ON CONFLICT (track_id, instrument_id) DO NOTHING
        ";
        
        $this->db->execute($query, [
            ':track_id' => $this->id,
            ':instrument_id' => $instrumentId
        ]);
        
        return true;
    }
    
    /**
     * Remove an instrument from this track
     * 
     * @param int $instrumentId The instrument ID
     * @return bool True on success
     */
    public function removeInstrument(int $instrumentId): bool
    {
        $query = "
            DELETE FROM track_instruments
            WHERE track_id = :track_id AND instrument_id = :instrument_id
        ";
        
        $this->db->execute($query, [
            ':track_id' => $this->id,
            ':instrument_id' => $instrumentId
        ]);
        
        return true;
    }
    
    /**
     * Get featured tracks
     * 
     * @param int $limit The maximum number of tracks to return
     * @return array The featured tracks
     */
    public static function featured(int $limit = 6): array
    {
        $instance = new static();
        $query = "
            SELECT t.*
            FROM {$instance->table} t
            WHERE t.featured = 1
            ORDER BY t.created_at DESC
            LIMIT :limit
        ";
        
        $results = $instance->db->fetchAll($query, [':limit' => $limit]);
        
        $tracks = [];
        foreach ($results as $result) {
            $tracks[] = new static($result);
        }
        
        return $tracks;
    }
    
    /**
     * Search for tracks
     * 
     * @param string $search The search term
     * @param int $limit The maximum number of tracks to return
     * @return array The matching tracks
     */
    public static function search(string $search, int $limit = 20): array
    {
        $instance = new static();
        $query = "
            SELECT t.*
            FROM {$instance->table} t
            LEFT JOIN artists a ON t.artist_id = a.id
            LEFT JOIN albums al ON t.album_id = al.id
            WHERE t.title LIKE :search OR a.name LIKE :search OR al.title LIKE :search
            ORDER BY t.title ASC
            LIMIT :limit
        ";
        
        $results = $instance->db->fetchAll($query, [
            ':search' => '%' . $search . '%',
            ':limit' => $limit
        ]);
        
        $tracks = [];
        foreach ($results as $result) {
            $tracks[] = new static($result);
        }
        
        return $tracks;
    }
}