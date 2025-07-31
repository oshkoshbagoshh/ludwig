<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Database;
use App\Models\Track;
use App\Models\Album;

/**
 * Artist Model
 * 
 * Represents a music artist in the database.
 */
class Artist extends Model
{
    /**
     * @var string The table name
     */
    protected string $table = 'artists';
    
    /**
     * @var array The fillable attributes
     */
    protected array $fillable = [
        'name',
        'bio',
        'avatar',
        'artist_of_week'
    ];
    
    /**
     * Get all tracks by this artist
     * 
     * @return array The tracks
     */
    public function tracks(): array
    {
        $query = "
            SELECT t.*
            FROM tracks t
            WHERE t.artist_id = :artist_id
            ORDER BY t.title ASC
        ";
        
        $results = $this->db->fetchAll($query, [':artist_id' => $this->id]);
        
        $tracks = [];
        foreach ($results as $result) {
            $tracks[] = new Track($result);
        }
        
        return $tracks;
    }
    
    /**
     * Get all albums by this artist
     * 
     * @return array The albums
     */
    public function albums(): array
    {
        $query = "
            SELECT a.*
            FROM albums a
            WHERE a.artist_id = :artist_id
            ORDER BY a.release_date DESC
        ";
        
        $results = $this->db->fetchAll($query, [':artist_id' => $this->id]);
        
        $albums = [];
        foreach ($results as $result) {
            $albums[] = new Album($result);
        }
        
        return $albums;
    }
    
    /**
     * Get the featured tracks by this artist
     * 
     * @param int $limit The maximum number of tracks to return
     * @return array The featured tracks
     */
    public function featuredTracks(int $limit = 5): array
    {
        $query = "
            SELECT t.*
            FROM tracks t
            WHERE t.artist_id = :artist_id AND t.featured = 1
            ORDER BY t.created_at DESC
            LIMIT :limit
        ";
        
        $results = $this->db->fetchAll($query, [
            ':artist_id' => $this->id,
            ':limit' => $limit
        ]);
        
        $tracks = [];
        foreach ($results as $result) {
            $tracks[] = new Track($result);
        }
        
        return $tracks;
    }
    
    /**
     * Get the latest album by this artist
     * 
     * @return Album|null The latest album or null if not found
     */
    public function latestAlbum(): ?Album
    {
        $query = "
            SELECT a.*
            FROM albums a
            WHERE a.artist_id = :artist_id
            ORDER BY a.release_date DESC
            LIMIT 1
        ";
        
        $result = $this->db->fetch($query, [':artist_id' => $this->id]);
        
        if ($result) {
            return new Album($result);
        }
        
        return null;
    }
    
    /**
     * Get the artist of the week
     * 
     * @return static|null The artist of the week or null if not found
     */
    public static function artistOfWeek(): ?self
    {
        $instance = new static();
        $query = "
            SELECT *
            FROM {$instance->table}
            WHERE artist_of_week = 1
            LIMIT 1
        ";
        
        $result = $instance->db->fetch($query);
        
        if ($result) {
            return new static($result);
        }
        
        return null;
    }
    
    /**
     * Set this artist as the artist of the week
     * 
     * @return bool True on success
     */
    public function setAsArtistOfWeek(): bool
    {
        // First, unset any existing artist of the week
        $query = "
            UPDATE {$this->table}
            SET artist_of_week = 0
            WHERE artist_of_week = 1
        ";
        
        $this->db->execute($query);
        
        // Then set this artist as the artist of the week
        $this->artist_of_week = 1;
        return $this->save();
    }
    
    /**
     * Search for artists
     * 
     * @param string $search The search term
     * @param int $limit The maximum number of artists to return
     * @return array The matching artists
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
        
        $artists = [];
        foreach ($results as $result) {
            $artists[] = new static($result);
        }
        
        return $artists;
    }
}