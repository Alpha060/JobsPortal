<?php
/**
 * Setting Model
 * 
 * Manages site settings stored in the database.
 * Caches settings in session to avoid repeated DB queries.
 */
class Setting
{
    private Database $db;
    private static array $cache = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->loadCache();
    }

    /** Load all settings into cache */
    private function loadCache(): void
    {
        if (!empty(self::$cache)) return;

        if (isset($_SESSION['_settings_cache'])) {
            self::$cache = $_SESSION['_settings_cache'];
            return;
        }

        $rows = $this->db->fetchAll("SELECT `setting_key`, `setting_value` FROM `settings`");
        foreach ($rows as $row) {
            self::$cache[$row['setting_key']] = $row['setting_value'];
        }
        $_SESSION['_settings_cache'] = self::$cache;
    }

    /** Get a setting value */
    public function get(string $key, string $default = ''): string
    {
        return self::$cache[$key] ?? $default;
    }

    /** Get a localized setting (appends _en or _hi suffix) */
    public function getLocalized(string $key): string
    {
        $lang = Lang::getInstance()->current();
        $value = $this->get($key . '_' . $lang);
        if (empty($value)) {
            $value = $this->get($key . '_en');
        }
        return $value;
    }

    /** Set a setting value */
    public function set(string $key, string $value): void
    {
        $existing = $this->db->fetch(
            "SELECT id FROM `settings` WHERE `setting_key` = ?",
            [$key]
        );

        if ($existing) {
            $this->db->update('settings', ['setting_value' => $value], 'setting_key = ?', [$key]);
        } else {
            $this->db->insert('settings', [
                'setting_key'   => $key,
                'setting_value' => $value,
            ]);
        }

        self::$cache[$key] = $value;
        $_SESSION['_settings_cache'] = self::$cache;
    }

    /** Set multiple settings at once */
    public function setMany(array $settings): void
    {
        foreach ($settings as $key => $value) {
            $this->set($key, $value);
        }
    }

    /** Get all settings */
    public function getAll(): array
    {
        return self::$cache;
    }

    /** Clear the settings cache */
    public function clearCache(): void
    {
        self::$cache = [];
        unset($_SESSION['_settings_cache']);
    }
}
