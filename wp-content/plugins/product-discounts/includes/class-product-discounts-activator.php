<?php
class Product_Discounts_Activator{
	/**
	 * On plugin activation create prefix_product_discounts table
	 */
	public static function activate(){
        global $wpdb;
        $table_name = $wpdb->prefix."product_discounts";
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `discount_name` varchar(255) NOT NULL,
                    `discount_value` varchar(255) NOT NULL,
                    `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
                    ) $charset_collate";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // Check WooCommerce plugin is active or not
        if(current_user_can('activate_plugins') && !is_plugin_active('woocommerce/woocommerce.php')){
            // Deactivate the plugin.
            deactivate_plugins(plugin_basename(__FILE__));

            $notices = get_option('my_plugin_woocommerce_install_and_activate_admin_notices', array());
            
            $notices[] = 'Please install and activate WooCommerce plugin <a href="' . esc_url('https://wordpress.org/plugins/woocommerce' ) . '" target="_blank">WooCommerce</a>';

            update_option('my_plugin_woocommerce_install_and_activate_admin_notices', $notices);
        }
	}
}
