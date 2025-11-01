<?php
/**
 * Ingredient Converter Tool
 */
if (!defined('ABSPATH')) {
    exit;
}

class SKS_Tool_Ingredient_Converter {
    
    public static function register() {
        SKS_Tool_Registry::register('ingredient-converter', [
            'name' => __('Ingredient Converter', 'smartkitchen-suite'),
            'slug' => 'ingredient-converter',
            'category' => 'cooking',
            'description' => __('Convert between different units of measurement for common ingredients and cooking measurements.', 'smartkitchen-suite'),
            'icon' => '⚖️',
            'class_name' => __CLASS__,
            'shortcode' => 'sks_ingredient_converter',
            'settings' => []
        ]);
    }
    
    public function render($args = []) {
        ?>
        <div class="sks-tool-container sks-ingredient-converter">
            <div class="sks-tool-header">
                <h2><?php _e('Ingredient Converter', 'smartkitchen-suite'); ?></h2>
                <p class="sks-tool-subtitle"><?php _e('Convert between cooking units and measurements', 'smartkitchen-suite'); ?></p>
            </div>
            
            <div class="sks-converter-wrapper">
                <div class="sks-converter-input">
                    <input type="number" id="sks-convert-amount" class="sks-input-large" value="1" step="0.1" oninput="sksConvertIngredient()">
                    <select id="sks-convert-from" class="sks-select" onchange="sksConvertIngredient()">
                        <option value="cups"><?php _e('Cups', 'smartkitchen-suite'); ?></option>
                        <option value="tablespoons"><?php _e('Tablespoons', 'smartkitchen-suite'); ?></option>
                        <option value="teaspoons"><?php _e('Teaspoons', 'smartkitchen-suite'); ?></option>
                        <option value="floz"><?php _e('Fluid Ounces', 'smartkitchen-suite'); ?></option>
                        <option value="ml"><?php _e('Milliliters', 'smartkitchen-suite'); ?></option>
                        <option value="liters"><?php _e('Liters', 'smartkitchen-suite'); ?></option>
                        <option value="grams"><?php _e('Grams', 'smartkitchen-suite'); ?></option>
                        <option value="oz"><?php _e('Ounces', 'smartkitchen-suite'); ?></option>
                        <option value="lb"><?php _e('Pounds', 'smartkitchen-suite'); ?></option>
                    </select>
                </div>
                
                <div class="sks-converter-arrow">→</div>
                
                <div class="sks-converter-output">
                    <div class="sks-output-value" id="sks-convert-result">0</div>
                    <select id="sks-convert-to" class="sks-select" onchange="sksConvertIngredient()">
                        <option value="cups"><?php _e('Cups', 'smartkitchen-suite'); ?></option>
                        <option value="tablespoons"><?php _e('Tablespoons', 'smartkitchen-suite'); ?></option>
                        <option value="teaspoons"><?php _e('Teaspoons', 'smartkitchen-suite'); ?></option>
                        <option value="floz" selected><?php _e('Fluid Ounces', 'smartkitchen-suite'); ?></option>
                        <option value="ml"><?php _e('Milliliters', 'smartkitchen-suite'); ?></option>
                        <option value="liters"><?php _e('Liters', 'smartkitchen-suite'); ?></option>
                        <option value="grams"><?php _e('Grams', 'smartkitchen-suite'); ?></option>
                        <option value="oz"><?php _e('Ounces', 'smartkitchen-suite'); ?></option>
                        <option value="lb"><?php _e('Pounds', 'smartkitchen-suite'); ?></option>
                    </select>
                </div>
            </div>
            
            <div class="sks-conversion-table">
                <h3><?php _e('Common Conversions', 'smartkitchen-suite'); ?></h3>
                <table>
                    <tr>
                        <th><?php _e('Measurement', 'smartkitchen-suite'); ?></th>
                        <th><?php _e('Equivalent', 'smartkitchen-suite'); ?></th>
                    </tr>
                    <tr><td>1 cup</td><td>16 tablespoons</td></tr>
                    <tr><td>1 cup</td><td>8 fl oz</td></tr>
                    <tr><td>1 tbsp</td><td>3 tsp</td></tr>
                    <tr><td>1 cup</td><td>240 ml</td></tr>
                    <tr><td>1 fl oz</td><td>30 ml</td></tr>
                    <tr><td>1 lb</td><td>16 oz</td></tr>
                </table>
            </div>
            
            <style>
                .sks-ingredient-converter { max-width: 800px; margin: 0 auto; }
                .sks-converter-wrapper { display: flex; align-items: center; justify-content: center; gap: 30px; margin: 40px 0; }
                .sks-converter-input, .sks-converter-output { text-align: center; }
                .sks-converter-arrow { font-size: 3em; color: #2196f3; }
                .sks-output-value { font-size: 2.5em; font-weight: bold; color: #2196f3; margin-bottom: 15px; }
                .sks-conversion-table { background: #f9f9f9; padding: 30px; border-radius: 8px; }
                .sks-conversion-table table { width: 100%; border-collapse: collapse; }
                .sks-conversion-table th, .sks-conversion-table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
                .sks-conversion-table th { background: #2196f3; color: white; }
                @media (max-width: 768px) {
                    .sks-converter-wrapper { flex-direction: column; }
                    .sks-converter-arrow { transform: rotate(90deg); }
                }
            </style>
            
            <script>
            function sksConvertIngredient() {
                const amount = parseFloat(document.getElementById('sks-convert-amount').value) || 0;
                const fromUnit = document.getElementById('sks-convert-from').value;
                const toUnit = document.getElementById('sks-convert-to').value;
                
                // Convert to ml first (base unit for volume), then to target
                const conversions = {
                    // Volume to ml
                    'cups': 240, 'tablespoons': 15, 'teaspoons': 5, 'floz': 30, 'ml': 1, 'liters': 1000,
                    // Weight to grams
                    'grams': 1, 'oz': 28.35, 'lb': 453.6
                };
                
                const mlFrom = amount * (conversions[fromUnit] || 1);
                const result = mlFrom / (conversions[toUnit] || 1);
                
                document.getElementById('sks-convert-result').textContent = result.toFixed(2);
            }
            </script>
        </div>
        <?php
    }
}

SKS_Tool_Ingredient_Converter::register();

