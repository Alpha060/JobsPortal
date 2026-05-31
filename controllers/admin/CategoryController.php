<?php
/**
 * Admin Category Controller
 */
class AdminCategoryController
{
    private Category $categoryModel;
    private Auth $auth;

    public function __construct()
    {
        $this->auth = new Auth();
        $this->auth->requireAuth();
        $this->categoryModel = new Category();
    }

    /** List all categories */
    public function index(): void
    {
        $categories = $this->categoryModel->getAll(false);
        $categoryModel = $this->categoryModel;
        $categoriesWithCount = $categoryModel->getWithPostCount();

        $adminPageTitle = __('manage_categories');
        include VIEWS_PATH . '/admin/categories/index.php';
    }

    /** Store a new category */
    public function store(): void
    {
        if (!verify_csrf()) {
            setFlash('error', 'Invalid security token.');
            redirect(url('admin/categories'));
        }

        $data = [
            'name_en'        => trim($_POST['name_en'] ?? ''),
            'name_hi'        => trim($_POST['name_hi'] ?? ''),
            'slug'           => slugify(trim($_POST['name_en'] ?? '')),
            'description_en' => trim($_POST['description_en'] ?? ''),
            'description_hi' => trim($_POST['description_hi'] ?? ''),
            'icon'           => trim($_POST['icon'] ?? '📋'),
            'color'          => trim($_POST['color'] ?? '#6366F1'),
            'gradient_from'  => trim($_POST['gradient_from'] ?? '#6366F1'),
            'gradient_to'    => trim($_POST['gradient_to'] ?? '#8B5CF6'),
            'sort_order'     => (int)($_POST['sort_order'] ?? 0),
            'is_active'      => isset($_POST['is_active']) ? 1 : 0,
        ];

        if (empty($data['name_en'])) {
            setFlash('error', 'Category name (English) is required.');
            redirect(url('admin/categories'));
        }

        $this->categoryModel->create($data);
        setFlash('success', 'Category created successfully!');
        redirect(url('admin/categories'));
    }

    /** Update a category */
    public function update(string $id): void
    {
        if (!verify_csrf()) {
            setFlash('error', 'Invalid security token.');
            redirect(url('admin/categories'));
        }

        $data = [
            'name_en'        => trim($_POST['name_en'] ?? ''),
            'name_hi'        => trim($_POST['name_hi'] ?? ''),
            'description_en' => trim($_POST['description_en'] ?? ''),
            'description_hi' => trim($_POST['description_hi'] ?? ''),
            'icon'           => trim($_POST['icon'] ?? '📋'),
            'color'          => trim($_POST['color'] ?? '#6366F1'),
            'gradient_from'  => trim($_POST['gradient_from'] ?? '#6366F1'),
            'gradient_to'    => trim($_POST['gradient_to'] ?? '#8B5CF6'),
            'sort_order'     => (int)($_POST['sort_order'] ?? 0),
            'is_active'      => isset($_POST['is_active']) ? 1 : 0,
        ];

        $this->categoryModel->update((int)$id, $data);
        setFlash('success', 'Category updated successfully!');
        redirect(url('admin/categories'));
    }

    /** Delete a category */
    public function delete(string $id): void
    {
        if (!$this->categoryModel->delete((int)$id)) {
            setFlash('error', 'Cannot delete category that has posts. Move or delete the posts first.');
        } else {
            setFlash('success', 'Category deleted successfully!');
        }
        redirect(url('admin/categories'));
    }

    /** Toggle active status */
    public function toggleActive(string $id): void
    {
        $this->categoryModel->toggleActive((int)$id);
        redirect(url('admin/categories'));
    }
}
