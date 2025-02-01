<?php
namespace Hellaz;

class Analyzer {
    public static function init() {
        // Register hooks
    }

    public static function analyze_url($url) {
        $transient_key = 'hellaz_analysis_' . md5($url);
        $data = get_transient($transient_key);

        if ($data === false) {
            $response = wp_remote_get($url, ['timeout' => 15]);
            if (is_wp_error($response)) return false;

            $html = wp_remote_retrieve_body($response);
            $data = self::parse_html($html);
            set_transient($transient_key, $data, HOUR_IN_SECONDS);
        }

        return $data;
    }

    private static function parse_html($html) {
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        
        // Extract metadata
        $data = [
            'title' => self::get_meta_content($dom, 'og:title', 'twitter:title', 'title'),
            'description' => self::get_meta_content($dom, 'og:description', 'twitter:description', 'description'),
            // Add more fields
        ];

        return $data;
    }

    private static function get_meta_content($dom, ...$names) {
        foreach ($names as $name) {
            $meta = $dom->getElementsByTagName('meta');
            foreach ($meta as $tag) {
                if ($tag->getAttribute('property') === $name || $tag->getAttribute('name') === $name) {
                    return $tag->getAttribute('content');
                }
            }
        }
        return '';
    }
}
