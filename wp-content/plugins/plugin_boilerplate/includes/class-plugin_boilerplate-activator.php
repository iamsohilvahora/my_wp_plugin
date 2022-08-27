<?php

/**
 * Fired during plugin activation
 *
 * @link       https://author.example.com/
 * @since      1.0.0
 *
 * @package    Plugin_boilerplate
 * @subpackage Plugin_boilerplate/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Plugin_boilerplate
 * @subpackage Plugin_boilerplate/includes
 * @author     Sohil Vahora <vohrasohil693@gmail.com>
 */
class Plugin_boilerplate_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate(){
        global $wpdb;
        $table_name = $wpdb->prefix . "playlist";
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
          id int(11) NOT NULL AUTO_INCREMENT,
          name varchar(255) DEFAULT NULL,
          thumbnail varchar(255) DEFAULT NULL,
          playlist_for text DEFAULT NULL,
          PRIMARY KEY (id)
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // call function for creating dynamic page
        Plugin_boilerplate_Activator::create_dynamic_page();
	}
  function create_dynamic_page(){
      global $wpdb;
      $table_name = $wpdb->prefix."post";
      // Create post object (For create dynamic page)
      $is_slug_exists = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM $table_name WHERE post_name = %s", 'plugin_development_page'), ARRAY_A);

      if(empty($is_slug_exists)){
          $page = array(
            'post_title' => 'Plugin Development Page',
            'post_content' => 'This is a page for plugin development',
            'post_status' => 'publish',
            'post_name' => 'plugin_development_page',
            'post_type' => 'page',
          );
          // Insert the post into the database
          $post_id = wp_insert_post($page); // insert data into wp_posts table
          update_option('plugin_page', $post_id); // insert data into wp_options table                      
      }
  }
}
