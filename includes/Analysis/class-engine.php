<?php
namespace Hellaz\Analysis;

use Hellaz\Exceptions\AnalysisException;
use Hellaz\Utilities\Formatter;

class Engine {
    private static $instance = null;
    private $cache;
    private $parser;

    public static function init() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->cache = new Cache();
        $this->parser = new Parser();
    }

    public function analyze($url) {
        try {
            if (!Formatter::validate_url($url)) {
                throw new AnalysisException(__('Invalid URL format', 'hellaz-website-analyzer'));
            }

            // First check cache
            $cached = $this->cache->get($url);
            
            // Always trigger async updates regardless of cache status
            do_action('hellaz_async_api_call', [
                'type' => 'technology',
                'url' => $url
            ]);

            do_action('hellaz_async_api_call', [
                'type' => 'ssl',
                'url' => $url
            ]);

            // Return cached data immediately if available
            if ($cached) return $cached;

            // Proceed with fresh analysis
            $response = $this->fetch_url($url);
            $data = $this->parser->parse($response['body'], $url);
            
            $this->cache->set($url, $data);
            return $data;

        } catch (AnalysisException $e) {
            hellaz_log($e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    private function fetch_url($url) {
        $response = wp_safe_remote_get($url, [
            'timeout' => 15,
            'redirection' => 2,
            'user-agent' => 'Hellaz Website Analyzer/1.0 (+https://github.com/hellaz/hellaz-website-analyzer)'
        ]);

        if (is_wp_error($response)) {
            throw new AnalysisException($response->get_error_message());
        }

        if (200 !== wp_remote_retrieve_response_code($response)) {
            throw new AnalysisException(__('Remote server returned an error', 'hellaz-website-analyzer'));
        }

        return [
            'headers' => wp_remote_retrieve_headers($response),
            'body' => wp_remote_retrieve_body($response)
        ];
    }
}
