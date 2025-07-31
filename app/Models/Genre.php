<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Database;
use App\Models\Track;

/**
 * Genre Model
 * 
 * Represents a music genre in the database.
 */
class Genre extends Model
{
    /**
     * @var string The table name
     */
    protected string $table = 'genres';
    
    /**
     * @var array The fillable attributes
     */
    protected array $fillable = [
        'name'
    ];
    
    /**
     * Get all tracks in this genre
     * 
     * @return array The tracks
     */
    public function tracks(): array
    {
        $query = "
            SELECT t.*
            FROM tracks t
            JOIN track_genres tg ON t.id = tg.track_id
            WHERE tg.genre_id = :genre_id
            ORDER BY t.title ASC
        ";
        
        $results = $this->db->fetchAll($query, [':genre_id' => $this->id]);
        
        $tracks = [];
        foreach ($results as $result) {
            $tracks[] = new Track($result);
        }
        
        return $tracks;
    }
    
    /**
     * Get the number of tracks in this genre
     * 
     * @return int The number of tracks
     */
    public function trackCount(): int
    {
        $query = "
            SELECT COUNT(*) as count
            FROM track_genres
            WHERE genre_id = :genre_id
        ";
        
        $result = $this->db->fetch($query, [':genre_id' => $this->id]);
        
        return (int) ($result['count'] ?? 0);
    }
    
    /**
     * Get the most popular genres based on track count
     * 
     * @param int $limit The maximum number of genres to return
     * @return array The popular genres
     */
    public static function popular(int $limit = 10): array
    {
        $instance = new static();
        $query = "
            SELECT g.*, COUNT(tg.track_id) as track_count
            FROM {$instance->table} g
            JOIN track_genres tg ON g.id = tg.genre_id
            GROUP BY g.id
            ORDER BY track_count DESC
            LIMIT :limit
        ";
        
        $results = $instance->db->fetchAll($query, [':limit' => $limit]);
        
        $genres = [];
        foreach ($results as $result) {
            $genre = new static([
                'id' => $result['id'],
                'name' => $result['name'],
                'created_at' => $result['created_at'],
                'updated_at' => $result['updated_at']
            ]);
            $genre->track_count = (int) $result['track_count'];
            $genres[] = $genre;
        }
        
        return $genres;
    }
    
    /**
     * Get featured tracks in this genre
     * 
     * @param int $limit The maximum number of tracks to return
     * @return array The featured tracks
     */
    public function featuredTracks(int $limit = 5): array
    {
        $query = "
            SELECT t.*
            FROM tracks t
            JOIN track_genres tg ON t.id = tg.track_id
            WHERE tg.genre_id = :genre_id AND t.featured = 1
            ORDER BY t.created_at DESC
            LIMIT :limit
        ";
        
        $results = $this->db->fetchAll($query, [
            ':genre_id' => $this->id,
            ':limit' => $limit
        ]);
        
        $tracks = [];
        foreach ($results as $result) {
            $tracks[] = new Track($result);
        }
        
        return $tracks;
    }
    
    /**
     * Search for genres
     * 
     * @param string $search The search term
     * @param int $limit The maximum number of genres to return
     * @return array The matching genres
     */
    public static function search(string $search, int $limit = 20): array
    {
        $instance = new static();
        $query = "
            SELECT *
            FROM {$instance->table}
            WHERE name LIKE :search
            ORDER BY name ASC
            LIMIT :limit
        ";
        
        $results = $instance->db->fetchAll($query, [
            ':search' => '%' . $search . '%',
            ':limit' => $limit
        ]);
        
        $genres = [];
        foreach ($results as $result) {
            $genres[] = new static($result);
        }
        
        return $genres;
    }
}