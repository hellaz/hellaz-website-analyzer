<?php
namespace Hellaz\Utilities;

class Formatter {
    public static function sanitize_html($html) {
        return wp_kses($html, [
            'a' => ['href' => true, 'title' => true],
            'br' => [], 'em' => [], 'strong' => [],
            'ul' => [], 'ol' => [], 'li' => []
        ]);
    }

    public static function validate_url($url) {
        return filter_var($url, FILTER_VALIDATE_URL) && preg_match('/^https?:\/\//i', $url);
    }

    public static function format_date($date_str) {
        $date = date_create($date_str);
        return $date ? date_format($date, 'Y-m-d H:i:s') : null;
    }

    public static function normalize_domain($url) {
        $parsed = parse_url($url);
        return isset($parsed['host']) ? strtolower($parsed['host']) : null;
    }
}
