<?php
/**
 * Recipe Generator Tool
 */
if (!defined('ABSPATH')) {
    exit;
}

class SKS_Tool_Recipe_Generator {
    
    /**
     * Register the tool
     */
    public static function register() {
        SKS_Tool_Registry::register('recipe-generator', [
            'name' => __('AI Recipe Generator', 'smartkitchen-suite'),
            'slug' => 'recipe-generator',
            'category' => 'cooking',
            'description' => __('Generate personalized recipes using AI based on your ingredients, dietary preferences, and cooking skill level.', 'smartkitchen-suite'),
            'icon' => '🍳',
            'class_name' => __CLASS__,
            'shortcode' => 'sks_recipe_generator',
            'settings' => []
        ]);
    }
    
    /**
     * Render the tool
     */
    public function render($args = []) {
        ?>
        <div class="sks-tool-container sks-recipe-generator">
            <div class="sks-tool-header">
                <h2><?php _e('AI Recipe Generator', 'smartkitchen-suite'); ?></h2>
                <p class="sks-tool-subtitle"><?php _e('Create personalized recipes with artificial intelligence', 'smartkitchen-suite'); ?></p>
            </div>
            
            <div class="sks-recipe-form">
                <div class="sks-form-group">
                    <label for="sks-recipe-ingredients"><?php _e('Ingredients (comma-separated)', 'smartkitchen-suite'); ?></label>
                    <textarea id="sks-recipe-ingredients" class="sks-textarea" rows="4" placeholder="<?php _e('e.g., chicken breast, tomatoes, garlic, olive oil', 'smartkitchen-suite'); ?>"></textarea>
                </div>
                
                <div class="sks-form-row">
                    <div class="sks-form-group">
                        <label for="sks-recipe-cuisine"><?php _e('Cuisine Type', 'smartkitchen-suite'); ?></label>
                        <select id="sks-recipe-cuisine" class="sks-select">
                            <option value=""><?php _e('Any', 'smartkitchen-suite'); ?></option>
                            <option value="Italian"><?php _e('Italian', 'smartkitchen-suite'); ?></option>
                            <option value="Asian"><?php _e('Asian', 'smartkitchen-suite'); ?></option>
                            <option value="Mexican"><?php _e('Mexican', 'smartkitchen-suite'); ?></option>
                            <option value="French"><?php _e('French', 'smartkitchen-suite'); ?></option>
                            <option value="Mediterranean"><?php _e('Mediterranean', 'smartkitchen-suite'); ?></option>
                            <option value="American"><?php _e('American', 'smartkitchen-suite'); ?></option>
                            <option value="Indian"><?php _e('Indian', 'smartkitchen-suite'); ?></option>
                        </select>
                    </div>
                    
                    <div class="sks-form-group">
                        <label for="sks-recipe-servings"><?php _e('Servings', 'smartkitchen-suite'); ?></label>
                        <input type="number" id="sks-recipe-servings" class="sks-input" value="4" min="1" max="12">
                    </div>
                    
                    <div class="sks-form-group">
                        <label for="sks-recipe-level"><?php _e('Difficulty', 'smartkitchen-suite'); ?></label>
                        <select id="sks-recipe-level" class="sks-select">
                            <option value="beginner"><?php _e('Beginner', 'smartkitchen-suite'); ?></option>
                            <option value="intermediate"><?php _e('Intermediate', 'smartkitchen-suite'); ?></option>
                            <option value="advanced"><?php _e('Advanced', 'smartkitchen-suite'); ?></option>
                        </select>
                    </div>
                </div>
                
                <div class="sks-form-group">
                    <label>
                        <input type="checkbox" id="sks-recipe-dietary-vegetarian">
                        <?php _e('Vegetarian', 'smartkitchen-suite'); ?>
                    </label>
                    <label>
                        <input type="checkbox" id="sks-recipe-dietary-vegan">
                        <?php _e('Vegan', 'smartkitchen-suite'); ?>
                    </label>
                    <label>
                        <input type="checkbox" id="sks-recipe-dietary-glutenfree">
                        <?php _e('Gluten-Free', 'smartkitchen-suite'); ?>
                    </label>
                </div>
                
                <button type="button" class="sks-btn sks-btn-primary sks-btn-generate" onclick="sksGenerateRecipe()">
                    <?php _e('Generate Recipe', 'smartkitchen-suite'); ?>
                </button>
            </div>
            
            <div id="sks-recipe-result" class="sks-recipe-result" style="display: none;">
                <div class="sks-recipe-loading" id="sks-recipe-loading" style="display: none;">
                    <div class="sks-spinner"></div>
                    <p><?php _e('Creating your perfect recipe...', 'smartkitchen-suite'); ?></p>
                </div>
                <div id="sks-recipe-content" class="sks-recipe-content"></div>
            </div>
            
            <style>
                .sks-recipe-generator { max-width: 900px; margin: 0 auto; }
                .sks-recipe-form { background: #f9f9f9; padding: 30px; border-radius: 8px; margin: 20px 0; }
                .sks-form-group { margin-bottom: 20px; }
                .sks-form-group label { display: block; margin-bottom: 8px; font-weight: 600; }
                .sks-form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
                .sks-textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; resize: vertical; }
                .sks-btn-generate { width: 100%; padding: 15px; font-size: 1.1em; }
                .sks-recipe-loading { text-align: center; padding: 40px; }
                .sks-spinner { border: 4px solid #f3f3f3; border-top: 4px solid #2196f3; border-radius: 50%; width: 50px; height: 50px; animation: spin 1s linear infinite; margin: 0 auto 20px; }
                @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
                .sks-recipe-content { background: white; padding: 30px; border-radius: 8px; margin-top: 20px; }
                .sks-recipe-content h3 { color: #2196f3; margin-bottom: 15px; }
                .sks-recipe-content pre { white-space: pre-wrap; line-height: 1.8; }
            </style>
            
            <script>
            async function sksGenerateRecipe() {
                const ingredients = document.getElementById('sks-recipe-ingredients').value.trim();
                
                if (!ingredients) {
                    alert('Please enter at least one ingredient.');
                    return;
                }
                
                const servings = document.getElementById('sks-recipe-servings').value;
                const cuisine = document.getElementById('sks-recipe-cuisine').value;
                const level = document.getElementById('sks-recipe-level').value;
                const vegetarian = document.getElementById('sks-recipe-dietary-vegetarian').checked;
                const vegan = document.getElementById('sks-recipe-dietary-vegan').checked;
                const glutenFree = document.getElementById('sks-recipe-dietary-glutenfree').checked;
                
                const dietaryPrefs = [];
                if (vegetarian) dietaryPrefs.push('vegetarian');
                if (vegan) dietaryPrefs.push('vegan');
                if (glutenFree) dietaryPrefs.push('gluten-free');
                
                document.getElementById('sks-recipe-result').style.display = 'block';
                document.getElementById('sks-recipe-loading').style.display = 'block';
                document.getElementById('sks-recipe-content').style.display = 'none';
                
                try {
                    const response = await fetch('<?php echo rest_url('sks/v1/ai/generate-content'); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
                        },
                        body: JSON.stringify({
                            prompt: `Create a detailed recipe with these ingredients: ${ingredients}. ${cuisine ? 'Cuisine: ' + cuisine + '. ' : ''}Servings: ${servings}. Difficulty: ${level}. ${dietaryPrefs.length > 0 ? 'Dietary requirements: ' + dietaryPrefs.join(', ') + '.' : ''} Include preparation time, cooking time, step-by-step instructions, and nutritional information.`
                        })
                    });
                    
                    const data = await response.json();
                    
                    document.getElementById('sks-recipe-loading').style.display = 'none';
                    document.getElementById('sks-recipe-content').style.display = 'block';
                    document.getElementById('sks-recipe-content').innerHTML = '<h3><?php _e('Your Recipe', 'smartkitchen-suite'); ?></h3><pre>' + data.content.replace(/\n/g, '<br>') + '</pre>';
                } catch (error) {
                    console.error('Error generating recipe:', error);
                    document.getElementById('sks-recipe-loading').style.display = 'none';
                    document.getElementById('sks-recipe-content').style.display = 'block';
                    document.getElementById('sks-recipe-content').innerHTML = '<p style="color: red;"><?php _e('Sorry, there was an error generating your recipe. Please try again.', 'smartkitchen-suite'); ?></p>';
                }
            }
            </script>
        </div>
        <?php
    }
}

// Auto-register on load
SKS_Tool_Recipe_Generator::register();

