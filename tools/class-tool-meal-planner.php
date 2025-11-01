<?php
/**
 * Weekly Meal Planner Tool
 */
if (!defined('ABSPATH')) {
    exit;
}

class SKS_Tool_Meal_Planner {
    
    /**
     * Register the tool
     */
    public static function register() {
        SKS_Tool_Registry::register('meal-planner', [
            'name' => __('Weekly Meal Planner', 'smartkitchen-suite'),
            'slug' => 'meal-planner',
            'category' => 'nutrition',
            'description' => __('Plan your weekly meals with AI-generated meal plans tailored to your dietary preferences and nutritional goals.', 'smartkitchen-suite'),
            'icon' => '📅',
            'class_name' => __CLASS__,
            'shortcode' => 'sks_meal_planner',
            'settings' => []
        ]);
    }
    
    /**
     * Render the tool
     */
    public function render($args = []) {
        ?>
        <div class="sks-tool-container sks-meal-planner">
            <div class="sks-tool-header">
                <h2><?php _e('Weekly Meal Planner', 'smartkitchen-suite'); ?></h2>
                <p class="sks-tool-subtitle"><?php _e('Plan your week with AI-generated meal suggestions', 'smartkitchen-suite'); ?></p>
            </div>
            
            <div class="sks-planner-form">
                <div class="sks-form-row">
                    <div class="sks-form-group">
                        <label for="sks-planner-days"><?php _e('Days', 'smartkitchen-suite'); ?></label>
                        <select id="sks-planner-days" class="sks-select">
                            <option value="7"><?php _e('7 Days', 'smartkitchen-suite'); ?></option>
                            <option value="14"><?php _e('14 Days', 'smartkitchen-suite'); ?></option>
                        </select>
                    </div>
                    
                    <div class="sks-form-group">
                        <label for="sks-planner-calories"><?php _e('Daily Calories', 'smartkitchen-suite'); ?></label>
                        <input type="number" id="sks-planner-calories" class="sks-input" value="2000" min="1200" max="4000" step="100">
                    </div>
                </div>
                
                <div class="sks-form-group">
                    <label><?php _e('Dietary Preferences', 'smartkitchen-suite'); ?></label>
                    <div class="sks-checkbox-group">
                        <label><input type="checkbox" id="sks-planner-vegetarian"> <?php _e('Vegetarian', 'smartkitchen-suite'); ?></label>
                        <label><input type="checkbox" id="sks-planner-vegan"> <?php _e('Vegan', 'smartkitchen-suite'); ?></label>
                        <label><input type="checkbox" id="sks-planner-glutenfree"> <?php _e('Gluten-Free', 'smartkitchen-suite'); ?></label>
                        <label><input type="checkbox" id="sks-planner-lowcarb"> <?php _e('Low-Carb', 'smartkitchen-suite'); ?></label>
                        <label><input type="checkbox" id="sks-planner-keto"> <?php _e('Keto', 'smartkitchen-suite'); ?></label>
                        <label><input type="checkbox" id="sks-planner-paleo"> <?php _e('Paleo', 'smartkitchen-suite'); ?></label>
                    </div>
                </div>
                
                <button type="button" class="sks-btn sks-btn-primary" onclick="sksGenerateMealPlan()">
                    <?php _e('Generate Meal Plan', 'smartkitchen-suite'); ?>
                </button>
            </div>
            
            <div id="sks-plan-result" class="sks-plan-result" style="display: none;"></div>
            
            <style>
                .sks-meal-planner { max-width: 1000px; margin: 0 auto; }
                .sks-planner-form { background: #f9f9f9; padding: 30px; border-radius: 8px; margin: 20px 0; }
                .sks-checkbox-group { display: flex; flex-wrap: wrap; gap: 15px; }
                .sks-checkbox-group label { display: flex; align-items: center; }
                .sks-plan-result { margin-top: 30px; }
                .sks-day-plan { background: white; border: 1px solid #ddd; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
                .sks-day-plan h3 { color: #2196f3; border-bottom: 2px solid #2196f3; padding-bottom: 10px; margin-bottom: 15px; }
                .sks-meal-item { padding: 15px; margin-bottom: 15px; background: #f5f5f5; border-radius: 6px; }
                .sks-meal-item strong { display: block; margin-bottom: 8px; color: #333; }
            </style>
            
            <script>
            async function sksGenerateMealPlan() {
                const days = document.getElementById('sks-planner-days').value;
                const calories = document.getElementById('sks-planner-calories').value;
                const prefs = [];
                
                if (document.getElementById('sks-planner-vegetarian').checked) prefs.push('vegetarian');
                if (document.getElementById('sks-planner-vegan').checked) prefs.push('vegan');
                if (document.getElementById('sks-planner-glutenfree').checked) prefs.push('gluten-free');
                if (document.getElementById('sks-planner-lowcarb').checked) prefs.push('low-carb');
                if (document.getElementById('sks-planner-keto').checked) prefs.push('keto');
                if (document.getElementById('sks-planner-paleo').checked) prefs.push('paleo');
                
                document.getElementById('sks-plan-result').innerHTML = '<div class="sks-loading"><div class="sks-spinner"></div><p>Generating your meal plan...</p></div>';
                document.getElementById('sks-plan-result').style.display = 'block';
                
                try {
                    const response = await fetch('<?php echo rest_url('sks/v1/ai/generate-content'); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
                        },
                        body: JSON.stringify({
                            prompt: `Create a ${days}-day meal plan${calories ? ' with ' + calories + ' daily calories' : ''}. ${prefs.length > 0 ? 'Preferences: ' + prefs.join(', ') + '.' : ''} For each day, provide: Breakfast, Lunch, Dinner, and 2 snacks. Include calorie counts. Format as HTML with clear sections.`
                        })
                    });
                    
                    const data = await response.json();
                    document.getElementById('sks-plan-result').innerHTML = data.content;
                } catch (error) {
                    document.getElementById('sks-plan-result').innerHTML = '<p style="color: red;">Error generating meal plan. Please try again.</p>';
                }
            }
            </script>
        </div>
        <?php
    }
}

SKS_Tool_Meal_Planner::register();

