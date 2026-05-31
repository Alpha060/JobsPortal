<?php
/**
 * Search Controller
 */
class SearchController
{
    public function index(): void
    {
        $query = trim($_GET['q'] ?? '');
        $categorySlug = $_GET['category'] ?? null;
        $page = max(1, (int)($_GET['page'] ?? 1));

        if (empty($query)) {
            $result = ['posts' => [], 'pagination' => new Pagination(0), 'total' => 0, 'query' => ''];
        } else {
            $postModel = new Post();
            $result = $postModel->search($query, $categorySlug, $page);
        }

        include VIEWS_PATH . '/public/search.php';
    }
}
