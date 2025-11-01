<?php
/**
 * Grocery List Generator Tool
 */
if (!defined('ABSPATH')) {
    exit;
}

class SKS_Tool_Grocery_List {
    
    /**
     * Register the tool
     */
    public static function register() {
        SKS_Tool_Registry::register('grocery-list', [
            'name' => __('Smart Grocery List', 'smartkitchen-suite'),
            'slug' => 'grocery-list',
            'category' => 'shopping',
            'description' => __('Create organized grocery lists with categories and quantities from your recipes and meal plans.', 'smartkitchen-suite'),
            'icon' => '🛒',
            'class_name' => __CLASS__,
            'shortcode' => 'sks_grocery_list',
            'settings' => []
        ]);
    }
    
    /**
     * Render the tool
     */
    public function render($args = []) {
        ?>
        <div class="sks-tool-container sks-grocery-list">
            <div class="sks-tool-header">
                <h2><?php _e('Smart Grocery List', 'smartkitchen-suite'); ?></h2>
                <p class="sks-tool-subtitle"><?php _e('Create organized shopping lists', 'smartkitchen-suite'); ?></p>
            </div>
            
            <div class="sks-grocery-container">
                <div class="sks-grocery-input">
                    <input type="text" id="sks-grocery-item" class="sks-input" placeholder="<?php _e('Add grocery item...', 'smartkitchen-suite'); ?>" onkeypress="if(event.key==='Enter') sksAddGroceryItem()">
                    <button type="button" class="sks-btn sks-btn-primary" onclick="sksAddGroceryItem()">
                        <?php _e('Add Item', 'smartkitchen-suite'); ?>
                    </button>
                </div>
                
                <div class="sks-grocery-categories">
                    <h3><?php _e('Your List', 'smartkitchen-suite'); ?></h3>
                    <div id="sks-grocery-list" class="sks-list-items"></div>
                </div>
                
                <div class="sks-grocery-actions">
                    <button type="button" class="sks-btn sks-btn-secondary" onclick="sksSaveGroceryList()">
                        <?php _e('Save List', 'smartkitchen-suite'); ?>
                    </button>
                    <button type="button" class="sks-btn sks-btn-secondary" onclick="sksPrintGroceryList()">
                        <?php _e('Print List', 'smartkitchen-suite'); ?>
                    </button>
                    <button type="button" class="sks-btn sks-btn-danger" onclick="sksClearGroceryList()">
                        <?php _e('Clear All', 'smartkitchen-suite'); ?>
                    </button>
                </div>
            </div>
            
            <style>
                .sks-grocery-list { max-width: 800px; margin: 0 auto; }
                .sks-grocery-container { background: #f9f9f9; padding: 30px; border-radius: 8px; }
                .sks-grocery-input { display: flex; gap: 10px; margin-bottom: 20px; }
                .sks-grocery-input input { flex: 1; }
                .sks-list-items { margin: 20px 0; }
                .sks-list-category { margin-bottom: 25px; }
                .sks-list-category h4 { color: #2196f3; margin-bottom: 10px; padding-bottom: 5px; border-bottom: 2px solid #2196f3; }
                .sks-list-item { display: flex; align-items: center; padding: 10px; background: white; margin-bottom: 5px; border-radius: 4px; }
                .sks-list-item input[type="checkbox"] { margin-right: 10px; }
                .sks-list-item.completed { opacity: 0.5; text-decoration: line-through; }
                .sks-grocery-actions { display: flex; gap: 10px; margin-top: 20px; }
            </style>
            
            <script>
            let groceryItems = [];
            
            function sksAddGroceryItem() {
                const input = document.getElementById('sks-grocery-item');
                const item = input.value.trim();
                
                if (item) {
                    groceryItems.push({ id: Date.now(), name: item, category: 'Other', checked: false });
                    input.value = '';
                    sksRenderGroceryList();
                }
            }
            
            function sksRenderGroceryList() {
                const container = document.getElementById('sks-grocery-list');
                
                if (groceryItems.length === 0) {
                    container.innerHTML = '<p style="text-align: center; color: #999;"><?php _e('Your grocery list is empty. Add items to get started!', 'smartkitchen-suite'); ?></p>';
                    return;
                }
                
                // Group by category
                const categories = {};
                groceryItems.forEach(item => {
                    if (!categories[item.category]) {
                        categories[item.category] = [];
                    }
                    categories[item.category].push(item);
                });
                
                let html = '';
                Object.keys(categories).forEach(category => {
                    html += `<div class="sks-list-category"><h4>${category}</h4>`;
                    categories[category].forEach(item => {
                        const checkedClass = item.checked ? 'completed' : '';
                        html += `<div class="sks-list-item ${checkedClass}">
                            <input type="checkbox" ${item.checked ? 'checked' : ''} onchange="sksToggleItem(${item.id})">
                            <span>${item.name}</span>
                            <button onclick="sksRemoveItem(${item.id})" style="margin-left: auto; color: red; border: none; background: none; cursor: pointer;">×</button>
                        </div>`;
                    });
                    html += '</div>';
                });
                
                container.innerHTML = html;
                sksSaveToLocalStorage();
            }
            
            function sksToggleItem(id) {
                groceryItems = groceryItems.map(item => {
                    if (item.id === id) {
                        item.checked = !item.checked;
                    }
                    return item;
                });
                sksRenderGroceryList();
            }
            
            function sksRemoveItem(id) {
                groceryItems = groceryItems.filter(item => item.id !== id);
                sksRenderGroceryList();
            }
            
            function sksClearGroceryList() {
                if (confirm('<?php _e('Are you sure you want to clear all items?', 'smartkitchen-suite'); ?>')) {
                    groceryItems = [];
                    sksRenderGroceryList();
                }
            }
            
            function sksSaveGroceryList() {
                alert('<?php _e('Grocery list saved!', 'smartkitchen-suite'); ?>');
            }
            
            function sksPrintGroceryList() {
                window.print();
            }
            
            function sksSaveToLocalStorage() {
                localStorage.setItem('sks_grocery_list', JSON.stringify(groceryItems));
            }
            
            function sksLoadFromLocalStorage() {
                const saved = localStorage.getItem('sks_grocery_list');
                if (saved) {
                    groceryItems = JSON.parse(saved);
                    sksRenderGroceryList();
                }
            }
            
            // Load on page load
            sksLoadFromLocalStorage();
            </script>
        </div>
        <?php
    }
}

SKS_Tool_Grocery_List::register();

