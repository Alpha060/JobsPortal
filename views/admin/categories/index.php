<?php
/**
 * Admin Categories Management Page
 */
$lang = Lang::getInstance();
$editing = isset($categoryToEdit) ? $categoryToEdit : null;

// Check if URL has ?edit=ID
if (isset($_GET['edit']) && !$editing) {
    $editId = (int)$_GET['edit'];
    foreach ($categories as $cat) {
        if ((int)$cat['id'] === $editId) {
            $editing = $cat;
            break;
        }
    }
}

$actionUrl = $editing ? url('admin/categories/update/' . $editing['id']) : url('admin/categories/store');

ob_start();
?>

<!-- Toolbar -->
<div class="toolbar">
    <div class="toolbar-left">
        <h2 class="admin-page-title" style="margin: 0; font-size: var(--text-lg); font-weight: var(--weight-bold);">
            <i data-lucide="layout-grid" style="vertical-align: middle; margin-right: 6px;"></i>
            Manage Categories
        </h2>
    </div>
    <div class="toolbar-right">
        <button type="button" id="btnCreateCategory" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Create Category
        </button>
    </div>
</div>

<!-- Categories Table -->
<div class="admin-card">
    <div class="admin-card-body" style="padding: 0;">
        <?php if (empty($categories)): ?>
        <div class="empty-state">
            <div class="empty-state-icon"><i data-lucide="folder" style="width: 48px; height: 48px; opacity: 0.4;"></i></div>
            <h3 class="empty-state-title">No categories found</h3>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 60px; text-align: center;">Order</th>
                        <th style="width: 60px; text-align: center;">Icon</th>
                        <th>Name (English / Hindi)</th>
                        <th>Slug</th>
                        <th>Gradient Preview</th>
                        <th>Status</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                    <tr <?= ($editing && (int)$editing['id'] === (int)$cat['id']) ? 'style="background: rgba(99, 102, 241, 0.05);"' : '' ?>>
                        <td style="text-align: center; color: var(--text-tertiary); font-weight: var(--weight-medium);"><?= (int)$cat['sort_order'] ?></td>
                        <td style="text-align: center; color: var(--primary);"><i data-lucide="<?= sanitize($cat['icon']) ?>"></i></td>
                        <td>
                            <div>
                                <strong style="font-size: var(--text-sm); color: var(--text-primary);"><?= sanitize($cat['name_en']) ?></strong>
                                <div style="font-size: 11px; color: var(--text-tertiary); margin-top: 2px;"><?= sanitize($cat['name_hi']) ?></div>
                            </div>
                        </td>
                        <td style="font-family: monospace; font-size: var(--text-xs); color: var(--text-secondary);"><?= sanitize($cat['slug']) ?></td>
                        <td>
                            <div style="width: 110px; height: 26px; border-radius: var(--radius-sm); 
                                        background: linear-gradient(135deg, <?= $cat['gradient_from'] ?>, <?= $cat['gradient_to'] ?>);
                                        border: 1px solid rgba(255, 255, 255, 0.1); display: flex; align-items: center; justify-content: center;
                                        font-size: 10px; color: #fff; font-weight: bold; text-shadow: 0 1px 2px rgba(0,0,0,0.4);
                                        box-shadow: 0 2px 6px rgba(0,0,0,0.05);">
                                <?= sanitize($cat['color']) ?>
                            </div>
                        </td>
                        <td>
                            <a href="<?= url('admin/categories/toggle/' . $cat['id']) ?>" class="badge <?= $cat['is_active'] ? 'badge-success' : 'badge-danger' ?>" style="text-decoration: none;">
                                <?= $cat['is_active'] ? 'Active' : 'Inactive' ?>
                            </a>
                        </td>
                        <td>
                            <div class="action-buttons-group">
                                <a href="?edit=<?= $cat['id'] ?>" class="btn-action-icon btn-action-edit" title="Edit"><i data-lucide="edit-3"></i></a>
                                <a href="<?= url('admin/categories/delete/' . $cat['id']) ?>" class="btn-action-icon btn-action-delete" title="Delete"
                                   onclick="return confirm('Are you sure you want to delete this category? If there are posts assigned to this category, it will fail.');"><i data-lucide="trash-2"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Create / Edit Category Modal -->
<div class="pw-modal" id="categoryModal" style="display: <?= $editing ? 'flex' : 'none' ?>; justify-content: center; align-items: center;">
    <div class="pw-modal-backdrop" id="categoryModalBackdrop"></div>
    <div class="pw-modal-content animate-fade-in-up" style="max-width: 500px; width: 90%; height: auto; max-height: 92vh; display: flex; flex-direction: column;">
        <div class="pw-modal-header">
            <h3 class="pw-modal-title" id="categoryModalTitle">
                <i data-lucide="<?= $editing ? 'edit-3' : 'plus-circle' ?>" style="margin-right: 6px; color: var(--primary);"></i>
                <?= $editing ? 'Edit Category' : 'Create Category' ?>
            </h3>
            <button class="pw-modal-close" id="categoryModalClose" aria-label="Close modal"><i data-lucide="x"></i></button>
        </div>
        <div class="pw-modal-body" style="overflow-y: auto; flex: 1; scroll-behavior: smooth; -webkit-overflow-scrolling: touch;">
            <form method="POST" id="categoryForm" action="<?= $actionUrl ?>" class="admin-form">
                <?= csrf_field() ?>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Name (English) <span style="color: var(--danger-light);">*</span></label>
                        <input type="text" name="name_en" id="catNameEn" class="form-input" required 
                               value="<?= $editing ? sanitize($editing['name_en']) : '' ?>" placeholder="e.g. Latest Jobs">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Name (Hindi) <span style="color: var(--danger-light);">*</span></label>
                        <input type="text" name="name_hi" id="catNameHi" class="form-input" required 
                               value="<?= $editing ? sanitize($editing['name_hi']) : '' ?>" placeholder="e.g. नवीनतम नौकरियां">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Description (English)</label>
                    <input type="text" name="description_en" id="catDescEn" class="form-input" 
                           value="<?= $editing ? sanitize($editing['description_en']) : '' ?>" placeholder="e.g. Latest government job notifications">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description (Hindi)</label>
                    <input type="text" name="description_hi" id="catDescHi" class="form-input" 
                           value="<?= $editing ? sanitize($editing['description_hi']) : '' ?>" placeholder="e.g. नवीनतम सरकारी नौकरी अधिसूचनाएं">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Lucide Icon Name</label>
                        <input type="text" name="icon" id="catIcon" class="form-input" 
                               value="<?= $editing ? sanitize($editing['icon']) : 'briefcase' ?>" placeholder="e.g. briefcase">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" id="catSortOrder" class="form-input" 
                               value="<?= $editing ? (int)$editing['sort_order'] : 0 ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Solid Color</label>
                        <input type="color" name="color" id="catColor" class="form-input" style="height: 40px; padding: 2px; cursor: pointer;"
                               value="<?= $editing ? sanitize($editing['color']) : '#6366F1' ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Is Active?</label>
                        <div style="margin-top: 8px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="is_active" id="catIsActive" value="1" <?= (!$editing || $editing['is_active']) ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                                <span>Active / Visible</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Gradient From</label>
                        <input type="color" name="gradient_from" id="catGradFrom" class="form-input" style="height: 40px; padding: 2px; cursor: pointer;"
                               value="<?= $editing ? sanitize($editing['gradient_from']) : '#6366F1' ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gradient To</label>
                        <input type="color" name="gradient_to" id="catGradTo" class="form-input" style="height: 40px; padding: 2px; cursor: pointer;"
                               value="<?= $editing ? sanitize($editing['gradient_to']) : '#8B5CF6' ?>">
                    </div>
                </div>

                <div class="form-actions" style="margin-top: var(--space-6); display: flex; gap: var(--space-3); justify-content: flex-end;">
                    <button type="button" class="btn btn-secondary" id="btnCancelModal">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="min-width: 140px;">
                        <?= $editing ? 'Update Category' : 'Create Category' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('categoryModal');
    const backdrop = document.getElementById('categoryModalBackdrop');
    const btnCreate = document.getElementById('btnCreateCategory');
    const btnClose = document.getElementById('categoryModalClose');
    const btnCancel = document.getElementById('btnCancelModal');
    const form = document.getElementById('categoryForm');
    const modalTitle = document.getElementById('categoryModalTitle');
    const isEditing = <?= $editing ? 'true' : 'false' ?>;

    // Show modal for Create
    if (btnCreate) {
        btnCreate.addEventListener('click', function() {
            // Reset form for fresh create
            if (!isEditing) {
                form.reset();
                form.action = "<?= url('admin/categories/store') ?>";
                modalTitle.innerHTML = '<i data-lucide="plus-circle" style="margin-right: 6px; color: var(--primary);"></i> Create Category';
                document.getElementById('catNameEn').value = "";
                document.getElementById('catNameHi').value = "";
                document.getElementById('catDescEn').value = "";
                document.getElementById('catDescHi').value = "";
                document.getElementById('catIcon').value = "briefcase";
                document.getElementById('catSortOrder').value = 0;
                document.getElementById('catColor').value = "#6366F1";
                document.getElementById('catGradFrom').value = "#6366F1";
                document.getElementById('catGradTo').value = "#8B5CF6";
                document.getElementById('catIsActive').checked = true;
                if (window.lucide) {
                    lucide.createIcons();
                }
            }
            modal.style.display = 'flex';
        });
    }

    // Hide Modal function
    function hideModal() {
        if (isEditing) {
            // If editing, redirect to clear URL query parameters
            window.location.href = "<?= url('admin/categories') ?>";
        } else {
            modal.style.display = 'none';
        }
    }

    if (btnClose) btnClose.addEventListener('click', hideModal);
    if (btnCancel) btnCancel.addEventListener('click', hideModal);
    if (backdrop) backdrop.addEventListener('click', hideModal);
});
</script>

<?php
$adminContent = ob_get_clean();
include VIEWS_PATH . '/layouts/admin.php';
?>
