<?php
/**
 * BMI Calculator Tool
 */
if (!defined('ABSPATH')) {
    exit;
}

class SKS_Tool_BMI_Calculator {
    
    /**
     * Register the tool
     */
    public static function register() {
        SKS_Tool_Registry::register('bmi-calculator', [
            'name' => __('BMI Calculator', 'smartkitchen-suite'),
            'slug' => 'bmi-calculator',
            'category' => 'nutrition',
            'description' => __('Calculate your Body Mass Index (BMI) and get personalized health recommendations based on your weight and height.', 'smartkitchen-suite'),
            'icon' => '📊',
            'class_name' => __CLASS__,
            'shortcode' => 'sks_bmi_calculator',
            'settings' => []
        ]);
    }
    
    /**
     * Render the tool
     */
    public function render($args = []) {
        ?>
        <div class="sks-tool-container sks-bmi-calculator">
            <div class="sks-tool-header">
                <h2><?php _e('BMI Calculator', 'smartkitchen-suite'); ?></h2>
                <p class="sks-tool-subtitle"><?php _e('Calculate your Body Mass Index and understand your health status', 'smartkitchen-suite'); ?></p>
            </div>
            
            <div class="sks-bmi-form">
                <div class="sks-form-group">
                    <label for="sks-bmi-height"><?php _e('Height', 'smartkitchen-suite'); ?></label>
                    <div class="sks-input-group">
                        <input type="number" id="sks-bmi-height" class="sks-input" placeholder="170" min="50" max="250">
                        <select id="sks-bmi-height-unit" class="sks-select">
                            <option value="cm"><?php _e('cm', 'smartkitchen-suite'); ?></option>
                            <option value="ft"><?php _e('ft', 'smartkitchen-suite'); ?></option>
                        </select>
                    </div>
                </div>
                
                <div class="sks-form-group">
                    <label for="sks-bmi-weight"><?php _e('Weight', 'smartkitchen-suite'); ?></label>
                    <div class="sks-input-group">
                        <input type="number" id="sks-bmi-weight" class="sks-input" placeholder="70" min="20" max="300">
                        <select id="sks-bmi-weight-unit" class="sks-select">
                            <option value="kg"><?php _e('kg', 'smartkitchen-suite'); ?></option>
                            <option value="lb"><?php _e('lb', 'smartkitchen-suite'); ?></option>
                        </select>
                    </div>
                </div>
                
                <button type="button" class="sks-btn sks-btn-primary sks-btn-calculate" onclick="sksCalculateBMI()">
                    <?php _e('Calculate BMI', 'smartkitchen-suite'); ?>
                </button>
            </div>
            
            <div id="sks-bmi-result" class="sks-bmi-result" style="display: none;">
                <div class="sks-bmi-score">
                    <span class="sks-bmi-value" id="sks-bmi-value">0</span>
                    <span class="sks-bmi-label"><?php _e('Your BMI', 'smartkitchen-suite'); ?></span>
                </div>
                <div class="sks-bmi-status" id="sks-bmi-status"></div>
                <div class="sks-bmi-scale">
                    <div class="sks-scale-bar">
                        <div class="sks-scale-section underweight" title="<?php _e('Underweight', 'smartkitchen-suite'); ?>">&lt;18.5</div>
                        <div class="sks-scale-section normal" title="<?php _e('Normal', 'smartkitchen-suite'); ?>">18.5-25</div>
                        <div class="sks-scale-section overweight" title="<?php _e('Overweight', 'smartkitchen-suite'); ?>">25-30</div>
                        <div class="sks-scale-section obese" title="<?php _e('Obese', 'smartkitchen-suite'); ?>">&gt;30</div>
                    </div>
                    <div class="sks-scale-indicator" id="sks-bmi-indicator"></div>
                </div>
                <div class="sks-bmi-recommendation" id="sks-bmi-recommendation"></div>
            </div>
            
            <style>
                .sks-bmi-calculator { max-width: 600px; margin: 0 auto; }
                .sks-bmi-form { background: #f9f9f9; padding: 30px; border-radius: 8px; margin: 20px 0; }
                .sks-form-group { margin-bottom: 20px; }
                .sks-form-group label { display: block; margin-bottom: 8px; font-weight: 600; }
                .sks-input-group { display: flex; gap: 10px; }
                .sks-input-group input { flex: 1; }
                .sks-input, .sks-select { padding: 12px; border: 1px solid #ddd; border-radius: 4px; }
                .sks-btn-calculate { width: 100%; padding: 15px; font-size: 1.1em; }
                .sks-bmi-result { text-align: center; margin: 30px 0; }
                .sks-bmi-score { margin-bottom: 20px; }
                .sks-bmi-value { font-size: 4em; font-weight: bold; display: block; }
                .sks-bmi-label { font-size: 1.1em; color: #666; }
                .sks-bmi-status { font-size: 1.5em; margin: 15px 0; }
                .sks-bmi-scale { position: relative; margin: 30px 0; }
                .sks-scale-bar { display: flex; height: 30px; }
                .sks-scale-section { flex: 1; display: flex; align-items: center; justify-content: center; font-size: 0.9em; color: white; }
                .sks-scale-section.underweight { background: #3f51b5; }
                .sks-scale-section.normal { background: #4caf50; }
                .sks-scale-section.overweight { background: #ff9800; }
                .sks-scale-section.obese { background: #f44336; }
                .sks-scale-indicator { width: 3px; height: 40px; background: #000; position: absolute; top: -5px; transition: left 0.3s ease; }
                .sks-bmi-recommendation { background: #e3f2fd; padding: 20px; border-radius: 8px; margin-top: 20px; text-align: left; }
            </style>
            
            <script>
            function sksCalculateBMI() {
                const height = parseFloat(document.getElementById('sks-bmi-height').value);
                const heightUnit = document.getElementById('sks-bmi-height-unit').value;
                const weight = parseFloat(document.getElementById('sks-bmi-weight').value);
                const weightUnit = document.getElementById('sks-bmi-weight-unit').value;
                
                if (!height || !weight || height <= 0 || weight <= 0) {
                    alert('Please enter valid height and weight values.');
                    return;
                }
                
                // Convert to metric if needed
                let heightInM = heightUnit === 'ft' ? height * 0.3048 : height / 100;
                let weightInKg = weightUnit === 'lb' ? weight * 0.453592 : weight;
                
                // Calculate BMI
                const bmi = weightInKg / (heightInM * heightInM);
                
                // Display result
                document.getElementById('sks-bmi-value').textContent = bmi.toFixed(1);
                
                // Determine status
                let status, statusClass, recommendation;
                if (bmi < 18.5) {
                    status = '<?php _e('Underweight', 'smartkitchen-suite'); ?>';
                    statusClass = 'underweight';
                    recommendation = '<?php _e('Consider consulting with a healthcare provider to develop a healthy eating plan.', 'smartkitchen-suite'); ?>';
                } else if (bmi < 25) {
                    status = '<?php _e('Normal Weight', 'smartkitchen-suite'); ?>';
                    statusClass = 'normal';
                    recommendation = '<?php _e('Great! Maintain your healthy lifestyle with balanced nutrition and regular exercise.', 'smartkitchen-suite'); ?>';
                } else if (bmi < 30) {
                    status = '<?php _e('Overweight', 'smartkitchen-suite'); ?>';
                    statusClass = 'overweight';
                    recommendation = '<?php _e('Consider a balanced diet and regular physical activity. Consult a healthcare provider for a personalized plan.', 'smartkitchen-suite'); ?>';
                } else {
                    status = '<?php _e('Obese', 'smartkitchen-suite'); ?>';
                    statusClass = 'obese';
                    recommendation = '<?php _e('Please consult with a healthcare provider to develop a comprehensive weight management plan.', 'smartkitchen-suite'); ?>';
                }
                
                document.getElementById('sks-bmi-status').textContent = status;
                document.getElementById('sks-bmi-status').className = 'sks-bmi-status ' + statusClass;
                document.getElementById('sks-bmi-recommendation').textContent = recommendation;
                
                // Update indicator position
                let indicatorPosition = 0;
                if (bmi < 18.5) {
                    indicatorPosition = bmi / 18.5 * 25;
                } else if (bmi < 25) {
                    indicatorPosition = 25 + ((bmi - 18.5) / 6.5 * 25);
                } else if (bmi < 30) {
                    indicatorPosition = 50 + ((bmi - 25) / 5 * 25);
                } else {
                    indicatorPosition = 75 + Math.min(((bmi - 30) / 10 * 25), 25);
                }
                document.getElementById('sks-bmi-indicator').style.left = indicatorPosition + '%';
                
                document.getElementById('sks-bmi-result').style.display = 'block';
                
                // Track usage
                if (typeof fetch !== 'undefined') {
                    fetch('<?php echo rest_url('sks/v1/tools/bmi-calculator'); ?>', {
                        method: 'GET',
                        headers: {
                            'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
                        }
                    });
                }
            }
            </script>
        </div>
        <?php
    }
}

// Auto-register on load
SKS_Tool_BMI_Calculator::register();

