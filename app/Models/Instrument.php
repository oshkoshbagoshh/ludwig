<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Database;
use App\Models\Track;

/**
 * Instrument Model
 * 
 * Represents a musical instrument in the database.
 */
class Instrument extends Model
{
    /**
     * @var string The table name
     */
    protected string $table = 'instruments';
    
    /**
     * @var array The fillable attributes
     */
    protected array $fillable = [
        'name'
    ];
    
    /**
     * Get all tracks featuring this instrument
     * 
     * @return array The tracks
     */
    public function tracks(): array
    {
        $query = "
            SELECT t.*
            FROM tracks t
            JOIN track_instruments ti ON t.id = ti.track_id
            WHERE ti.instrument_id = :instrument_id
            ORDER BY t.title ASC
        ";
        
        $results = $this->db->fetchAll($query, [':instrument_id' => $this->id]);
        
        $tracks = [];
        foreach ($results as $result) {
            $tracks[] = new Track($result);
        }
        
        return $tracks;
    }
    
    /**
     * Get the number of tracks featuring this instrument
     * 
     * @return int The number of tracks
     */
    public function trackCount(): int
    {
        $query = "
            SELECT COUNT(*) as count
            FROM track_instruments
            WHERE instrument_id = :instrument_id
        ";
        
        $result = $this->db->fetch($query, [':instrument_id' => $this->id]);
        
        return (int) ($result['count'] ?? 0);
    }
    
    /**
     * Get the most popular instruments based on track count
     * 
     * @param int $limit The maximum number of instruments to return
     * @return array The popular instruments
     */
    public static function popular(int $limit = 10): array
    {
        $instance = new static();
        $query = "
            SELECT i.*, COUNT(ti.track_id) as track_count
            FROM {$instance->table} i
            JOIN track_instruments ti ON i.id = ti.instrument_id
            GROUP BY i.id
            ORDER BY track_count DESC
            LIMIT :limit
        ";
        
        $results = $instance->db->fetchAll($query, [':limit' => $limit]);
        
        $instruments = [];
        foreach ($results as $result) {
            $instrument = new static([
                'id' => $result['id'],
                'name' => $result['name'],
                'created_at' => $result['created_at'],
                'updated_at' => $result['updated_at']
            ]);
            $instrument->track_count = (int) $result['track_count'];
            $instruments[] = $instrument;
        }
        
        return $instruments;
    }
    
    /**
     * Get featured tracks featuring this instrument
     * 
     * @param int $limit The maximum number of tracks to return
     * @return array The featured tracks
     */
    public function featuredTracks(int $limit = 5): array
    {
        $query = "
            SELECT t.*
            FROM tracks t
            JOIN track_instruments ti ON t.id = ti.track_id
            WHERE ti.instrument_id = :instrument_id AND t.featured = 1
            ORDER BY t.created_at DESC
            LIMIT :limit
        ";
        
        $results = $this->db->fetchAll($query, [
            ':instrument_id' => $this->id,
            ':limit' => $limit
        ]);
        
        $tracks = [];
        foreach ($results as $result) {
            $tracks[] = new Track($result);
        }
        
        return $tracks;
    }
    
    /**
     * Search for instruments
     * 
     * @param string $search The search term
     * @param int $limit The maximum number of instruments to return
     * @return array The matching instruments
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
        
        $instruments = [];
        foreach ($results as $result) {
            $instruments[] = new static($result);
        }
        
        return $instruments;
    }
}