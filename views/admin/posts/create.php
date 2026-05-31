<?php
/**
 * Admin Create Post Page
 */
$lang = Lang::getInstance();

ob_start();
?>

<form method="POST" action="<?= url('admin/posts/store') ?>" enctype="multipart/form-data" class="admin-form wizard-form">
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
                <h2 class="admin-card-title"><i data-lucide="file-text"></i> Post Details</h2>
            </div>
            <div class="admin-card-body">
                <!-- Title (English & Hindi) -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Title (English) <span style="color: var(--danger-light);">*</span></label>
                        <input type="text" name="title_en" class="form-input" placeholder="Post title in English" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Title (Hindi)</label>
                        <input type="text" name="title_hi" class="form-input" placeholder="पोस्ट शीर्षक हिंदी में (Hindi)">
                    </div>
                </div>

                <!-- Category & Organization -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Category <span style="color: var(--danger-light);">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Select category</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= sanitize($cat['name_en']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Organization</label>
                        <input type="text" name="organization" class="form-input" placeholder="e.g., UPSC, SSC, Railway">
                    </div>
                </div>

                <!-- Excerpt (English & Hindi) -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Excerpt / Short Description (English)</label>
                        <textarea name="excerpt_en" class="form-textarea" rows="3" placeholder="Brief description in English"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Excerpt / Short Description (Hindi)</label>
                        <textarea name="excerpt_hi" class="form-textarea" rows="3" placeholder="संक्षिप्त विवरण हिंदी में (Hindi)"></textarea>
                    </div>
                </div>

                <!-- Content (English) -->
                <div class="form-group">
                    <label class="form-label">Full Content (English)</label>
                    <textarea name="content_en" class="form-textarea" rows="8" placeholder="Full post content in English (HTML supported)" id="contentEn"></textarea>
                </div>

                <!-- Content (Hindi) -->
                <div class="form-group" style="margin-top: var(--space-4);">
                    <label class="form-label">Full Content (Hindi)</label>
                    <textarea name="content_hi" class="form-textarea" rows="8" placeholder="पूर्ण पोस्ट सामग्री हिंदी में (HTML समर्थित) (Hindi)" id="contentHi"></textarea>
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
                        <input type="date" name="post_date" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Last Date (Deadline)</label>
                        <input type="date" name="last_date" class="form-input">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Exam Date</label>
                        <input type="date" name="exam_date" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Total Vacancies</label>
                        <input type="number" name="total_vacancies" class="form-input" placeholder="e.g., 1500">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Qualification</label>
                        <input type="text" name="qualification" class="form-input" placeholder="e.g., 10th, 12th, Graduation, Post Graduation">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Age Limit</label>
                        <input type="text" name="age_limit" class="form-input" placeholder="e.g., 18-35 Years">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Application Fee</label>
                    <input type="text" name="application_fee" class="form-input" placeholder="e.g., Gen/OBC: ₹500, SC/ST: ₹250">
                </div>
            </div>
        </div>

        <div class="admin-card mb-6">
            <div class="admin-card-header">
                <h2 class="admin-card-title"><i data-lucide="calendar"></i> Important Dates</h2>
            </div>
            <div class="admin-card-body">
                <div class="links-builder" id="datesBuilder">
                    <div class="link-row">
                        <input type="text" name="date_label[]" class="form-input" placeholder="Event (e.g., Apply Start)">
                        <input type="text" name="date_value[]" class="form-input" placeholder="Date (e.g., 15 Jan 2026)">
                        <button type="button" class="btn btn-ghost btn-icon remove-row" title="Remove"><i data-lucide="trash-2"></i></button>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary btn-sm add-link-btn mt-4" onclick="addDateRow()">
                    + Add Date
                </button>
            </div>
        </div>
    </div>

    <!-- Step 3: Useful Links -->
    <div class="wizard-step" data-step="3">
        <div class="admin-card mb-6">
            <div class="admin-card-header">
                <h2 class="admin-card-title"><i data-lucide="link"></i> Important Links</h2>
            </div>
            <div class="admin-card-body">
                <div class="links-builder" id="linksBuilder">
                    <div class="link-row">
                        <input type="text" name="link_label[]" class="form-input" placeholder="Label (e.g., Apply Online)">
                        <input type="url" name="link_url[]" class="form-input" placeholder="https://example.com/apply">
                        <button type="button" class="btn btn-ghost btn-icon remove-row" title="Remove"><i data-lucide="trash-2"></i></button>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary btn-sm add-link-btn mt-4" onclick="addLinkRow()">
                    + Add Link
                </button>
            </div>
        </div>
    </div>

    <!-- Step 4: Settings & SEO -->
    <div class="wizard-step" data-step="4">
        <div class="admin-card mb-6">
            <div class="admin-card-header">
                <h2 class="admin-card-title"><i data-lucide="settings"></i> Settings & Media</h2>
            </div>
            <div class="admin-card-body">
                <!-- Image Upload -->
                <div class="form-group">
                    <label class="form-label">Featured Image</label>
                    
                    <div style="display: flex; gap: var(--space-2); margin-bottom: var(--space-2);">
                        <button type="button" class="btn btn-ghost btn-sm" id="btnUploadFileMode" style="border: 1px solid var(--border-color); background: var(--glass-bg); font-weight: bold; color: var(--primary);">Upload File</button>
                        <button type="button" class="btn btn-ghost btn-sm" id="btnUploadUrlMode" style="border: 1px solid var(--border-color);">Enter Image URL</button>
                    </div>

                    <div class="upload-area" id="uploadArea">
                        <input type="file" name="featured_image" accept="image/*" id="imageInput">
                        <div class="upload-area-icon"><i data-lucide="camera" style="width: 24px; height: 24px;"></i></div>
                        <div class="upload-area-text">Click or drag to upload an image</div>
                        <div class="upload-preview" id="imagePreview" style="display:none;"></div>
                    </div>

                    <div id="uploadUrlInputWrapper" style="display: none; margin-top: var(--space-2);">
                        <input type="text" name="featured_image_url" class="form-input" id="imageInputUrl" 
                               placeholder="Paste image URL here (e.g. from Media Library)">
                        <div style="font-size: 11px; color: var(--text-tertiary); margin-top: 4px;">
                            Tip: Copy any image's URL from the <strong>Media Library</strong> and paste it here.
                        </div>
                    </div>
                </div>

                <script>
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
                        if (inputUrl.dataset.oldValue) {
                            inputUrl.value = inputUrl.dataset.oldValue;
                        }
                        imageInput.value = '';
                        const imagePreview = document.getElementById('imagePreview');
                        if (imagePreview) {
                            imagePreview.innerHTML = '';
                            imagePreview.style.display = 'none';
                        }
                    });
                })();
                </script>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>
                </div>

                <div class="form-row" style="margin-top: var(--space-4);">
                    <div class="form-group">
                        <label class="form-label">Featured</label>
                        <label style="display: flex; align-items: center; gap: var(--space-2); cursor: pointer; padding: var(--space-3); background: var(--glass-bg); border: 1px solid var(--border-color); border-radius: var(--radius-lg);">
                            <input type="checkbox" name="is_featured" value="1" style="width: 18px; height: 18px;">
                            <span>Show on homepage featured section</span>
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Trending</label>
                        <label style="display: flex; align-items: center; gap: var(--space-2); cursor: pointer; padding: var(--space-3); background: var(--glass-bg); border: 1px solid var(--border-color); border-radius: var(--radius-lg);">
                            <input type="checkbox" name="is_trending" value="1" style="width: 18px; height: 18px;">
                            <span>Show in top marquee news ticker</span>
                        </label>
                    </div>
                </div>

                <!-- SEO -->
                <div class="form-group">
                    <label class="form-label">Meta Title (SEO)</label>
                    <input type="text" name="meta_title" class="form-input" placeholder="Custom SEO title (leave empty to use post title)">
                </div>
                <div class="form-group">
                    <label class="form-label">Meta Description (SEO)</label>
                    <textarea name="meta_description" class="form-textarea" rows="2" placeholder="Custom SEO description (leave empty to use excerpt)"></textarea>
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
                <button type="submit" class="btn btn-primary btn-lg">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Publish Post
                </button>
            </div>
        </div>
    </div>
</form>

<?php
$adminContent = ob_get_clean();
include VIEWS_PATH . '/layouts/admin.php';
?>
