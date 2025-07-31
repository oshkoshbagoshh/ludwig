<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Database;
use App\Models\BlogPost;

/**
 * Blog Tag Model
 * 
 * Represents a blog tag in the database.
 */
class BlogTag extends Model
{
    /**
     * @var string The table name
     */
    protected string $table = 'blog_tags';
    
    /**
     * @var array The fillable attributes
     */
    protected array $fillable = [
        'name',
        'slug'
    ];
    
    /**
     * Get the posts with this tag
     * 
     * @param int $limit The maximum number of posts to return
     * @param int $offset The offset for pagination
     * @return array The posts with this tag
     */
    public function posts(int $limit = 10, int $offset = 0): array
    {
        $query = "
            SELECT p.*
            FROM blog_posts p
            JOIN blog_post_tags pt ON p.id = pt.post_id
            WHERE pt.tag_id = :tag_id
            AND p.status = 'published'
            AND p.published_at <= datetime('now')
            ORDER BY p.published_at DESC
            LIMIT :limit OFFSET :offset
        ";
        
        $results = $this->db->fetchAll($query, [
            ':tag_id' => $this->id,
            ':limit' => $limit,
            ':offset' => $offset
        ]);
        
        $posts = [];
        foreach ($results as $result) {
            $posts[] = new BlogPost($result);
        }
        
        return $posts;
    }
    
    /**
     * Count the posts with this tag
     * 
     * @return int The number of posts with this tag
     */
    public function postCount(): int
    {
        $query = "
            SELECT COUNT(*) as count
            FROM blog_posts p
            JOIN blog_post_tags pt ON p.id = pt.post_id
            WHERE pt.tag_id = :tag_id
            AND p.status = 'published'
            AND p.published_at <= datetime('now')
        ";
        
        $result = $this->db->fetch($query, [':tag_id' => $this->id]);
        
        return (int) ($result['count'] ?? 0);
    }
    
    /**
     * Find a tag by slug
     * 
     * @param string $slug The slug to search for
     * @return static|null The tag or null if not found
     */
    public static function findBySlug(string $slug): ?self
    {
        $instance = new static();
        $query = "
            SELECT *
            FROM {$instance->table}
            WHERE slug = :slug
            LIMIT 1
        ";
        
        $result = $instance->db->fetch($query, [':slug' => $slug]);
        
        if ($result) {
            return new static($result);
        }
        
        return null;
    }
    
    /**
     * Get all tags with post counts
     * 
     * @return array The tags with post counts
     */
    public static function allWithPostCounts(): array
    {
        $instance = new static();
        $query = "
            SELECT t.*, COUNT(pt.post_id) as post_count
            FROM {$instance->table} t
            LEFT JOIN blog_post_tags pt ON t.id = pt.tag_id
            LEFT JOIN blog_posts p ON pt.post_id = p.id AND p.status = 'published' AND p.published_at <= datetime('now')
            GROUP BY t.id
            ORDER BY t.name ASC
        ";
        
        $results = $instance->db->fetchAll($query);
        
        $tags = [];
        foreach ($results as $result) {
            $tag = new static([
                'id' => $result['id'],
                'name' => $result['name'],
                'slug' => $result['slug'],
                'created_at' => $result['created_at'],
                'updated_at' => $result['updated_at']
            ]);
            $tag->post_count = (int) $result['post_count'];
            $tags[] = $tag;
        }
        
        return $tags;
    }
    
    /**
     * Get popular tags based on post count
     * 
     * @param int $limit The maximum number of tags to return
     * @return array The popular tags
     */
    public static function popular(int $limit = 10): array
    {
        $instance = new static();
        $query = "
            SELECT t.*, COUNT(pt.post_id) as post_count
            FROM {$instance->table} t
            JOIN blog_post_tags pt ON t.id = pt.tag_id
            JOIN blog_posts p ON pt.post_id = p.id AND p.status = 'published' AND p.published_at <= datetime('now')
            GROUP BY t.id
            ORDER BY post_count DESC
            LIMIT :limit
        ";
        
        $results = $instance->db->fetchAll($query, [':limit' => $limit]);
        
        $tags = [];
        foreach ($results as $result) {
            $tag = new static([
                'id' => $result['id'],
                'name' => $result['name'],
                'slug' => $result['slug'],
                'created_at' => $result['created_at'],
                'updated_at' => $result['updated_at']
            ]);
            $tag->post_count = (int) $result['post_count'];
            $tags[] = $tag;
        }
        
        return $tags;
    }
    
    /**
     * Create a slug from a name
     * 
     * @param string $name The name to create a slug from
     * @return string The slug
     */
    public static function createSlug(string $name): string
    {
        // Convert to lowercase and replace spaces with hyphens
        $slug = strtolower(str_replace(' ', '-', $name));
        
        // Remove any character that is not a letter, number, or hyphen
        $slug = preg_replace('/[^a-z0-9-]/', '', $slug);
        
        // Remove multiple hyphens
        $slug = preg_replace('/-+/', '-', $slug);
        
        // Trim hyphens from the beginning and end
        $slug = trim($slug, '-');
        
        return $slug;
    }
    
    /**
     * Generate a unique slug
     * 
     * @param string $name The name to create a slug from
     * @param int $id The ID to exclude (for updates)
     * @return string The unique slug
     */
    public static function generateUniqueSlug(string $name, int $id = 0): string
    {
        $slug = self::createSlug($name);
        $originalSlug = $slug;
        $counter = 1;
        
        $instance = new static();
        
        while (true) {
            $query = "
                SELECT COUNT(*) as count
                FROM {$instance->table}
                WHERE slug = :slug
            ";
            
            $params = [':slug' => $slug];
            
            if ($id > 0) {
                $query .= " AND id != :id";
                $params[':id'] = $id;
            }
            
            $result = $instance->db->fetch($query, $params);
            
            if ((int) ($result['count'] ?? 0) === 0) {
                break;
            }
            
            $slug = $originalSlug . '-' . $counter++;
        }
        
        return $slug;
    }
    
    /**
     * Override the save method to automatically generate a slug
     * 
     * @return bool True on success
     */
    public function save(): bool
    {
        // Generate a slug if not provided
        if (empty($this->attributes['slug']) && !empty($this->attributes['name'])) {
            $this->attributes['slug'] = self::generateUniqueSlug(
                $this->attributes['name'],
                $this->attributes['id'] ?? 0
            );
        }
        
        return parent::save();
    }
}