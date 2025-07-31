<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Database;
use App\Models\BlogPost;

/**
 * Blog Category Model
 * 
 * Represents a blog category in the database.
 */
class BlogCategory extends Model
{
    /**
     * @var string The table name
     */
    protected string $table = 'blog_categories';
    
    /**
     * @var array The fillable attributes
     */
    protected array $fillable = [
        'name',
        'slug',
        'description'
    ];
    
    /**
     * Get the posts in this category
     * 
     * @param int $limit The maximum number of posts to return
     * @param int $offset The offset for pagination
     * @return array The posts in this category
     */
    public function posts(int $limit = 10, int $offset = 0): array
    {
        $query = "
            SELECT p.*
            FROM blog_posts p
            JOIN blog_post_categories pc ON p.id = pc.post_id
            WHERE pc.category_id = :category_id
            AND p.status = 'published'
            AND p.published_at <= datetime('now')
            ORDER BY p.published_at DESC
            LIMIT :limit OFFSET :offset
        ";
        
        $results = $this->db->fetchAll($query, [
            ':category_id' => $this->id,
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
     * Count the posts in this category
     * 
     * @return int The number of posts in this category
     */
    public function postCount(): int
    {
        $query = "
            SELECT COUNT(*) as count
            FROM blog_posts p
            JOIN blog_post_categories pc ON p.id = pc.post_id
            WHERE pc.category_id = :category_id
            AND p.status = 'published'
            AND p.published_at <= datetime('now')
        ";
        
        $result = $this->db->fetch($query, [':category_id' => $this->id]);
        
        return (int) ($result['count'] ?? 0);
    }
    
    /**
     * Find a category by slug
     * 
     * @param string $slug The slug to search for
     * @return static|null The category or null if not found
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
     * Get all categories with post counts
     * 
     * @return array The categories with post counts
     */
    public static function allWithPostCounts(): array
    {
        $instance = new static();
        $query = "
            SELECT c.*, COUNT(pc.post_id) as post_count
            FROM {$instance->table} c
            LEFT JOIN blog_post_categories pc ON c.id = pc.category_id
            LEFT JOIN blog_posts p ON pc.post_id = p.id AND p.status = 'published' AND p.published_at <= datetime('now')
            GROUP BY c.id
            ORDER BY c.name ASC
        ";
        
        $results = $instance->db->fetchAll($query);
        
        $categories = [];
        foreach ($results as $result) {
            $category = new static([
                'id' => $result['id'],
                'name' => $result['name'],
                'slug' => $result['slug'],
                'description' => $result['description'],
                'created_at' => $result['created_at'],
                'updated_at' => $result['updated_at']
            ]);
            $category->post_count = (int) $result['post_count'];
            $categories[] = $category;
        }
        
        return $categories;
    }
    
    /**
     * Get popular categories based on post count
     * 
     * @param int $limit The maximum number of categories to return
     * @return array The popular categories
     */
    public static function popular(int $limit = 5): array
    {
        $instance = new static();
        $query = "
            SELECT c.*, COUNT(pc.post_id) as post_count
            FROM {$instance->table} c
            JOIN blog_post_categories pc ON c.id = pc.category_id
            JOIN blog_posts p ON pc.post_id = p.id AND p.status = 'published' AND p.published_at <= datetime('now')
            GROUP BY c.id
            ORDER BY post_count DESC
            LIMIT :limit
        ";
        
        $results = $instance->db->fetchAll($query, [':limit' => $limit]);
        
        $categories = [];
        foreach ($results as $result) {
            $category = new static([
                'id' => $result['id'],
                'name' => $result['name'],
                'slug' => $result['slug'],
                'description' => $result['description'],
                'created_at' => $result['created_at'],
                'updated_at' => $result['updated_at']
            ]);
            $category->post_count = (int) $result['post_count'];
            $categories[] = $category;
        }
        
        return $categories;
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