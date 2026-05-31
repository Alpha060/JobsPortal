<?php
/**
 * Post Controller
 * Handles category listing and single post detail pages.
 */
class PostController
{
    private Post $postModel;
    private Category $categoryModel;

    public function __construct()
    {
        $this->postModel = new Post();
        $this->categoryModel = new Category();
    }

    /**
     * Category listing page
     */
    public function listing(string $categorySlug): void
    {
        $category = $this->categoryModel->getBySlug($categorySlug);

        if (!$category) {
            http_response_code(404);
            $lang = Lang::getInstance();
            include VIEWS_PATH . '/public/404.php';
            return;
        }

        $page = max(1, (int)($_GET['page'] ?? 1));
        $result = $this->postModel->getAll($categorySlug, $page);

        include VIEWS_PATH . '/public/listing.php';
    }

    /**
     * Single post detail page
     */
    public function detail(string $categorySlug, string $postSlug): void
    {
        $post = $this->postModel->getBySlug($postSlug);

        if (!$post || $post['category_slug'] !== $categorySlug) {
            http_response_code(404);
            $lang = Lang::getInstance();
            include VIEWS_PATH . '/public/404.php';
            return;
        }

        // Increment views
        $this->postModel->incrementViews($post['id']);
        $post['views']++;

        // Get related posts
        $relatedPosts = $this->postModel->getRelated($post['id'], $post['category_id'], 5);

        include VIEWS_PATH . '/public/detail.php';
    }
}
