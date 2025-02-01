<?php
/*
Plugin Name: HellaZ Website Analyzer
Description: Comprehensive remote website analysis tool.
Version: 1.0.0
Author: Hellaz.Team
Text Domain: hellaz-website-analyzer
*/

if (!defined('ABSPATH')) exit;

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'Hellaz\\';
    $base_dir = __DIR__ . '/includes/';
    if (strpos($class, $prefix) === 0) {
        $file = $base_dir . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
        if (file_exists($file)) require $file;
    }
});

// Initialize plugin
add_action('plugins_loaded', function () {
    Hellaz\Analyzer::init();
    Hellaz\Admin::init();
    Hellaz\Block::init();
    Hellaz\Shortcode::init();
});
