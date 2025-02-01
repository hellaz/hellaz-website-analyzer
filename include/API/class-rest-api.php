<?php
namespace Hellaz\API;

use Hellaz\Analysis\Engine;
use Hellaz\Exceptions\AnalysisException;

class Rest_API {
    public static function init() {
        add_action('rest_api_init', [self::class, 'register_routes']);
    }

    public static function register_routes() {
        // Existing analyze endpoint
        register_rest_route('hellaz/v1', '/analyze', [
            'methods' => 'GET',
            'callback' => [self::class, 'handle_analysis_request'],
            'args' => [
                'url' => [
                    'required' => true,
                    'validate_callback' => function($param) {
                        return filter_var($param, FILTER_VALIDATE_URL);
                    }
                ]
            ]
        ]);
    
        // New batch analysis endpoint
        register_rest_route('hellaz/v1', '/batch-analyze', [
            'methods' => 'POST',
            'callback' => [self::class, 'handle_batch_analysis'],
            'permission_callback' => function() {
                return current_user_can('manage_options');
            }
        ]);
    }
    
    public static function handle_batch_analysis(\WP_REST_Request $request) {
        $urls = $request->get_param('urls');
        $results = [];
        
        foreach ($urls as $url) {
            if (Formatter::validate_url($url)) {
                $results[] = [
                    'url' => $url,
                    'data' => (new Engine())->analyze($url)
                ];
            }
        }
        
        return rest_ensure_response($results);
    }
}
