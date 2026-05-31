<?php
/**
 * Admin Media Library Page
 */
$lang = Lang::getInstance();
$files = $result['files'];
$pagination = $result['pagination'];
$total = $result['total'];

ob_start();
?>

<!-- Upload Box -->
<div class="admin-card mb-6">
    <div class="admin-card-header">
        <h2 class="admin-card-title"><i data-lucide="upload-cloud"></i> Upload New Media</h2>
    </div>
    <div class="admin-card-body">
        <form method="POST" action="<?= url('admin/media/upload') ?>" enctype="multipart/form-data" class="admin-form" style="display: flex; gap: var(--space-4); align-items: flex-end; flex-wrap: wrap;">
            <?= csrf_field() ?>
            <div class="form-group" style="flex: 1; min-width: 250px; margin-bottom: 0;">
                <label class="form-label">Select Image or PDF Document (Max 5MB)</label>
                <input type="file" name="media_file" class="form-input" accept="image/*,application/pdf" required>
            </div>
            <button type="submit" class="btn btn-primary" style="height: 42px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Upload File
            </button>
        </form>
    </div>
</div>

<!-- Media Grid -->
<div class="admin-card">
    <div class="admin-card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h2 class="admin-card-title"><i data-lucide="image"></i> Media Files</h2>
        <span style="font-size: var(--text-sm); color: var(--text-secondary);"><?= $total ?> files total</span>
    </div>
    <div class="admin-card-body">
        <?php if (empty($files)): ?>
        <div class="empty-state" style="padding: var(--space-10) 0;">
            <div class="empty-state-icon"><i data-lucide="image" style="width: 48px; height: 48px; opacity: 0.4;"></i></div>
            <h3 class="empty-state-title">No media files uploaded yet</h3>
            <p class="empty-state-desc">Upload images or PDFs to use in your job posts.</p>
        </div>
        <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: var(--space-4);">
            <?php foreach ($files as $file): ?>
            <?php 
                $isImage = strpos($file['mime_type'], 'image/') === 0;
                $fileSize = round($file['size'] / 1024, 1); // in KB
                $displaySize = $fileSize > 1024 ? round($fileSize / 1024, 1) . ' MB' : $fileSize . ' KB';
            ?>
            <div class="media-item" style="border: 1px solid var(--border-color); border-radius: var(--radius-lg); background: var(--bg-card); overflow: hidden; display: flex; flex-direction: column; transition: all var(--transition-base);">
                
                <!-- Preview area -->
                <div style="height: 120px; background: rgba(0,0,0,0.15); display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden; border-bottom: 1px solid var(--border-color);">
                    <?php if ($isImage): ?>
                    <img src="<?= $file['url'] ?>" alt="<?= sanitize($file['alt_text'] ?: $file['original_name']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                    <div style="font-size: 3rem; color: var(--text-tertiary);"><i data-lucide="file-text" style="width: 48px; height: 48px;"></i></div>
                    <div style="position: absolute; bottom: 8px; left: 8px; right: 8px; background: rgba(0,0,0,0.6); color: #fff; font-size: 9px; padding: 2px 4px; border-radius: 4px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden; text-align: center;">
                        <?= strtoupper(pathinfo($file['filename'], PATHINFO_EXTENSION)) ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Info area -->
                <div style="padding: var(--space-3); flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                    <div style="margin-bottom: var(--space-2);">
                        <div style="font-size: var(--text-xs); font-weight: var(--weight-bold); text-overflow: ellipsis; white-space: nowrap; overflow: hidden; color: var(--text-primary);" title="<?= sanitize($file['original_name']) ?>">
                            <?= sanitize($file['original_name']) ?>
                        </div>
                        <div style="font-size: 10px; color: var(--text-secondary); margin-top: 2px;">
                            Size: <?= $displaySize ?>
                        </div>
                        <div style="font-size: 9px; color: var(--text-tertiary); margin-top: 1px;">
                            <?= date('d M Y', strtotime($file['uploaded_at'])) ?>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div style="display: flex; gap: var(--space-2); margin-top: var(--space-1);">
                        <button class="btn btn-ghost btn-sm" style="flex: 1; font-size: 10px; padding: var(--space-1) 0; border: 1px solid var(--border-color); display: flex; gap: 4px; justify-content: center; align-items: center;" 
                                onclick="copyToClipboard('<?= $file['url'] ?>', this)">
                            <i data-lucide="link" style="width: 12px; height: 12px;"></i> Copy URL
                        </button>
                        <a href="<?= url('admin/media/delete/' . $file['id']) ?>" class="btn btn-ghost btn-icon btn-sm" style="color: var(--danger-light); padding: var(--space-1) var(--space-2); border: 1px solid var(--border-color);" 
                           title="Delete" onclick="return confirm('Are you sure you want to delete this file? This will break any posts using it.');">
                            <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Pagination -->
<div style="margin-top: var(--space-6);">
    <?= $pagination->render() ?>
</div>

<script>
function copyToClipboard(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i data-lucide="check" style="width: 12px; height: 12px;"></i> Copied!';
        btn.style.borderColor = 'var(--success)';
        btn.style.color = 'var(--success)';
        if (window.lucide) {
            lucide.createIcons({
                attrs: { class: 'lucide' },
                nameAttr: 'data-lucide',
                nodeList: btn.querySelectorAll('[data-lucide]')
            });
        }
        setTimeout(() => {
            btn.innerHTML = originalHtml;
            btn.style.borderColor = 'var(--border-color)';
            btn.style.color = '';
            if (window.lucide) {
                lucide.createIcons({
                    attrs: { class: 'lucide' },
                    nameAttr: 'data-lucide',
                    nodeList: btn.querySelectorAll('[data-lucide]')
                });
            }
        }, 1500);
    }).catch(err => {
        alert('Failed to copy link: ' + err);
    });
}
</script>

<?php
$adminContent = ob_get_clean();
include VIEWS_PATH . '/layouts/admin.php';
?>
