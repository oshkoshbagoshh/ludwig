<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Database;
use App\Models\Track;

/**
 * Mood Model
 * 
 * Represents a music mood in the database.
 */
class Mood extends Model
{
    /**
     * @var string The table name
     */
    protected string $table = 'moods';
    
    /**
     * @var array The fillable attributes
     */
    protected array $fillable = [
        'name'
    ];
    
    /**
     * Get all tracks with this mood
     * 
     * @return array The tracks
     */
    public function tracks(): array
    {
        $query = "
            SELECT t.*
            FROM tracks t
            JOIN track_moods tm ON t.id = tm.track_id
            WHERE tm.mood_id = :mood_id
            ORDER BY t.title ASC
        ";
        
        $results = $this->db->fetchAll($query, [':mood_id' => $this->id]);
        
        $tracks = [];
        foreach ($results as $result) {
            $tracks[] = new Track($result);
        }
        
        return $tracks;
    }
    
    /**
     * Get the number of tracks with this mood
     * 
     * @return int The number of tracks
     */
    public function trackCount(): int
    {
        $query = "
            SELECT COUNT(*) as count
            FROM track_moods
            WHERE mood_id = :mood_id
        ";
        
        $result = $this->db->fetch($query, [':mood_id' => $this->id]);
        
        return (int) ($result['count'] ?? 0);
    }
    
    /**
     * Get the most popular moods based on track count
     * 
     * @param int $limit The maximum number of moods to return
     * @return array The popular moods
     */
    public static function popular(int $limit = 10): array
    {
        $instance = new static();
        $query = "
            SELECT m.*, COUNT(tm.track_id) as track_count
            FROM {$instance->table} m
            JOIN track_moods tm ON m.id = tm.mood_id
            GROUP BY m.id
            ORDER BY track_count DESC
            LIMIT :limit
        ";
        
        $results = $instance->db->fetchAll($query, [':limit' => $limit]);
        
        $moods = [];
        foreach ($results as $result) {
            $mood = new static([
                'id' => $result['id'],
                'name' => $result['name'],
                'created_at' => $result['created_at'],
                'updated_at' => $result['updated_at']
            ]);
            $mood->track_count = (int) $result['track_count'];
            $moods[] = $mood;
        }
        
        return $moods;
    }
    
    /**
     * Get featured tracks with this mood
     * 
     * @param int $limit The maximum number of tracks to return
     * @return array The featured tracks
     */
    public function featuredTracks(int $limit = 5): array
    {
        $query = "
            SELECT t.*
            FROM tracks t
            JOIN track_moods tm ON t.id = tm.track_id
            WHERE tm.mood_id = :mood_id AND t.featured = 1
            ORDER BY t.created_at DESC
            LIMIT :limit
        ";
        
        $results = $this->db->fetchAll($query, [
            ':mood_id' => $this->id,
            ':limit' => $limit
        ]);
        
        $tracks = [];
        foreach ($results as $result) {
            $tracks[] = new Track($result);
        }
        
        return $tracks;
    }
    
    /**
     * Search for moods
     * 
     * @param string $search The search term
     * @param int $limit The maximum number of moods to return
     * @return array The matching moods
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
        
        $moods = [];
        foreach ($results as $result) {
            $moods[] = new static($result);
        }
        
        return $moods;
    }
}