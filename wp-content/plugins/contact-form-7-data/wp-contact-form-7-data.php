<?php
    /**
     * Plugin Name:       Contact Form 7 Data
     * Plugin URI:        https://example.com/plugins/the-basics/
     * Description:       Save contact form 7 data into database table when user submit the form.
     * Version:           1.0.0
     * Requires at least: 5.2
     * Requires PHP:      7.2
     * Author:            Sohil Vahora
     * Author URI:        https://author.example.com/
     * License:           GPL v2 or later
     * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
     * Update URI:        https://example.com/my-plugin/
     * Text Domain:       wp-contact-form-7-plugin
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

    // Database file
    require plugin_dir_path(__FILE__) .'/inc/db.php';

    // Call the function (wp_contact_form_7_table) in database file when plugin is activated
    register_activation_hook( __FILE__, 'wp_contact_form_7_table');

    // Show contact form 7 data inside menu page
    require plugin_dir_path(__FILE__) .'/inc/settings.php';
    
    // Load css and js
    require plugin_dir_path(__FILE__) .'/inc/assets.php';
?>