<?php
/**
 * Category Model
 */
class Category
{
    private Database $db;
    private static array $cache = [];
    private static ?array $countCache = null;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /** Get all active categories ordered by sort_order */
    public function getAll(bool $activeOnly = true): array
    {
        $cacheKey = $activeOnly ? 'active' : 'all';
        if (isset(self::$cache[$cacheKey])) {
            return self::$cache[$cacheKey];
        }

        $where = $activeOnly ? "WHERE is_active = 1" : "";
        $result = $this->db->fetchAll(
            "SELECT * FROM `categories` {$where} ORDER BY `sort_order` ASC"
        );
        self::$cache[$cacheKey] = $result;
        return $result;
    }

    /** Get a category by slug */
    public function getBySlug(string $slug): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM `categories` WHERE `slug` = ? LIMIT 1",
            [$slug]
        );
    }

    /** Get a category by ID */
    public function getById(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM `categories` WHERE `id` = ? LIMIT 1",
            [$id]
        );
    }

    /** Get all categories with their post count */
    public function getWithPostCount(): array
    {
        if (self::$countCache !== null) {
            return self::$countCache;
        }

        self::$countCache = $this->db->fetchAll(
            "SELECT c.*, COUNT(p.id) AS post_count
             FROM `categories` c
             LEFT JOIN `posts` p ON c.id = p.category_id AND p.status = 'published' AND p.is_active = 1
             WHERE c.is_active = 1
             GROUP BY c.id
             ORDER BY c.sort_order ASC"
        );

        return self::$countCache;
    }

    private function clearCache(): void
    {
        self::$cache = [];
        self::$countCache = null;
    }

    /** Create a new category */
    public function create(array $data): int
    {
        $this->clearCache();
        $id = $this->db->insert('categories', $data);
        triggerRealtimeUpdate();
        return $id;
    }

    /** Update a category */
    public function update(int $id, array $data): int
    {
        $this->clearCache();
        $result = $this->db->update('categories', $data, 'id = ?', [$id]);
        triggerRealtimeUpdate();
        return $result;
    }

    /** Delete a category (only if no posts attached) */
    public function delete(int $id): bool
    {
        $postCount = $this->db->count('posts', 'category_id = ?', [$id]);
        if ($postCount > 0) return false;
        $this->clearCache();
        $this->db->delete('categories', 'id = ?', [$id]);
        triggerRealtimeUpdate();
        return true;
    }

    /** Toggle active status */
    public function toggleActive(int $id): void
    {
        $this->clearCache();
        $this->db->query(
            "UPDATE `categories` SET `is_active` = NOT `is_active` WHERE `id` = ?",
            [$id]
        );
        triggerRealtimeUpdate();
    }
}
