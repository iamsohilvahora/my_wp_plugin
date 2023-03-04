<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://https://www.google.com
 * @since      1.0.0
 *
 * @package    Books_Management_Tool
 * @subpackage Books_Management_Tool/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Books_Management_Tool
 * @subpackage Books_Management_Tool/includes
 * @author     Sohil Vahora <vohrasohil693@gmail.com>
 */
class Books_Management_Tool_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
        // delete table on plugin deactivation
        global $wpdb;
        $book_table_name = $wpdb->prefix."books";
        $book_shelf_table_name = $wpdb->prefix."book_shelf";

        $wpdb->query("DROP TABLE IF EXISTS $book_table_name");
        $wpdb->query("DROP TABLE IF EXISTS $book_shelf_table_name");
	}

}
