<?php
/**
 * Shortcodes Class
 */
if (!defined('ABSPATH')) {
    exit;
}

class SKS_Shortcodes {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', [$this, 'register_shortcodes']);
    }
    
    /**
     * Register all shortcodes
     */
    public function register_shortcodes() {
        // Register shortcode for each tool
        add_shortcode('sks_tool', [$this, 'render_tool_shortcode']);
    }
    
    /**
     * Render tool shortcode
     */
    public function render_tool_shortcode($atts) {
        $atts = shortcode_atts([
            'id' => '',
            'slug' => ''
        ], $atts);
        
        if (empty($atts['id']) && empty($atts['slug'])) {
            return '<p class="sks-error">Please specify a tool ID or slug.</p>';
        }
        
        $tool_id = $atts['id'];
        
        // If slug provided, find tool by slug
        if (empty($tool_id) && !empty($atts['slug'])) {
            global $wpdb;
            $table = $wpdb->prefix . 'sks_tools';
            $tool_id = $wpdb->get_var($wpdb->prepare(
                "SELECT tool_id FROM {$table} WHERE slug = %s",
                $atts['slug']
            ));
        }
        
        if (!$tool_id) {
            return '<p class="sks-error">Tool not found.</p>';
        }
        
        return SKS_Tool_Loader::render_tool($tool_id);
    }
}

