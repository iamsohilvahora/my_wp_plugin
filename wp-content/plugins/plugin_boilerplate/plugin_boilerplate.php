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
 * @package           Plugin_boilerplate
 *
 * @wordpress-plugin
 * Plugin Name:       Plugin Boilerplate
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Sohil Vahora
 * Author URI:        https://author.example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       plugin_boilerplate
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
define( 'PLUGIN_BOILERPLATE_VERSION', '1.0.0' );

if(!defined('WP_PLUGIN_DIR_PATH')){
    define('WP_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));
}

if(!defined('WP_PLUGIN_DIR_URL')){
    define('WP_PLUGIN_DIR_URL', plugins_url().'/plugin_boilerplate');
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin_boilerplate-activator.php
 */
function activate_plugin_boilerplate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-plugin_boilerplate-activator.php';
	Plugin_boilerplate_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin_boilerplate-deactivator.php
 */
function deactivate_plugin_boilerplate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-plugin_boilerplate-deactivator.php';
	Plugin_boilerplate_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_plugin_boilerplate');
register_deactivation_hook(__FILE__, 'deactivate_plugin_boilerplate');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-plugin_boilerplate.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_plugin_boilerplate(){
	$plugin = new Plugin_boilerplate();
	$plugin->run();
}
run_plugin_boilerplate();
