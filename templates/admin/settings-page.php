<div class="wrap hellaz-settings">
    <h1><?php esc_html_e('HellaZ Website Analyzer Settings', 'hellaz-website-analyzer'); ?></h1>
    
    <form method="post" action="options.php">
        <?php 
        settings_fields('hellaz_settings_group');
        do_settings_sections('hellaz-settings');
        submit_button(); 
        ?>
    </form>
    
    <?php if (current_user_can('manage_options')) : ?>
    <div class="hellaz-debug-info">
        <h3><?php esc_html_e('System Information', 'hellaz-website-analyzer'); ?></h3>
        <p><?php printf(
            __('Last cache cleanup: %s', 'hellaz-website-analyzer'), 
            get_option('hellaz_last_cache_cleanup') ?: __('Never', 'hellaz-website-analyzer')
        ); ?></p>
    </div>
    <?php endif; ?>
</div>
