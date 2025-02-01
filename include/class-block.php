// Gutenberg block
<?php
namespace Hellaz;

class Block {
    public static function init() {
        add_action('init', [self::class, 'register_block']);
    }

    public static function register_block() {
        register_block_type(HELLAZ_PLUGIN_PATH . 'blocks/analyzer', [
            'render_callback' => [self::class, 'render_block']
        ]);
    }

    public static function render_block($attributes) {
        if (empty($attributes['url'])) return '';

        ob_start();
        $analysis_data = \Hellaz\Analysis\Engine::analyze(esc_url($attributes['url']));
        include HELLAZ_PLUGIN_PATH . 'templates/block-output.php';
        return ob_get_clean();
    }
}
