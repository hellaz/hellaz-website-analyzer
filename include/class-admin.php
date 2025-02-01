// Settings UI
<?php
namespace Hellaz;

class Admin {
    public static function init() {
        add_action('admin_enqueue_scripts', [self::class, 'enqueue_assets']);
        Admin\Settings::init();
        Admin\Post_Meta::init();
    }

    public static function enqueue_assets($hook) {
        if ('toplevel_page_hellaz-settings' === $hook) {
            wp_enqueue_style(
                'hellaz-admin-css',
                HELLAZ_PLUGIN_URL . 'assets/css/admin.css',
                [],
                HELLAZ_PLUGIN_VERSION
            );
            
            wp_enqueue_script(
                'hellaz-admin-js',
                HELLAZ_PLUGIN_URL . 'assets/js/admin.js',
                ['jquery'],
                HELLAZ_PLUGIN_VERSION
            );
        }
    }
}
