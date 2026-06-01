/**
 * JobsPortal — Admin JavaScript
 * Handles: sidebar toggle, language tabs, image upload preview,
 * dynamic links/dates builder, form validation
 */

document.addEventListener('DOMContentLoaded', () => {
    initThemeToggle();
    initSidebarToggle();
    initLangTabs();
    initImagePreview();
    initRemoveRows();
    initProfileDropdown();
    initChangePasswordModal();
    initPasswordVisibilityToggles();
    initFormWizard();
    initAdminSPA();
});

/* ── Theme Toggle (Dark/Light) ── */
function initThemeToggle() {
    const toggle = document.getElementById('themeToggle');
    if (!toggle) return;

    const savedTheme = localStorage.getItem('theme') || 'light';
    if (savedTheme !== 'dark') {
        document.documentElement.classList.add('light-mode');
        updateThemeIcons(true);
    } else {
        document.documentElement.classList.remove('light-mode');
        updateThemeIcons(false);
    }

    toggle.addEventListener('click', () => {
        const isLight = document.documentElement.classList.toggle('light-mode');
        localStorage.setItem('theme', isLight ? 'light' : 'dark');
        updateThemeIcons(isLight);
    });
}

function updateThemeIcons(isLight) {
    const darkIcon = document.querySelector('.theme-icon-dark');
    const lightIcon = document.querySelector('.theme-icon-light');
    if (darkIcon) darkIcon.style.display = isLight ? 'none' : 'block';
    if (lightIcon) lightIcon.style.display = isLight ? 'block' : 'none';
}

/* ── Sidebar Toggle (Mobile) ── */
function initSidebarToggle() {
    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('adminSidebar');

    if (!toggle || !sidebar) return;

    toggle.addEventListener('click', () => {
        sidebar.classList.toggle('open');
    });

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', (e) => {
        if (window.innerWidth <= 1024 &&
            sidebar.classList.contains('open') &&
            !sidebar.contains(e.target) &&
            !toggle.contains(e.target)) {
            sidebar.classList.remove('open');
        }
    });
}

/* ── Language Tabs in Forms ── */
function initLangTabs() {
    document.querySelectorAll('.lang-tabs').forEach(tabGroup => {
        const tabs = tabGroup.querySelectorAll('.lang-tab');
        const parent = tabGroup.closest('.form-group');
        if (!parent) return;

        const fields = parent.querySelectorAll('.lang-field');

        tabs.forEach(tab => {
            tab.addEventListener('click', (e) => {
                e.preventDefault();
                const targetLang = tab.dataset.lang;

                // Deactivate all in this group
                tabs.forEach(t => t.classList.remove('active'));
                fields.forEach(f => f.classList.remove('active'));

                // Activate selected
                tab.classList.add('active');
                const target = parent.querySelector(`.lang-field[data-lang="${targetLang}"]`);
                if (target) target.classList.add('active');
            });
        });
    });
}

/* ── Image Upload Preview ── */
function initImagePreview() {
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');
    const uploadArea = document.getElementById('uploadArea');

    if (!imageInput || !imagePreview) return;

    imageInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;

        // Validate
        if (!file.type.startsWith('image/')) {
            alert('Please select an image file.');
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            alert('Image must be less than 5MB.');
            return;
        }

        const reader = new FileReader();
        reader.onload = (event) => {
            imagePreview.innerHTML = `
                <img src="${event.target.result}" alt="Preview">
                <button type="button" class="upload-preview-remove" onclick="removeImagePreview()">×</button>
            `;
            imagePreview.style.display = 'inline-block';
        };
        reader.readAsDataURL(file);
    });
}

function removeImagePreview() {
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');
    if (imageInput) imageInput.value = '';
    if (imagePreview) {
        imagePreview.innerHTML = '';
        imagePreview.style.display = 'none';
    }
}

/* ── Dynamic Links Builder ── */
function addLinkRow() {
    const builder = document.getElementById('linksBuilder');
    if (!builder) return;

    const row = document.createElement('div');
    row.className = 'link-row';
    row.innerHTML = `
        <input type="text" name="link_label[]" class="form-input" placeholder="Label (e.g., Apply Online)">
        <input type="url" name="link_url[]" class="form-input" placeholder="https://example.com/apply">
        <button type="button" class="btn btn-ghost btn-icon remove-row" title="Remove" onclick="this.parentElement.remove()"><i data-lucide="trash-2"></i></button>
    `;
    builder.appendChild(row);
    if (window.lucide) {
        window.lucide.createIcons({
            attrs: {
                class: 'lucide'
            },
            nameAttr: 'data-lucide',
            nodeList: row.querySelectorAll('[data-lucide]')
        });
    }
    row.querySelector('input').focus();
}

function addDateRow() {
    const builder = document.getElementById('datesBuilder');
    if (!builder) return;

    const row = document.createElement('div');
    row.className = 'link-row';
    row.innerHTML = `
        <input type="text" name="date_label[]" class="form-input" placeholder="Event (e.g., Apply Start)">
        <input type="text" name="date_value[]" class="form-input" placeholder="Date (e.g., 15 Jan 2026)">
        <button type="button" class="btn btn-ghost btn-icon remove-row" title="Remove" onclick="this.parentElement.remove()"><i data-lucide="trash-2"></i></button>
    `;
    builder.appendChild(row);
    if (window.lucide) {
        window.lucide.createIcons({
            attrs: {
                class: 'lucide'
            },
            nameAttr: 'data-lucide',
            nodeList: row.querySelectorAll('[data-lucide]')
        });
    }
    row.querySelector('input').focus();
}

/* ── Remove Row Buttons ── */
function initRemoveRows() {
    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-row') || e.target.closest('.remove-row')) {
            const btn = e.target.classList.contains('remove-row') ? e.target : e.target.closest('.remove-row');
            const row = btn.closest('.link-row');
            if (row) {
                const builder = row.parentElement;
                // Keep at least one row
                if (builder.querySelectorAll('.link-row').length > 1) {
                    row.remove();
                } else {
                    // Clear inputs instead of removing
                    row.querySelectorAll('input').forEach(input => input.value = '');
                }
            }
        }
    });
}

/* ── Confirm Delete ── */
function confirmDelete(message) {
    return confirm(message || 'Are you sure you want to delete this item?');
}

/* ── Profile Dropdown Toggle ── */
function initProfileDropdown() {
    const dropdown = document.getElementById('profileDropdown');
    const dropdownBtn = document.getElementById('profileDropdownBtn');

    if (!dropdown || !dropdownBtn) return;

    dropdownBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        dropdown.classList.toggle('open');
    });

    // Close when clicking outside
    document.addEventListener('click', (e) => {
        if (!dropdown.contains(e.target)) {
            dropdown.classList.remove('open');
        }
    });
}

/* ── Change Password Modal ── */
function initChangePasswordModal() {
    const modal = document.getElementById('changePasswordModal');
    const openBtn = document.getElementById('changePasswordBtn');
    const closeBtn = document.getElementById('modalClose');
    const cancelBtn = document.getElementById('modalCancelBtn');
    const form = document.getElementById('changePasswordForm');
    const alertBox = document.getElementById('modalAlert');
    const submitBtn = document.getElementById('modalSubmitBtn');

    if (!modal || !openBtn) return;

    // Open modal
    openBtn.addEventListener('click', (e) => {
        e.preventDefault();
        // Close dropdown
        const dropdown = document.getElementById('profileDropdown');
        if (dropdown) dropdown.classList.remove('open');
        
        modal.classList.add('open');
        form.reset();
        alertBox.style.display = 'none';
        
        // Reset password input fields back to 'password' type and hide eye buttons
        form.querySelectorAll('.password-input-wrapper').forEach(wrapper => {
            const input = wrapper.querySelector('input');
            const btn = wrapper.querySelector('.password-toggle-btn');
            const icon = wrapper.querySelector('.password-toggle-btn i') || wrapper.querySelector('.password-toggle-btn svg');
            if (input) input.type = 'password';
            if (btn) btn.style.display = 'none';
            if (icon && window.lucide) {
                icon.setAttribute('data-lucide', 'eye');
                window.lucide.createIcons({
                    nodeList: [icon]
                });
            }
        });
        
        // Focus first input
        setTimeout(() => {
            const firstInput = form.querySelector('input');
            if (firstInput) firstInput.focus();
        }, 100);
    });

    // Close modal
    const closeModal = () => {
        modal.classList.remove('open');
    };

    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);
    document.getElementById('modalBackdrop').addEventListener('click', closeModal);

    // Form submission
    form.addEventListener('submit', (e) => {
        e.preventDefault();

        // Get passwords
        const currentPassword = form.elements['current_password'].value;
        const newPassword = form.elements['new_password'].value;
        const confirmPassword = form.elements['confirm_password'].value;

        if (newPassword !== confirmPassword) {
            showAlert('error', 'New passwords do not match.');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerText = 'Updating...';

        // Prepare AJAX request
        const formData = new FormData(form);
        
        const url = (typeof APP_URL !== 'undefined' ? APP_URL : '/') + 'admin/change-password';

        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            return response.json().then(data => {
                if (!response.ok) {
                    throw new Error(data.message || 'Server error occurred.');
                }
                return data;
            }).catch(e => {
                if (!response.ok) {
                    throw new Error('Server returned error status: ' + response.status);
                }
                throw e;
            });
        })
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.innerText = 'Update Password';

            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => {
                    closeModal();
                }, 1500);
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.innerText = 'Update Password';
            showAlert('error', err.message || 'An error occurred. Please try again.');
            console.error('Error updating password:', err);
        });
    });

    function showAlert(type, message) {
        alertBox.className = 'alert ' + (type === 'success' ? 'alert-success' : 'alert-danger');
        alertBox.innerText = message;
        alertBox.style.display = 'block';
    }
}

/* ── Password Visibility Toggles ── */
function initPasswordVisibilityToggles() {
    document.querySelectorAll('.password-input-wrapper').forEach(wrapper => {
        const input = wrapper.querySelector('input');
        const btn = wrapper.querySelector('.password-toggle-btn');
        
        if (!input || !btn) return;

        // Force toggle button to always stay visible
        const toggleBtnVisibility = () => {
            btn.style.display = 'flex';
        };

        // Initialize visibility
        toggleBtnVisibility();

        // Monitor input typing
        input.addEventListener('input', toggleBtnVisibility);
        
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            
            // Toggle data-lucide and re-create icon
            const icon = btn.querySelector('i') || btn.querySelector('svg');
            if (icon && window.lucide) {
                const newIconName = isPassword ? 'eye-off' : 'eye';
                icon.setAttribute('data-lucide', newIconName);
                window.lucide.createIcons({
                    attrs: {
                        class: 'lucide'
                    },
                    nameAttr: 'data-lucide',
                    nodeList: [icon]
                });
            }
        });
    });
}

/* ── Form Wizard Navigation ── */
function initFormWizard() {
    const wizardForm = document.querySelector('.wizard-form');
    if (!wizardForm) return;

    const steps = wizardForm.querySelectorAll('.wizard-step');
    const nodes = wizardForm.querySelectorAll('.wizard-step-node');
    const progressBar = wizardForm.querySelector('.wizard-progress-bar');
    const prevBtn = wizardForm.querySelector('.wizard-prev-btn');
    const nextBtn = wizardForm.querySelector('.wizard-next-btn');
    const submitGroup = wizardForm.querySelector('.wizard-submit-group');

    let currentStep = 1;
    const totalSteps = steps.length;

    function updateWizardUI() {
        // Toggle steps
        steps.forEach(step => {
            const stepNum = parseInt(step.dataset.step, 10);
            if (stepNum === currentStep) {
                step.classList.add('active');
            } else {
                step.classList.remove('active');
            }
        });

        // Update progress nodes
        nodes.forEach(node => {
            const nodeNum = parseInt(node.dataset.step, 10);
            if (nodeNum === currentStep) {
                node.classList.add('active');
                node.classList.remove('completed');
            } else if (nodeNum < currentStep) {
                node.classList.add('completed');
                node.classList.remove('active');
            } else {
                node.classList.remove('active', 'completed');
            }
        });

        // Update progress bar width percentage
        const percent = ((currentStep - 1) / (totalSteps - 1)) * 100;
        if (progressBar) {
            progressBar.style.width = `${percent}%`;
        }

        // Toggle buttons
        if (prevBtn) {
            prevBtn.style.display = currentStep === 1 ? 'none' : 'flex';
        }

        if (currentStep === totalSteps) {
            if (nextBtn) nextBtn.style.display = 'none';
            if (submitGroup) submitGroup.style.display = 'flex';
        } else {
            if (nextBtn) nextBtn.style.display = 'flex';
            if (submitGroup) submitGroup.style.display = 'none';
        }

        // Re-run Lucide icons for step buttons
        if (window.lucide) {
            window.lucide.createIcons();
        }

        // Scroll to top of the form
        wizardForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function validateCurrentStep() {
        const activeStepContainer = wizardForm.querySelector(`.wizard-step[data-step="${currentStep}"]`);
        if (!activeStepContainer) return true;

        const fields = activeStepContainer.querySelectorAll('input, select, textarea');
        for (let i = 0; i < fields.length; i++) {
            const field = fields[i];
            
            // Check if standard validation passes
            if (!field.checkValidity()) {
                field.reportValidity();
                field.focus();
                return false;
            }
        }
        return true;
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (validateCurrentStep()) {
                if (currentStep < totalSteps) {
                    currentStep++;
                    updateWizardUI();
                }
            }
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (currentStep > 1) {
                currentStep--;
                updateWizardUI();
            }
        });
    }

    // Allow user to click completed steps or previous steps to jump directly
    nodes.forEach(node => {
        node.addEventListener('click', () => {
            const targetStep = parseInt(node.dataset.step, 10);
            
            if (targetStep < currentStep) {
                currentStep = targetStep;
                updateWizardUI();
            } else if (targetStep > currentStep) {
                let canJump = true;
                const originalStep = currentStep;
                for (let s = currentStep; s < targetStep; s++) {
                    currentStep = s;
                    if (!validateCurrentStep()) {
                        canJump = false;
                        break;
                    }
                }
                if (canJump) {
                    currentStep = targetStep;
                    updateWizardUI();
                } else {
                    updateWizardUI();
                }
            }
        });
    });

    // Initialize UI
    updateWizardUI();
}

/* ── Admin SPA Router (PJAX) ── */
function initAdminSPA() {
    // Intercept clicks on local admin links
    document.addEventListener('click', (e) => {
        const link = e.target.closest('a');
        if (!link) return;

        // Skip middle click, right click, Ctrl/Cmd click
        if (e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

        const href = link.getAttribute('href');
        const target = link.getAttribute('target');

        // Check if link is local and inside admin namespace
        if (href && !href.startsWith('#') && !href.startsWith('javascript:') && (!target || target === '_self')) {
            const isLogout = href.includes('admin/logout');
            // View Site link should open normally
            const isViewSite = href === APP_URL || href === APP_URL + '/' || !href.includes('admin');
            
            if (href.includes('admin') && !isLogout && !isViewSite) {
                e.preventDefault();
                navigateAdminSPA(href, true);
            }
        }
    });

    // Intercept form submissions
    document.addEventListener('submit', (e) => {
        const form = e.target.closest('form');
        if (!form) return;

        // If form action is an admin path
        const action = form.getAttribute('action') || window.location.href;
        if (action.includes('admin') && !action.includes('admin/logout') && !form.dataset.noSpa) {
            e.preventDefault();
            submitAdminSPAForm(form);
        }
    });

    // Back/Forward buttons support
    window.addEventListener('popstate', (e) => {
        navigateAdminSPA(window.location.href, false);
    });
}

function navigateAdminSPA(url, pushState = true) {
    const mainContent = document.querySelector('.admin-content');
    if (!mainContent) return;

    // Show a loading indicator
    let loader = document.getElementById('adminSpaLoader');
    if (!loader) {
        loader = document.createElement('div');
        loader.id = 'adminSpaLoader';
        loader.style = 'position: fixed; top: 0; left: 0; right: 0; height: 3px; background: var(--primary); z-index: 99999; width: 0; transition: width 0.2s ease, opacity 0.2s ease;';
        document.body.appendChild(loader);
    }
    loader.style.width = '30%';
    loader.style.opacity = '1';

    fetch(url)
        .then(res => {
            loader.style.width = '70%';
            return res.text();
        })
        .then(html => {
            loader.style.width = '100%';
            setTimeout(() => {
                loader.style.opacity = '0';
                loader.style.width = '0';
            }, 300);

            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Swap page title
            document.title = doc.title;

            // Swap header page title
            const newHeaderTitle = doc.querySelector('.admin-page-title');
            const oldHeaderTitle = document.querySelector('.admin-page-title');
            if (newHeaderTitle && oldHeaderTitle) {
                oldHeaderTitle.innerHTML = newHeaderTitle.innerHTML;
            }

            // Swap content
            const newContent = doc.querySelector('.admin-content');
            if (newContent) {
                mainContent.innerHTML = newContent.innerHTML;
                
                // Swap flash messages if any
                const newAlerts = doc.querySelector('.admin-alerts');
                const oldAlerts = document.querySelector('.admin-alerts');
                if (newAlerts && oldAlerts) {
                    oldAlerts.innerHTML = newAlerts.innerHTML;
                    oldAlerts.style.display = 'block';
                } else if (newAlerts) {
                    const mainParent = document.querySelector('.admin-main');
                    const headerEl = document.querySelector('.admin-topbar');
                    if (mainParent && headerEl) {
                        const alertDiv = document.createElement('div');
                        alertDiv.className = 'admin-alerts';
                        alertDiv.style = 'padding: var(--space-4) var(--space-6) 0 var(--space-6);';
                        alertDiv.innerHTML = newAlerts.innerHTML;
                        mainParent.insertBefore(alertDiv, headerEl.nextSibling);
                    }
                } else if (oldAlerts) {
                    oldAlerts.innerHTML = '';
                }

                // Update active link in sidebar
                const currentPath = new URL(url, window.location.origin).pathname;
                document.querySelectorAll('.admin-sidebar-nav a').forEach(a => {
                    const hrefPath = new URL(a.href, window.location.origin).pathname;
                    if (currentPath === hrefPath || currentPath.startsWith(hrefPath + '/')) {
                        a.classList.add('active');
                    } else {
                        a.classList.remove('active');
                    }
                });

                if (pushState) {
                    window.history.pushState({}, doc.title, url);
                }

                // Reinitialize admin scripts
                reinitAdminModules();
                executeSwappedScripts(mainContent);
            }
        })
        .catch(err => {
            console.error('SPA navigation failed:', err);
            window.location.href = url; // Fallback to full load
        });
}

function submitAdminSPAForm(form) {
    const action = form.getAttribute('action') || window.location.href;
    const method = (form.getAttribute('method') || 'GET').toUpperCase();
    const formData = new FormData(form);

    const loader = document.getElementById('adminSpaLoader');
    if (loader) {
        loader.style.width = '40%';
        loader.style.opacity = '1';
    }

    let fetchOptions = {
        method: method
    };

    if (method === 'POST') {
        fetchOptions.body = formData;
    } else {
        const params = new URLSearchParams(formData).toString();
        const separator = action.includes('?') ? '&' : '?';
        action = action + separator + params;
    }

    fetch(action, fetchOptions)
        .then(res => {
            if (loader) loader.style.width = '80%';
            if (res.redirected) {
                return navigateAdminSPA(res.url, true);
            }
            return res.text().then(html => {
                if (loader) {
                    loader.style.width = '100%';
                    setTimeout(() => {
                        loader.style.opacity = '0';
                        loader.style.width = '0';
                    }, 300);
                }

                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                document.title = doc.title;
                const mainContent = document.querySelector('.admin-content');
                const newContent = doc.querySelector('.admin-content');
                if (newContent && mainContent) {
                    mainContent.innerHTML = newContent.innerHTML;
                    
                    const newHeaderTitle = doc.querySelector('.admin-page-title');
                    const oldHeaderTitle = document.querySelector('.admin-page-title');
                    if (newHeaderTitle && oldHeaderTitle) {
                        oldHeaderTitle.innerHTML = newHeaderTitle.innerHTML;
                    }

                    const newAlerts = doc.querySelector('.admin-alerts');
                    const oldAlerts = document.querySelector('.admin-alerts');
                    if (newAlerts && oldAlerts) {
                        oldAlerts.innerHTML = newAlerts.innerHTML;
                    }

                    window.history.pushState({}, doc.title, res.url || action);
                    reinitAdminModules();
                    executeSwappedScripts(mainContent);
                }
            });
        })
        .catch(err => {
            console.error('SPA form submit failed:', err);
            form.submit();
        });
}

function reinitAdminModules() {
    if (window.lucide) {
        window.lucide.createIcons();
    }
    initLangTabs();
    initImagePreview();
    initRemoveRows();
    initProfileDropdown();
    initChangePasswordModal();
    initPasswordVisibilityToggles();
    initFormWizard();
}

function executeSwappedScripts(container) {
    const scripts = container.querySelectorAll('script');
    scripts.forEach(oldScript => {
        const newScript = document.createElement('script');
        let code = oldScript.textContent;
        if (code.includes("DOMContentLoaded")) {
            code = `
            (function() {
                const originalAdd = document.addEventListener;
                document.addEventListener = function(event, callback) {
                    if (event === 'DOMContentLoaded') {
                        callback();
                    } else {
                        originalAdd.apply(document, arguments);
                    }
                };
                try {
                    ${code}
                } finally {
                    document.addEventListener = originalAdd;
                }
            })();
            `;
        }
        newScript.textContent = code;
        oldScript.parentNode.replaceChild(newScript, oldScript);
    });
}

