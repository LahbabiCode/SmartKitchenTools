<?php
/**
 * Admin Class
 */
if (!defined('ABSPATH')) {
    exit;
}

class SKS_Admin {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('admin_init', [$this, 'register_settings']);
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('SmartKitchen Suite', 'smartkitchen-suite'),
            __('SmartKitchen', 'smartkitchen-suite'),
            'manage_options',
            'smartkitchen-suite',
            [$this, 'render_dashboard'],
            'dashicons-food',
            30
        );
        
        add_submenu_page(
            'smartkitchen-suite',
            __('All Tools', 'smartkitchen-suite'),
            __('All Tools', 'smartkitchen-suite'),
            'manage_options',
            'smartkitchen-suite',
            [$this, 'render_dashboard']
        );
        
        add_submenu_page(
            'smartkitchen-suite',
            __('AI Generator', 'smartkitchen-suite'),
            __('AI Generator', 'smartkitchen-suite'),
            'manage_options',
            'smartkitchen-ai-generator',
            [$this, 'render_ai_generator']
        );
        
        add_submenu_page(
            'smartkitchen-suite',
            __('Analytics', 'smartkitchen-suite'),
            __('Analytics', 'smartkitchen-suite'),
            'manage_options',
            'smartkitchen-analytics',
            [$this, 'render_analytics']
        );
        
        add_submenu_page(
            'smartkitchen-suite',
            __('Settings', 'smartkitchen-suite'),
            __('Settings', 'smartkitchen-suite'),
            'manage_options',
            'smartkitchen-settings',
            [$this, 'render_settings']
        );
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'smartkitchen') === false) {
            return;
        }
        
        // Enqueue React app
        $app_path = SKS_PLUGIN_DIR . 'admin/build/index.js';
        $app_url = SKS_PLUGIN_URL . 'admin/build/index.js';
        
        if (file_exists($app_path)) {
            wp_enqueue_script(
                'sks-admin-app',
                $app_url,
                [],
                SKS_VERSION,
                true
            );
            
            // Localize script
            wp_localize_script('sks-admin-app', 'sksData', [
                'apiUrl' => rest_url('sks/v1/'),
                'nonce' => wp_create_nonce('wp_rest'),
                'categories' => SKS_Utilities::get_categories(),
                'darkMode' => get_option('sks_dark_mode', false)
            ]);
        }
        
        // Enqueue admin styles
        wp_enqueue_style(
            'sks-admin-style',
            SKS_PLUGIN_URL . 'admin/assets/admin.css',
            [],
            SKS_VERSION
        );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('sks_settings', 'sks_ai_api_key');
        register_setting('sks_settings', 'sks_analytics_enabled');
        register_setting('sks_settings', 'sks_dark_mode');
    }
    
    /**
     * Render main dashboard
     */
    public function render_dashboard() {
        ?>
        <div class="wrap sks-admin-wrap">
            <h1><?php _e('SmartKitchen Suite', 'smartkitchen-suite'); ?></h1>
            <div id="sks-admin-root"></div>
        </div>
        <?php
    }
    
    /**
     * Render AI generator page
     */
    public function render_ai_generator() {
        ?>
        <div class="wrap sks-admin-wrap">
            <h1><?php _e('AI Tool Generator', 'smartkitchen-suite'); ?></h1>
            <div id="sks-ai-generator-root"></div>
        </div>
        <?php
    }
    
    /**
     * Render analytics page
     */
    public function render_analytics() {
        ?>
        <div class="wrap sks-admin-wrap">
            <h1><?php _e('Analytics', 'smartkitchen-suite'); ?></h1>
            <div id="sks-analytics-root"></div>
        </div>
        <?php
    }
    
    /**
     * Render settings page
     */
    public function render_settings() {
        ?>
        <div class="wrap sks-admin-wrap">
            <h1><?php _e('Settings', 'smartkitchen-suite'); ?></h1>
            <div id="sks-settings-root"></div>
        </div>
        <?php
    }
}

