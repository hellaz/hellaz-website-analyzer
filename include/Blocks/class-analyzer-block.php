<?php
namespace Hellaz\Blocks;

class Analyzer_Block {
    public static function init() {
        add_action('init', [self::class, 'register_block']);
        add_action('enqueue_block_editor_assets', [self::class, 'enqueue_assets']);
    }

    public static function register_block() {
        register_block_type(__DIR__ . '/../../blocks/analyzer', [
            'render_callback' => [self::class, 'render_block']
        ]);
    }

    public static function enqueue_assets() {
        wp_enqueue_script(
            'hellaz-block-editor',
            HELLAZ_PLUGIN_URL . 'assets/js/block-editor.js',
            ['wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor'],
            HELLAZ_PLUGIN_VERSION
        );
    }

    public static function render_block($attributes) {
        $url = sanitize_url($attributes['url'] ?? '');
        if (empty($url)) return '';

        ob_start();
        include HELLAZ_PLUGIN_PATH . 'templates/frontend/block-output.php';
        return ob_get_clean();
    }
}
