<?php
/**
 * Utilities Class
 */
if (!defined('ABSPATH')) {
    exit;
}

class SKS_Utilities {
    
    /**
     * Get tool categories
     */
    public static function get_categories() {
        return [
            'cooking' => __('Cooking Tools', 'smartkitchen-suite'),
            'nutrition' => __('Nutrition Tools', 'smartkitchen-suite'),
            'health' => __('Health & Wellness', 'smartkitchen-suite'),
            'community' => __('Community Tools', 'smartkitchen-suite'),
            'shopping' => __('Shopping & Planning', 'smartkitchen-suite'),
            'general' => __('General', 'smartkitchen-suite')
        ];
    }
    
    /**
     * Get category icon
     */
    public static function get_category_icon($category) {
        $icons = [
            'cooking' => '🍳',
            'nutrition' => '🥗',
            'health' => '💊',
            'community' => '👥',
            'shopping' => '🛒',
            'general' => '🔧'
        ];
        
        return $icons[$category] ?? '🔧';
    }
    
    /**
     * Sanitize tool data
     */
    public static function sanitize_tool_data($data) {
        $sanitized = [];
        
        if (isset($data['name'])) {
            $sanitized['name'] = sanitize_text_field($data['name']);
        }
        
        if (isset($data['description'])) {
            $sanitized['description'] = sanitize_textarea_field($data['description']);
        }
        
        if (isset($data['slug'])) {
            $sanitized['slug'] = sanitize_title($data['slug']);
        }
        
        if (isset($data['category'])) {
            $sanitized['category'] = sanitize_text_field($data['category']);
        }
        
        if (isset($data['icon'])) {
            $sanitized['icon'] = sanitize_text_field($data['icon']);
        }
        
        if (isset($data['is_active'])) {
            $sanitized['is_active'] = (bool) $data['is_active'];
        }
        
        return $sanitized;
    }
    
    /**
     * Generate unique tool ID
     */
    public static function generate_tool_id($name) {
        $slug = sanitize_title($name);
        $tool_id = $slug;
        $counter = 1;
        
        global $wpdb;
        $table = $wpdb->prefix . 'sks_tools';
        
        while ($wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE tool_id = %s",
            $tool_id
        )) > 0) {
            $tool_id = $slug . '-' . $counter;
            $counter++;
        }
        
        return $tool_id;
    }
    
    /**
     * Create tool page
     */
    public static function create_tool_page($tool_id, $tool_data) {
        $page_data = [
            'post_title' => $tool_data['name'],
            'post_content' => '[sks_tool id="' . $tool_id . '"]',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_author' => get_current_user_id()
        ];
        
        $page_id = wp_insert_post($page_data);
        
        if ($page_id) {
            update_post_meta($page_id, '_sks_tool_id', $tool_id);
            update_post_meta($page_id, '_sks_tool_category', $tool_data['category']);
        }
        
        return $page_id;
    }
    
    /**
     * Delete tool page
     */
    public static function delete_tool_page($page_id) {
        if ($page_id) {
            wp_delete_post($page_id, true);
        }
    }
    
    /**
     * Format number with commas
     */
    public static function format_number($number, $decimals = 0) {
        return number_format($number, $decimals);
    }
    
    /**
     * Truncate text
     */
    public static function truncate($text, $length = 100, $suffix = '...') {
        if (strlen($text) <= $length) {
            return $text;
        }
        
        return substr($text, 0, $length) . $suffix;
    }
    
    /**
     * Get user's IP address
     */
    public static function get_ip_address() {
        $ip_keys = [
            'HTTP_CF_CONNECTING_IP', // Cloudflare
            'HTTP_X_REAL_IP',
            'HTTP_X_FORWARDED_FOR',
            'REMOTE_ADDR'
        ];
        
        foreach ($ip_keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                
                // Handle comma-separated IPs (proxy)
                if (strpos($ip, ',') !== false) {
                    $ip = explode(',', $ip)[0];
                }
                
                return trim($ip);
            }
        }
        
        return '';
    }
}

