<?php
/**
 * Variables provided:
 * - $analysis_data (array) Analysis results
 * - $attributes (array) Block attributes
 */
?>
<div class="hellaz-analysis-block <?php echo esc_attr($attributes['className'] ?? ''); ?>">
    <?php if (!empty($analysis_data['error'])) : ?>
        <div class="hellaz-error"><?php echo esc_html($analysis_data['error']); ?></div>
    <?php else : ?>
        <div class="hellaz-analysis-header">
            <?php if (!empty($analysis_data['favicon'])) : ?>
                <img src="<?php echo esc_url($analysis_data['favicon']); ?>" 
                     class="hellaz-favicon" 
                     alt="<?php esc_attr_e('Website favicon', 'hellaz-website-analyzer'); ?>">
            <?php endif; ?>
            <h3><?php echo esc_html($analysis_data['title']); ?></h3>
        </div>
        
        <div class="hellaz-analysis-grid">
            <?php foreach ($analysis_data['sections'] as $section) : ?>
                <?php if (in_array($section['id'], get_option('hellaz_settings')['active_modules'])) : ?>
                    <div class="hellaz-analysis-section">
                        <h4><?php echo esc_html($section['title']); ?></h4>
                        <div class="hellaz-section-content">
                            <?php echo wp_kses_post($section['content']); ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        
        <?php if (get_option('hellaz_settings')['enable_disclaimer']) : ?>
        <div class="hellaz-disclaimer">
            <?php esc_html_e('Data automatically collected from remote website analysis.', 'hellaz-website-analyzer'); ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
