<?php
/**
 * Admin Dashboard Page
 */
$lang = Lang::getInstance();

ob_start();
?>

<!-- Stats -->
<div class="dashboard-stats">
    <div class="stats-card animate-fade-in-up stagger-1">
        <div class="stats-card-icon" style="background: var(--primary-50); color: var(--primary-light);"><i data-lucide="file-text"></i></div>
        <div class="stats-card-value" style="color: var(--primary-light);"><?= $stats['total'] ?></div>
        <div class="stats-card-label">Total Posts</div>
    </div>
    <div class="stats-card animate-fade-in-up stagger-2">
        <div class="stats-card-icon" style="background: rgba(16,185,129,0.1); color: var(--success-light);"><i data-lucide="check-circle-2"></i></div>
        <div class="stats-card-value" style="color: var(--success-light);"><?= $stats['published'] ?></div>
        <div class="stats-card-label">Published</div>
    </div>
    <div class="stats-card animate-fade-in-up stagger-3">
        <div class="stats-card-icon" style="background: rgba(245,158,11,0.1); color: var(--warning-light);"><i data-lucide="file"></i></div>
        <div class="stats-card-value" style="color: var(--warning-light);"><?= $stats['draft'] ?></div>
        <div class="stats-card-label">Drafts</div>
    </div>
    <div class="stats-card animate-fade-in-up stagger-4">
        <div class="stats-card-icon" style="background: rgba(236,72,153,0.1); color: #F472B6;"><i data-lucide="eye"></i></div>
        <div class="stats-card-value" style="color: #F472B6;"><?= formatNumber($stats['views']) ?></div>
        <div class="stats-card-label">Total Views</div>
    </div>
</div>

<!-- Quick Actions -->
<div style="display: flex; gap: var(--space-3); margin-bottom: var(--space-8);">
    <a href="<?= url('admin/posts/create') ?>" class="btn btn-primary">
        <i data-lucide="plus"></i>
        Create New Post
    </a>
    <a href="<?= url('admin/settings') ?>" class="btn btn-secondary"><i data-lucide="settings"></i> Settings</a>
    <a href="<?= url('/') ?>" class="btn btn-secondary" target="_blank"><i data-lucide="globe"></i> View Site</a>
</div>

<!-- Recent Posts -->
<div class="admin-card animate-fade-in-up">
    <div class="admin-card-header">
        <h2 class="admin-card-title"><i data-lucide="list"></i> Recent Posts</h2>
        <a href="<?= url('admin/posts') ?>" class="btn btn-ghost btn-sm">View All →</a>
    </div>
    <div class="admin-card-body" style="padding: 0;">
        <?php if (empty($latestPosts)): ?>
        <div class="empty-state">
            <div class="empty-state-icon"><i data-lucide="inbox" style="width: 48px; height: 48px; opacity: 0.4;"></i></div>
            <h3 class="empty-state-title">No posts yet</h3>
            <p class="empty-state-desc">Create your first post to get started.</p>
            <a href="<?= url('admin/posts/create') ?>" class="btn btn-primary mt-4">Create Post</a>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Views</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($latestPosts as $post): ?>
                    <tr>
                        <td>
                            <div style="max-width: 300px;">
                                <strong style="font-size: var(--text-sm);"><?= sanitize(truncate($lang->field($post, 'title'), 50)) ?></strong>
                                <?php if ($post['organization']): ?>
                                <div style="font-size: var(--text-xs); color: var(--text-tertiary);"><?= sanitize($post['organization']) ?></div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><span class="badge badge-primary" style="display: inline-flex; align-items: center; gap: 4px;"><i data-lucide="<?= sanitize($post['category_icon']) ?>" style="width: 12px; height: 12px;"></i> <?= sanitize($lang->field($post, 'category_name')) ?></span></td>
                        <td>
                            <?php if ($post['status'] === 'published'): ?>
                            <span class="badge badge-success">Published</span>
                            <?php elseif ($post['status'] === 'draft'): ?>
                            <span class="badge badge-warning">Draft</span>
                            <?php else: ?>
                            <span class="badge badge-info">Archived</span>
                            <?php endif; ?>
                        </td>
                        <td><span style="font-size: var(--text-sm);"><?= formatNumber($post['views']) ?></span></td>
                        <td><span style="font-size: var(--text-xs); color: var(--text-tertiary);"><?= formatDate($post['created_at']) ?></span></td>
                        <td>
                            <div style="display: flex; gap: var(--space-1);">
                                <a href="<?= url('admin/posts/edit/' . $post['id']) ?>" class="btn btn-ghost btn-icon btn-sm" title="Edit"><i data-lucide="edit-3"></i></a>
                                <a href="<?= url($post['category_slug'] . '/' . $post['slug']) ?>" class="btn btn-ghost btn-icon btn-sm" target="_blank" title="View"><i data-lucide="eye"></i></a>
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

<?php
$adminContent = ob_get_clean();
include VIEWS_PATH . '/layouts/admin.php';
?>
