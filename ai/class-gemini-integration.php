<?php
/**
 * Google Gemini Flash Integration Class
 */
if (!defined('ABSPATH')) {
    exit;
}

class SKS_Gemini_Integration {
    
    /**
     * API endpoint
     */
    private $api_url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent';
    
    /**
     * API key
     */
    private $api_key;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->api_key = get_option('sks_ai_api_key', '');
    }
    
    /**
     * Check if API key is configured
     */
    public function is_configured() {
        return !empty($this->api_key);
    }
    
    /**
     * Generate content using Gemini
     */
    public function generate_content($prompt, $args = []) {
        if (!$this->is_configured()) {
            return false;
        }
        
        // Check cache first
        $cache_key = 'gemini_' . md5($prompt . serialize($args));
        $cached = $this->get_cached_content($cache_key);
        
        if ($cached !== false) {
            return $cached;
        }
        
        $defaults = [
            'temperature' => 0.7,
            'max_tokens' => 2000
        ];
        
        $args = wp_parse_args($args, $defaults);
        
        $body = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => $args['temperature'],
                'maxOutputTokens' => $args['max_tokens']
            ]
        ];
        
        $response = wp_remote_post($this->api_url . '?key=' . $this->api_key, [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($body),
            'timeout' => 30
        ]);
        
        if (is_wp_error($response)) {
            return false;
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if ($status_code !== 200 || empty($data['candidates'][0]['content']['parts'][0]['text'])) {
            return false;
        }
        
        $content = $data['candidates'][0]['content']['parts'][0]['text'];
        
        // Cache the result
        $this->cache_content($cache_key, $content);
        
        return $content;
    }
    
    /**
     * Generate multiple options
     */
    public function generate_options($prompt, $count = 3) {
        $options = [];
        
        for ($i = 0; $i < $count; $i++) {
            $content = $this->generate_content($prompt . ' (Option ' . ($i + 1) . ')');
            if ($content) {
                $options[] = $content;
            }
        }
        
        return $options;
    }
    
    /**
     * Generate tool description
     */
    public function generate_tool_description($tool_name, $category) {
        $prompt = sprintf(
            "Create a compelling, SEO-friendly description for a %s tool called '%s'. Keep it under 200 words, professional, and focused on benefits.",
            $category,
            $tool_name
        );
        
        return $this->generate_content($prompt);
    }
    
    /**
     * Generate recipe
     */
    public function generate_recipe($ingredients, $cuisine = '') {
        $prompt = sprintf(
            "Generate a detailed recipe using these ingredients: %s. %s Include preparation time, cooking time, servings, step-by-step instructions, and nutritional information.",
            implode(', ', $ingredients),
            $cuisine ? "Cuisine style: $cuisine." : ""
        );
        
        return $this->generate_content($prompt);
    }
    
    /**
     * Generate meal plan
     */
    public function generate_meal_plan($days = 7, $preferences = '') {
        $prompt = sprintf(
            "Create a %d-day meal plan%s. For each day, provide breakfast, lunch, dinner, and 2 snacks with calorie counts and nutritional breakdown.",
            $days,
            $preferences ? " for someone with these preferences: $preferences" : ""
        );
        
        return $this->generate_content($prompt);
    }
    
    /**
     * Get cached content
     */
    private function get_cached_content($cache_key) {
        global $wpdb;
        $table = $wpdb->prefix . 'sks_ai_cache';
        
        $cached = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE cache_key = %s AND (expires_at IS NULL OR expires_at > NOW())",
            $cache_key
        ), ARRAY_A);
        
        return $cached ? maybe_unserialize($cached['content']) : false;
    }
    
    /**
     * Cache content
     */
    private function cache_content($cache_key, $content, $expiry_hours = 24) {
        global $wpdb;
        $table = $wpdb->prefix . 'sks_ai_cache';
        
        $expires_at = null;
        if ($expiry_hours > 0) {
            $expires_at = date('Y-m-d H:i:s', strtotime("+{$expiry_hours} hours"));
        }
        
        $wpdb->replace($table, [
            'cache_key' => $cache_key,
            'content' => maybe_serialize($content),
            'model' => 'gemini-flash',
            'expires_at' => $expires_at
        ]);
    }
    
    /**
     * Clear cache
     */
    public function clear_cache() {
        global $wpdb;
        $table = $wpdb->prefix . 'sks_ai_cache';
        
        $wpdb->query("DELETE FROM {$table} WHERE expires_at < NOW()");
    }
}

