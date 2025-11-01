<?php
/**
 * Plugin uninstallation handler
 */

// Exit if not called from WordPress
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;

// Drop database tables
$table_prefix = $wpdb->prefix;

$tables = [
    $table_prefix . 'sks_tools',
    $table_prefix . 'sks_analytics',
    $table_prefix . 'sks_ai_cache'
];

foreach ($tables as $table) {
    $wpdb->query("DROP TABLE IF EXISTS {$table}");
}

// Delete options
delete_option('sks_version');
delete_option('sks_ai_api_key');
delete_option('sks_analytics_enabled');
delete_option('sks_dark_mode');
delete_option('sks_menu_sync_needed');

// Delete user meta
$wpdb->delete($wpdb->usermeta, ['meta_key' => 'sks_preferences']);

// Clear transients
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_sks_%'");
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_sks_%'");

// Flush rewrite rules
flush_rewrite_rules();

