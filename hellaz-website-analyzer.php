<?php
/**
 * Plugin Name: Hellaz Website Analyzer
 * Plugin URI: https://github.com/hellaz/hellaz-website-analyzer/
 * Description: Comprehensive remote website analysis tool with advanced metadata extraction and technology detection.
 * Version: 1.0.0
 * Author: Hellaz.Team
 * Author URI: https://hellaz.net
 * Text Domain: hellaz-website-analyzer
 * Domain Path: /languages
 * Requires at least: 5.6
 * Requires PHP: 7.4
 * License: GPLv2 or later
 */

defined('ABSPATH') || exit;

if (version_compare(PHP_VERSION, '7.4', '<')) {
    add_action('admin_notices', function() {
        echo '<div class="error"><p>' . 
            esc_html__('Hellaz Analyzer requires PHP 7.4+', 'hellaz-website-analyzer') . 
            '</p></div>';
    });
    return;
}

// Define plugin constants
define('HELLAZ_PLUGIN_VERSION', '1.0.0');
define('HELLAZ_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('HELLAZ_PLUGIN_URL', plugin_dir_url(__FILE__));
define('HELLAZ_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('HELLAZ_CACHE_TTL', 6 * HOUR_IN_SECONDS); // 6 hours cache

// Autoload classes
spl_autoload_register(function ($class_name) {
    if (strpos($class_name, 'Hellaz\\') === 0) {
        $class_file = HELLAZ_PLUGIN_PATH . 'includes/' . str_replace('\\', '/', substr($class_name, 7)) . '.php';
        if (file_exists($class_file)) {
            require_once $class_file;
        }
    }
});

// Register activation/deactivation hooks
register_activation_hook(__FILE__, ['Hellaz\\Installer', 'activate']);
register_deactivation_hook(__FILE__, ['Hellaz\\Installer', 'deactivate']);
register_uninstall_hook(__FILE__, ['Hellaz\\Installer', 'uninstall']);

// Initialize the plugin
add_action('plugins_loaded', function () {
    // Load translations
    load_plugin_textdomain(
        'hellaz-website-analyzer',
        false,
        dirname(plugin_basename(__FILE__)) . '/languages/'
    );

    // Initialize core components
    Hellaz\Admin\Settings::init();
    Hellaz\Analysis\Engine::init();
    Hellaz\Blocks\Analyzer_Block::init();
    Hellaz\Shortcodes\Analyzer_Shortcode::init();
    Hellaz\API\External_Services::init();

    // Initialize REST API endpoints
    if (did_action('init')) {
        Hellaz\API\Rest_API::init();
    } else {
        add_action('init', ['Hellaz\API\Rest_API', 'init']);
    }

    // Initialize cron jobs
    Hellaz\Cron\Cache_Cleaner::schedule_events();
});

// Handle plugin updates
add_action('init', function () {
    if (class_exists('Hellaz\Utilities\Updater')) {
        new Hellaz\Utilities\Updater(__FILE__);
    }
});

// Add settings link to plugin actions
add_filter('plugin_action_links_' . HELLAZ_PLUGIN_BASENAME, function ($links) {
    $settings_link = sprintf(
        '<a href="%s">%s</a>',
        esc_url(admin_url('admin.php?page=hellaz-settings')),
        esc_html__('Settings', 'hellaz-website-analyzer')
    );
    array_unshift($links, $settings_link);
    return $links;
});

/**
 * Debugging helper function
 */
if (!function_exists('hellaz_log')) {
    function hellaz_log($message) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[Hellaz Analyzer] ' . print_r($message, true));
        }
    }
}
