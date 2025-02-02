// Shortcode handler
<?php
namespace Hellaz;

class Shortcode {
    public static function init() {
        add_shortcode('hellaz_analyzer', [self::class, 'handle_shortcode']);
    }

    public static function handle_shortcode($atts) {
        $atts = shortcode_atts([
            'url' => '',
            'show' => 'all'
        ], $atts);

        if (empty($atts['url'])) return '';

        ob_start();
        $analysis_data = \Hellaz\Analysis\Engine::analyze(esc_url($atts['url']));
        include HELLAZ_PLUGIN_PATH . 'templates/shortcode-output.php';
        return ob_get_clean();
    }
}
