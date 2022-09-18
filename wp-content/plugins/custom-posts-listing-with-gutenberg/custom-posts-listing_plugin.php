<?php
/**
 * Plugin Name:       Custom posts listing with gutenberg block 
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Gutenberg block - "Custom Posts" allow user to display post either using load more button option or using infinite scroll option.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Sohil Vahora
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       custom-posts-listing-with-gutenberg-plugin
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

if(!defined('CUSTOM_POSTS_PLUGIN_DIR_PATH')){
    define('CUSTOM_POSTS_PLUGIN_DIR_PATH', plugin_dir_url(__FILE__));
}

// Check if plugin is active or not in admin
if(!function_exists('is_plugin_active_for_network')){
    include_once(ABSPATH . '/wp-admin/includes/plugin.php');
}

// action on activate plugin
function wp_activate_acf_plugin_func(){
    // Check acf pro plugin is active or not
    if(current_user_can('activate_plugins') && !is_plugin_active('advanced-custom-fields-pro/acf.php')){
        // Deactivate the plugin.
        deactivate_plugins(plugin_basename(__FILE__));
        $notices = get_option('my_plugin_acf_install_and_activate_admin_notices', array());
        $notices[] = 'Please install and activate Advanced Custom Fields Pro plugin <a href="' . esc_url('https://www.advancedcustomfields.com' ) . '" target="_blank">Advanced Custom Fields Pro</a>';
        update_option('my_plugin_acf_install_and_activate_admin_notices', $notices);
    }
}
register_activation_hook(__FILE__, 'wp_activate_acf_plugin_func');

// Check acf pro plugin is not activated
if(!is_plugin_active('advanced-custom-fields-pro/acf.php')){
    $notices = get_option('my_plugin_deferred_admin_notices', array());
    $notices[] = 'Please activate Advanced Custom Fields Pro plugin <a href="' . esc_url('https://www.advancedcustomfields.com') . '" target="_blank">Advanced Custom Fields Pro</a> <b>to use Custom posts listing with gutenberg block</b>';
    update_option('my_plugin_deferred_admin_notices', $notices);
}

// display admin notices 
function my_plugin_admin_notices(){
    // if acf plugin is not activated or installed
    if($notices = get_option('my_plugin_acf_install_and_activate_admin_notices')){
        foreach($notices as $notice){
            echo "<div class='updated notice error is-dismissible'><p>$notice</p></div>";
            break;
        }
        delete_option('my_plugin_acf_install_and_activate_admin_notices');
        deactivate_plugins(plugin_basename(__FILE__));

        if(isset($_GET['activate'])){
            unset($_GET['activate']);
        }
    }

    // if acf plugin is installed and not activated
    if($notices = get_option('my_plugin_deferred_admin_notices')){
        foreach($notices as $notice){
            echo "<div class='updated notice error is-dismissible'><p>$notice</p></div>";
            break;
        }
        delete_option('my_plugin_deferred_admin_notices');
    }
}
add_action('admin_notices', 'my_plugin_admin_notices');

// action on plugin deactivation 
function my_plugin_deactivation(){
    delete_option('my_plugin_acf_install_and_activate_admin_notices');
    delete_option('my_plugin_deferred_admin_notices'); 
}
register_deactivation_hook(__FILE__, 'my_plugin_deactivation');

// enqueue style and script
if(!function_exists('my_plugin_style_script')){
    /**
     * Proper way to enqueue scripts and styles.
     */
    function my_plugin_style_script(){
        // Plugin Frontend CSS
        wp_enqueue_style('main-style-css', CUSTOM_POSTS_PLUGIN_DIR_PATH .'/assets/css/main.css');
        // load bootstrap css
        wp_enqueue_style('bootstrap-style-css', CUSTOM_POSTS_PLUGIN_DIR_PATH .'/assets/css/bootstrap.min.css');
        // load bootstrap js
        wp_enqueue_script('bootstrap-script-js', CUSTOM_POSTS_PLUGIN_DIR_PATH . '/assets/js/bootstrap.min.js', 'jQuery', '1.0.0', true);
        // Plugin Frontend JS
        wp_enqueue_script('main-script-js', CUSTOM_POSTS_PLUGIN_DIR_PATH . '/assets/js/main.js', 'jQuery', '1.0.0', true);
        // Plugin AJAX JS
        wp_enqueue_script('plugin-ajax-js', CUSTOM_POSTS_PLUGIN_DIR_PATH . '/assets/js/ajax.js', array('jquery'), null, true);
        wp_localize_script('plugin-ajax-js', 'post_list_admin_URL_NAME',
           array( 
               'ajaxurl' => admin_url('admin-ajax.php')
           )
        );
    }
    add_action('wp_enqueue_scripts', 'my_plugin_style_script');
}
// Setting custom posts block for admin and display custom posts list
require plugin_dir_path(__FILE__) .'/inc/blocks/block-custom-post.php';
// Setting custom posts block for admin and display custom posts list
require plugin_dir_path(__FILE__) .'/inc/general_functions.php';
// Fire AJAX action for both logged in and non-logged in users (Load more button)
add_action('wp_ajax_get_more_posts', 'wp_loadmore_ajax_handler'); // wp_ajax_{action}
add_action('wp_ajax_nopriv_get_more_posts', 'wp_loadmore_ajax_handler'); // wp_ajax_nopriv_{action}
// Fire AJAX action (Infinite scroll)
add_action('wp_ajax_infinite_scroll_post', 'wp_infinite_scroll_ajax_handler'); // wp_ajax_{action}
add_action('wp_ajax_nopriv_infinite_scroll_post', 'wp_infinite_scroll_ajax_handler'); // wp_ajax_nopriv_{action}
?>