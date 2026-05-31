<?php
/**
 * Admin Posts List Page
 */
$lang = Lang::getInstance();
$posts = $result['posts'];
$pagination = $result['pagination'];

ob_start();
?>

<!-- Toolbar -->
<div class="toolbar">
    <div class="toolbar-left">
        <div class="toolbar-search">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <form method="GET" action="<?= url('admin/posts') ?>">
                <input type="text" name="search" value="<?= sanitize($_GET['search'] ?? '') ?>" placeholder="Search posts...">
            </form>
        </div>
        <div class="toolbar-filter">
            <form method="GET" action="<?= url('admin/posts') ?>">
                <select name="category" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['slug'] ?>" <?= ($_GET['category'] ?? '') === $cat['slug'] ? 'selected' : '' ?>>
                        <?= sanitize($lang->field($cat, 'name')) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
        <div class="toolbar-filter">
            <form method="GET" action="<?= url('admin/posts') ?>">
                <select name="status" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="published" <?= ($_GET['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                    <option value="draft" <?= ($_GET['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="archived" <?= ($_GET['status'] ?? '') === 'archived' ? 'selected' : '' ?>>Archived</option>
                </select>
            </form>
        </div>
    </div>
    <div class="toolbar-right">
        <a href="<?= url('admin/posts/create') ?>" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Create Post
        </a>
    </div>
</div>

<!-- Posts Table -->
<div class="admin-card">
    <div class="admin-card-body" style="padding: 0;">
        <?php if (empty($posts)): ?>
        <div class="empty-state">
            <div class="empty-state-icon"><i data-lucide="inbox" style="width: 48px; height: 48px; opacity: 0.4;"></i></div>
            <h3 class="empty-state-title">No posts found</h3>
            <p class="empty-state-desc">Try adjusting your filters or create a new post.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Featured</th>
                        <th>Trending</th>
                        <th>Views</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $index => $post): ?>
                    <tr>
                        <td style="color: var(--text-tertiary); font-weight: var(--weight-medium);"><?= $pagination->offset() + $index + 1 ?></td>
                        <td>
                            <div style="max-width: 320px;">
                                <a href="<?= url('admin/posts/edit/' . $post['id']) ?>" style="font-weight: var(--weight-semibold); font-size: var(--text-sm);">
                                    <?= sanitize(truncate($lang->field($post, 'title'), 60)) ?>
                                </a>
                                <?php if ($post['organization']): ?>
                                <div style="font-size: var(--text-xs); color: var(--text-tertiary); display: flex; align-items: center; gap: 4px;">
                                    <i data-lucide="building-2" style="width: 12px; height: 12px;"></i> <?= sanitize($post['organization']) ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><span class="badge badge-primary"><?= sanitize($lang->field($post, 'category_name')) ?></span></td>
                        <td class="status-cell" data-status="<?= sanitize($post['status']) ?>">
                            <?php if (!$post['is_active']): ?>
                            <span class="badge badge-danger badge-status"><i data-lucide="eye-off"></i> Inactive</span>
                            <?php elseif ($post['status'] === 'published'): ?>
                            <span class="badge badge-success badge-status"><i data-lucide="check-circle-2"></i> Published</span>
                            <?php elseif ($post['status'] === 'draft'): ?>
                            <span class="badge badge-warning badge-status"><i data-lucide="file"></i> Draft</span>
                            <?php else: ?>
                            <span class="badge badge-info badge-status"><i data-lucide="archive"></i> Archived</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= url('admin/posts/toggle-featured/' . $post['id']) ?>" class="btn-toggle-icon btn-toggle-featured <?= $post['is_featured'] ? 'active' : '' ?>" title="Toggle featured">
                                <i data-lucide="star"></i>
                            </a>
                        </td>
                        <td>
                            <a href="<?= url('admin/posts/toggle-trending/' . $post['id']) ?>" class="btn-toggle-icon btn-toggle-trending <?= (isset($post['is_trending']) && $post['is_trending']) ? 'active' : '' ?>" title="Toggle trending">
                                <i data-lucide="zap"></i>
                            </a>
                        </td>
                        <td style="font-size: var(--text-sm); font-weight: var(--weight-medium);"><?= formatNumber($post['views']) ?></td>
                        <td style="font-size: var(--text-xs); color: var(--text-tertiary);"><?= formatDate($post['created_at']) ?></td>
                        <td>
                            <div class="action-buttons-group">
                                <a href="<?= url('admin/posts/edit/' . $post['id']) ?>" class="btn-action-icon btn-action-edit" title="Edit"><i data-lucide="edit-3"></i></a>
                                <a href="<?= url($post['category_slug'] . '/' . $post['slug']) ?>" class="btn-action-icon btn-action-view" target="_blank" title="View"><i data-lucide="eye"></i></a>
                                <a href="<?= url('admin/posts/toggle/' . $post['id']) ?>" class="btn-action-icon btn-action-toggle <?= $post['is_active'] ? 'active-status' : 'inactive-status' ?>" title="Toggle active">
                                    <span class="apple-switch"></span>
                                </a>
                                <a href="<?= url('admin/posts/delete/' . $post['id']) ?>" class="btn-action-icon btn-action-delete" title="Delete"
                                   onclick="return confirm('Are you sure you want to delete this post?');"><i data-lucide="trash-2"></i></a>
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

<!-- Pagination -->
<?= $pagination->render() ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleButtons = document.querySelectorAll('.btn-toggle-icon, .btn-action-toggle');
    
    toggleButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault(); 
            const url = this.getAttribute('href');
            
            // Instantly trigger background fetch
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => {
                    if (res.ok) {
                        if (this.classList.contains('btn-toggle-featured')) {
                            this.classList.toggle('active');
                        } else if (this.classList.contains('btn-toggle-trending')) {
                            this.classList.toggle('active');
                        } else if (this.classList.contains('btn-action-toggle')) {
                            const isActive = this.classList.contains('active-status');
                            this.classList.toggle('active-status', !isActive);
                            this.classList.toggle('inactive-status', isActive);
                            
                            // Dynamically update the status badge cell in the same row
                            const tr = this.closest('tr');
                            const statusCell = tr.querySelector('.status-cell');
                            if (statusCell) {
                                const origStatus = statusCell.getAttribute('data-status');
                                if (!isActive) { // Turning ON (was inactive, now active)
                                    if (origStatus === 'published') {
                                        statusCell.innerHTML = '<span class="badge badge-success badge-status"><i data-lucide="check-circle-2"></i> Published</span>';
                                    } else if (origStatus === 'draft') {
                                        statusCell.innerHTML = '<span class="badge badge-warning badge-status"><i data-lucide="file"></i> Draft</span>';
                                    } else {
                                        statusCell.innerHTML = '<span class="badge badge-info badge-status"><i data-lucide="archive"></i> Archived</span>';
                                    }
                                } else { // Turning OFF (now inactive)
                                    statusCell.innerHTML = '<span class="badge badge-danger badge-status"><i data-lucide="eye-off"></i> Inactive</span>';
                                }
                                if (window.lucide) {
                                    lucide.createIcons();
                                }
                            }
                        }
                    }
                })
                .catch(err => console.error('Error toggling status:', err));
        });
    });
});
</script>

<?php
$adminContent = ob_get_clean();
include VIEWS_PATH . '/layouts/admin.php';
?>
