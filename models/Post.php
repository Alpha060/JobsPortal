<?php
/**
 * Post Model
 * 
 * Handles CRUD operations for all post types (jobs, results, admit cards, etc.)
 */
class Post
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get paginated posts, optionally filtered by category
     */
    public function getAll(?string $categorySlug = null, int $page = 1, int $perPage = 20, string $status = 'published'): array
    {
        $where = "p.status = ? AND p.is_active = 1";
        $params = [$status];

        if ($categorySlug) {
            $where .= " AND c.slug = ?";
            $params[] = $categorySlug;
        }

        // Get total count
        $total = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM `posts` p JOIN `categories` c ON p.category_id = c.id WHERE {$where}",
            $params
        );

        $pagination = new Pagination($total, $page, $perPage);

        // Get posts
        $posts = $this->db->fetchAll(
            "SELECT p.*, c.name_en AS category_name_en, c.name_hi AS category_name_hi,
                    c.slug AS category_slug, c.icon AS category_icon, c.color AS category_color,
                    c.gradient_from, c.gradient_to
             FROM `posts` p
             JOIN `categories` c ON p.category_id = c.id
             WHERE {$where}
             ORDER BY p.is_featured DESC, p.created_at DESC
             LIMIT ? OFFSET ?",
            array_merge($params, [$pagination->limit(), $pagination->offset()])
        );

        return [
            'posts'      => $posts,
            'pagination' => $pagination,
            'total'      => $total,
        ];
    }

    /**
     * Get a single post by slug
     */
    public function getBySlug(string $slug): ?array
    {
        return $this->db->fetch(
            "SELECT p.*, c.name_en AS category_name_en, c.name_hi AS category_name_hi,
                    c.slug AS category_slug, c.icon AS category_icon, c.color AS category_color,
                    c.gradient_from, c.gradient_to
             FROM `posts` p
             JOIN `categories` c ON p.category_id = c.id
             WHERE p.slug = ? AND p.is_active = 1
             LIMIT 1",
            [$slug]
        );
    }

    /**
     * Get a single post by ID
     */
    public function getById(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT p.*, c.name_en AS category_name_en, c.name_hi AS category_name_hi,
                    c.slug AS category_slug
             FROM `posts` p
             JOIN `categories` c ON p.category_id = c.id
             WHERE p.id = ?
             LIMIT 1",
            [$id]
        );
    }

    /**
     * Get featured posts for the homepage
     */
    public function getFeatured(int $limit = 5): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, c.name_en AS category_name_en, c.name_hi AS category_name_hi,
                    c.slug AS category_slug, c.icon AS category_icon, c.color AS category_color,
                    c.gradient_from, c.gradient_to
             FROM `posts` p
             JOIN `categories` c ON p.category_id = c.id
             WHERE p.is_featured = 1 AND p.status = 'published' AND p.is_active = 1
             ORDER BY p.created_at DESC
             LIMIT ?",
            [$limit]
        );
    }

    /**
     * Get trending posts for the news ticker marquee
     */
    public function getTrending(int $limit = 10): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, c.slug AS category_slug
             FROM `posts` p
             JOIN `categories` c ON p.category_id = c.id
             WHERE p.is_trending = 1 AND p.status = 'published' AND p.is_active = 1
             ORDER BY p.updated_at DESC
             LIMIT ?",
            [$limit]
        );
    }

    /**
     * Get latest posts across all categories
     */
    public function getLatest(int $limit = 10): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, c.name_en AS category_name_en, c.name_hi AS category_name_hi,
                    c.slug AS category_slug, c.icon AS category_icon, c.color AS category_color,
                    c.gradient_from, c.gradient_to
             FROM `posts` p
             JOIN `categories` c ON p.category_id = c.id
             WHERE p.status = 'published' AND p.is_active = 1
             ORDER BY p.created_at DESC
             LIMIT ?",
            [$limit]
        );
    }

    /**
     * Get latest posts for a specific category
     */
    public function getLatestByCategory(string $categorySlug, int $limit = 5): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, c.name_en AS category_name_en, c.name_hi AS category_name_hi,
                    c.slug AS category_slug, c.icon AS category_icon, c.color AS category_color
             FROM `posts` p
             JOIN `categories` c ON p.category_id = c.id
             WHERE c.slug = ? AND p.status = 'published' AND p.is_active = 1
             ORDER BY p.created_at DESC
             LIMIT ?",
            [$categorySlug, $limit]
        );
    }

    /**
     * Get latest posts for multiple categories in a single database roundtrip using UNION ALL
     */
    public function getLatestForCategories(array $categorySlugs, int $limit = 10): array
    {
        if (empty($categorySlugs)) {
            return [];
        }

        $queries = [];
        $params = [];

        foreach ($categorySlugs as $slug) {
            $queries[] = "(SELECT p.*, c.name_en AS category_name_en, c.name_hi AS category_name_hi,
                                   c.slug AS category_slug, c.icon AS category_icon, c.color AS category_color
                            FROM `posts` p
                            JOIN `categories` c ON p.category_id = c.id
                            WHERE c.slug = ? AND p.status = 'published' AND p.is_active = 1
                            ORDER BY p.created_at DESC
                            LIMIT " . (int)$limit . ")";
            $params[] = $slug;
        }

        $sql = implode(" UNION ALL ", $queries);
        
        $rows = $this->db->fetchAll($sql, $params);

        $grouped = [];
        foreach ($categorySlugs as $slug) {
            $grouped[$slug] = [];
        }
        foreach ($rows as $row) {
            $grouped[$row['category_slug']][] = $row;
        }

        return $grouped;
    }

    /**
     * Get most viewed posts
     */
    public function getMostViewed(int $limit = 10): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, c.name_en AS category_name_en, c.name_hi AS category_name_hi,
                    c.slug AS category_slug, c.icon AS category_icon, c.color AS category_color
             FROM `posts` p
             JOIN `categories` c ON p.category_id = c.id
             WHERE p.status = 'published' AND p.is_active = 1
             ORDER BY p.views DESC
             LIMIT ?",
            [$limit]
        );
    }

    /**
     * Search posts with full-text search
     */
    public function search(string $query, ?string $categorySlug = null, int $page = 1, int $perPage = 20): array
    {
        $searchTerm = '%' . $query . '%';
        $where = "(p.title_en LIKE ? OR p.title_hi LIKE ? OR p.excerpt_en LIKE ? OR p.organization LIKE ?) AND p.status = 'published' AND p.is_active = 1";
        $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];

        if ($categorySlug) {
            $where .= " AND c.slug = ?";
            $params[] = $categorySlug;
        }

        // Get total count
        $total = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM `posts` p JOIN `categories` c ON p.category_id = c.id WHERE {$where}",
            $params
        );

        $pagination = new Pagination($total, $page, $perPage);

        $posts = $this->db->fetchAll(
            "SELECT p.*, c.name_en AS category_name_en, c.name_hi AS category_name_hi,
                    c.slug AS category_slug, c.icon AS category_icon, c.color AS category_color,
                    c.gradient_from, c.gradient_to
             FROM `posts` p
             JOIN `categories` c ON p.category_id = c.id
             WHERE {$where}
             ORDER BY p.created_at DESC
             LIMIT ? OFFSET ?",
            array_merge($params, [$pagination->limit(), $pagination->offset()])
        );

        return [
            'posts'      => $posts,
            'pagination' => $pagination,
            'total'      => $total,
            'query'      => $query,
        ];
    }

    /**
     * Increment the view counter
     */
    public function incrementViews(int $id): void
    {
        $this->db->query("UPDATE `posts` SET `views` = `views` + 1 WHERE `id` = ?", [$id]);
    }

    /**
     * Get related posts (same category, excluding current)
     */
    public function getRelated(int $postId, int $categoryId, int $limit = 5): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, c.name_en AS category_name_en, c.name_hi AS category_name_hi,
                    c.slug AS category_slug, c.icon AS category_icon, c.color AS category_color,
                    c.gradient_from, c.gradient_to
             FROM `posts` p
             JOIN `categories` c ON p.category_id = c.id
             WHERE p.category_id = ? AND p.id != ? AND p.status = 'published' AND p.is_active = 1
             ORDER BY p.created_at DESC
             LIMIT ?",
            [$categoryId, $postId, $limit]
        );
    }

    /**
     * Create a new post
     */
    public function create(array $data): int
    {
        $id = $this->db->insert('posts', $data);
        triggerRealtimeUpdate();
        return $id;
    }

    /**
     * Update a post
     */
    public function update(int $id, array $data): int
    {
        $result = $this->db->update('posts', $data, 'id = ?', [$id]);
        triggerRealtimeUpdate();
        return $result;
    }

    /**
     * Delete a post
     */
    public function delete(int $id): int
    {
        $result = $this->db->delete('posts', 'id = ?', [$id]);
        triggerRealtimeUpdate();
        return $result;
    }

    /**
     * Toggle post active/inactive status
     */
    public function toggleActive(int $id): void
    {
        $this->db->query(
            "UPDATE `posts` SET `is_active` = NOT `is_active` WHERE `id` = ?",
            [$id]
        );
        triggerRealtimeUpdate();
    }

    /**
     * Toggle post featured status
     */
    public function toggleFeatured(int $id): void
    {
        $this->db->query(
            "UPDATE `posts` SET `is_featured` = NOT `is_featured` WHERE `id` = ?",
            [$id]
        );
        triggerRealtimeUpdate();
    }

    /**
     * Toggle post trending status
     */
    public function toggleTrending(int $id): void
    {
        $this->db->query(
            "UPDATE `posts` SET `is_trending` = NOT `is_trending` WHERE `id` = ?",
            [$id]
        );
        triggerRealtimeUpdate();
    }

    /**
     * Get post statistics for admin dashboard
     */
    public function getStats(): array
    {
        return [
            'total'     => $this->db->count('posts'),
            'published' => $this->db->count('posts', "status = 'published'"),
            'draft'     => $this->db->count('posts', "status = 'draft'"),
            'featured'  => $this->db->count('posts', "is_featured = 1"),
            'views'     => (int) $this->db->fetchColumn("SELECT SUM(views) FROM `posts`") ?: 0,
        ];
    }

    /**
     * Get all posts for admin (including drafts)
     */
    public function getAllAdmin(?string $categorySlug = null, ?string $status = null, ?string $search = null, int $page = 1, int $perPage = 20): array
    {
        $where = "1=1";
        $params = [];

        if ($categorySlug) {
            $where .= " AND c.slug = ?";
            $params[] = $categorySlug;
        }

        if ($status) {
            $where .= " AND p.status = ?";
            $params[] = $status;
        }

        if ($search) {
            $where .= " AND (p.title_en LIKE ? OR p.title_hi LIKE ? OR p.organization LIKE ?)";
            $searchTerm = '%' . $search . '%';
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
        }

        $total = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM `posts` p JOIN `categories` c ON p.category_id = c.id WHERE {$where}",
            $params
        );

        $pagination = new Pagination($total, $page, $perPage);

        $posts = $this->db->fetchAll(
            "SELECT p.*, c.name_en AS category_name_en, c.name_hi AS category_name_hi,
                    c.slug AS category_slug
             FROM `posts` p
             JOIN `categories` c ON p.category_id = c.id
             WHERE {$where}
             ORDER BY p.created_at DESC
             LIMIT ? OFFSET ?",
            array_merge($params, [$pagination->limit(), $pagination->offset()])
        );

        return [
            'posts'      => $posts,
            'pagination' => $pagination,
            'total'      => $total,
        ];
    }
}
