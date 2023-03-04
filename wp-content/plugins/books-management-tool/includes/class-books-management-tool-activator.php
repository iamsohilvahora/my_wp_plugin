<?php

/**
 * Fired during plugin activation
 *
 * @link       https://https://www.google.com
 * @since      1.0.0
 *
 * @package    Books_Management_Tool
 * @subpackage Books_Management_Tool/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Books_Management_Tool
 * @subpackage Books_Management_Tool/includes
 * @author     Sohil Vahora <vohrasohil693@gmail.com>
 */
class Books_Management_Tool_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        global $wpdb;
        $book_table_name = $wpdb->prefix."books";
        $charset_collate = $wpdb->get_charset_collate();
        $book_query = "CREATE TABLE IF NOT EXISTS $book_table_name (
                     `id` int NOT NULL AUTO_INCREMENT,
                     `name` varchar(150) DEFAULT NULL,
                     `amount` int DEFAULT NULL,
                     `description` text,
                     `book_image` varchar(200) DEFAULT NULL,
                     `publication` varchar(150) DEFAULT NULL,
                     `email` varchar(150) DEFAULT NULL,
                     `shelf_id` int DEFAULT NULL,
                     `status` int NOT NULL DEFAULT '1',
                     `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                     PRIMARY KEY (`id`)
                    ) $charset_collate";

        $book_shelf_table_name = $wpdb->prefix."book_shelf";
        $book_shelf_query = "CREATE TABLE IF NOT EXISTS $book_shelf_table_name (
                     `id` int NOT NULL AUTO_INCREMENT,
                     `shelf_name` varchar(150) NOT NULL,
                     `capacity` varchar(200) NOT NULL,
                     `shelf_location` varchar(200) NOT NULL,
                     `status` int NOT NULL DEFAULT '1',
                     `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                     PRIMARY KEY (`id`)
                    ) $charset_collate";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($book_query);
        dbDelta($book_shelf_query);

        /* *** Create page on plugin activation *** */
        $book_page = array(
            'post_title'    => wp_strip_all_tags('Book Tool'),
            'post_name' => 'book_tool',
            'post_content'  => 'Simple page content for book tool',
            'post_status'   => 'publish',
            'post_author'   => 1,
            'post_type'     => 'page',
        );
        /* On plugin activation, set the default template */
        if(!empty(get_option('book_plugin_template'))){}
        else{
            $book_page_id = wp_insert_post($book_page);
            add_option('book_page_id', $book_page_id);
            add_option('book_plugin_template', 'book-plugin-template');
        }
	}

}
