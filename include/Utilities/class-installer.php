<?php
namespace Hellaz\Utilities;

class Installer {
    const DB_VERSION = '1.1';
    const DB_VERSION_OPTION = 'hellaz_db_version';
    const MIN_DB_VERSION = '1.0';

    public static function activate() {
        self::create_tables();
        self::set_default_options();
        self::maybe_upgrade();
    }

    public static function deactivate() {
        wp_clear_scheduled_hook('hellaz_clean_expired_cache');
    }

    public static function uninstall() {
        global $wpdb;
        
        // Remove database tables
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}hellaz_analysis_data");
        
        // Remove all options
        delete_option('hellaz_settings');
        delete_option('hellaz_async_salt');
        delete_option('hellaz_salt_rotation');
        delete_option(self::DB_VERSION_OPTION);
    }

    private static function create_tables() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'hellaz_analysis_data';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            url varchar(255) NOT NULL,
            data longtext NOT NULL,
            created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            updated_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            expires_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            PRIMARY KEY  (id),
            UNIQUE KEY url_index (url(191)),
            KEY expires_at_index (expires_at)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    private static function set_default_options() {
        // Async security salt
        if (!get_option('hellaz_async_salt')) {
            update_option(
                'hellaz_async_salt',
                wp_generate_password(64, true, true),
                false // No autoload
            );
        }

        // Salt rotation tracking
        if (!get_option('hellaz_salt_rotation')) {
            add_option(
                'hellaz_salt_rotation',
                time(),
                '',
                'no' // No autoload
            );
        }

        // Main settings
        if (!get_option('hellaz_settings')) {
            add_option('hellaz_settings', [
                'cache_ttl' => 6,
                'enable_ssl_check' => true,
                'default_open_links' => '_blank',
                'active_modules' => ['metadata', 'social', 'security'],
                'async_enabled' => true,
                'max_redirects' => 3,
                'enable_disclaimer' => true
            ], '', 'yes'); // Autoload
        }
    }

    public static function maybe_upgrade() {
        $current_version = get_option(self::DB_VERSION_OPTION, '0.1');

        if (version_compare($current_version, self::DB_VERSION, '<')) {
            if (version_compare($current_version, '1.0', '<')) {
                self::upgrade_to_1_0();
            }

            if (version_compare($current_version, '1.1', '<')) {
                self::upgrade_to_1_1();
            }

            update_option(self::DB_VERSION_OPTION, self::DB_VERSION);
        }
    }

    private static function upgrade_to_1_0() {
        global $wpdb;
        
        // Add new column for v1.0
        $table_name = $wpdb->prefix . 'hellaz_analysis_data';
        $wpdb->query(
            "ALTER TABLE $table_name ADD COLUMN expires_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00'"
        );
    }

    private static function upgrade_to_1_1() {
        // Add new settings while preserving existing values
        $old_settings = get_option('hellaz_settings', []);
        $new_defaults = [
            'async_enabled' => true,
            'max_redirects' => 3,
            'enable_disclaimer' => true
        ];
        
        update_option(
            'hellaz_settings',
            array_merge($new_defaults, $old_settings)
        );

        // Initialize salt rotation if missing
        if (!get_option('hellaz_salt_rotation')) {
            update_option(
                'hellaz_salt_rotation',
                time(),
                false
            );
        }
    }
}
