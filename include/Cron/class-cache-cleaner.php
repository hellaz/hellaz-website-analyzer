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
        // Add salt rotation every 30 days
        if (rand(1, 30) === 1) { // Random chance to rotate
            update_option('hellaz_async_salt', wp_generate_password(64, true, true));
        }
        $transient_prefix = '_transient_hellaz_analysis_%';
        $wpdb->query(
            $wpdb->prepare("
                DELETE FROM {$wpdb->options}
                WHERE option_name LIKE %s
                AND option_value < %s
            ", $transient_prefix, time())
        );
    }
}
