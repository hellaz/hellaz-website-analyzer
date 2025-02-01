<?php
namespace Hellaz\Admin;

class Settings {
    public static function init() {
        add_action('admin_menu', [self::class, 'add_menu']);
        add_action('admin_init', [self::class, 'register_settings']);
    }

    public static function add_menu() {
        add_menu_page(
            __('HellaZ Analyzer Settings', 'hellaz-website-analyzer'),
            __('HellaZ Analyzer', 'hellaz-website-analyzer'),
            'manage_options',
            'hellaz-settings',
            [self::class, 'render_settings_page'],
            'dashicons-analytics'
        );
    }

    public static function register_settings() {
        register_setting('hellaz_settings_group', 'hellaz_settings', [
            'sanitize_callback' => [self::class, 'sanitize_settings']
        ]);

        // General Settings Section
        add_settings_section(
            'hellaz_general',
            __('General Settings', 'hellaz-website-analyzer'),
            null,
            'hellaz-settings'
        );

        add_settings_field(
            'cache_ttl',
            __('Cache Duration (hours)', 'hellaz-website-analyzer'),
            [self::class, 'render_number_field'],
            'hellaz-settings',
            'hellaz_general',
            [
                'name' => 'cache_ttl',
                'min' => 1,
                'max' => 24
            ]
        );

        // Module Activation Section
        add_settings_section(
            'hellaz_modules',
            __('Active Modules', 'hellaz-website-analyzer'),
            null,
            'hellaz-settings'
        );

        $modules = [
            'metadata' => __('Metadata Analysis', 'hellaz-website-analyzer'),
            'social' => __('Social Media Detection', 'hellaz-website-analyzer'),
            'security' => __('Security Analysis', 'hellaz-website-analyzer'),
            'technology' => __('Technology Detection', 'hellaz-website-analyzer')
        ];

        foreach ($modules as $id => $label) {
            add_settings_field(
                "module_$id",
                $label,
                [self::class, 'render_checkbox'],
                'hellaz-settings',
                'hellaz_modules',
                [
                    'name' => "active_modules[$id]",
                    'label' => __('Enable', 'hellaz-website-analyzer')
                ]
            );
        }
    }

    public static function render_settings_page() {
        include HELLAZ_PLUGIN_PATH . 'templates/admin/settings-page.php';
    }

    public static function sanitize_settings($input) {
        // Add validation logic here
        return $input;
    }
}
