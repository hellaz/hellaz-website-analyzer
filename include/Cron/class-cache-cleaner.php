<?php
namespace Hellaz\Cron;

class Cache_Cleaner {
    public static function schedule_events() {
        add_action('hellaz_clean_expired_cache', [self::class, 'clean_cache']);
        
        if (!wp_next_scheduled('hellaz_clean_expired_cache')) {
            wp_schedule_event(time(), 'twicedaily', 'hellaz_clean_expired_cache');
        }
    }

    public static function clean_cache() {
        global $wpdb;
    
        // Salt rotation with 30-day interval
        $last_rotation = get_option('hellaz_salt_rotation', 0);
        if (time() - $last_rotation > 30 * DAY_IN_SECONDS) {
            update_option('hellaz_async_salt', wp_generate_password(64, true, true));
            update_option('hellaz_salt_rotation', time());
        }
    
        // Clean expired transients
        $transient_prefix = '_transient_hellaz_analysis_%';
        $wpdb->query(
            $wpdb->prepare("
                DELETE FROM {$wpdb->options}
                WHERE option_name LIKE %s
                AND option_value < %d
            ", $transient_prefix, time())
        );
    }
}
