<?php
/**
 * Tool Generator Class
 * Uses AI to generate new tools dynamically
 */
if (!defined('ABSPATH')) {
    exit;
}

require_once SKS_PLUGIN_DIR . 'ai/class-gemini-integration.php';

class SKS_Tool_Generator {
    
    /**
     * Gemini integration
     */
    private $gemini;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->gemini = new SKS_Gemini_Integration();
    }
    
    /**
     * Generate tool from prompt
     */
    public function generate_from_prompt($prompt) {
        if (!$this->gemini->is_configured()) {
            return false;
        }
        
        // Generate tool details using AI
        $system_prompt = $this->get_system_prompt();
        $full_prompt = $system_prompt . "\n\nUser request: " . $prompt;
        
        $response = $this->gemini->generate_content($full_prompt, [
            'temperature' => 0.8
        ]);
        
        if (!$response) {
            return false;
        }
        
        // Parse AI response (expecting JSON)
        $tool_data = $this->parse_ai_response($response, $prompt);
        
        if (!$tool_data) {
            return false;
        }
        
        // Enhance with AI-generated description
        if (empty($tool_data['description'])) {
            $tool_data['description'] = $this->gemini->generate_tool_description(
                $tool_data['name'],
                $tool_data['category']
            );
        }
        
        // Generate shortcode
        if (empty($tool_data['shortcode'])) {
            $tool_id = $tool_data['tool_id'] ?? SKS_Utilities::generate_tool_id($tool_data['name']);
            $tool_data['shortcode'] = 'sks_' . $tool_id;
        }
        
        // Mark as AI generated
        $tool_data['ai_generated'] = 1;
        
        return $tool_data;
    }
    
    /**
     * Get system prompt for tool generation
     */
    private function get_system_prompt() {
        return <<<PROMPT
You are a tool generator for SmartKitchen Suite, a WordPress plugin with cooking and nutrition tools.

Generate a tool based on the user's request. Return ONLY valid JSON with this structure:
{
    "tool_id": "unique-id-in-lowercase-with-dashes",
    "name": "Tool Name",
    "slug": "tool-name-slug",
    "category": "cooking|nutrition|health|community|shopping|general",
    "icon": "em em-icon-name or font-awesome class",
    "description": "Brief description (2-3 sentences)",
    "template": "tool-template-name.php",
    "settings": {
        "has_form": true,
        "requires_js": true,
        "custom_fields": []
    }
}

Categories available:
- cooking: Recipe tools, cooking calculators, timers, converters
- nutrition: BMI, calories, macro calculators, meal planning
- health: Sleep, wellness, allergens, vitamin info
- community: Sharing, forums, ratings, favorites
- shopping: Grocery lists, meal planning, substitutions
- general: Other kitchen-related tools

Be creative but practical. Focus on real kitchen/cooking/nutrition needs.
PROMPT;
    }
    
    /**
     * Parse AI response
     */
    private function parse_ai_response($response, $prompt) {
        // Try to extract JSON from response
        $json_match = [];
        preg_match('/\{[\s\S]*\}/', $response, $json_match);
        
        if (!empty($json_match[0])) {
            $tool_data = json_decode($json_match[0], true);
            
            if (json_last_error() === JSON_ERROR_NONE && is_array($tool_data)) {
                return $this->validate_and_enhance_tool_data($tool_data, $prompt);
            }
        }
        
        // Fallback: generate basic tool from prompt if JSON parsing fails
        return $this->generate_fallback_tool($prompt);
    }
    
    /**
     * Validate and enhance tool data
     */
    private function validate_and_enhance_tool_data($data, $prompt) {
        $categories = array_keys(SKS_Utilities::get_categories());
        
        $validated = [
            'tool_id' => !empty($data['tool_id']) ? sanitize_title($data['tool_id']) : SKS_Utilities::generate_tool_id($data['name'] ?? $prompt),
            'name' => sanitize_text_field($data['name'] ?? 'New Tool'),
            'slug' => !empty($data['slug']) ? sanitize_title($data['slug']) : sanitize_title($data['name'] ?? $prompt),
            'category' => in_array($data['category'] ?? '', $categories) ? $data['category'] : 'general',
            'icon' => sanitize_text_field($data['icon'] ?? 'cutlery'),
            'description' => sanitize_textarea_field($data['description'] ?? ''),
            'settings' => wp_json_encode($data['settings'] ?? [])
        ];
        
        return $validated;
    }
    
    /**
     * Generate fallback tool if AI parsing fails
     */
    private function generate_fallback_tool($prompt) {
        $slug = sanitize_title($prompt);
        
        return [
            'tool_id' => SKS_Utilities::generate_tool_id($prompt),
            'name' => ucwords(str_replace('-', ' ', $slug)),
            'slug' => $slug,
            'category' => 'general',
            'icon' => 'cutlery',
            'description' => 'AI-generated tool based on: ' . $prompt,
            'settings' => wp_json_encode([])
        ];
    }
    
    /**
     * Suggest tool improvements
     */
    public function suggest_improvements($tool_data) {
        $prompt = sprintf(
            "Analyze this kitchen/cooking tool and suggest 3-5 improvements:\n\nTool: %s\nCategory: %s\nDescription: %s\n\nProvide specific, actionable suggestions.",
            $tool_data['name'],
            $tool_data['category'],
            $tool_data['description']
        );
        
        return $this->gemini->generate_content($prompt);
    }
    
    /**
     * Auto-generate tool content
     */
    public function generate_tool_content($tool_data, $type = 'description') {
        switch ($type) {
            case 'description':
                return $this->gemini->generate_tool_description($tool_data['name'], $tool_data['category']);
                
            case 'features':
                $prompt = sprintf("List 5-7 key features for a %s tool called '%s'", $tool_data['category'], $tool_data['name']);
                return $this->gemini->generate_content($prompt);
                
            case 'tips':
                $prompt = sprintf("Provide 5 helpful tips for using a %s tool", $tool_data['name']);
                return $this->gemini->generate_content($prompt);
                
            default:
                return '';
        }
    }
}

