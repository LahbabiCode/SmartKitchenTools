<?php
/**
 * Tool Loader Class
 * Handles loading and execution of tools
 */
if (!defined('ABSPATH')) {
    exit;
}

class SKS_Tool_Loader {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('template_redirect', [$this, 'maybe_load_tool']);
    }
    
    /**
     * Load tool if on tool page
     */
    public function maybe_load_tool() {
        global $post;
        
        if (!$post) {
            return;
        }
        
        // Check if this post is associated with a tool
        $tool_id = get_post_meta($post->ID, '_sks_tool_id', true);
        
        if (!$tool_id) {
            return;
        }
        
        // Load the tool
        $this->load_tool($tool_id);
    }
    
    /**
     * Load a tool
     */
    public function load_tool($tool_id) {
        $tool = SKS_Tool_Registry::get($tool_id);
        
        if (!$tool) {
            return false;
        }
        
        // Track usage
        SKS_Database::track_usage($tool_id);
        
        // Load tool-specific assets
        $this->enqueue_tool_assets($tool_id);
        
        return true;
    }
    
    /**
     * Enqueue tool-specific assets
     */
    private function enqueue_tool_assets($tool_id) {
        $css_file = SKS_PLUGIN_DIR . 'assets/css/tools/' . sanitize_file_name($tool_id) . '.css';
        $js_file = SKS_PLUGIN_DIR . 'assets/js/tools/' . sanitize_file_name($tool_id) . '.js';
        
        // Enqueue CSS if exists
        if (file_exists($css_file)) {
            wp_enqueue_style(
                'sks-tool-' . $tool_id,
                SKS_PLUGIN_URL . 'assets/css/tools/' . sanitize_file_name($tool_id) . '.css',
                [],
                SKS_VERSION
            );
        }
        
        // Enqueue JS if exists
        if (file_exists($js_file)) {
            wp_enqueue_script(
                'sks-tool-' . $tool_id,
                SKS_PLUGIN_URL . 'assets/js/tools/' . sanitize_file_name($tool_id) . '.js',
                ['jquery'],
                SKS_VERSION,
                true
            );
        }
    }
    
    /**
     * Render tool output
     */
    public static function render_tool($tool_id, $args = []) {
        $tool = SKS_Tool_Registry::get($tool_id);
        
        if (!$tool) {
            return '<p class="sks-error">Tool not found.</p>';
        }
        
        // Check if tool is active
        $db_tool = SKS_Database::get_tool($tool_id);
        if ($db_tool && !$db_tool['is_active']) {
            return '<p class="sks-error">This tool is currently disabled.</p>';
        }
        
        // Load class if available
        if (!empty($tool['class_name']) && class_exists($tool['class_name'])) {
            $tool_instance = new $tool['class_name']();
            
            if (method_exists($tool_instance, 'render')) {
                ob_start();
                $tool_instance->render($args);
                return ob_get_clean();
            }
        }
        
        // Load template if available
        if (!empty($tool['template'])) {
            $template = SKS_PLUGIN_DIR . 'templates/' . $tool['template'];
            
            if (file_exists($template)) {
                ob_start();
                include $template;
                return ob_get_clean();
            }
        }
        
        return '<p class="sks-error">Tool template not found.</p>';
    }
}

