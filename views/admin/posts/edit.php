<?php
/**
 * Admin Edit Post Page
 */
$lang = Lang::getInstance();

$importantLinks = $post['important_links'] ? json_decode($post['important_links'], true) : [];
$importantDates = $post['important_dates'] ? json_decode($post['important_dates'], true) : [];

ob_start();
?>

<form method="POST" action="<?= url('admin/posts/update/' . $post['id']) ?>" enctype="multipart/form-data" class="admin-form wizard-form">
    <?= csrf_field() ?>

    <!-- Wizard Progress bar -->
    <div class="wizard-progress">
        <div class="wizard-progress-bar" style="width: 0%;"></div>
        <div class="wizard-step-node active" data-step="1">
            <div class="wizard-step-circle">1</div>
            <div class="wizard-step-label">Post Info</div>
        </div>
        <div class="wizard-step-node" data-step="2">
            <div class="wizard-step-circle">2</div>
            <div class="wizard-step-label">Job Details & Dates</div>
        </div>
        <div class="wizard-step-node" data-step="3">
            <div class="wizard-step-circle">3</div>
            <div class="wizard-step-label">Useful Links</div>
        </div>
        <div class="wizard-step-node" data-step="4">
            <div class="wizard-step-circle">4</div>
            <div class="wizard-step-label">Settings & SEO</div>
        </div>
    </div>

    <!-- Step 1: Post Info -->
    <div class="wizard-step active" data-step="1">
        <div class="admin-card mb-6">
            <div class="admin-card-header">
                <h2 class="admin-card-title"><i data-lucide="edit-3"></i> Edit Post #<?= $post['id'] ?></h2>
                <a href="<?= url($post['category_slug'] . '/' . $post['slug']) ?>" class="btn btn-ghost btn-sm" target="_blank"><i data-lucide="eye"></i> View</a>
            </div>
            <div class="admin-card-body">
                <!-- Title (English & Hindi) -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Title (English) <span style="color: var(--danger-light);">*</span></label>
                        <input type="text" name="title_en" class="form-input" value="<?= sanitize($post['title_en']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Title (Hindi)</label>
                        <input type="text" name="title_hi" class="form-input" value="<?= sanitize($post['title_hi'] ?? '') ?>" placeholder="पोस्ट शीर्षक हिंदी में (Hindi)">
                    </div>
                </div>

                <!-- Category & Org -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Category <span style="color: var(--danger-light);">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $post['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                <?= sanitize($cat['name_en']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Organization</label>
                        <input type="text" name="organization" class="form-input" value="<?= sanitize($post['organization'] ?? '') ?>">
                    </div>
                </div>

                <!-- Excerpt (English & Hindi) -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Excerpt (English)</label>
                        <textarea name="excerpt_en" class="form-textarea" rows="3"><?= sanitize($post['excerpt_en'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Excerpt (Hindi)</label>
                        <textarea name="excerpt_hi" class="form-textarea" rows="3" placeholder="संक्षिप्त विवरण हिंदी में (Hindi)"><?= sanitize($post['excerpt_hi'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Content (English) -->
                <div class="form-group">
                    <label class="form-label">Full Content (English)</label>
                    <textarea name="content_en" class="form-textarea" rows="8"><?= $post['content_en'] ?? '' ?></textarea>
                </div>

                <!-- Content (Hindi) -->
                <div class="form-group" style="margin-top: var(--space-4);">
                    <label class="form-label">Full Content (Hindi)</label>
                    <textarea name="content_hi" class="form-textarea" rows="8" placeholder="पूर्ण पोस्ट सामग्री हिंदी में (Hindi)"><?= $post['content_hi'] ?? '' ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 2: Job Details & Dates -->
    <div class="wizard-step" data-step="2">
        <div class="admin-card mb-6">
            <div class="admin-card-header">
                <h2 class="admin-card-title"><i data-lucide="info"></i> Job / Exam Details</h2>
            </div>
            <div class="admin-card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Post Date</label>
                        <input type="date" name="post_date" class="form-input" value="<?= $post['post_date'] ?? '' ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Last Date</label>
                        <input type="date" name="last_date" class="form-input" value="<?= $post['last_date'] ?? '' ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Exam Date</label>
                        <input type="date" name="exam_date" class="form-input" value="<?= $post['exam_date'] ?? '' ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Total Vacancies</label>
                        <input type="number" name="total_vacancies" class="form-input" value="<?= $post['total_vacancies'] ?? '' ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Qualification</label>
                        <input type="text" name="qualification" class="form-input" value="<?= sanitize($post['qualification'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Age Limit</label>
                        <input type="text" name="age_limit" class="form-input" value="<?= sanitize($post['age_limit'] ?? '') ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Application Fee</label>
                    <input type="text" name="application_fee" class="form-input" value="<?= sanitize($post['application_fee'] ?? '') ?>">
                </div>
            </div>
        </div>

        <div class="admin-card mb-6">
            <div class="admin-card-header"><h2 class="admin-card-title"><i data-lucide="calendar"></i> Important Dates</h2></div>
            <div class="admin-card-body">
                <div class="links-builder" id="datesBuilder">
                    <?php if (!empty($importantDates)): ?>
                    <?php foreach ($importantDates as $key => $val): ?>
                    <?php 
                    if (is_array($val)) {
                        $event = $val['label'] ?? $val['event'] ?? $key;
                        $date = $val['date'] ?? $val['value'] ?? '';
                    } else {
                        $event = $key;
                        $date = $val;
                    }
                    ?>
                    <div class="link-row">
                        <input type="text" name="date_label[]" class="form-input" value="<?= sanitize($event) ?>">
                        <input type="text" name="date_value[]" class="form-input" value="<?= sanitize($date) ?>">
                        <button type="button" class="btn btn-ghost btn-icon remove-row" title="Remove"><i data-lucide="trash-2"></i></button>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <div class="link-row">
                        <input type="text" name="date_label[]" class="form-input" placeholder="Event">
                        <input type="text" name="date_value[]" class="form-input" placeholder="Date">
                        <button type="button" class="btn btn-ghost btn-icon remove-row" title="Remove"><i data-lucide="trash-2"></i></button>
                    </div>
                    <?php endif; ?>
                </div>
                <button type="button" class="btn btn-secondary btn-sm add-link-btn mt-4" onclick="addDateRow()">+ Add Date</button>
            </div>
        </div>
    </div>

    <!-- Step 3: Useful Links -->
    <div class="wizard-step" data-step="3">
        <div class="admin-card mb-6">
            <div class="admin-card-header"><h2 class="admin-card-title"><i data-lucide="link"></i> Important Links</h2></div>
            <div class="admin-card-body">
                <div class="links-builder" id="linksBuilder">
                    <?php if (!empty($importantLinks)): ?>
                    <?php foreach ($importantLinks as $key => $val): ?>
                    <?php 
                    if (is_array($val)) {
                        $label = $val['label'] ?? $key;
                        $linkUrl = $val['url'] ?? $val['link'] ?? '';
                    } else {
                        $label = $key;
                        $linkUrl = $val;
                    }
                    ?>
                    <div class="link-row">
                        <input type="text" name="link_label[]" class="form-input" value="<?= sanitize($label) ?>">
                        <input type="url" name="link_url[]" class="form-input" value="<?= sanitize($linkUrl) ?>">
                        <button type="button" class="btn btn-ghost btn-icon remove-row" title="Remove"><i data-lucide="trash-2"></i></button>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <div class="link-row">
                        <input type="text" name="link_label[]" class="form-input" placeholder="Label">
                        <input type="url" name="link_url[]" class="form-input" placeholder="URL">
                        <button type="button" class="btn btn-ghost btn-icon remove-row" title="Remove"><i data-lucide="trash-2"></i></button>
                    </div>
                    <?php endif; ?>
                </div>
                <button type="button" class="btn btn-secondary btn-sm add-link-btn mt-4" onclick="addLinkRow()">+ Add Link</button>
            </div>
        </div>
    </div>

    <!-- Step 4: Settings & SEO -->
    <div class="wizard-step" data-step="4">
        <div class="admin-card mb-6">
            <div class="admin-card-header"><h2 class="admin-card-title"><i data-lucide="settings"></i> Settings & Media</h2></div>
            <div class="admin-card-body">
                <div class="form-group">
                    <label class="form-label">Featured Image</label>
                    <?php 
                    $currentImage = '';
                    if (!empty($post['featured_image'])) {
                        if (filter_var($post['featured_image'], FILTER_VALIDATE_URL)) {
                            $currentImage = $post['featured_image'];
                        } else {
                            $currentImage = UPLOADS_URL . '/' . $post['featured_image'];
                        }
                    }
                    ?>
                    <?php if ($currentImage): ?>
                    <div style="margin-bottom: var(--space-3);" id="currentImageWrapper">
                        <img src="<?= $currentImage ?>" style="max-height: 120px; border-radius: var(--radius-md);">
                    </div>
                    <?php endif; ?>
                    
                    <div style="display: flex; gap: var(--space-2); margin-bottom: var(--space-2);">
                        <button type="button" class="btn btn-ghost btn-sm" id="btnUploadFileMode" style="border: 1px solid var(--border-color); background: var(--glass-bg); font-weight: bold; color: var(--primary);">Upload File</button>
                        <button type="button" class="btn btn-ghost btn-sm" id="btnUploadUrlMode" style="border: 1px solid var(--border-color);">Enter Image URL</button>
                    </div>

                    <div class="upload-area" id="uploadArea">
                        <input type="file" name="featured_image" accept="image/*" id="imageInput">
                        <div class="upload-area-icon"><i data-lucide="camera" style="width: 24px; height: 24px;"></i></div>
                        <div class="upload-area-text"><?= $post['featured_image'] ? 'Upload new image to replace' : 'Click to upload' ?></div>
                        <div class="upload-preview" id="imagePreview" style="display:none;"></div>
                    </div>

                    <div id="uploadUrlInputWrapper" style="display: none; margin-top: var(--space-2);">
                        <input type="text" name="featured_image_url" class="form-input" id="imageInputUrl" 
                               value="<?= $currentImage ?>" placeholder="Paste image URL here (e.g. from Media Library)">
                        <div style="font-size: 11px; color: var(--text-tertiary); margin-top: 4px;">
                            Tip: Copy any image's URL from the <strong>Media Library</strong> and paste it here.
                        </div>
                    </div>
                </div>

                <script>
                // We wrap this inside a function or standard IIFE so it works with the SPA/PJAX swaps
                (function() {
                    const btnFileMode = document.getElementById('btnUploadFileMode');
                    const btnUrlMode = document.getElementById('btnUploadUrlMode');
                    const uploadArea = document.getElementById('uploadArea');
                    const urlWrapper = document.getElementById('uploadUrlInputWrapper');
                    const inputUrl = document.getElementById('imageInputUrl');
                    const imageInput = document.getElementById('imageInput');

                    if (!btnFileMode || !btnUrlMode) return;

                    btnFileMode.addEventListener('click', () => {
                        uploadArea.style.display = 'block';
                        urlWrapper.style.display = 'none';
                        btnFileMode.style.background = 'var(--glass-bg)';
                        btnFileMode.style.fontWeight = 'bold';
                        btnFileMode.style.color = 'var(--primary)';
                        btnUrlMode.style.background = '';
                        btnUrlMode.style.fontWeight = '';
                        btnUrlMode.style.color = '';
                        // Clear URL input so controller knows to use file upload
                        inputUrl.dataset.oldValue = inputUrl.value;
                        inputUrl.value = '';
                    });

                    btnUrlMode.addEventListener('click', () => {
                        uploadArea.style.display = 'none';
                        urlWrapper.style.display = 'block';
                        btnUrlMode.style.background = 'var(--glass-bg)';
                        btnUrlMode.style.fontWeight = 'bold';
                        btnUrlMode.style.color = 'var(--primary)';
                        btnFileMode.style.background = '';
                        btnFileMode.style.fontWeight = '';
                        btnFileMode.style.color = '';
                        // Restore URL value if cleared
                        if (inputUrl.dataset.oldValue) {
                            inputUrl.value = inputUrl.dataset.oldValue;
                        }
                        // Clear file input
                        imageInput.value = '';
                        const imagePreview = document.getElementById('imagePreview');
                        if (imagePreview) {
                            imagePreview.innerHTML = '';
                            imagePreview.style.display = 'none';
                        }
                    });

                    // If a URL already exists on edit, default to showing URL mode
                    <?php if ($post['featured_image'] && (filter_var($post['featured_image'], FILTER_VALIDATE_URL) || strpos($post['featured_image'], '/') !== false)): ?>
                    btnUrlMode.click();
                    <?php endif; ?>
                })();
                </script>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="draft" <?= $post['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="published" <?= $post['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                            <option value="archived" <?= $post['status'] === 'archived' ? 'selected' : '' ?>>Archived</option>
                        </select>
                    </div>
                </div>

                <div class="form-row" style="margin-top: var(--space-4);">
                    <div class="form-group">
                        <label class="form-label">Featured</label>
                        <label style="display: flex; align-items: center; gap: var(--space-2); cursor: pointer; padding: var(--space-3); background: var(--glass-bg); border: 1px solid var(--border-color); border-radius: var(--radius-lg);">
                            <input type="checkbox" name="is_featured" value="1" <?= $post['is_featured'] ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                            <span>Show on homepage</span>
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Trending</label>
                        <label style="display: flex; align-items: center; gap: var(--space-2); cursor: pointer; padding: var(--space-3); background: var(--glass-bg); border: 1px solid var(--border-color); border-radius: var(--radius-lg);">
                            <input type="checkbox" name="is_trending" value="1" <?= isset($post['is_trending']) && $post['is_trending'] ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                            <span>Show in top marquee news ticker</span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Meta Title (SEO)</label>
                    <input type="text" name="meta_title" class="form-input" value="<?= sanitize($post['meta_title'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Meta Description (SEO)</label>
                    <textarea name="meta_description" class="form-textarea" rows="2"><?= sanitize($post['meta_description'] ?? '') ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Wizard Navigation actions -->
    <div class="wizard-actions">
        <button type="button" class="btn btn-secondary wizard-prev-btn" style="display: none;">
            <i data-lucide="arrow-left"></i> Previous
        </button>
        <div class="wizard-actions-right">
            <button type="button" class="btn btn-primary wizard-next-btn">
                Next <i data-lucide="arrow-right"></i>
            </button>
            <div class="wizard-submit-group" style="display: none; gap: var(--space-3);">
                <a href="<?= url('admin/posts') ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-success btn-lg">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    Update Post
                </button>
            </div>
        </div>
    </div>
</form>

<?php
$adminContent = ob_get_clean();
include VIEWS_PATH . '/layouts/admin.php';
?>
