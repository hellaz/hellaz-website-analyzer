<?php
namespace Hellaz\API;

use Hellaz\Exceptions\ApiException;
use Hellaz\Analysis\Cache;

class External_Services {
    public static function init() {
        add_action('hellaz_async_api_call', [self::class, 'handle_async_call']);
        add_action('wp_ajax_hellaz_process_async', [self::class, 'process_async_request']);
        add_action('wp_ajax_nopriv_hellaz_process_async', [self::class, 'process_async_request']);
    }

    public static function handle_async_call($data) {
        if (!self::validate_async_data($data)) return;

        $salt = get_option('hellaz_async_salt');
        $hash = hash_hmac('sha256', wp_json_encode($data), $salt);

        wp_remote_post(admin_url('admin-ajax.php'), [
            'blocking'  => false,
            'timeout'   => 0.01,
            'sslverify' => apply_filters('hellaz_async_ssl_verify', false),
            'body'      => [
                'action' => 'hellaz_process_async',
                'data'   => wp_json_encode($data),
                'hash'   => $hash
            ]
        ]);
    }

    public static function process_async_request() {
        try {
            // Validate request
            if (!isset($_POST['data'], $_POST['hash'])) {
                throw new ApiException('Invalid async request', 400);
            }

            $salt = get_option('hellaz_async_salt');
            $data = json_decode(wp_unslash($_POST['data']), true);
            $hash = $_POST['hash'];

            if (!hash_equals(hash_hmac('sha256', wp_unslash($_POST['data']), $salt), $hash)) {
                throw new ApiException('Authentication failed', 403);
            }

            // Process request
            switch ($data['type']) {
                case 'technology':
                    $result = self::detect_technology($data['url']);
                    self::cache_result($data['url'], 'technology', $result);
                    break;

                case 'ssl':
                    $domain = parse_url($data['url'], PHP_URL_HOST);
                    $result = self::get_ssl_info($domain);
                    self::cache_result($data['url'], 'security', $result);
                    break;

                default:
                    throw new ApiException('Unknown async type', 400);
            }

            wp_send_json_success($result);
        } catch (ApiException $e) {
            wp_send_json_error([
                'message' => $e->getMessage(),
                'code' => $e->getStatusCode()
            ], $e->getStatusCode());
        }
    }

    private static function validate_async_data($data) {
        return isset($data['type'], $data['url']) && in_array($data['type'], ['technology', 'ssl']);
    }

    private static function cache_result($url, $category, $data) {
        $cache = new Cache();
        $existing = $cache->get($url) ?: [];
        
        // Merge new data while preserving existing cache
        $existing[$category] = array_merge(
            $existing[$category] ?? [],
            $data
        );
        
        $cache->set($url, $existing);
    }
}
