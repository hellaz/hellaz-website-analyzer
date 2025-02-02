# Transient caching
// In class-cache.php
namespace Hellaz;
class Cache {
    public static function get($key) {
        return get_transient($key);
    }

    public static function set($key, $data, $expiration = 3600) {
        set_transient($key, $data, $expiration);
    }
}
