<?php
/**
 * Register a custom menu page.
 */
function wp_custom_menu_page(){
    add_menu_page('Default Post Data',
        'Post Database',
        'manage_options',
        'post_db_slug',
        'post_db_page_html',
        'dashicons-database-view',
        25
    );
}

function post_db_page_html(){
	require get_template_directory() .'/post-templates/admin-post-data.php';	    
}
add_action('admin_menu', 'wp_custom_menu_page');

// Call this function when wordpress load using init action hooks
function wp_export_all_posts(){
	global $post;
    if(isset($_GET['export_all_posts'])){
        if(isset($_GET['start_date']) && isset($_GET['end_date'])){
    		$start_date = date('Y-m-d', strtotime($_GET['start_date']));
    		$end_date = date('Y-m-d', strtotime($_GET['end_date']));
    		// query for get total post using date filter
    		$post_total_query = new WP_Query(
    			array(
    				'post_type' => 'post',
    				'post_status' => 'publish',
    				'posts_per_page' => -1,
    			    'date_query' => array(
    			        array(
    			            'after'     => $start_date,
    			            'before'    => $end_date,
    			            'inclusive' => true,
    			        ),
    			    ),
    			)
    		);
    	}
    	else{
    		// query for get total post
    		$post_total_query = new WP_Query(
    			array(
    				'post_type' => 'post',
    				'post_status' => 'publish',
    				'posts_per_page' => -1,
    		));
    	}
    	// code for export post in csv file
		if($post_total_query->have_posts()):
			header('Content-type: text/csv');
            header('Content-Disposition: attachment; filename="wp-posts.csv"');
            header('Pragma: no-cache');
            header('Expires: 0');
            // open the "output" stream
			$file = fopen('php://output', 'w');  
			fputcsv($file, array('Post Title', 'Post Excerpt', 'Post Status', 'Post Date', 'Post author'));
			while($post_total_query->have_posts()):
				$post_total_query->the_post();
				fputcsv($file, array($post->post_title, substr($post->post_excerpt, 0, 50), $post->post_status, date("F j, Y", strtotime($post->post_date)), get_the_author_meta('display_name', $post->post_author)));
			endwhile;
			exit();
		endif;
    }
}
add_action('init', 'wp_export_all_posts');

// Year shortcode
function year_shortcode(){
	$year = date('Y');
	return $year;
}
add_shortcode('year', 'year_shortcode');

// Set character limit for post title and post content
function wp_insert_post_func($post){
	// If post status is trash then do nothing
	if($post->post_status == 'trash' || $post->post_status == 'inherit') return;

	// verify this is not an auto save routine. 
    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

	// Get post title and post content
	$title = $post->post_title;
	$content = $post->post_content;

	// echo "<br />";
	// echo $post_id;
	// echo "<br />";
	// echo $title;
	// echo "<br />";
	// echo $content;

	// wp_die(__('vcvc characters.'));
	
	// get character length of post title and post content
	// $title_length = strlen(trim($title));
	// $content_length = strlen(trim(strip_tags($content)));

	// // check if post title is not empty 
	// if(!empty(trim($title))){
	// 	if($title_length > 300){ // maximum of 300 characters
	//     	wp_die(__('Your post title length is maximum 300 characters.'));
	// 	}
	// }
	
	// // check if post content is not empty
	// if(!empty(trim($content))){
	// 	if($content_length <= 300){ // minimum of 300 characters
	// 	    wp_die(__('Your post content length is minimum 300 characters.'));
	// 	}
	// }
}
// add_action('publish_post', 'wp_post_max_letter_count', 1);
// add_action('save_post', 'wp_insert_post_func',10, 2);

// General setting acf for option page
if(function_exists('acf_add_options_page')){
	acf_add_options_page(array(
		'page_title' 	=> 'General Settings',
		'menu_title'	=> 'General Settings',
		'menu_slug' 	=> 'general-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));
}

/**
 * Custom elementor widget.
 */
function wp_load_elementor_widget_func(){
	if(did_action('elementor/loaded')){
		require get_template_directory() . '/inc/elementor_posts_widget.php'; // get posts widget
		require get_template_directory() . '/inc/elementor_wpcf7_widget.php'; // get wpcf7 widget
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new ElementorPostsWidget());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new ElementorWPCF7Widget());
	}
}
add_action('init', 'wp_load_elementor_widget_func');

/**
 * register cpt and taxonomy
 */
require get_template_directory(). '/inc/custom_posts_type.php';

// Add the custom columns to the projects post type
function set_custom_edit_projects_columns($columns){
	$columns = array(
		// 'cb' => $columns['cb'],
		'title' => __('Title'),
		'shortcode' => __('Shortcode'),
		'taxonomy' => __('Taxonomy'),
		'thumbnail' => __('Thumbnail'),
		'author' => __('Author'),
		'date' => __('Date'),
	);
    return $columns;
}
add_filter('manage_projects_posts_columns', 'set_custom_edit_projects_columns');

// Add the data to the custom columns for the projects post type:
function custom_projects_column($column, $post_id){
    switch($column){
        case 'taxonomy':
            echo get_the_term_list($post_id , 'project-type' , '' , ' - ' , '' );
            break;
        case 'thumbnail':
            echo get_the_post_thumbnail($post_id, array(32, 32)); 
            break;
        case 'shortcode':    
            echo "[show-project id='{$post_id}' title='".get_the_title($post_id)."']"; 
            break;
    }
}
add_action('manage_projects_posts_custom_column', 'custom_projects_column', 10, 2);

// add shortcode for display projects type
function wp_show_project_type_func($atts){
    $atts = shortcode_atts(
        array(
            'id' => '1',
            'title' => 'default title',
        ), $atts, 'show-project');
    return 'id: '.esc_html($atts['id']).' - title: '.esc_html($atts['title']);
}
add_shortcode('show-project', 'wp_show_project_type_func');

// Make a Column Sortable
function wp_sortable_projects_column($columns){
    $columns['taxonomy'] = 'Taxonomy';
    $columns['author'] = 'Author';
    //To make a column 'un-sortable' remove it from the array
    unset($columns['date']);
    return $columns;
}
add_filter('manage_edit-projects_sortable_columns', 'wp_sortable_projects_column');

/**
 * register required and recommended plugin.
 */
require get_template_directory(). '/inc/required-plugins.php';

/**
 * register custom wp widget
 */
require get_template_directory() . '/inc/custom-wp-widget.php';
require get_template_directory() . '/inc/custom-category-widget.php';
require get_template_directory() . '/inc/custom-product-widget.php';
function wp_register_custom_widget(){
	register_widget('demo_widget');
	register_widget('Category_List_Widget');
}
add_action('widgets_init', 'wp_register_custom_widget');

// verify contact form 7 data (google recaptcha)
function save_contact_form_7_data(){
	// Server side validation
	function isValid(){
	   // This is the most basic validation for demo purposes. Replace this with your own server side validation
	   if($_POST['your-name'] != "" && $_POST['your-email'] != "" && $_POST['your-subject'] != "" && $_POST['your-message'] != ""){
	   	   return true;
	   }
	   else{
	       return false;
	   }
	}
	// email validation
	function emailValidation($email) {
	    $regex = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,10})$/";
	    $email = strtolower($email);
	    return preg_match($regex, $email);
	}
	$error_output = '';
	$success_output = '';
	// Execute if all field fulfill all condition
	if(isValid()){
		if(emailValidation($_POST['your-email'])){
    		// Build POST request to get the reCAPTCHA v3 score from Google
			$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
			$recaptcha_secret = RECAPTCHA_SECRET_KEY; // Insert your secret key here
			$recaptcha_response = $_POST['recaptcha_response'];	 
			// Make the POST request
			$recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
			// Converts it into a PHP object
			$data = json_decode($recaptcha);
			if($data->success == true && $data->action == "contact_form7_submission"){
		   		$success_output = "Your message sent successfully";
		   		$data = "<div>
		   			Name:". $_POST['your-name'] ."<br/>
		   			Email:". $_POST['your-email'] ."<br/>
		   			Subject:". $_POST['your-subject'] ."<br/>
		   			Message:". $_POST['your-message'] ."<br/>
		   		</div>";
			}
		}
		else{
    		$error_output = "Please enter valid email address";
    		$data = "";
		}
	}else{
	   // Server side validation failed
	   $error_output = "Please fill all the required fields";
	   $data = "";
	}
	$output = array(
	   'error'     =>  $error_output,
	   'success'   =>  $success_output,
	   'data' => $data,
	);
	// Output needs to be in JSON format
	echo json_encode($output);
	exit;
}
add_action("wp_ajax_save_contact_form_7_data", "save_contact_form_7_data");
add_action("wp_ajax_nopriv_save_contact_form_7_data", "save_contact_form_7_data");

// Customize Login Page Logo (admin login)
function wp_custom_login_logo(){ ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
background-image: url(https://images.unsplash.com/photo-1517594422361-5eeb8ae275a9?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80);
        height:100px;
        width:300px;
        background-size: 300px 100px;
        background-repeat: no-repeat;
        padding-bottom: 10px;
    	}
    </style>
<?php }
add_action('login_enqueue_scripts', 'wp_custom_login_logo');

// add url on login logo
function wp_custom_login_url() {
    return home_url();
}
add_filter('login_headerurl', 'wp_custom_login_url');

// add the title attribute of the logo
function wp_login_logo_url_redirect() {
    return "Pyramid Time Systems";
}
add_filter('login_headertitle', 'wp_login_logo_url_redirect');

// disabling the WordPress Login Page Language Switcher
add_filter('login_display_language_dropdown', '__return_false');

// add assets for admin login
function my_login_scrpts() {
  wp_enqueue_style('custom-login-label', get_stylesheet_directory_uri() . '/style-login.css' );
  wp_enqueue_script('custom-login-label', get_stylesheet_directory_uri() . '/style-login.js' );   
}
add_action('login_head', 'my_login_scrpts');






?>