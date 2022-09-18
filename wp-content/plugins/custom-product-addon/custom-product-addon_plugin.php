<?php
/**
 * Plugin Name:       Custom Product Addon 
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Custom Product Addon - allow user to modify field of product detail page at wp admin (In edit product area - https://i.imgur.com/vMXRk6f.png)
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Sohil Vahora
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       custom-product-addon-plugin
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
if(!defined('CUSTOM_PRODUCT_PLUGIN_DIR_PATH')){
    define('CUSTOM_PRODUCT_PLUGIN_DIR_PATH', plugin_dir_url(__FILE__));
}
// Check if plugin is active or not in admin
if(!function_exists('is_plugin_active_for_network')){
    include_once(ABSPATH . '/wp-admin/includes/plugin.php');
}
// action on activate plugin
function wp_activate_product_addon_plugin_func(){
    // Check WooCommerce plugin is active or not
    if(current_user_can('activate_plugins') && !is_plugin_active('woocommerce/woocommerce.php')){
        // Deactivate the plugin.
        deactivate_plugins(plugin_basename(__FILE__));

        $notices = get_option('my_plugin_woocommerce_install_and_activate_admin_notices', array());
        
        $notices[] = 'Please install and activate WooCommerce plugin <a href="' . esc_url('https://wordpress.org/plugins/woocommerce' ) . '" target="_blank">WooCommerce</a>';

        update_option('my_plugin_woocommerce_install_and_activate_admin_notices', $notices);
    }
}
register_activation_hook(__FILE__, 'wp_activate_product_addon_plugin_func');
// Check woocommerce plugin is not activated
if(!is_plugin_active('woocommerce/woocommerce.php')){
    $notices = get_option('my_plugin_deferred_admin_notices', array());
    
    $notices[] = 'Please activate WooCommerce plugin <a href="' . esc_url('https://wordpress.org/plugins/woocommerce') . '" target="_blank">WooCommerce</a> <b>to use Custom Product Addon</b>';
    update_option('my_plugin_deferred_admin_notices', $notices);
}
// display admin notices 
function custom_product_addon_plugin_admin_notices(){
    // if woocommerce plugin is not activated or installed
    if($notices = get_option('my_plugin_woocommerce_install_and_activate_admin_notices')){
        foreach($notices as $notice){
            echo "<div class='updated notice error is-dismissible'><p>$notice</p></div>";
            break;
        }
        delete_option('my_plugin_woocommerce_install_and_activate_admin_notices');
        deactivate_plugins(plugin_basename(__FILE__));

        if(isset($_GET['activate'])){
            unset($_GET['activate']);
        }
    }
    // if woocommerce plugin is installed and not activated
    if($notices = get_option('my_plugin_deferred_admin_notices')){
        foreach($notices as $notice){
            echo "<div class='updated notice error is-dismissible'><p>$notice</p></div>";
            break;
        }
        delete_option('my_plugin_deferred_admin_notices');
    }
}
add_action('admin_notices', 'custom_product_addon_plugin_admin_notices');
// action on plugin deactivation 
function custom_product_addon_plugin_deactivation(){
    delete_option('my_plugin_woocommerce_install_and_activate_admin_notices'); 
    delete_option('my_plugin_deferred_admin_notices'); 
}
register_deactivation_hook(__FILE__, 'custom_product_addon_plugin_deactivation');
// enqueue style and script
if(!function_exists('my_plugin_style_script')){
    /**
     * Proper way to enqueue scripts and styles.
     */
    function my_plugin_style_script(){
        // Plugin Frontend CSS
        wp_enqueue_style('main-style', CUSTOM_PRODUCT_PLUGIN_DIR_PATH .'/assets/css/main.css'); 
        // load bootstrap css
        wp_enqueue_style('bootstrap-style', CUSTOM_PRODUCT_PLUGIN_DIR_PATH .'/assets/css/bootstrap.min.css');
        // load bootstrap js
        wp_enqueue_script('bootstrap-script', CUSTOM_PRODUCT_PLUGIN_DIR_PATH . '/assets/js/bootstrap.min.js', 'jQuery', '1.0.0', true);
        // Plugin Frontend JS
        wp_enqueue_script('main-script', CUSTOM_PRODUCT_PLUGIN_DIR_PATH . '/assets/js/main.js', 'jQuery', '1.0.0', true);
    }
    add_action('wp_enqueue_scripts', 'my_plugin_style_script');
}
/**
 * Load all common function
 */
require plugin_dir_path(__FILE__) . '/inc/common-functions.php';
/**
 * Load all woocommerce hooks and function
 */
require plugin_dir_path(__FILE__) . '/inc/woocommerce.php';
?>