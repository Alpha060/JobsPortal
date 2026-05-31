<?php
/**
 * Category Model
 */
class Category
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /** Get all active categories ordered by sort_order */
    public function getAll(bool $activeOnly = true): array
    {
        $where = $activeOnly ? "WHERE is_active = 1" : "";
        return $this->db->fetchAll(
            "SELECT * FROM `categories` {$where} ORDER BY `sort_order` ASC"
        );
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
        return $this->db->fetchAll(
            "SELECT c.*, COUNT(p.id) AS post_count
             FROM `categories` c
             LEFT JOIN `posts` p ON c.id = p.category_id AND p.status = 'published' AND p.is_active = 1
             WHERE c.is_active = 1
             GROUP BY c.id
             ORDER BY c.sort_order ASC"
        );
    }

    /** Create a new category */
    public function create(array $data): int
    {
        $id = $this->db->insert('categories', $data);
        triggerRealtimeUpdate();
        return $id;
    }

    /** Update a category */
    public function update(int $id, array $data): int
    {
        $result = $this->db->update('categories', $data, 'id = ?', [$id]);
        triggerRealtimeUpdate();
        return $result;
    }

    /** Delete a category (only if no posts attached) */
    public function delete(int $id): bool
    {
        $postCount = $this->db->count('posts', 'category_id = ?', [$id]);
        if ($postCount > 0) return false;
        $this->db->delete('categories', 'id = ?', [$id]);
        triggerRealtimeUpdate();
        return true;
    }

    /** Toggle active status */
    public function toggleActive(int $id): void
    {
        $this->db->query(
            "UPDATE `categories` SET `is_active` = NOT `is_active` WHERE `id` = ?",
            [$id]
        );
        triggerRealtimeUpdate();
    }
}
