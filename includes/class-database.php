<?php
/**
 * Database Management Class
 */
if (!defined('ABSPATH')) {
    exit;
}

class SKS_Database {
    
    /**
     * Create database tables
     */
    public static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        $table_prefix = $wpdb->prefix;
        
        // Tools registry table
        $tools_table = $table_prefix . 'sks_tools';
        $tools_sql = "CREATE TABLE IF NOT EXISTS {$tools_table} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            tool_id varchar(255) NOT NULL,
            name varchar(255) NOT NULL,
            slug varchar(255) NOT NULL,
            category varchar(100) NOT NULL,
            description text,
            icon varchar(255) DEFAULT 'cutlery',
            is_active tinyint(1) DEFAULT 1,
            is_builtin tinyint(1) DEFAULT 0,
            has_menu tinyint(1) DEFAULT 0,
            menu_position int(11) DEFAULT 0,
            page_id bigint(20) DEFAULT NULL,
            shortcode varchar(50) NOT NULL,
            settings longtext,
            ai_generated tinyint(1) DEFAULT 0,
            usage_count bigint(20) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY tool_id (tool_id),
            KEY slug (slug),
            KEY category (category),
            KEY is_active (is_active)
        ) {$charset_collate};";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($tools_sql);
        
        // Analytics table
        $analytics_table = $table_prefix . 'sks_analytics';
        $analytics_sql = "CREATE TABLE IF NOT EXISTS {$analytics_table} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            tool_id varchar(255) NOT NULL,
            user_id bigint(20) DEFAULT NULL,
            ip_address varchar(45) NOT NULL,
            user_agent text,
            session_id varchar(255),
            event_type varchar(50) NOT NULL,
            event_data longtext,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY tool_id (tool_id),
            KEY user_id (user_id),
            KEY created_at (created_at)
        ) {$charset_collate};";
        
        dbDelta($analytics_sql);
        
        // AI generated content cache
        $ai_cache_table = $table_prefix . 'sks_ai_cache';
        $ai_cache_sql = "CREATE TABLE IF NOT EXISTS {$ai_cache_table} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            cache_key varchar(255) NOT NULL,
            content longtext NOT NULL,
            model varchar(100) DEFAULT 'gemini-flash',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            expires_at datetime DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY cache_key (cache_key),
            KEY expires_at (expires_at)
        ) {$charset_collate};";
        
        dbDelta($ai_cache_sql);
    }
    
    /**
     * Get tools from database
     */
    public static function get_tools($args = []) {
        global $wpdb;
        $table = $wpdb->prefix . 'sks_tools';
        
        $defaults = [
            'is_active' => null,
            'category' => null,
            'search' => null,
            'limit' => 100,
            'offset' => 0,
            'orderby' => 'menu_position',
            'order' => 'ASC'
        ];
        
        $args = wp_parse_args($args, $defaults);
        
        $where = ['1=1'];
        $values = [];
        
        if ($args['is_active'] !== null) {
            $where[] = 'is_active = %d';
            $values[] = $args['is_active'];
        }
        
        if ($args['category']) {
            $where[] = 'category = %s';
            $values[] = $args['category'];
        }
        
        if ($args['search']) {
            $where[] = '(name LIKE %s OR description LIKE %s OR tool_id LIKE %s)';
            $search = '%' . $wpdb->esc_like($args['search']) . '%';
            $values[] = $search;
            $values[] = $search;
            $values[] = $search;
        }
        
        $where_clause = implode(' AND ', $where);
        
        $valid_orderby = ['name', 'category', 'menu_position', 'usage_count', 'created_at'];
        $orderby = in_array($args['orderby'], $valid_orderby) ? $args['orderby'] : 'menu_position';
        
        $order = strtoupper($args['order']) === 'DESC' ? 'DESC' : 'ASC';
        
        $query = "SELECT * FROM {$table} WHERE {$where_clause} ORDER BY {$orderby} {$order} LIMIT %d OFFSET %d";
        $values[] = $args['limit'];
        $values[] = $args['offset'];
        
        if (!empty($values)) {
            $query = $wpdb->prepare($query, $values);
        }
        
        return $wpdb->get_results($query, ARRAY_A);
    }
    
    /**
     * Get single tool by ID
     */
    public static function get_tool($tool_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'sks_tools';
        
        return $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE tool_id = %s", $tool_id),
            ARRAY_A
        );
    }
    
    /**
     * Insert or update tool
     */
    public static function save_tool($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'sks_tools';
        
        $defaults = [
            'tool_id' => '',
            'name' => '',
            'slug' => '',
            'category' => 'general',
            'description' => '',
            'icon' => 'cutlery',
            'is_active' => 1,
            'is_builtin' => 0,
            'has_menu' => 0,
            'menu_position' => 0,
            'page_id' => null,
            'shortcode' => '',
            'settings' => '{}',
            'ai_generated' => 0
        ];
        
        $data = wp_parse_args($data, $defaults);
        
        // Get existing tool if it exists
        $existing = self::get_tool($data['tool_id']);
        
        if ($existing) {
            // Update
            unset($data['tool_id']); // Don't update ID
            $result = $wpdb->update($table, $data, ['tool_id' => $existing['tool_id']]);
            return $result !== false ? $existing['tool_id'] : false;
        } else {
            // Insert
            $result = $wpdb->insert($table, $data);
            return $result ? $wpdb->insert_id : false;
        }
    }
    
    /**
     * Delete tool
     */
    public static function delete_tool($tool_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'sks_tools';
        
        return $wpdb->delete($table, ['tool_id' => $tool_id]);
    }
    
    /**
     * Track tool usage
     */
    public static function track_usage($tool_id, $data = []) {
        global $wpdb;
        
        // Update usage count
        $tools_table = $wpdb->prefix . 'sks_tools';
        $wpdb->query($wpdb->prepare(
            "UPDATE {$tools_table} SET usage_count = usage_count + 1 WHERE tool_id = %s",
            $tool_id
        ));
        
        // Record analytics if enabled
        if (get_option('sks_analytics_enabled', true)) {
            $analytics_table = $wpdb->prefix . 'sks_analytics';
            $wpdb->insert($analytics_table, [
                'tool_id' => $tool_id,
                'user_id' => get_current_user_id(),
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'session_id' => session_id(),
                'event_type' => 'usage',
                'event_data' => json_encode($data)
            ]);
        }
    }
}

