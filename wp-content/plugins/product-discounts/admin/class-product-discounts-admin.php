<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://author.example.com/
 * @since      1.0.0
 *
 * @package    Product_Discounts
 * @subpackage Product_Discounts/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Product_Discounts
 * @subpackage Product_Discounts/admin
 * @author     Sohil Vahora <sohil.vahora@bytestechnolab.com>
 */
class Product_Discounts_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	public $table;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Product_Discounts_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Product_Discounts_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/product-discounts-admin.css', array(), $this->version, 'all' );

		// load bootstrap css
		wp_enqueue_style( 'bootstrap.min.js', plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Product_Discounts_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Product_Discounts_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/product-discounts-admin.js', array( 'jquery' ), $this->version, false );

		// load bootstrap JS
		wp_enqueue_script( 'bootstrap.min.js', plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js', array('jquery'), $this->version, true );

		// load main js file
		wp_enqueue_script( 'main.js', plugin_dir_url( __FILE__ ) . 'js/main.js', array('jquery'), $this->version, true );
		wp_localize_script( 'main.js', 'plugin_ajax_object',
		   array( 
		       'ajaxurl' => admin_url( 'admin-ajax.php' )
		   )
		);


	}

	public function register_custom_menu_page(){
		// menu page for list of product discounts  
		$this->hook = add_menu_page('Product Discounts',
		    'Product Discounts',
		    'manage_options',
		    'product_discounts',
		    array($this, 'wp_show_product_discounts'),
		    'dashicons-money-alt',
		    6
		);
		
		
	}

	// list of product discount
	public function wp_show_product_discounts(){
		if($_GET['action'] == 'add' || $_GET['action'] == 'edit'){
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/product-discounts-admin-add-update.php';
		}
		else{
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/product-discounts-admin-display.php';
			$this->table = new ProductDiscountsTableList();
		}
	}

	function wp_tbl_add_options(){
		$option = 'per_page';
		$args = array(
		    'label' => 'Items per page',
		    'default' => 5,
		    'option' => 'items_per_page'
		);
		add_screen_option($option, $args);
		$empTable = $this->table;
	}

	// insert product discount data into the database
	public function wp_save_product_discounts_details(){
		if($_POST['action'] == 'save_product_discounts_details'){
			$discount_name = $_POST['discount_name'];
			$discount_value = $_POST['discount_value'];

			global $wpdb;
			$table_name = $wpdb->prefix."product_discounts";
			// insert product discount
			$result = $wpdb->insert($table_name, array("discount_name" => $discount_name, "discount_value" => $discount_value), array("%s", "%s"));
			if(!empty($result)){
				$message = '<div class="notice notice-success is-dismissible" id="product-discount-status"><p>Product discount added successfully</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>'; 

				$response = array("status" => true, "message" => $message);
				echo wp_json_encode($response);  
				wp_die();
			}
			else{
				$message = '<div class="notice notice-warning is-dismissible" id="product-discount-status"><p>Error to insert data</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
				$response = array("status" => false, "message" => $message);
				echo wp_json_encode($response);  
				wp_die();
			}
		}
	}

	// delete product discount data from database
	public function wp_delete_product_discounts_details(){
		if($_POST['action'] == 'delete_product_discounts_details'){
			$delete_id = $_POST['delete_id'];
			global $wpdb;
			$table_name = $wpdb->prefix."product_discounts";
			// delete query
			$result = $wpdb->delete($table_name, array('id' => $delete_id));

			if(!empty($result)){
				$message = '<div class="notice notice-success is-dismissible" id="product-discount-status"><p>Discount details deleted successfully</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
				$response = array("status" => true, "message" => $message);
				echo wp_json_encode($response);  
				wp_die();
			}
			else{
				$message = '<div class="notice notice-warning is-dismissible" id="product-discount-status"><p>Error to delete data</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
				$response = array("status" => false, "message" => $message);
				echo wp_json_encode($response);  
				wp_die();
			}
		}
	}

	// Update product discount data into the database
	public function wp_update_product_discounts_details(){
		if($_POST['action'] == 'update_product_discounts_details'){
			$edit_id = $_POST['edit_id'];
			$discount_name = $_POST['discount_name'];
			$discount_value = $_POST['discount_value'];

			global $wpdb;
			$table_name = $wpdb->prefix."product_discounts";
			// update query
			$result = $wpdb->update($table_name, 
			    array( 
			        'discount_name' => $discount_name,
			        'discount_value' => $discount_value,
			    ), 
			    array(
			        "id" => $edit_id
			    ) 
			);

			if(!empty($result)){
				$message = '<div class="notice notice-success is-dismissible" id="product-discount-status"><p>Discount details updated successfully</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
				</div>';

				$response = array("status" => true, "message" => $message);
				echo wp_json_encode($response);  
				wp_die();
			}
			else{
				$message = '<div class="notice notice-warning is-dismissible" id="product-discount-status"><p>Error to update data - please change value</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
				$response = array("status" => false, "message" => $message);
				echo wp_json_encode($response);  
				wp_die();
			}
		}
	}

	// Show admin notice
	public function sample_admin_notice__success(){
		?>
		<div class="notice is-dismissible" id="show_data_status" style="display: none;">
		    <p></p>
		</div>
		<?php
	}

	// Show product discount tab
	public function wc_product_discount_tab($tabs){
		// Product discount tab
		$tabs['product_discount'] = array(
						'label'    => 'Product Discount',
						'target'   => 'discount_product_data',
						'priority' => 92,
					);
		return $tabs;
	}

	// Show product discount tab's content
	public function wc_show_product_discount_tab_content(){ ?>
		<div id="discount_product_data" class="panel woocommerce_options_panel">
		<?php
			global $wpdb;
			$table_name = $wpdb->prefix."product_discounts";
			$product_discounts = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
			$product_id = get_the_id(); // get product id
			// Get the selected value
			$value = get_post_meta($product_id, '_product_discounts', true);
			if(empty($value)){
				$value = '';
			}
			// Run of product discount details is not empty
			if($product_discounts):
				$options[''] = __('Select a discount value', 'woocommerce'); // default value
			    foreach($product_discounts as $discount){
			    	$term =  $discount['discount_value'];
			        $options[$term] = $term;
				}
			    echo '<div class="options_group">';
			    woocommerce_wp_select(array(
			        'id'      => '_product_discounts',
			        'label'   => __('Choose Discount Value', 'woocommerce'),
			        'options' =>  $options,
			        'value'   => $value,
			    ));
			    echo '</div>';
			else:
				echo "<p>Product Discount Details Not Found</p>";
			endif;
		?>
		</div>
	<?php
	}

	// Save 'Product Discount' data to post meta
	function product_discount_save_data($product_id){
	    global $pagenow, $typenow;
	    if('post.php' !== $pagenow || 'product' !== $typenow) return;
	    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	    // Save product discount data
	    if(isset($_POST['_product_discounts'])){
			if($_POST['_product_discounts']){
				update_post_meta($product_id, '_product_discounts', $_POST['_product_discounts']);
			}
	    }
	    else{
			delete_post_meta($product_id, '_product_discounts');
		}
	}

	// load plugin setting link
	function load_plugin_settings_link($links){
		$links[] = '<a href="' .
		    admin_url('admin.php?page=product_discounts') .
		    '">' . __('Settings') . '</a>';
		return $links; 
	}

	// get saved screen option meta values 
	function wp_table_set_option($status, $option, $value){
		return $value;
	}

}
