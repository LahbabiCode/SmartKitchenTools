<?php
/**
 * Plugin Name: SmartKitchen Suite
 * Plugin URI: https://smartkitchen.suite
 * Description: AI-powered suite of 60+ intelligent cooking, kitchen management, and nutrition tools
 * Version: 1.0.0
 * Author: SmartKitchen Team
 * Author URI: https://smartkitchen.suite
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: smartkitchen-suite
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('SKS_VERSION', '1.0.0');
define('SKS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SKS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SKS_PLUGIN_FILE', __FILE__);
define('SKS_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main SmartKitchen Suite Plugin Class
 */
final class SmartKitchen_Suite {
    
    /**
     * Plugin instance
     */
    private static $instance = null;
    
    /**
     * Get plugin instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->includes();
        $this->init();
    }
    
    /**
     * Include required files
     */
    private function includes() {
        // Core classes
        require_once SKS_PLUGIN_DIR . 'includes/class-database.php';
        require_once SKS_PLUGIN_DIR . 'includes/class-tool-registry.php';
        require_once SKS_PLUGIN_DIR . 'includes/class-tool-loader.php';
        require_once SKS_PLUGIN_DIR . 'includes/class-shortcodes.php';
        require_once SKS_PLUGIN_DIR . 'includes/class-utilities.php';
        require_once SKS_PLUGIN_DIR . 'includes/class-menu-integration.php';
        
        // AI integration
        require_once SKS_PLUGIN_DIR . 'ai/class-gemini-integration.php';
        require_once SKS_PLUGIN_DIR . 'ai/class-tool-generator.php';
        
        // Admin classes
        if (is_admin()) {
            require_once SKS_PLUGIN_DIR . 'admin/class-admin.php';
        }
        
        // REST API
        require_once SKS_PLUGIN_DIR . 'includes/class-rest-api.php';
    }
    
    /**
     * Initialize plugin
     */
    private function init() {
        // Load text domain
        add_action('plugins_loaded', [$this, 'load_textdomain']);
        
        // Initialize components
        new SKS_Tool_Registry();
        new SKS_Tool_Loader();
        new SKS_Shortcodes();
        
        // Initialize admin
        if (is_admin()) {
            new SKS_Admin();
        }
        
        // Initialize REST API
        new SKS_REST_API();
        
        // Load built-in tools
        $this->load_builtin_tools();
    }
    
    /**
     * Load text domain for translations
     */
    public function load_textdomain() {
        load_plugin_textdomain('smartkitchen-suite', false, dirname(SKS_PLUGIN_BASENAME) . '/languages');
    }
    
    /**
     * Load built-in tool classes
     */
    private function load_builtin_tools() {
        $tools_dir = SKS_PLUGIN_DIR . 'tools/';
        
        if (is_dir($tools_dir)) {
            $tool_files = glob($tools_dir . 'class-tool-*.php');
            
            foreach ($tool_files as $file) {
                if (file_exists($file)) {
                    require_once $file;
                }
            }
        }
    }
}

/**
 * Plugin activation hook
 */
function sks_activate() {
    require_once SKS_PLUGIN_DIR . 'includes/class-database.php';
    SKS_Database::create_tables();
    
    // Create default options
    add_option('sks_version', SKS_VERSION);
    add_option('sks_ai_api_key', '');
    add_option('sks_analytics_enabled', true);
    add_option('sks_dark_mode', false);
    
    // Flush rewrite rules
    flush_rewrite_rules();
}

/**
 * Plugin deactivation hook
 */
function sks_deactivate() {
    flush_rewrite_rules();
}

// Register activation/deactivation hooks
register_activation_hook(__FILE__, 'sks_activate');
register_deactivation_hook(__FILE__, 'sks_deactivate');

/**
 * Initialize plugin
 */
function sks_init() {
    SmartKitchen_Suite::get_instance();
}
add_action('init', 'sks_init', 0);

/**
 * Helper function to get plugin instance
 */
function sks() {
    return SmartKitchen_Suite::get_instance();
}

