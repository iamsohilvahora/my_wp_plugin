<?php
/**
 * Plugin Name:       Like Dislike Post Plugin
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Plugin for like or dislike wordpress post.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Sohil Vahora
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       like-dislike-postplugin
 * Domain Path:       /languages
 */
if(!defined('ABSPATH')){
    die();
}
//If this file called directly, abort
if(!defined('WPINC')){
    die();
}
if(!defined('MY_PLUGIN_VERSION')){
    define('MY_PLUGIN_VERSION', '1.0.0');
}
if(!defined('PLUGIN_DIR_PATH')){
    define('PLUGIN_DIR_PATH', plugin_dir_url(__FILE__));
}
// Setting menu & page
require plugin_dir_path(__FILE__) .'/inc/settings.php';
// Create table for our plugin
require plugin_dir_path(__FILE__) .'/inc/db.php';
// Create like and dislike button using filter
require plugin_dir_path(__FILE__) .'/inc/btns.php';
// load common function
require plugin_dir_path(__FILE__) .'/inc/common-functions.php';
// Call when activate the plugin
register_activation_hook(__FILE__, 'wp_likes_table');
// Add settings link for plugin
function plugin_development_add_plugin_page_settings_link($links){
    $links[] = '<a href="' .
        admin_url('admin.php?page=like_dislike_post') .
        '">' . __('Settings') . '</a>';
    return $links;
} 
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'plugin_development_add_plugin_page_settings_link');
?>