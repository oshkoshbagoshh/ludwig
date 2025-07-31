<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Database;
use App\Models\Track;
use App\Models\Artist;
use DateTime;

/**
 * Album Model
 * 
 * Represents a music album in the database.
 */
class Album extends Model
{
    /**
     * @var string The table name
     */
    protected string $table = 'albums';
    
    /**
     * @var array The fillable attributes
     */
    protected array $fillable = [
        'artist_id',
        'title',
        'cover',
        'release_date'
    ];
    
    /**
     * @var array The date attributes
     */
    protected array $dates = [
        'created_at',
        'updated_at',
        'release_date'
    ];
    
    /**
     * Get the artist associated with this album
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
     * Get all tracks in this album
     * 
     * @return array The tracks
     */
    public function tracks(): array
    {
        $query = "
            SELECT t.*
            FROM tracks t
            WHERE t.album_id = :album_id
            ORDER BY t.title ASC
        ";
        
        $results = $this->db->fetchAll($query, [':album_id' => $this->id]);
        
        $tracks = [];
        foreach ($results as $result) {
            $tracks[] = new Track($result);
        }
        
        return $tracks;
    }
    
    /**
     * Get the number of tracks in this album
     * 
     * @return int The number of tracks
     */
    public function trackCount(): int
    {
        $query = "
            SELECT COUNT(*) as count
            FROM tracks
            WHERE album_id = :album_id
        ";
        
        $result = $this->db->fetch($query, [':album_id' => $this->id]);
        
        return (int) ($result['count'] ?? 0);
    }
    
    /**
     * Get the total duration of all tracks in this album
     * 
     * @return int The total duration in seconds
     */
    public function totalDuration(): int
    {
        $tracks = $this->tracks();
        $duration = 0;
        
        foreach ($tracks as $track) {
            $duration += (int) ($track->duration ?? 0);
        }
        
        return $duration;
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
     * Get the formatted release date
     * 
     * @param string $format The date format
     * @return string The formatted date
     */
    public function formattedReleaseDate(string $format = 'F j, Y'): string
    {
        if ($this->release_date instanceof DateTime) {
            return $this->release_date->format($format);
        }
        
        return '';
    }
    
    /**
     * Get recent albums
     * 
     * @param int $limit The maximum number of albums to return
     * @return array The recent albums
     */
    public static function recent(int $limit = 6): array
    {
        $instance = new static();
        $query = "
            SELECT a.*
            FROM {$instance->table} a
            ORDER BY a.release_date DESC
            LIMIT :limit
        ";
        
        $results = $instance->db->fetchAll($query, [':limit' => $limit]);
        
        $albums = [];
        foreach ($results as $result) {
            $albums[] = new static($result);
        }
        
        return $albums;
    }
    
    /**
     * Search for albums
     * 
     * @param string $search The search term
     * @param int $limit The maximum number of albums to return
     * @return array The matching albums
     */
    public static function search(string $search, int $limit = 20): array
    {
        $instance = new static();
        $query = "
            SELECT a.*
            FROM {$instance->table} a
            LEFT JOIN artists ar ON a.artist_id = ar.id
            WHERE a.title LIKE :search OR ar.name LIKE :search
            ORDER BY a.title ASC
            LIMIT :limit
        ";
        
        $results = $instance->db->fetchAll($query, [
            ':search' => '%' . $search . '%',
            ':limit' => $limit
        ]);
        
        $albums = [];
        foreach ($results as $result) {
            $albums[] = new static($result);
        }
        
        return $albums;
    }
}