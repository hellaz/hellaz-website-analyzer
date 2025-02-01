<?php
namespace Hellaz\Utilities;

class Translator {
    public static function load_textdomain() {
        load_plugin_textdomain(
            'hellaz-website-analyzer',
            false,
            dirname(plugin_basename(HELLAZ_PLUGIN_FILE)) . '/languages/'
        );
    }

    public static function get_available_locales() {
        $translations = get_site_transient('available_translations');
        return array_keys($translations ?: []);
    }
}
