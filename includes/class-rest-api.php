<?php
/**
 * REST API Class
 */
if (!defined('ABSPATH')) {
    exit;
}

class SKS_REST_API {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }
    
    /**
     * Register REST routes
     */
    public function register_routes() {
        $namespace = 'sks/v1';
        
        // Tools routes
        register_rest_route($namespace, '/tools', [
            [
                'methods' => 'GET',
                'callback' => [$this, 'get_tools'],
                'permission_callback' => '__return_true'
            ],
            [
                'methods' => 'POST',
                'callback' => [$this, 'create_tool'],
                'permission_callback' => [$this, 'check_admin_permissions']
            ]
        ]);
        
        register_rest_route($namespace, '/tools/(?P<tool_id>[a-zA-Z0-9-_]+)', [
            [
                'methods' => 'GET',
                'callback' => [$this, 'get_tool'],
                'permission_callback' => '__return_true'
            ],
            [
                'methods' => 'PUT',
                'callback' => [$this, 'update_tool'],
                'permission_callback' => [$this, 'check_admin_permissions']
            ],
            [
                'methods' => 'DELETE',
                'callback' => [$this, 'delete_tool'],
                'permission_callback' => [$this, 'check_admin_permissions']
            ]
        ]);
        
        // AI routes
        register_rest_route($namespace, '/ai/generate-tool', [
            'methods' => 'POST',
            'callback' => [$this, 'generate_tool'],
            'permission_callback' => [$this, 'check_admin_permissions']
        ]);
        
        register_rest_route($namespace, '/ai/generate-content', [
            'methods' => 'POST',
            'callback' => [$this, 'generate_content'],
            'permission_callback' => [$this, 'check_admin_permissions']
        ]);
        
        // Analytics routes
        register_rest_route($namespace, '/analytics', [
            'methods' => 'GET',
            'callback' => [$this, 'get_analytics'],
            'permission_callback' => [$this, 'check_admin_permissions']
        ]);
        
        // Settings routes
        register_rest_route($namespace, '/settings', [
            'methods' => 'GET',
            'callback' => [$this, 'get_settings'],
            'permission_callback' => [$this, 'check_admin_permissions']
        ]);
        
        register_rest_route($namespace, '/settings', [
            'methods' => 'POST',
            'callback' => [$this, 'update_settings'],
            'permission_callback' => [$this, 'check_admin_permissions']
        ]);
    }
    
    /**
     * Check admin permissions
     */
    public function check_admin_permissions() {
        return current_user_can('manage_options');
    }
    
    /**
     * Get all tools
     */
    public function get_tools($request) {
        $is_active = $request->get_param('is_active');
        $category = $request->get_param('category');
        $search = $request->get_param('search');
        
        $args = [];
        
        if ($is_active !== null) {
            $args['is_active'] = (bool) $is_active;
        }
        
        if ($category) {
            $args['category'] = sanitize_text_field($category);
        }
        
        if ($search) {
            $args['search'] = sanitize_text_field($search);
        }
        
        $tools = SKS_Database::get_tools($args);
        
        return new WP_REST_Response($tools, 200);
    }
    
    /**
     * Get single tool
     */
    public function get_tool($request) {
        $tool_id = $request->get_param('tool_id');
        $tool = SKS_Database::get_tool($tool_id);
        
        if (!$tool) {
            return new WP_Error('tool_not_found', 'Tool not found', ['status' => 404]);
        }
        
        return new WP_REST_Response($tool, 200);
    }
    
    /**
     * Create tool
     */
    public function create_tool($request) {
        $data = $request->get_json_params();
        $data = SKS_Utilities::sanitize_tool_data($data);
        
        if (empty($data['name'])) {
            return new WP_Error('invalid_data', 'Tool name is required', ['status' => 400]);
        }
        
        // Generate tool ID if not provided
        if (empty($data['tool_id'])) {
            $data['tool_id'] = SKS_Utilities::generate_tool_id($data['name']);
        }
        
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = sanitize_title($data['name']);
        }
        
        // Generate shortcode
        if (empty($data['shortcode'])) {
            $data['shortcode'] = 'sks_' . $data['tool_id'];
        }
        
        $result = SKS_Database::save_tool($data);
        
        if ($result) {
            return new WP_REST_Response($data, 201);
        }
        
        return new WP_Error('creation_failed', 'Failed to create tool', ['status' => 500]);
    }
    
    /**
     * Update tool
     */
    public function update_tool($request) {
        $tool_id = $request->get_param('tool_id');
        $data = $request->get_json_params();
        $data = SKS_Utilities::sanitize_tool_data($data);
        
        $existing = SKS_Database::get_tool($tool_id);
        
        if (!$existing) {
            return new WP_Error('tool_not_found', 'Tool not found', ['status' => 404]);
        }
        
        // Merge with existing data
        $data['tool_id'] = $tool_id;
        $data = array_merge($existing, $data);
        
        $result = SKS_Database::save_tool($data);
        
        if ($result !== false) {
            return new WP_REST_Response($data, 200);
        }
        
        return new WP_Error('update_failed', 'Failed to update tool', ['status' => 500]);
    }
    
    /**
     * Delete tool
     */
    public function delete_tool($request) {
        $tool_id = $request->get_param('tool_id');
        
        $tool = SKS_Database::get_tool($tool_id);
        
        if (!$tool) {
            return new WP_Error('tool_not_found', 'Tool not found', ['status' => 404]);
        }
        
        // Delete associated page
        if ($tool['page_id']) {
            SKS_Utilities::delete_tool_page($tool['page_id']);
        }
        
        $result = SKS_Database::delete_tool($tool_id);
        
        if ($result) {
            return new WP_REST_Response(['success' => true], 200);
        }
        
        return new WP_Error('delete_failed', 'Failed to delete tool', ['status' => 500]);
    }
    
    /**
     * Generate tool using AI
     */
    public function generate_tool($request) {
        $data = $request->get_json_params();
        
        if (empty($data['prompt'])) {
            return new WP_Error('invalid_data', 'Prompt is required', ['status' => 400]);
        }
        
        require_once SKS_PLUGIN_DIR . 'ai/class-tool-generator.php';
        
        $generator = new SKS_Tool_Generator();
        $tool_data = $generator->generate_from_prompt($data['prompt']);
        
        if (!$tool_data) {
            return new WP_Error('generation_failed', 'Failed to generate tool', ['status' => 500]);
        }
        
        // Save to database
        $tool_id = SKS_Database::save_tool($tool_data);
        
        if ($tool_id) {
            // Create page if requested
            if (!empty($data['create_page'])) {
                $page_id = SKS_Utilities::create_tool_page($tool_data['tool_id'], $tool_data);
                SKS_Database::save_tool(['tool_id' => $tool_data['tool_id'], 'page_id' => $page_id]);
            }
        }
        
        return new WP_REST_Response($tool_data, 200);
    }
    
    /**
     * Generate content using AI
     */
    public function generate_content($request) {
        $data = $request->get_json_params();
        
        if (empty($data['prompt'])) {
            return new WP_Error('invalid_data', 'Prompt is required', ['status' => 400]);
        }
        
        require_once SKS_PLUGIN_DIR . 'ai/class-gemini-integration.php';
        
        $gemini = new SKS_Gemini_Integration();
        $content = $gemini->generate_content($data['prompt']);
        
        if (!$content) {
            return new WP_Error('generation_failed', 'Failed to generate content', ['status' => 500]);
        }
        
        return new WP_REST_Response(['content' => $content], 200);
    }
    
    /**
     * Get analytics
     */
    public function get_analytics($request) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'sks_analytics';
        
        $stats = [
            'total_uses' => $wpdb->get_var("SELECT COUNT(*) FROM {$table}"),
            'unique_users' => $wpdb->get_var("SELECT COUNT(DISTINCT user_id) FROM {$table}"),
            'today_uses' => $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$table} WHERE DATE(created_at) = %s",
                current_time('Y-m-d')
            ))
        ];
        
        return new WP_REST_Response($stats, 200);
    }
    
    /**
     * Get settings
     */
    public function get_settings($request) {
        $settings = [
            'ai_api_key' => get_option('sks_ai_api_key', ''),
            'analytics_enabled' => get_option('sks_analytics_enabled', true),
            'dark_mode' => get_option('sks_dark_mode', false)
        ];
        
        return new WP_REST_Response($settings, 200);
    }
    
    /**
     * Update settings
     */
    public function update_settings($request) {
        $data = $request->get_json_params();
        
        if (isset($data['ai_api_key'])) {
            update_option('sks_ai_api_key', sanitize_text_field($data['ai_api_key']));
        }
        
        if (isset($data['analytics_enabled'])) {
            update_option('sks_analytics_enabled', (bool) $data['analytics_enabled']);
        }
        
        if (isset($data['dark_mode'])) {
            update_option('sks_dark_mode', (bool) $data['dark_mode']);
        }
        
        return new WP_REST_Response(['success' => true], 200);
    }
}

