<?php

namespace App\Services;

use App\Interfaces\ServiceInterface;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Core\Database;
use Exception;

/**
 * Blog Service
 * 
 * Handles business logic related to blog posts, categories, and tags
 */
class BlogService implements ServiceInterface
{
    /**
     * @var Database
     */
    private Database $db;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get all blog posts
     * 
     * @param array $params Optional parameters for filtering, sorting, etc.
     * @return array Collection of blog posts
     */
    public function getAll(array $params = []): array
    {
        $limit = $params['limit'] ?? 20;
        $offset = $params['offset'] ?? 0;
        $orderBy = $params['order_by'] ?? 'published_at';
        $orderDir = $params['order_dir'] ?? 'DESC';
        $publishedOnly = $params['published_only'] ?? true;
        
        $query = "SELECT * FROM blog_posts";
        
        if ($publishedOnly) {
            $query .= " WHERE status = 'published' AND published_at <= NOW()";
        }
        
        $query .= " ORDER BY {$orderBy} {$orderDir} LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $posts = [];
        while ($row = $result->fetch_assoc()) {
            $post = new BlogPost();
            $post->fill($row);
            $posts[] = $post;
        }
        
        return $posts;
    }
    
    /**
     * Get a blog post by ID
     * 
     * @param int $id Blog post ID
     * @return BlogPost|null The blog post or null if not found
     */
    public function getById(int $id): ?BlogPost
    {
        $query = "SELECT * FROM blog_posts WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $post = new BlogPost();
            $post->fill($row);
            return $post;
        }
        
        return null;
    }
    
    /**
     * Get a blog post by slug
     * 
     * @param string $slug Blog post slug
     * @return BlogPost|null The blog post or null if not found
     */
    public function getBySlug(string $slug): ?BlogPost
    {
        $query = "SELECT * FROM blog_posts WHERE slug = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $slug);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $post = new BlogPost();
            $post->fill($row);
            return $post;
        }
        
        return null;
    }
    
    /**
     * Create a new blog post
     * 
     * @param array $data Blog post data
     * @return BlogPost|null The created blog post
     */
    public function create(array $data): ?BlogPost
    {
        if (!isset($data['user_id'])) {
            throw new Exception('User ID is required');
        }
        
        $post = new BlogPost();
        $post->fill($data);
        
        if ($post->save()) {
            // Handle categories if provided
            if (isset($data['categories']) && is_array($data['categories'])) {
                foreach ($data['categories'] as $categoryId) {
                    $post->addCategory((int)$categoryId);
                }
            }
            
            // Handle tags if provided
            if (isset($data['tags']) && is_array($data['tags'])) {
                foreach ($data['tags'] as $tagId) {
                    $post->addTag((int)$tagId);
                }
            }
            
            return $post;
        }
        
        return null;
    }
    
    /**
     * Update an existing blog post
     * 
     * @param int $id Blog post ID
     * @param array $data Updated blog post data
     * @return BlogPost|null The updated blog post
     */
    public function update(int $id, array $data): ?BlogPost
    {
        $post = $this->getById($id);
        
        if (!$post) {
            return null;
        }
        
        // Handle categories if provided
        if (isset($data['categories'])) {
            // Get current categories
            $currentCategories = $post->categories();
            $currentCategoryIds = array_map(function($category) {
                return $category->id;
            }, $currentCategories);
            
            // Add new categories
            foreach ($data['categories'] as $categoryId) {
                if (!in_array($categoryId, $currentCategoryIds)) {
                    $post->addCategory((int)$categoryId);
                }
            }
            
            // Remove categories that are no longer associated
            foreach ($currentCategoryIds as $categoryId) {
                if (!in_array($categoryId, $data['categories'])) {
                    $post->removeCategory((int)$categoryId);
                }
            }
            
            unset($data['categories']);
        }
        
        // Handle tags if provided
        if (isset($data['tags'])) {
            // Get current tags
            $currentTags = $post->tags();
            $currentTagIds = array_map(function($tag) {
                return $tag->id;
            }, $currentTags);
            
            // Add new tags
            foreach ($data['tags'] as $tagId) {
                if (!in_array($tagId, $currentTagIds)) {
                    $post->addTag((int)$tagId);
                }
            }
            
            // Remove tags that are no longer associated
            foreach ($currentTagIds as $tagId) {
                if (!in_array($tagId, $data['tags'])) {
                    $post->removeTag((int)$tagId);
                }
            }
            
            unset($data['tags']);
        }
        
        // Update other attributes
        $post->fill($data);
        
        // Save changes
        $post->save();
        
        return $post;
    }
    
    /**
     * Delete a blog post
     * 
     * @param int $id Blog post ID
     * @return bool True if successful, false otherwise
     */
    public function delete(int $id): bool
    {
        $post = $this->getById($id);
        
        if (!$post) {
            return false;
        }
        
        return $post->delete();
    }
    
    /**
     * Search for blog posts
     * 
     * @param string $search Search term
     * @param int $limit Maximum number of results
     * @param int $offset Offset for pagination
     * @return array Matching blog posts
     */
    public function search(string $search, int $limit = 20, int $offset = 0): array
    {
        return BlogPost::search($search, $limit, $offset);
    }
    
    /**
     * Get published blog posts
     * 
     * @param int $limit Maximum number of posts to return
     * @param int $offset Offset for pagination
     * @return array Published blog posts
     */
    public function getPublished(int $limit = 20, int $offset = 0): array
    {
        return BlogPost::published($limit, $offset);
    }
    
    /**
     * Get blog posts by category
     * 
     * @param int $categoryId Category ID
     * @param int $limit Maximum number of posts to return
     * @param int $offset Offset for pagination
     * @return array Blog posts in the specified category
     */
    public function getByCategory(int $categoryId, int $limit = 20, int $offset = 0): array
    {
        return BlogPost::byCategory($categoryId, $limit, $offset);
    }
    
    /**
     * Get blog posts by tag
     * 
     * @param int $tagId Tag ID
     * @param int $limit Maximum number of posts to return
     * @param int $offset Offset for pagination
     * @return array Blog posts with the specified tag
     */
    public function getByTag(int $tagId, int $limit = 20, int $offset = 0): array
    {
        return BlogPost::byTag($tagId, $limit, $offset);
    }
    
    /**
     * Get blog posts by author
     * 
     * @param int $authorId Author ID
     * @param int $limit Maximum number of posts to return
     * @param int $offset Offset for pagination
     * @return array Blog posts by the specified author
     */
    public function getByAuthor(int $authorId, int $limit = 20, int $offset = 0): array
    {
        $query = "SELECT * FROM blog_posts WHERE user_id = ? AND status = 'published' AND published_at <= NOW() ORDER BY published_at DESC LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('iii', $authorId, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $posts = [];
        while ($row = $result->fetch_assoc()) {
            $post = new BlogPost();
            $post->fill($row);
            $posts[] = $post;
        }
        
        return $posts;
    }
    
    /**
     * Count published blog posts
     * 
     * @return int Number of published blog posts
     */
    public function countPublished(): int
    {
        return BlogPost::countPublished();
    }
    
    /**
     * Count blog posts by category
     * 
     * @param int $categoryId Category ID
     * @return int Number of blog posts in the specified category
     */
    public function countByCategory(int $categoryId): int
    {
        return BlogPost::countByCategory($categoryId);
    }
    
    /**
     * Count blog posts by tag
     * 
     * @param int $tagId Tag ID
     * @return int Number of blog posts with the specified tag
     */
    public function countByTag(int $tagId): int
    {
        return BlogPost::countByTag($tagId);
    }
    
    /**
     * Get all blog categories
     * 
     * @return array All blog categories
     */
    public function getAllCategories(): array
    {
        $query = "SELECT * FROM blog_categories ORDER BY name ASC";
        $result = $this->db->query($query);
        
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $category = new BlogCategory();
            $category->fill($row);
            $categories[] = $category;
        }
        
        return $categories;
    }
    
    /**
     * Get a blog category by ID
     * 
     * @param int $id Category ID
     * @return BlogCategory|null The category or null if not found
     */
    public function getCategoryById(int $id): ?BlogCategory
    {
        $query = "SELECT * FROM blog_categories WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $category = new BlogCategory();
            $category->fill($row);
            return $category;
        }
        
        return null;
    }
    
    /**
     * Create a new blog category
     * 
     * @param array $data Category data
     * @return BlogCategory|null The created category
     */
    public function createCategory(array $data): ?BlogCategory
    {
        $category = new BlogCategory();
        $category->fill($data);
        
        if ($category->save()) {
            return $category;
        }
        
        return null;
    }
    
    /**
     * Update an existing blog category
     * 
     * @param int $id Category ID
     * @param array $data Updated category data
     * @return BlogCategory|null The updated category
     */
    public function updateCategory(int $id, array $data): ?BlogCategory
    {
        $category = $this->getCategoryById($id);
        
        if (!$category) {
            return null;
        }
        
        $category->fill($data);
        $category->save();
        
        return $category;
    }
    
    /**
     * Delete a blog category
     * 
     * @param int $id Category ID
     * @return bool True if successful, false otherwise
     */
    public function deleteCategory(int $id): bool
    {
        $category = $this->getCategoryById($id);
        
        if (!$category) {
            return false;
        }
        
        return $category->delete();
    }
    
    /**
     * Get all blog tags
     * 
     * @return array All blog tags
     */
    public function getAllTags(): array
    {
        $query = "SELECT * FROM blog_tags ORDER BY name ASC";
        $result = $this->db->query($query);
        
        $tags = [];
        while ($row = $result->fetch_assoc()) {
            $tag = new BlogTag();
            $tag->fill($row);
            $tags[] = $tag;
        }
        
        return $tags;
    }
    
    /**
     * Get a blog tag by ID
     * 
     * @param int $id Tag ID
     * @return BlogTag|null The tag or null if not found
     */
    public function getTagById(int $id): ?BlogTag
    {
        $query = "SELECT * FROM blog_tags WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $tag = new BlogTag();
            $tag->fill($row);
            return $tag;
        }
        
        return null;
    }
    
    /**
     * Create a new blog tag
     * 
     * @param array $data Tag data
     * @return BlogTag|null The created tag
     */
    public function createTag(array $data): ?BlogTag
    {
        $tag = new BlogTag();
        $tag->fill($data);
        
        if ($tag->save()) {
            return $tag;
        }
        
        return null;
    }
    
    /**
     * Update an existing blog tag
     * 
     * @param int $id Tag ID
     * @param array $data Updated tag data
     * @return BlogTag|null The updated tag
     */
    public function updateTag(int $id, array $data): ?BlogTag
    {
        $tag = $this->getTagById($id);
        
        if (!$tag) {
            return null;
        }
        
        $tag->fill($data);
        $tag->save();
        
        return $tag;
    }
    
    /**
     * Delete a blog tag
     * 
     * @param int $id Tag ID
     * @return bool True if successful, false otherwise
     */
    public function deleteTag(int $id): bool
    {
        $tag = $this->getTagById($id);
        
        if (!$tag) {
            return false;
        }
        
        return $tag->delete();
    }
}