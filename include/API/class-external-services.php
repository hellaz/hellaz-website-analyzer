<?php
namespace Hellaz\API;

use Hellaz\Exceptions\ApiException;

class External_Services {
    public static function init() {
        add_action('hellaz_async_api_call', [self::class, 'handle_async_call']);
    }

    public static function detect_technology($url) {
        $endpoint = "https://api.wappalyzer.com/v2/lookup/?url=" . urlencode($url);
        return self::call_api($endpoint);
    }

    public static function get_ssl_info($domain) {
        $endpoint = "https://ssl-cert-checker.p.rapidapi.com/api/check?domain=" . urlencode($domain);
        return self::call_api($endpoint, [
            'headers' => [
                'X-RapidAPI-Host' => 'ssl-cert-checker.p.rapidapi.com',
                'X-RapidAPI-Key' => ''
            ]
        ]);
    }

    private static function call_api($endpoint, $args = []) {
        $response = wp_safe_remote_get($endpoint, array_merge([
            'timeout' => 10,
            'redirection' => 2
        ], $args));

        if (is_wp_error($response)) {
            throw new ApiException($response->get_error_message());
        }

        return json_decode(wp_remote_retrieve_body($response), true);
    }

    public static function handle_async_call($data) {
        // Implement async request handling
    }
}
