<?php
namespace Hellaz\Admin;

class Post_Meta {
    public static function init() {
        add_action('add_meta_boxes', [self::class, 'add_meta_box']);
        add_action('save_post', [self::class, 'save_meta']);
    }

    public static function add_meta_box() {
        add_meta_box(
            'hellaz_link_analysis',
            __('Website Analysis', 'hellaz-website-analyzer'),
            [self::class, 'render_meta_box'],
            'post',
            'side',
            'default'
        );
    }

    public static function render_meta_box($post) {
        $url = get_post_meta($post->ID, '_hellaz_analysis_url', true);
        wp_nonce_field('hellaz_save_analysis_url', 'hellaz_analysis_nonce');
        ?>
        <p>
            <label for="hellaz_analysis_url">
                <?php esc_html_e('Website URL to Analyze:', 'hellaz-website-analyzer'); ?>
            </label>
            <input type="url" id="hellaz_analysis_url" name="hellaz_analysis_url" 
                   value="<?php echo esc_url($url); ?>" class="widefat">
        </p>
        <?php
    }

    public static function save_meta($post_id) {
        if (!isset($_POST['hellaz_analysis_nonce']) || 
            !wp_verify_nonce($_POST['hellaz_analysis_nonce'], 'hellaz_save_analysis_url')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;

        $url = esc_url_raw($_POST['hellaz_analysis_url'] ?? '');
        update_post_meta($post_id, '_hellaz_analysis_url', $url);
    }
}
