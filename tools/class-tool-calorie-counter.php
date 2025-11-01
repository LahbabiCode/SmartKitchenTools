<?php
/**
 * Calorie Counter Tool
 */
if (!defined('ABSPATH')) {
    exit;
}

class SKS_Tool_Calorie_Counter {
    
    public static function register() {
        SKS_Tool_Registry::register('calorie-counter', [
            'name' => __('Calorie Counter', 'smartkitchen-suite'),
            'slug' => 'calorie-counter',
            'category' => 'nutrition',
            'description' => __('Track your daily calorie intake and maintain a healthy diet with accurate nutritional information.', 'smartkitchen-suite'),
            'icon' => '📈',
            'class_name' => __CLASS__,
            'shortcode' => 'sks_calorie_counter',
            'settings' => []
        ]);
    }
    
    public function render($args = []) {
        ?>
        <div class="sks-tool-container sks-calorie-counter">
            <div class="sks-tool-header">
                <h2><?php _e('Calorie Counter', 'smartkitchen-suite'); ?></h2>
                <p class="sks-tool-subtitle"><?php _e('Track your daily calories and nutritional intake', 'smartkitchen-suite'); ?></p>
            </div>
            
            <div class="sks-daily-summary">
                <div class="sks-calorie-circle">
                    <div class="sks-circle-content">
                        <span class="sks-calorie-total" id="sks-total-calories">0</span>
                        <span class="sks-calorie-label"><?php _e('Calories', 'smartkitchen-suite'); ?></span>
                        <span class="sks-calorie-goal"><?php _e('Goal: 2000', 'smartkitchen-suite'); ?></span>
                    </div>
                </div>
                <div class="sks-macro-breakdown">
                    <div class="sks-macro-item">
                        <span class="sks-macro-label"><?php _e('Carbs', 'smartkitchen-suite'); ?></span>
                        <span class="sks-macro-value" id="sks-carbs">0g</span>
                    </div>
                    <div class="sks-macro-item">
                        <span class="sks-macro-label"><?php _e('Protein', 'smartkitchen-suite'); ?></span>
                        <span class="sks-macro-value" id="sks-protein">0g</span>
                    </div>
                    <div class="sks-macro-item">
                        <span class="sks-macro-label"><?php _e('Fat', 'smartkitchen-suite'); ?></span>
                        <span class="sks-macro-value" id="sks-fat">0g</span>
                    </div>
                </div>
            </div>
            
            <div class="sks-food-adder">
                <input type="text" id="sks-food-name" class="sks-input" placeholder="<?php _e('Food item...', 'smartkitchen-suite'); ?>">
                <button class="sks-btn sks-btn-primary" onclick="sksAddFood()"><?php _e('Add Food', 'smartkitchen-suite'); ?></button>
            </div>
            
            <div id="sks-food-list" class="sks-food-list"></div>
            
            <style>
                .sks-calorie-counter { max-width: 800px; margin: 0 auto; }
                .sks-daily-summary { display: grid; grid-template-columns: 1fr 2fr; gap: 30px; margin: 30px 0; }
                .sks-calorie-circle { width: 200px; height: 200px; border-radius: 50%; border: 10px solid #2196f3; display: flex; align-items: center; justify-content: center; margin: 0 auto; }
                .sks-circle-content { text-align: center; }
                .sks-calorie-total { font-size: 3em; font-weight: bold; display: block; }
                .sks-calorie-label { font-size: 1em; color: #666; }
                .sks-calorie-goal { font-size: 0.9em; color: #999; }
                .sks-macro-breakdown { display: flex; flex-direction: column; gap: 15px; }
                .sks-macro-item { display: flex; justify-content: space-between; padding: 15px; background: #f5f5f5; border-radius: 8px; }
                .sks-macro-label { font-weight: 600; }
                .sks-macro-value { color: #2196f3; font-weight: bold; }
                .sks-food-adder { display: flex; gap: 10px; margin: 30px 0; }
                .sks-food-adder input { flex: 1; }
                .sks-food-list { margin-top: 20px; }
                .sks-food-item { display: flex; justify-content: space-between; align-items: center; padding: 15px; background: white; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 10px; }
                @media (max-width: 768px) {
                    .sks-daily-summary { grid-template-columns: 1fr; }
                }
            </style>
            
            <script>
            let totalCalories = 0;
            let totalCarbs = 0;
            let totalProtein = 0;
            let totalFat = 0;
            let foods = [];
            
            function sksAddFood() {
                const input = document.getElementById('sks-food-name');
                const foodName = input.value.trim();
                
                if (!foodName) return;
                
                // Sample nutrition data (in real app, would fetch from API)
                const nutrition = { calories: 150, carbs: 20, protein: 5, fat: 6 };
                
                foods.push({ name: foodName, ...nutrition });
                totalCalories += nutrition.calories;
                totalCarbs += nutrition.carbs;
                totalProtein += nutrition.protein;
                totalFat += nutrition.fat;
                
                sksUpdateDisplay();
                input.value = '';
            }
            
            function sksUpdateDisplay() {
                document.getElementById('sks-total-calories').textContent = totalCalories;
                document.getElementById('sks-carbs').textContent = totalCarbs + 'g';
                document.getElementById('sks-protein').textContent = totalProtein + 'g';
                document.getElementById('sks-fat').textContent = totalFat + 'g';
                
                const list = document.getElementById('sks-food-list');
                list.innerHTML = foods.map((food, i) => 
                    `<div class="sks-food-item">
                        <span>${food.name} - ${food.calories} cal</span>
                        <button onclick="sksRemoveFood(${i})" style="color: red; border: none; background: none; cursor: pointer;">×</button>
                    </div>`
                ).join('');
            }
            
            function sksRemoveFood(index) {
                const food = foods[index];
                totalCalories -= food.calories;
                totalCarbs -= food.carbs;
                totalProtein -= food.protein;
                totalFat -= food.fat;
                foods.splice(index, 1);
                sksUpdateDisplay();
            }
            </script>
        </div>
        <?php
    }
}

SKS_Tool_Calorie_Counter::register();

