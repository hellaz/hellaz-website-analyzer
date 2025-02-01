<?php
/**
 * Variables provided:
 * - $analysis_data (array) Analysis results
 * - $atts (array) Shortcode attributes
 */
?>
<div class="hellaz-analysis-shortcode">
    <?php if (!empty($analysis_data['error'])) : ?>
        <div class="hellaz-error notice notice-error">
            <?php echo esc_html($analysis_data['error']); ?>
        </div>
    <?php else : ?>
        <div class="hellaz-summary">
            <h2 class="hellaz-title">
                <?php if (!empty($analysis_data['favicon'])) : ?>
                    <img src="<?php echo esc_url($analysis_data['favicon']); ?>" 
                         class="hellaz-favicon"
                         alt="<?php esc_attr_e('Website icon', 'hellaz-website-analyzer'); ?>">
                <?php endif; ?>
                <?php echo esc_html($analysis_data['title']); ?>
            </h2>
            
            <?php if (!empty($analysis_data['description'])) : ?>
                <p class="hellaz-description">
                    <?php echo esc_html($analysis_data['description']); ?>
                </p>
            <?php endif; ?>
        </div>

        <div class="hellaz-details">
            <?php foreach (get_option('hellaz_settings')['active_modules'] as $module) : ?>
                <?php if (!empty($analysis_data[$module])) : ?>
                    <div class="hellaz-module hellaz-module-<?php echo esc_attr($module); ?>">
                        <h3><?php echo esc_html(ucfirst($module)); ?></h3>
                        <div class="hellaz-module-content">
                            <?php echo wp_kses_post($analysis_data[$module]); ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
