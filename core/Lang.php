<?php
/**
 * Language / i18n Handler
 * 
 * Simple key-based translation system supporting Hindi and English.
 * Language preference is stored in a cookie.
 */
class Lang
{
    private static ?Lang $instance = null;
    private string $currentLang;
    private array $translations = [];

    private function __construct()
    {
        $this->currentLang = $this->detectLanguage();
        $this->loadTranslations();
    }

    /** Get singleton instance */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Detect the user's preferred language
     */
    private function detectLanguage(): string
    {
        // 1. Check URL parameter (for switching)
        if (isset($_GET['lang']) && in_array($_GET['lang'], SUPPORTED_LANGS)) {
            $lang = $_GET['lang'];
            $this->setLanguageCookie($lang);
            return $lang;
        }

        // 2. Check cookie
        if (isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], SUPPORTED_LANGS)) {
            return $_COOKIE['lang'];
        }

        // 3. Check session
        if (isset($_SESSION['lang']) && in_array($_SESSION['lang'], SUPPORTED_LANGS)) {
            return $_SESSION['lang'];
        }

        // 4. Default
        return DEFAULT_LANG;
    }

    /**
     * Load translation file for the current language
     */
    private function loadTranslations(): void
    {
        $file = LANG_PATH . '/' . $this->currentLang . '.php';
        if (file_exists($file)) {
            $this->translations = require $file;
        }
    }

    /**
     * Get a translated string by key
     */
    public function get(string $key, array $replace = []): string
    {
        $text = $this->translations[$key] ?? $key;

        // Handle placeholder replacements
        foreach ($replace as $placeholder => $value) {
            $text = str_replace(':' . $placeholder, $value, $text);
        }

        return $text;
    }

    /**
     * Get a database field suffix for the current language
     * Returns '_en' or '_hi'
     */
    public function suffix(): string
    {
        return '_' . $this->currentLang;
    }

    /**
     * Get the current language code
     */
    public function current(): string
    {
        return $this->currentLang;
    }

    /**
     * Check if a specific language is active
     */
    public function is(string $lang): bool
    {
        return $this->currentLang === $lang;
    }

    /**
     * Set the language and store in cookie
     */
    public function setLanguage(string $lang): void
    {
        if (in_array($lang, SUPPORTED_LANGS)) {
            $this->currentLang = $lang;
            $this->setLanguageCookie($lang);
            $this->loadTranslations();
        }
    }

    /**
     * Store language preference in cookie (30 days)
     */
    private function setLanguageCookie(string $lang): void
    {
        setcookie('lang', $lang, [
            'expires'  => time() + (30 * 24 * 60 * 60),
            'path'     => '/',
            'secure'   => !DEBUG_MODE,
            'httponly' => false,
            'samesite' => 'Lax',
        ]);
        $_SESSION['lang'] = $lang;
    }

    /**
     * Get the localized field value from a database row
     * Falls back to English if the localized field is empty
     */
    public function field(array $row, string $fieldBase): string
    {
        $localizedField = $fieldBase . $this->suffix();
        $englishField = $fieldBase . '_en';

        $value = $row[$localizedField] ?? '';
        if (empty($value) && isset($row[$englishField])) {
            $value = $row[$englishField];
        }

        return $value;
    }

    /**
     * Get URL to switch language
     */
    public function switchUrl(string $lang): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $parsed = parse_url($uri);
        $path = $parsed['path'] ?? '/';
        
        // Parse existing query string
        $query = [];
        if (isset($parsed['query'])) {
            parse_str($parsed['query'], $query);
        }
        $query['lang'] = $lang;
        
        return $path . '?' . http_build_query($query);
    }
}

/**
 * Global translation shorthand function
 */
function __(string $key, array $replace = []): string
{
    return Lang::getInstance()->get($key, $replace);
}

/**
 * Echo translated string
 */
function _e(string $key, array $replace = []): void
{
    echo Lang::getInstance()->get($key, $replace);
}
