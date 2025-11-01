<?php
/**
 * Menu Integration Class
 */
if (!defined('ABSPATH')) {
    exit;
}

class SKS_Menu_Integration {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', [$this, 'add_menu_items']);
        add_action('admin_init', [$this, 'maybe_sync_menu']);
    }
    
    /**
     * Add menu items for tools
     */
    public function add_menu_items() {
        $tools = SKS_Database::get_tools(['is_active' => 1, 'has_menu' => 1]);
        
        foreach ($tools as $tool) {
            if ($tool['page_id']) {
                add_submenu_page(
                    'smartkitchen-suite',
                    $tool['name'],
                    $tool['name'],
                    'read',
                    'sks-tool-' . $tool['tool_id'],
                    function() use ($tool) {
                        echo SKS_Tool_Loader::render_tool($tool['tool_id']);
                    }
                );
            }
        }
    }
    
    /**
     * Sync tools to menu if needed
     */
    public function maybe_sync_menu() {
        if (get_option('sks_menu_sync_needed', false)) {
            $this->sync_tools_to_menu();
            delete_option('sks_menu_sync_needed');
        }
    }
    
    /**
     * Sync all active tools to a menu
     */
    public function sync_tools_to_menu($menu_id = null) {
        if (!$menu_id) {
            $menus = wp_get_nav_menus();
            if (empty($menus)) {
                return false;
            }
            $menu_id = $menus[0]->term_id;
        }
        
        $tools = SKS_Database::get_tools(['is_active' => 1]);
        
        // Get existing menu items
        $menu_items = wp_get_nav_menu_items($menu_id);
        $existing_pages = [];
        
        foreach ($menu_items as $item) {
            if ($item->type === 'post_type' && $item->object === 'page') {
                $existing_pages[] = $item->object_id;
            }
        }
        
        // Add missing tool pages to menu
        foreach ($tools as $tool) {
            if ($tool['page_id'] && $tool['has_menu'] && !in_array($tool['page_id'], $existing_pages)) {
                wp_update_nav_menu_item($menu_id, 0, [
                    'menu-item-title' => $tool['name'],
                    'menu-item-object-id' => $tool['page_id'],
                    'menu-item-object' => 'page',
                    'menu-item-type' => 'post_type',
                    'menu-item-status' => 'publish'
                ]);
            }
        }
        
        return true;
    }
    
    /**
     * Remove tool from menu
     */
    public function remove_tool_from_menu($tool_id, $menu_id) {
        $tool = SKS_Database::get_tool($tool_id);
        
        if (!$tool || !$tool['page_id']) {
            return false;
        }
        
        $menu_items = wp_get_nav_menu_items($menu_id);
        
        foreach ($menu_items as $item) {
            if ($item->object_id == $tool['page_id'] && $item->type === 'post_type') {
                wp_delete_post($item->ID, true);
                return true;
            }
        }
        
        return false;
    }
}

