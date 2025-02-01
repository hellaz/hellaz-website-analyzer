<?php
namespace Hellaz\Utilities;

class Installer {
    const DB_VERSION = '1.0';
    const DB_VERSION_OPTION = 'hellaz_db_version';

    public static function activate() {
        self::create_tables();
        self::set_default_options();
        update_option(self::DB_VERSION_OPTION, self::DB_VERSION);
    }

    private static function create_tables() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'hellaz_analysis_data';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            url varchar(255) NOT NULL,
            data longtext NOT NULL,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY url_index (url(191))
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    private static function set_default_options() {
        if (!get_option('hellaz_async_salt')) {
        update_option('hellaz_async_salt', wp_generate_password(64, true, true), false);
        }
        add_option('hellaz_settings', [
            'cache_ttl' => 6,
            'enable_ssl_check' => true,
            'default_open_links' => '_blank',
            'active_modules' => ['metadata', 'social', 'security']
        ]);
    }

    public static function deactivate() {
        wp_clear_scheduled_hook('hellaz_clean_expired_cache');
    }

    public static function uninstall() {
        global $wpdb;
        
        // Remove tables
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}hellaz_analysis_data");
        
        // Remove options
        delete_option('hellaz_settings');
        delete_option(self::DB_VERSION_OPTION);
    }
}
