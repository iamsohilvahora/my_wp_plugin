<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://author.example.com/
 * @since      1.0.0
 *
 * @package    Plugin_boilerplate
 * @subpackage Plugin_boilerplate/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Plugin_boilerplate
 * @subpackage Plugin_boilerplate/includes
 * @author     Sohil Vahora <vohrasohil693@gmail.com>
 */
class Plugin_boilerplate_Deactivator{
	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
        global $wpdb;
        $table_name = $wpdb->prefix."playlist";
        $sql = "DROP TABLE IF EXISTS $table_name";
        $wpdb->query($sql);

        if(!empty(get_option('plugin_page'))){
            $page_id = get_option('plugin_page');
            wp_delete_post($page_id, true); // wp_posts
            delete_option('plugin_page', $page_id); // wp_options
        }
	}
}
