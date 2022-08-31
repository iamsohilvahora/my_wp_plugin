<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://author.example.com/
 * @since      1.0.0
 *
 * @package    Product_Discounts
 * @subpackage Product_Discounts/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Product_Discounts
 * @subpackage Product_Discounts/includes
 * @author     Sohil Vahora <sohil.vahora@bytestechnolab.com>
 */
class Product_Discounts_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
        delete_option('my_plugin_woocommerce_install_and_activate_admin_notices'); 
        delete_option('my_plugin_deferred_admin_notices'); 
	}  

}
