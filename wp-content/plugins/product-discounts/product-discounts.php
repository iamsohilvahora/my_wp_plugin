<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://author.example.com/
 * @since             1.0.0
 * @package           Product_Discounts
 *
 * @wordpress-plugin
 * Plugin Name:       Product Discounts
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Create custom discount for different product.
 * Version:           1.0.0
 * Author:            Sohil Vahora
 * Author URI:        https://author.example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       product-discounts
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PRODUCT_DISCOUNTS_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-product-discounts-activator.php
 */
function activate_product_discounts() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-product-discounts-activator.php';
	Product_Discounts_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-product-discounts-deactivator.php
 */
function deactivate_product_discounts() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-product-discounts-deactivator.php';
	Product_Discounts_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_product_discounts' );
register_deactivation_hook( __FILE__, 'deactivate_product_discounts' );

// Check if plugin is active or not in admin
if(!function_exists('is_plugin_active_for_network')){
    include_once(ABSPATH . '/wp-admin/includes/plugin.php');
}

// Check WooCommerce plugin is active or not
function load_plugin_validation_for_woocommerce(){
    if(current_user_can('activate_plugins') && !is_plugin_active('woocommerce/woocommerce.php')){
        // Deactivate the plugin.
        deactivate_plugins(plugin_basename(__FILE__));

        $notices = get_option('my_plugin_woocommerce_install_and_activate_admin_notices', array());
        
        $notices[] = 'Please install and activate WooCommerce plugin <a href="' . esc_url('https://wordpress.org/plugins/woocommerce' ) . '" target="_blank">WooCommerce</a>';

        update_option('my_plugin_woocommerce_install_and_activate_admin_notices', $notices);
    }
    else{
        run_product_discounts(); // run code after plugin is activated
    }
    // Check woocommerce plugin is not activated
    if(!is_plugin_active('woocommerce/woocommerce.php')){
        $notices = get_option('my_plugin_deferred_admin_notices', array());
        
        $notices[] = 'Please activate WooCommerce plugin <a href="' . esc_url('https://wordpress.org/plugins/woocommerce') . '" target="_blank">WooCommerce</a> <b>to use Product Discount plugin.</b>';
        update_option('my_plugin_deferred_admin_notices', $notices);
    }
}
add_action('init', 'load_plugin_validation_for_woocommerce');

// display admin notices 
function plugin_admin_notices(){
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
add_action('admin_notices', 'plugin_admin_notices');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-product-discounts.php';

// Load wp list table class
require_once(ABSPATH.'wp-admin/includes/class-wp-list-table.php');

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_product_discounts() {
    // add plugin setting link
    $plugin_setting_link = plugin_basename(__FILE__); // get plugin file name
    $plugin = new Product_Discounts($plugin_setting_link);
	$plugin->run();
}
// run_product_discounts();
