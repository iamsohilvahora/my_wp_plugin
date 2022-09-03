<?php
/*
 * Plugin Name: CRUD plugin
 * Description: Plugin for crud operation
 * Version: 1.0.0
 * Author: Sohil Vahora
 * Author URI: http://example.com
 */

// creating database table when plugin is activated
global $db_version;
$db_version = '1.0';

function create_data_table() {
    global $wpdb;
    global $db_version;
    $table_name = $wpdb->prefix.'employee_list';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		name tinytext NOT NULL,
		address text NOT NULL,
		role text NOT NULL,
		contact bigint(12), 
		PRIMARY KEY (id)
	) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    add_option('db_version', $db_version);
}
register_activation_hook(__FILE__, 'create_data_table');

// create admin menu in wp-admin 
function add_crud_menu() {
    //adding menu
    add_menu_page('employee_list', //page title
        'Employee Listing', //menu title
        'manage_options', //capabilities
        'Employee_Listing', //menu slug
        'employee_list' //function
    );
    //adding submenu to a menu
    add_submenu_page('Employee_Listing',//parent page slug
        'employee_insert',//page title
        'Employee Insert',//menu title
        'manage_options',//manage optios
        'Employee_Insert',//slug
        'employee_insert'//function
    );
    add_submenu_page(null,//parent page slug
        'employee_update',//$page_title
        'Employee Update',// $menu_title
        'manage_options',// $capability
        'Employee_Update',// $menu_slug,
        'employee_update'// $function
    );
    add_submenu_page(null,//parent page slug
        'employee_delete',//$page_title
        'Employee Delete',// $menu_title
        'manage_options',// $capability
        'Employee_Delete',// $menu_slug,
        'employee_delete'// $function
    );
}
add_action('admin_menu', 'add_crud_menu');

// returns the root directory path of particular plugin
define('ROOT_DIR', plugin_dir_path(__FILE__));
require_once(ROOT_DIR.'employee_list.php');
require_once(ROOT_DIR.'employee_insert.php');
require_once(ROOT_DIR.'employee_update.php');
require_once(ROOT_DIR.'employee_delete.php');
?>