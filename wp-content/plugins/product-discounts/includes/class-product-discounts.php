<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://author.example.com/
 * @since      1.0.0
 *
 * @package    Product_Discounts
 * @subpackage Product_Discounts/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Product_Discounts
 * @subpackage Product_Discounts/includes
 * @author     Sohil Vahora <sohil.vahora@bytestechnolab.com>
 */
class Product_Discounts {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Product_Discounts_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The plugin setting link.
	*/
	protected $plugin_setting_link;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct($plugin_setting_link) {
		if ( defined( 'PRODUCT_DISCOUNTS_VERSION' ) ) {
			$this->version = PRODUCT_DISCOUNTS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'product-discounts';
		$this->plugin_setting_link = $plugin_setting_link;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Product_Discounts_Loader. Orchestrates the hooks of the plugin.
	 * - Product_Discounts_i18n. Defines internationalization functionality.
	 * - Product_Discounts_Admin. Defines all hooks for the admin area.
	 * - Product_Discounts_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-product-discounts-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-product-discounts-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-product-discounts-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-product-discounts-public.php';

		$this->loader = new Product_Discounts_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Product_Discounts_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Product_Discounts_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks(){
		$plugin_admin = new Product_Discounts_Admin($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' ); // load admin css
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' ); // load admin js
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'register_custom_menu_page' ); // load admin menu page

		// initialize screen option
		$this->loader->add_action("load-toplevel_page_product_discounts", $plugin_admin, 'wp_tbl_add_options');

		// get saved screen meta value
		$this->loader->add_filter("set-screen-option", $plugin_admin, 'wp_table_set_option', 10, 3);

		// load hooks for insert product discount
		$this->loader->add_action( 'wp_ajax_save_product_discounts_details', $plugin_admin, 'wp_save_product_discounts_details' );
		$this->loader->add_action( 'wp_ajax_nopriv_save_product_discounts_details', $plugin_admin, 'wp_save_product_discounts_details' );

		// load hooks for delete product discount
		$this->loader->add_action( 'wp_ajax_delete_product_discounts_details', $plugin_admin, 'wp_delete_product_discounts_details' );
		$this->loader->add_action( 'wp_ajax_nopriv_delete_product_discounts_details', $plugin_admin, 'wp_delete_product_discounts_details' );

		// load hooks for update product discount
		$this->loader->add_action( 'wp_ajax_update_product_discounts_details', $plugin_admin, 'wp_update_product_discounts_details' );
		$this->loader->add_action( 'wp_ajax_nopriv_update_product_discounts_details', $plugin_admin, 'wp_update_product_discounts_details' );
		
		// show admin notice
		// $this->loader->add_action( 'admin_notices', $plugin_admin, 'sample_admin_notice__success' );

		/*** Add tab to Product Data area of Edit Product page ***/
		// Add new 'Product Discount' tab to Product Data area of Edit Product page.
		$this->loader->add_filter('woocommerce_product_data_tabs', $plugin_admin, 'wc_product_discount_tab', 10, 1);

		// Add 'Product Discount' tab content
		$this->loader->add_action('woocommerce_product_data_panels', $plugin_admin, 'wc_show_product_discount_tab_content');

		// Save 'Product Discount' data to post meta
		$this->loader->add_action('save_post_product', $plugin_admin, 'product_discount_save_data');
		// add plugin setting link filter	
		$this->loader->add_filter("plugin_action_links_$this->plugin_setting_link", $plugin_admin, "load_plugin_settings_link", 10, 1);
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks(){
		$plugin_public = new Product_Discounts_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles'); // load css
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts'); // load js
		$this->loader->add_action('woocommerce_after_add_to_cart_form', $plugin_public, 'wc_show_product_discount_func'); // show content after add to cart button on single product detail page
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run(){
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Product_Discounts_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
