<?php
namespace Hellaz\Utilities;

class Security {
    public static function generate_nonce($action = 'hellaz-analysis') {
        return wp_create_nonce($action);
    }

    public static function verify_nonce($nonce, $action = 'hellaz-analysis') {
        return wp_verify_nonce($nonce, $action);
    }

    public static function sanitize_array($array) {
        return array_map(function($item) {
            return is_array($item) ? self::sanitize_array($item) : sanitize_text_field($item);
        }, $array);
    }
}
