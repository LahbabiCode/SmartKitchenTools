<?php
/**
 * Tool Registry Class
 * Manages all registered tools
 */
if (!defined('ABSPATH')) {
    exit;
}

class SKS_Tool_Registry {
    
    /**
     * Registered tools
     */
    private static $tools = [];
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', [$this, 'register_builtin_tools']);
        add_action('wp_loaded', [$this, 'load_tools_from_db']);
    }
    
    /**
     * Register a tool
     */
    public static function register($tool_id, $args) {
        $defaults = [
            'name' => '',
            'slug' => '',
            'category' => 'general',
            'description' => '',
            'icon' => 'cutlery',
            'class_name' => '',
            'template' => '',
            'shortcode' => '',
            'settings' => []
        ];
        
        $args = wp_parse_args($args, $defaults);
        
        if (empty($tool_id) || empty($args['name'])) {
            return false;
        }
        
        // Generate slug if not provided
        if (empty($args['slug'])) {
            $args['slug'] = sanitize_title($args['name']);
        }
        
        self::$tools[$tool_id] = $args;
        
        return true;
    }
    
    /**
     * Get registered tool
     */
    public static function get($tool_id) {
        return isset(self::$tools[$tool_id]) ? self::$tools[$tool_id] : null;
    }
    
    /**
     * Get all registered tools
     */
    public static function get_all() {
        return self::$tools;
    }
    
    /**
     * Get tools by category
     */
    public static function get_by_category($category) {
        return array_filter(self::$tools, function($tool) use ($category) {
            return $tool['category'] === $category;
        });
    }
    
    /**
     * Get active tools
     */
    public static function get_active() {
        global $wpdb;
        $table = $wpdb->prefix . 'sks_tools';
        $active_tool_ids = $wpdb->get_col(
            "SELECT tool_id FROM {$table} WHERE is_active = 1"
        );
        
        return array_intersect_key(self::$tools, array_flip($active_tool_ids));
    }
    
    /**
     * Check if tool exists
     */
    public static function exists($tool_id) {
        return isset(self::$tools[$tool_id]);
    }
    
    /**
     * Register built-in tools
     */
    public function register_builtin_tools() {
        // Tools will be registered by their classes
    }
    
    /**
     * Load tools from database
     */
    public function load_tools_from_db() {
        $db_tools = SKS_Database::get_tools(['is_active' => null]);
        
        foreach ($db_tools as $tool) {
            // Merge database data with registered tools
            if (!isset(self::$tools[$tool['tool_id']])) {
                self::$tools[$tool['tool_id']] = [
                    'name' => $tool['name'],
                    'slug' => $tool['slug'],
                    'category' => $tool['category'],
                    'description' => $tool['description'],
                    'icon' => $tool['icon'],
                    'shortcode' => $tool['shortcode']
                ];
            }
        }
    }
}

