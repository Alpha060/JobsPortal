<?php
/**
 * Admin Settings Controller
 */
class AdminSettingController
{
    public function index(): void
    {
        $auth = new Auth();
        $auth->requireAuth();

        $settings = new Setting();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) {
                setFlash('error', 'Invalid security token.');
                redirect(url('admin/settings'));
            }

            $fields = [
                'site_name_en', 'site_name_hi',
                'site_tagline_en', 'site_tagline_hi',
                'site_description_en', 'site_description_hi',
                'footer_text_en', 'footer_text_hi',
                'contact_email',
                'social_facebook', 'social_twitter', 'social_telegram', 'social_youtube',
                'ticker_text_en', 'ticker_text_hi',
                'posts_per_page', 'default_language',
            ];

            foreach ($fields as $field) {
                if (isset($_POST[$field])) {
                    $settings->set($field, trim($_POST[$field]));
                }
            }

            // Handle logo upload
            if (!empty($_FILES['site_logo']['name'])) {
                $media = new Media();
                $uploaded = $media->upload($_FILES['site_logo']);
                if ($uploaded) {
                    $settings->set('site_logo', $uploaded['filename']);
                }
            }

            $settings->clearCache();
            setFlash('success', 'Settings saved successfully!');
            redirect(url('admin/settings'));
        }

        $adminPageTitle = __('site_settings');
        include VIEWS_PATH . '/admin/settings.php';
    }
}
