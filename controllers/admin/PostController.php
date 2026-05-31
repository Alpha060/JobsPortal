<?php
/**
 * Admin Post Controller
 * Handles CRUD operations for posts in the admin panel.
 */
class AdminPostController
{
    private Post $postModel;
    private Category $categoryModel;
    private Auth $auth;

    public function __construct()
    {
        $this->auth = new Auth();
        $this->auth->requireAuth();
        $this->postModel = new Post();
        $this->categoryModel = new Category();
    }

    /** List all posts */
    public function index(): void
    {
        $categorySlug = $_GET['category'] ?? null;
        $status = $_GET['status'] ?? null;
        $search = $_GET['search'] ?? null;
        $page = max(1, (int)($_GET['page'] ?? 1));

        $result = $this->postModel->getAllAdmin($categorySlug, $status, $search, $page);
        $categories = $this->categoryModel->getAll(false);

        $adminPageTitle = __('manage_posts');
        include VIEWS_PATH . '/admin/posts/index.php';
    }

    /** Show create form */
    public function create(): void
    {
        $categories = $this->categoryModel->getAll(false);
        $adminPageTitle = 'Create Post';
        include VIEWS_PATH . '/admin/posts/create.php';
    }

    /** Store a new post */
    public function store(): void
    {
        if (!verify_csrf()) {
            setFlash('error', 'Invalid security token.');
            redirect(url('admin/posts/create'));
        }

        $data = $this->extractPostData();

        // Generate slug
        $data['slug'] = slugify($data['title_en']);

        // Check slug uniqueness
        $existing = $this->postModel->getBySlug($data['slug']);
        if ($existing) {
            $data['slug'] .= '-' . time();
        }

        // Handle image upload or image URL
        if (!empty($_FILES['featured_image']['name'])) {
            $media = new Media();
            $uploaded = $media->upload($_FILES['featured_image']);
            if ($uploaded) {
                $data['featured_image'] = $uploaded['filename'];
            }
        } elseif (!empty($_POST['featured_image_url'])) {
            $imgUrl = trim($_POST['featured_image_url']);
            $uploadsUrl = UPLOADS_URL;
            if (str_starts_with($imgUrl, $uploadsUrl)) {
                $data['featured_image'] = ltrim(substr($imgUrl, strlen($uploadsUrl)), '/');
            } else {
                $data['featured_image'] = $imgUrl;
            }
        }

        $id = $this->postModel->create($data);
        setFlash('success', 'Post created successfully!');
        redirect(url('admin/posts'));
    }

    /** Show edit form */
    public function edit(string $id): void
    {
        $post = $this->postModel->getById((int)$id);
        if (!$post) {
            setFlash('error', 'Post not found.');
            redirect(url('admin/posts'));
        }

        $categories = $this->categoryModel->getAll(false);
        $adminPageTitle = 'Edit Post';
        include VIEWS_PATH . '/admin/posts/edit.php';
    }

    /** Update a post */
    public function update(string $id): void
    {
        if (!verify_csrf()) {
            setFlash('error', 'Invalid security token.');
            redirect(url('admin/posts/edit/' . $id));
        }

        $data = $this->extractPostData();

        // Handle image upload or image URL
        if (!empty($_FILES['featured_image']['name'])) {
            $media = new Media();
            $uploaded = $media->upload($_FILES['featured_image']);
            if ($uploaded) {
                $data['featured_image'] = $uploaded['filename'];
            }
        } elseif (isset($_POST['featured_image_url'])) {
            $imgUrl = trim($_POST['featured_image_url']);
            if ($imgUrl === '') {
                // If explicitly cleared, we can clear it or leave it. Let's clear if empty.
                $data['featured_image'] = null;
            } else {
                $uploadsUrl = UPLOADS_URL;
                if (str_starts_with($imgUrl, $uploadsUrl)) {
                    $data['featured_image'] = ltrim(substr($imgUrl, strlen($uploadsUrl)), '/');
                } else {
                    $data['featured_image'] = $imgUrl;
                }
            }
        }

        $this->postModel->update((int)$id, $data);
        setFlash('success', 'Post updated successfully!');
        redirect(url('admin/posts'));
    }

    /** Delete a post */
    public function delete(string $id): void
    {
        $this->postModel->delete((int)$id);
        setFlash('success', 'Post deleted successfully!');
        redirect(url('admin/posts'));
    }

    /** Toggle post status */
    public function toggleActive(string $id): void
    {
        $this->postModel->toggleActive((int)$id);
        redirect(url('admin/posts'));
    }

    /** Toggle featured status */
    public function toggleFeatured(string $id): void
    {
        $this->postModel->toggleFeatured((int)$id);
        redirect(url('admin/posts'));
    }

    /** Toggle trending status */
    public function toggleTrending(string $id): void
    {
        $this->postModel->toggleTrending((int)$id);
        redirect(url('admin/posts'));
    }

    /** Extract post data from POST request */
    private function extractPostData(): array
    {
        // Build important links JSON
        $linkLabels = $_POST['link_label'] ?? [];
        $linkUrls = $_POST['link_url'] ?? [];
        $importantLinks = [];
        foreach ($linkLabels as $i => $label) {
            if (!empty($label) && !empty($linkUrls[$i])) {
                $importantLinks[trim($label)] = cleanUrl(trim($linkUrls[$i]));
            }
        }

        // Build important dates JSON
        $dateLabels = $_POST['date_label'] ?? [];
        $dateValues = $_POST['date_value'] ?? [];
        $importantDates = [];
        foreach ($dateLabels as $i => $label) {
            if (!empty($label) && !empty($dateValues[$i])) {
                $importantDates[trim($label)] = trim($dateValues[$i]);
            }
        }

        return [
            'category_id'      => (int)$_POST['category_id'],
            'title_en'         => trim($_POST['title_en'] ?? ''),
            'title_hi'         => trim($_POST['title_hi'] ?? ''),
            'excerpt_en'       => trim($_POST['excerpt_en'] ?? ''),
            'excerpt_hi'       => trim($_POST['excerpt_hi'] ?? ''),
            'content_en'       => sanitizeHtml($_POST['content_en'] ?? ''),
            'content_hi'       => sanitizeHtml($_POST['content_hi'] ?? ''),
            'organization'     => trim($_POST['organization'] ?? ''),
            'post_date'        => $_POST['post_date'] ?: null,
            'last_date'        => $_POST['last_date'] ?: null,
            'exam_date'        => $_POST['exam_date'] ?: null,
            'total_vacancies'  => $_POST['total_vacancies'] ? (int)$_POST['total_vacancies'] : null,
            'qualification'    => trim($_POST['qualification'] ?? ''),
            'age_limit'        => trim($_POST['age_limit'] ?? ''),
            'application_fee'  => trim($_POST['application_fee'] ?? ''),
            'important_links'  => !empty($importantLinks) ? json_encode($importantLinks) : null,
            'important_dates'  => !empty($importantDates) ? json_encode($importantDates) : null,
            'is_featured'      => isset($_POST['is_featured']) ? 1 : 0,
            'is_trending'      => isset($_POST['is_trending']) ? 1 : 0,
            'status'           => $_POST['status'] ?? 'draft',
            'meta_title'       => trim($_POST['meta_title'] ?? ''),
            'meta_description' => trim($_POST['meta_description'] ?? ''),
        ];
    }
}
