<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Database;
use App\Models\User;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\BlogComment;
use DateTime;

/**
 * Blog Post Model
 * 
 * Represents a blog post in the database.
 */
class BlogPost extends Model
{
    /**
     * @var string The table name
     */
    protected string $table = 'blog_posts';
    
    /**
     * @var array The fillable attributes
     */
    protected array $fillable = [
        'user_id',
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image',
        'status',
        'published_at'
    ];
    
    /**
     * @var array The date attributes
     */
    protected array $dates = [
        'created_at',
        'updated_at',
        'published_at'
    ];
    
    /**
     * Get the author of this post
     * 
     * @return User|null The author or null if not found
     */
    public function author(): ?User
    {
        if (empty($this->user_id)) {
            return null;
        }
        
        return User::find($this->user_id);
    }
    
    /**
     * Get the categories of this post
     * 
     * @return array The categories
     */
    public function categories(): array
    {
        $query = "
            SELECT c.*
            FROM blog_categories c
            JOIN blog_post_categories pc ON c.id = pc.category_id
            WHERE pc.post_id = :post_id
            ORDER BY c.name ASC
        ";
        
        $results = $this->db->fetchAll($query, [':post_id' => $this->id]);
        
        $categories = [];
        foreach ($results as $result) {
            $categories[] = new BlogCategory($result);
        }
        
        return $categories;
    }
    
    /**
     * Get the tags of this post
     * 
     * @return array The tags
     */
    public function tags(): array
    {
        $query = "
            SELECT t.*
            FROM blog_tags t
            JOIN blog_post_tags pt ON t.id = pt.tag_id
            WHERE pt.post_id = :post_id
            ORDER BY t.name ASC
        ";
        
        $results = $this->db->fetchAll($query, [':post_id' => $this->id]);
        
        $tags = [];
        foreach ($results as $result) {
            $tags[] = new BlogTag($result);
        }
        
        return $tags;
    }
    
    /**
     * Get the comments of this post
     * 
     * @param string $status The comment status to filter by (all, pending, approved, spam)
     * @return array The comments
     */
    public function comments(string $status = 'approved'): array
    {
        $query = "
            SELECT c.*
            FROM blog_comments c
            WHERE c.post_id = :post_id
        ";
        
        $params = [':post_id' => $this->id];
        
        if ($status !== 'all') {
            $query .= " AND c.status = :status";
            $params[':status'] = $status;
        }
        
        $query .= " ORDER BY c.created_at DESC";
        
        $results = $this->db->fetchAll($query, $params);
        
        $comments = [];
        foreach ($results as $result) {
            $comments[] = new BlogComment($result);
        }
        
        return $comments;
    }
    
    /**
     * Add a category to this post
     * 
     * @param int $categoryId The category ID
     * @return bool True on success
     */
    public function addCategory(int $categoryId): bool
    {
        $query = "
            INSERT INTO blog_post_categories (post_id, category_id)
            VALUES (:post_id, :category_id)
            ON CONFLICT (post_id, category_id) DO NOTHING
        ";
        
        $this->db->execute($query, [
            ':post_id' => $this->id,
            ':category_id' => $categoryId
        ]);
        
        return true;
    }
    
    /**
     * Remove a category from this post
     * 
     * @param int $categoryId The category ID
     * @return bool True on success
     */
    public function removeCategory(int $categoryId): bool
    {
        $query = "
            DELETE FROM blog_post_categories
            WHERE post_id = :post_id AND category_id = :category_id
        ";
        
        $this->db->execute($query, [
            ':post_id' => $this->id,
            ':category_id' => $categoryId
        ]);
        
        return true;
    }
    
    /**
     * Add a tag to this post
     * 
     * @param int $tagId The tag ID
     * @return bool True on success
     */
    public function addTag(int $tagId): bool
    {
        $query = "
            INSERT INTO blog_post_tags (post_id, tag_id)
            VALUES (:post_id, :tag_id)
            ON CONFLICT (post_id, tag_id) DO NOTHING
        ";
        
        $this->db->execute($query, [
            ':post_id' => $this->id,
            ':tag_id' => $tagId
        ]);
        
        return true;
    }
    
    /**
     * Remove a tag from this post
     * 
     * @param int $tagId The tag ID
     * @return bool True on success
     */
    public function removeTag(int $tagId): bool
    {
        $query = "
            DELETE FROM blog_post_tags
            WHERE post_id = :post_id AND tag_id = :tag_id
        ";
        
        $this->db->execute($query, [
            ':post_id' => $this->id,
            ':tag_id' => $tagId
        ]);
        
        return true;
    }
    
    /**
     * Get the formatted published date
     * 
     * @param string $format The date format
     * @return string The formatted date
     */
    public function formattedPublishedDate(string $format = 'F j, Y'): string
    {
        if ($this->published_at instanceof DateTime) {
            return $this->published_at->format($format);
        }
        
        return '';
    }
    
    /**
     * Get the excerpt of the post
     * 
     * @param int $length The maximum length of the excerpt
     * @return string The excerpt
     */
    public function getExcerpt(int $length = 150): string
    {
        if (!empty($this->excerpt)) {
            return $this->excerpt;
        }
        
        $excerpt = strip_tags($this->content);
        if (strlen($excerpt) <= $length) {
            return $excerpt;
        }
        
        $excerpt = substr($excerpt, 0, $length);
        $lastSpace = strrpos($excerpt, ' ');
        if ($lastSpace !== false) {
            $excerpt = substr($excerpt, 0, $lastSpace);
        }
        
        return $excerpt . '...';
    }
    
    /**
     * Get published posts
     * 
     * @param int $limit The maximum number of posts to return
     * @param int $offset The offset for pagination
     * @return array The published posts
     */
    public static function published(int $limit = 10, int $offset = 0): array
    {
        $instance = new static();
        $query = "
            SELECT *
            FROM {$instance->table}
            WHERE status = 'published'
            AND published_at <= datetime('now')
            ORDER BY published_at DESC
            LIMIT :limit OFFSET :offset
        ";
        
        $results = $instance->db->fetchAll($query, [
            ':limit' => $limit,
            ':offset' => $offset
        ]);
        
        $posts = [];
        foreach ($results as $result) {
            $posts[] = new static($result);
        }
        
        return $posts;
    }
    
    /**
     * Get posts by category
     * 
     * @param int $categoryId The category ID
     * @param int $limit The maximum number of posts to return
     * @param int $offset The offset for pagination
     * @return array The posts in the category
     */
    public static function byCategory(int $categoryId, int $limit = 10, int $offset = 0): array
    {
        $instance = new static();
        $query = "
            SELECT p.*
            FROM {$instance->table} p
            JOIN blog_post_categories pc ON p.id = pc.post_id
            WHERE pc.category_id = :category_id
            AND p.status = 'published'
            AND p.published_at <= datetime('now')
            ORDER BY p.published_at DESC
            LIMIT :limit OFFSET :offset
        ";
        
        $results = $instance->db->fetchAll($query, [
            ':category_id' => $categoryId,
            ':limit' => $limit,
            ':offset' => $offset
        ]);
        
        $posts = [];
        foreach ($results as $result) {
            $posts[] = new static($result);
        }
        
        return $posts;
    }
    
    /**
     * Get posts by tag
     * 
     * @param int $tagId The tag ID
     * @param int $limit The maximum number of posts to return
     * @param int $offset The offset for pagination
     * @return array The posts with the tag
     */
    public static function byTag(int $tagId, int $limit = 10, int $offset = 0): array
    {
        $instance = new static();
        $query = "
            SELECT p.*
            FROM {$instance->table} p
            JOIN blog_post_tags pt ON p.id = pt.post_id
            WHERE pt.tag_id = :tag_id
            AND p.status = 'published'
            AND p.published_at <= datetime('now')
            ORDER BY p.published_at DESC
            LIMIT :limit OFFSET :offset
        ";
        
        $results = $instance->db->fetchAll($query, [
            ':tag_id' => $tagId,
            ':limit' => $limit,
            ':offset' => $offset
        ]);
        
        $posts = [];
        foreach ($results as $result) {
            $posts[] = new static($result);
        }
        
        return $posts;
    }
    
    /**
     * Search for posts
     * 
     * @param string $search The search term
     * @param int $limit The maximum number of posts to return
     * @param int $offset The offset for pagination
     * @return array The matching posts
     */
    public static function search(string $search, int $limit = 10, int $offset = 0): array
    {
        $instance = new static();
        $query = "
            SELECT *
            FROM {$instance->table}
            WHERE (title LIKE :search OR content LIKE :search)
            AND status = 'published'
            AND published_at <= datetime('now')
            ORDER BY published_at DESC
            LIMIT :limit OFFSET :offset
        ";
        
        $results = $instance->db->fetchAll($query, [
            ':search' => '%' . $search . '%',
            ':limit' => $limit,
            ':offset' => $offset
        ]);
        
        $posts = [];
        foreach ($results as $result) {
            $posts[] = new static($result);
        }
        
        return $posts;
    }
    
    /**
     * Count the total number of published posts
     * 
     * @return int The total number of published posts
     */
    public static function countPublished(): int
    {
        $instance = new static();
        $query = "
            SELECT COUNT(*) as count
            FROM {$instance->table}
            WHERE status = 'published'
            AND published_at <= datetime('now')
        ";
        
        $result = $instance->db->fetch($query);
        
        return (int) ($result['count'] ?? 0);
    }
    
    /**
     * Count the total number of posts in a category
     * 
     * @param int $categoryId The category ID
     * @return int The total number of posts in the category
     */
    public static function countByCategory(int $categoryId): int
    {
        $instance = new static();
        $query = "
            SELECT COUNT(*) as count
            FROM {$instance->table} p
            JOIN blog_post_categories pc ON p.id = pc.post_id
            WHERE pc.category_id = :category_id
            AND p.status = 'published'
            AND p.published_at <= datetime('now')
        ";
        
        $result = $instance->db->fetch($query, [':category_id' => $categoryId]);
        
        return (int) ($result['count'] ?? 0);
    }
    
    /**
     * Count the total number of posts with a tag
     * 
     * @param int $tagId The tag ID
     * @return int The total number of posts with the tag
     */
    public static function countByTag(int $tagId): int
    {
        $instance = new static();
        $query = "
            SELECT COUNT(*) as count
            FROM {$instance->table} p
            JOIN blog_post_tags pt ON p.id = pt.post_id
            WHERE pt.tag_id = :tag_id
            AND p.status = 'published'
            AND p.published_at <= datetime('now')
        ";
        
        $result = $instance->db->fetch($query, [':tag_id' => $tagId]);
        
        return (int) ($result['count'] ?? 0);
    }
}