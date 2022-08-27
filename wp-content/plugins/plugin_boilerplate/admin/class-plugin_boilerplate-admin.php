<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://author.example.com/
 * @since      1.0.0
 *
 * @package    Plugin_boilerplate
 * @subpackage Plugin_boilerplate/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Plugin_boilerplate
 * @subpackage Plugin_boilerplate/admin
 * @author     Sohil Vahora <vohrasohil693@gmail.com>
 */
class Plugin_boilerplate_Admin {

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
	public function enqueue_styles(){
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_boilerplate_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_boilerplate_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/plugin_boilerplate-admin.css', array(), $this->version, 'all' );
		// load bootstrap css
		wp_enqueue_style("bootstrap.min.css", plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css', array(), $this->version, 'all');
		// load jQuery datatable css
		wp_enqueue_style("jquery.dataTables.min.css", plugin_dir_url( __FILE__ ) . 'css/jquery.dataTables.min.css', array(), $this->version, 'all');
		// load custom css
		wp_enqueue_style("custom.css", plugin_dir_url( __FILE__ ) . 'css/custom.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts(){
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_boilerplate_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_boilerplate_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/plugin_boilerplate-admin.js', array( 'jquery' ), $this->version, true );
		// load bootstrap js
		wp_enqueue_script("bootstrap.min.js", plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js', array( 'jquery' ), $this->version, true);
		// jquery datatable js
		wp_enqueue_script("jquery.dataTables.min.js", plugin_dir_url( __FILE__ ) . 'js/jquery.dataTables.min.js', array( 'jquery' ), $this->version, true);
		// jquery notifyBar js
		wp_enqueue_script("jquery.notifyBar.js", plugin_dir_url( __FILE__ ) . 'js/jquery.notifyBar.js', array( 'jquery' ), $this->version, true);
		// jquery validation js
		wp_enqueue_script("jquery.validate.min.js", plugin_dir_url( __FILE__ ) . 'js/jquery.validate.min.js', array( 'jquery' ), $this->version, true);
		// custom js
		wp_enqueue_script("custom.js", plugin_dir_url(__FILE__).'js/custom.js', array('jquery'), $this->version, true);
		wp_localize_script('custom.js', 'admin_ajax_custom', array('ajaxurl' => admin_url('admin-ajax.php')));
	}

	public function wp_admin_menu_section(){
	    add_menu_page('WP Playlist', 'WP Playlist', 'manage_options', 'wp-main-menu', array($this, 'wp_playlist'), 'dashicons-admin-plugins', 7);
	    add_submenu_page('wp-main-menu', 'All Playlist', 'All Playlist', 'manage_options', 'wp-main-menu', array($this, 'wp_playlist'));
	    add_submenu_page('wp-main-menu', 'Add Playlist', 'Add Playlist', 'manage_options', 'wp-add-playlist', array($this, 'wp_add_playlist'));
	}

	public function wp_playlist(){
		include_once WP_PLUGIN_DIR_PATH.'admin/partials/plugin_boilerplate-wp-menu-all-playlist.php';
	}

	public function wp_add_playlist(){
		include_once WP_PLUGIN_DIR_PATH.'admin/partials/plugin_boilerplate-wp-menu-add-playlist.php';	
	}

	public function wp_save_playlist_details(){
		if(isset($_POST['action']) && $_POST['action'] == "save_playlist_details"){
			$name = isset($_POST['name']) ? $_POST['name'] : "";
			$image_url = isset($_POST['image-url']) ? $_POST['image-url'] : "";
			$level = isset($_POST['level']) ? $_POST['level'] : "";
			// level contains array so json_encode, serialize
			$level = json_encode($level);

			global $wpdb;
			$table_name = $wpdb->prefix."playlist";
			$wpdb->insert($table_name, array(
				'name' => $name,
				'thumbnail' => $image_url,
				'playlist_for' => $level,
			));	
			// Check result
			if($wpdb->insert_id > 0){
				echo json_encode(array(
					'status' => true,
					'message' => "Playlist has been created", 
				));
			}
			else{
				echo json_encode(array(
					'status' => true,
					'message' => "Failed to create playlist", 
				));
			}
		}
		wp_die();
	}

	public function wp_delete_playlist_details(){
		if(isset($_POST['action']) && $_POST['action'] == "delete_playlist_details"){
			global $wpdb;
			$table_name = $wpdb->prefix."playlist";
			$delete_id = $_POST['delete_id'];

			$is_exists = $wpdb->get_row(
				$wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $delete_id), ARRAY_A);

			if(!empty($is_exists)){
				$wpdb->delete($table_name, array(
					'id' => $delete_id
				));

				// echo WP_PLUGIN_DIR_PATH;
				ob_start(); // Start the buffer
				include_once WP_PLUGIN_DIR_PATH.'/admin/partials/templates/plugin-template-all-playlist.php';
				// read the buffer
				$template = ob_get_contents();
				// close the buffer
				ob_end_clean();

				echo json_encode(array(
					"status" => true, 
					"message" => "Playlist has been deleted", 
					"template" => $template
				));


			}
			else{
				echo json_encode(array(
					"status" => false, 
					"message" => "No record found"
				));
			}
		}
		wp_die();
	}
}