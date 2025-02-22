<?php
namespace Hellaz\Analysis;

class Cache {
    public function get($url) {
        $key = $this->get_cache_key($url);
        $data = get_transient($key);
        
        if ($data && $this->is_cache_valid($data)) {
            return $data['results'];
        }
        return false;
    }

    public function set($url, $data) {
        $key = $this->get_cache_key($url);
        $existing = $this->get($url) ?: [];
    
        // Merge new data with existing cache
        $cache_data = [
            'results' => array_merge($existing, $data),
            'cached_at' => time()
        ];
    
        set_transient($key, $cache_data, $this->get_ttl());
    }

    private function get_cache_key($url) {
        return 'hellaz_analysis_' . md5($url);
    }

    private function get_ttl() {
        return (int) get_option('hellaz_settings')['cache_ttl'] * HOUR_IN_SECONDS;
    }

    private function is_cache_valid($data) {
        $max_age = $this->get_ttl();
        return (time() - $data['cached_at']) < $max_age;
    }
}
