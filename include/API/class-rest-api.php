<?php
namespace Hellaz\API;

use Hellaz\Analysis\Engine;
use Hellaz\Exceptions\AnalysisException;

class Rest_API {
    public static function init() {
        add_action('rest_api_init', [self::class, 'register_routes']);
    }

    public static function register_routes() {
        register_rest_route('hellaz/v1', '/analyze', [
            'methods' => 'GET',
            'callback' => [self::class, 'handle_analysis_request'],
            'permission_callback' => '__return_true',
            'args' => [
                'url' => [
                    'required' => true,
                    'validate_callback' => function($param) {
                        return filter_var($param, FILTER_VALIDATE_URL);
                    }
                ]
            ]
        ]);
    }

    public static function handle_analysis_request(\WP_REST_Request $request) {
        try {
            $url = esc_url_raw($request->get_param('url'));
            $engine = new Engine();
            $data = $engine->analyze($url);
            
            return rest_ensure_response($data);
            
        } catch (AnalysisException $e) {
            return new \WP_Error('analysis_failed', $e->getMessage(), ['status' => 400]);
        }
    }
}
