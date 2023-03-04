<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://https://www.google.com
 * @since      1.0.0
 *
 * @package    Books_Management_Tool
 * @subpackage Books_Management_Tool/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Books_Management_Tool
 * @subpackage Books_Management_Tool/admin
 * @author     Sohil Vahora <vohrasohil693@gmail.com>
 */
class Books_Management_Tool_Admin {

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
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Books_Management_Tool_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Books_Management_Tool_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . '/css/books-management-tool-admin.css', array(), $this->version, 'all' );

		$valid_pages = array("book-management-dashboard", "book-management-create", "book-management-list", "book-management-create-book-shelf", "book-management-list-book-shelf");
		$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : "";
		if(in_array($page, $valid_pages)){
			wp_enqueue_style( "bootstrap.min.css", BOOKS_MANAGEMENT_TOOL_URL . 'assets/css/bootstrap.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( "jquery.dataTables.min.css", BOOKS_MANAGEMENT_TOOL_URL . 'assets/css/jquery.dataTables.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( "sweetalert.min.css", BOOKS_MANAGEMENT_TOOL_URL . 'assets/css/sweetalert.min.css', array(), $this->version, 'all' );
		}

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
		 * defined in Books_Management_Tool_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Books_Management_Tool_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( "books-management-tool-admin", plugin_dir_url( __FILE__ ) . 'js/books-management-tool-admin.js', array( 'jquery' ), $this->version, false );

		wp_localize_script("books-management-tool-admin" , "tech_book", array(
			"author" => "Sohil Vahora",
			"ajax_url" => admin_url("admin-ajax.php"),
		));

		$valid_pages = array("book-management-dashboard", "book-management-create", "book-management-list", "book-management-create-book-shelf", "book-management-list-book-shelf");
		$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : "";
		if(in_array($page, $valid_pages)){
			wp_enqueue_script( "bootstrap.min.js", BOOKS_MANAGEMENT_TOOL_URL . 'assets/js/bootstrap.min.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( "jquery.dataTables.min.js", BOOKS_MANAGEMENT_TOOL_URL . 'assets/js/jquery.dataTables.min.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( "sweetalert.min.js", BOOKS_MANAGEMENT_TOOL_URL . 'assets/js/sweetalert.min.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( "jquery.validate.min.js", BOOKS_MANAGEMENT_TOOL_URL . 'assets/js/jquery.validate.min.js', array( 'jquery' ), $this->version, false );
		}

	}

	// admin menu page
	public function bmt_create_admin_menu_page(){
		add_menu_page('Books management tool', 'Books management tool', 'manage_options', 'book-management-dashboard', array($this, 'bmt_book_management_dashboard'), 'dashicons-admin-site-alt', 22);

		add_submenu_page('book-management-dashboard', 'Dashboard', 'Dashboard', 'manage_options', 'book-management-dashboard', array($this, 'bmt_book_management_dashboard'));

		add_submenu_page('book-management-dashboard', 'Create book shelf', 'Create book shelf', 'manage_options', 'book-management-create-book-shelf', array($this, 'bmt_book_management_create_book_shelf'));
		add_submenu_page('book-management-dashboard', 'List book shelf', 'List book shelf', 'manage_options', 'book-management-list-book-shelf', array($this, 'bmt_book_management_list_book_shelf'));

		
		add_submenu_page('book-management-dashboard', 'Create book', 'Create book', 'manage_options', 'book-management-create', array($this, 'bmt_book_management_create'));
		add_submenu_page('book-management-dashboard', 'List book', 'List book', 'manage_options', 'book-management-list', array($this, 'bmt_book_management_list'));
	}

	// books management dashboard
	public function bmt_book_management_dashboard(){
		echo "Dashboard";
	}

	// Create book
	public function bmt_book_management_create(){
		global $wpdb;
		$book_shelf = $wpdb->get_results($wpdb->prepare("select * FROM ".$wpdb->prefix."book_shelf"));
		
		ob_start(); // started buffer

		include_once(BOOKS_MANAGEMENT_TOOL_PATH . "admin/partials/bmt_create_book.php"); // include template file

		$template = ob_get_contents(); // reading content

		ob_end_clean(); // closing and cleaning buffer

		echo $template;
	}

	// list of book
	public function bmt_book_management_list(){
		global $wpdb;
		$book_list = $wpdb->get_results($wpdb->prepare("select books.*, book_shelf.shelf_name FROM ".$wpdb->prefix."books as books INNER JOIN ".$wpdb->prefix."book_shelf as book_shelf ON books.shelf_id = book_shelf.id ORDER BY ID DESC"));

		ob_start(); // started buffer

		include_once(BOOKS_MANAGEMENT_TOOL_PATH . "admin/partials/bmt_list_book.php"); // include template file

		$template = ob_get_contents(); // reading content

		ob_end_clean(); // closing and cleaning buffer

		echo $template;
	}

	// create book shelf
	public function bmt_book_management_create_book_shelf(){
		ob_start(); // started buffer

		include_once(BOOKS_MANAGEMENT_TOOL_PATH . "admin/partials/bmt_create_book_shelf.php"); // include template file

		$template = ob_get_contents(); // reading content

		ob_end_clean(); // closing and cleaning buffer

		echo $template;
	}

	// list book shelf
	public function bmt_book_management_list_book_shelf(){
		global $wpdb;
		$book_shelf = $wpdb->get_results($wpdb->prepare("select * FROM ".$wpdb->prefix."book_shelf"));

		ob_start(); // started buffer

		include_once(BOOKS_MANAGEMENT_TOOL_PATH . "admin/partials/bmt_list_book_shelf.php"); // include template file

		$template = ob_get_contents(); // reading content

		ob_end_clean(); // closing and cleaning buffer

		echo $template;
	}

	// handle admin ajax request
	public function bmt_handle_admin_ajax_request(){
		$param = isset($_REQUEST['param']) ? $_REQUEST['param'] : "";
		if(!empty($param)){
			if($param == "first_simple_ajax"){
				echo json_encode(array(
					"status" => 1,
					"message" => "First AJAX request",
					"data" => array(
						"Author" => "Sohil Vahora",
						"Site" => "https://www.google.com"
					),	
				));
			}
			elseif($param == "create_book_shelf"){
				$txt_name = isset($_POST['txt_name']) ? $_POST['txt_name'] : "";
				$txt_capacity = isset($_POST['txt_capacity']) ? $_POST['txt_capacity'] : "";
				$txt_location = isset($_POST['txt_location']) ? $_POST['txt_location'] : "";
				$book_status = isset($_POST['book_status']) ? $_POST['book_status'] : "";

				global $wpdb;
				$wpdb->insert($wpdb->prefix."book_shelf", array(
						"shelf_name" => $txt_name,
						"capacity" => $txt_capacity,
						"shelf_location" => $txt_location,
						"status" => $book_status
				));

				if($wpdb->insert_id > 0){
					echo json_encode(array(
						"status" => 1,
						"message" => "Bookshelf created successfully."
					));
				}
				else{
					echo json_encode(array(
						"status" => 0,
						"message" => "Failde to create Bookshelf."
					));
				}
			}
			elseif($param == "delete_book_shelf"){
				$form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : "";
				global $wpdb;
				if($form_id > 0){
					$wpdb->delete($wpdb->prefix."book_shelf", array(
						"id" => $form_id
					));
					echo json_encode(array(
						"status" => 1,
						"message" => "Bookshelf deleted successfully."
					));
				}
				else{
					echo json_encode(array(
						"status" => 0,
						"message" => "Failed to delete Bookshelf."
					));
				}
			}
			elseif($param == "frm_create_book"){
				$bmt_book_shelf = isset($_POST['bmt_book_shelf']) ? intval($_POST['bmt_book_shelf']) : "";
				$book_name = isset($_POST['book_name']) ? $_POST['book_name'] : "";
				$user_email = isset($_POST['user_email']) ? $_POST['user_email'] : "";
				$book_publication = isset($_POST['book_publication']) ? $_POST['book_publication'] : "";
				$book_description = isset($_POST['book_description']) ? $_POST['book_description'] : "";
				$book_image = isset($_POST['book_cover_image']) ? $_POST['book_cover_image'] : "";
				$book_cost = isset($_POST['book_cost']) ? intval($_POST['book_cost']) : "";
				$book_status = isset($_POST['book_status']) ? intval($_POST['book_status']) : ""; 

				global $wpdb;
				$wpdb->insert($wpdb->prefix."books", array(
						"name" => strtolower($book_name),
						"amount" => $book_cost,
						"description" => $book_description,
						"book_image" => $book_image,
						"email" => $user_email,
						"shelf_id" => $bmt_book_shelf,
						"status" => $book_status,
				));

				if($wpdb->insert_id > 0){
					echo json_encode(array(
						"status" => 1,
						"message" => "Book created successfully."
					));
				}
				else{
					echo json_encode(array(
						"status" => 0,
						"message" => "Failed to create Book."
					));
				}
			}
			elseif($param == "delete_book"){
				$form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : "";
				global $wpdb;
				if($form_id > 0){
					$wpdb->delete($wpdb->prefix."books", array(
						"id" => $form_id
					));
					echo json_encode(array(
						"status" => 1,
						"message" => "Book deleted successfully."
					));
				}
				else{
					echo json_encode(array(
						"status" => 0,
						"message" => "Failed to delete Book."
					));
				}
			}
		}
		exit();
	}


}
