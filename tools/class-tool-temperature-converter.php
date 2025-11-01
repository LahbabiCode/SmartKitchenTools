<?php
/**
 * Temperature Converter Tool
 */
if (!defined('ABSPATH')) {
    exit;
}

class SKS_Tool_Temperature_Converter {
    
    /**
     * Register the tool
     */
    public static function register() {
        SKS_Tool_Registry::register('temperature-converter', [
            'name' => __('Temperature Converter', 'smartkitchen-suite'),
            'slug' => 'temperature-converter',
            'category' => 'cooking',
            'description' => __('Convert between Celsius, Fahrenheit, and Kelvin for precise cooking temperatures.', 'smartkitchen-suite'),
            'icon' => '🌡️',
            'class_name' => __CLASS__,
            'shortcode' => 'sks_temperature_converter',
            'settings' => []
        ]);
    }
    
    /**
     * Render the tool
     */
    public function render($args = []) {
        ?>
        <div class="sks-tool-container sks-temperature-converter">
            <div class="sks-tool-header">
                <h2><?php _e('Temperature Converter', 'smartkitchen-suite'); ?></h2>
                <p class="sks-tool-subtitle"><?php _e('Convert between Celsius, Fahrenheit, and Kelvin', 'smartkitchen-suite'); ?></p>
            </div>
            
            <div class="sks-converter-grid">
                <div class="sks-converter-unit">
                    <label for="sks-temp-celsius"><?php _e('Celsius (°C)', 'smartkitchen-suite'); ?></label>
                    <input type="number" id="sks-temp-celsius" class="sks-input-large" oninput="sksConvertTemp('celsius')">
                </div>
                
                <div class="sks-converter-unit">
                    <label for="sks-temp-fahrenheit"><?php _e('Fahrenheit (°F)', 'smartkitchen-suite'); ?></label>
                    <input type="number" id="sks-temp-fahrenheit" class="sks-input-large" oninput="sksConvertTemp('fahrenheit')">
                </div>
                
                <div class="sks-converter-unit">
                    <label for="sks-temp-kelvin"><?php _e('Kelvin (K)', 'smartkitchen-suite'); ?></label>
                    <input type="number" id="sks-temp-kelvin" class="sks-input-large" oninput="sksConvertTemp('kelvin')">
                </div>
            </div>
            
            <div class="sks-cooking-tips">
                <h3><?php _e('Common Cooking Temperatures', 'smartkitchen-suite'); ?></h3>
                <div class="sks-tips-grid">
                    <div class="sks-tip-item">
                        <strong><?php _e('Refrigerator', 'smartkitchen-suite'); ?></strong>
                        <p>4°C / 40°F</p>
                    </div>
                    <div class="sks-tip-item">
                        <strong><?php _e('Room Temperature', 'smartkitchen-suite'); ?></strong>
                        <p>20°C / 68°F</p>
                    </div>
                    <div class="sks-tip-item">
                        <strong><?php _e('Water Boiling', 'smartkitchen-suite'); ?></strong>
                        <p>100°C / 212°F</p>
                    </div>
                    <div class="sks-tip-item">
                        <strong><?php _e('Oven Moderate', 'smartkitchen-suite'); ?></strong>
                        <p>180°C / 350°F</p>
                    </div>
                    <div class="sks-tip-item">
                        <strong><?php _e('Oven Hot', 'smartkitchen-suite'); ?></strong>
                        <p>220°C / 425°F</p>
                    </div>
                    <div class="sks-tip-item">
                        <strong><?php _e('Custard/Water Bath', 'smartkitchen-suite'); ?></strong>
                        <p>80°C / 176°F</p>
                    </div>
                </div>
            </div>
            
            <style>
                .sks-temperature-converter { max-width: 900px; margin: 0 auto; }
                .sks-converter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px; margin: 30px 0; }
                .sks-converter-unit label { display: block; margin-bottom: 10px; font-weight: 600; }
                .sks-input-large { width: 100%; padding: 20px; font-size: 2em; text-align: center; border: 2px solid #2196f3; border-radius: 8px; }
                .sks-cooking-tips { background: #e3f2fd; padding: 30px; border-radius: 8px; margin-top: 30px; }
                .sks-cooking-tips h3 { margin-bottom: 20px; color: #1976d2; }
                .sks-tips-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
                .sks-tip-item { background: white; padding: 15px; border-radius: 6px; text-align: center; }
                .sks-tip-item strong { display: block; margin-bottom: 8px; color: #333; }
                .sks-tip-item p { color: #666; font-size: 1.1em; }
            </style>
            
            <script>
            function sksConvertTemp(fromUnit) {
                const celsiusInput = document.getElementById('sks-temp-celsius');
                const fahrenheitInput = document.getElementById('sks-temp-fahrenheit');
                const kelvinInput = document.getElementById('sks-temp-kelvin');
                
                let celsius = parseFloat(celsiusInput.value) || 0;
                
                if (fromUnit === 'celsius') {
                    celsius = parseFloat(celsiusInput.value) || 0;
                } else if (fromUnit === 'fahrenheit') {
                    const fahrenheit = parseFloat(fahrenheitInput.value) || 0;
                    celsius = (fahrenheit - 32) * 5 / 9;
                } else if (fromUnit === 'kelvin') {
                    const kelvin = parseFloat(kelvinInput.value) || 0;
                    celsius = kelvin - 273.15;
                }
                
                const fahrenheit = celsius * 9 / 5 + 32;
                const kelvin = celsius + 273.15;
                
                if (fromUnit !== 'celsius') celsiusInput.value = celsius.toFixed(2);
                if (fromUnit !== 'fahrenheit') fahrenheitInput.value = fahrenheit.toFixed(2);
                if (fromUnit !== 'kelvin') kelvinInput.value = kelvin.toFixed(2);
            }
            </script>
        </div>
        <?php
    }
}

SKS_Tool_Temperature_Converter::register();

