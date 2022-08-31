<?php
/**
 * Plugin Name:       Simple Contact Form
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Handle the basics with simple contact form plugin. [user-form-details]
 * Version:           1.1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Sohil Vahora
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       simple-contact-form
 * Domain Path:       /languages
*/
if(!defined('ABSPATH')){
	die("What are you trying to get");
}

// Adding Custom Widgets to WordPress Dashboard
function register_custom_dashboard_widget(){
    wp_add_dashboard_widget(
        'custom_dashboard_widget',
        'Custom Dashboard Widget',
        'custom_dashboard_widget_display'
    );
}
add_action('wp_dashboard_setup', 'register_custom_dashboard_widget');
function custom_dashboard_widget_display() {
    echo 'Hello, I\'m Widget';
}

// Simple contact form class
class SimpleContactForm{
    public function __construct(){
        // Create custom post type
        add_action('init', array($this, 'create_custom_post_type'));
        // Add assets (js, css, etc)
        add_action('wp_enqueue_scripts',array($this, 'load_assets'));
        // Add shortcode
        add_shortcode('user-form-details', array($this, 'load_shortcode'));
        // Load javascript footer side
        add_action('wp_footer', array($this, 'load_scripts'));
        // Register rest api
        add_action('rest_api_init', array($this, 'register_rest_api'));
        // Update CSS and JS within in Admin        
        add_action('admin_enqueue_scripts', array($this, 'admin_style_script'));
        // Register Meta box
        add_action('add_meta_boxes', array($this, 'wp_create_meta_box_func'));
        // Save meta box value 
        add_action("save_post", array($this, 'save_custom_meta_box'), 10, 3);
    }
    // create post type
    public function create_custom_post_type(){
        // CPT Options
        $args = array(
                'labels' => array(
                    'name' => __( 'Contact Form' ),
                    'singular_name' => __( 'Contact Form Entry' )
                ),
                'public' => true,
                'has_archive' => true,
                'supports' => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
                'rewrite' => array('slug' => 'contact_form'),
                'show_in_rest' => true,
                'publicly_queryable' => false,
                'exclude_from_search' => true,
                'capability' => 'manage_options',
                'menu_icon' => 'dashicons-media-text'
            );
        if(current_user_can('administrator')){   
            register_post_type('simple_contact_form', $args);
        }
    }
    // load css and js
    public function load_assets(){
        wp_enqueue_style( 'my_css', plugin_dir_url(__FILE__) . 'assets/css/main.css', array(), 1, 'all' );
        wp_enqueue_style( 'bootstrap_css', plugin_dir_url(__FILE__) . 'assets/css/bootstrap.min.css', array(), 1, 'all' );
        wp_enqueue_script( 'custom_js', plugin_dir_url(__FILE__) . 'assets/js/main.js', array('jquery'), 1, true );
        wp_enqueue_script( 'bootstrap_js', plugin_dir_url(__FILE__) . 'assets/js/bootstrap.min.js', array('jquery'), 1, true );
    }
    // load shortcode function
    public function load_shortcode(){
        $template = '<div class="simple-contact-form">
            <h1>Send us email</h1>
            <p>Please fill the below form</p>
            <form id="simple-contact-form_form">
                <div class="form-group mb-2">
                    <input type="text" name="name" placeholder="Name" class="form-control" id="name" required>
                </div>
                <div class="form-group mb-2">
                    <input type="email" name="email" placeholder="Email" class="form-control" id="email" required>
                </div>
                <div class="form-group mb-2">
                    <input type="tel" name="phone" placeholder="Phone" class="form-control" id="phone" required>
                </div>
                <div class="form-group mb-2">
                    <textarea placeholder="Type your message" id="msg" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-success btn-block w-100">Send Messeage</button>
                </div>                    
            </form>
        </div>';
        return $template;
    }
    // load footer script
    public function load_scripts(){
    ?>
        <script type="text/javascript">
        var nonce = '<?php echo wp_create_nonce('wp_rest'); ?>';
        (function($){
            $("#simple-contact-form_form").submit(function(e){
                e.preventDefault();
                var form = $(this).serialize();
                $.ajax({
                    method: 'post',
                    url: '<?php echo get_rest_url(null, 'simple-contact-form/v1/send-email'); ?>',
                    headers: {'X-WP-Nonce' : nonce},
                    data: form,
                    success: function(){
                        alert("Data submitted successfully");
                        $("#name").val("");
                        $("#email").val("");
                        $("#phone").val("");
                        $("#msg").val("");
                    }
                });
            });
        })(jQuery)
        </script>
    <?php 
    }
    function register_rest_api(){
        register_rest_route('simple-contact-form/v1', 'send-email', array(
                'methods' => 'post',
                'callback' => array($this, 'handle_contact_form'), 
        ));
    }
    function handle_contact_form($data){
        $headers = $data->get_headers();
        $params = $data->get_params();
        $nonce = $headers['x_wp_nonce'][0];
        // verify nonce
        if(!wp_verify_nonce($nonce, 'wp_rest')){
            return new WP_REST_Response('Message Not Sent', 422);
        }
        // insert post details
        $post_id = wp_insert_post([
            'post_type' => 'simple_contact_form',
            'post_title' => $params['name'],
            'post_status' => 'publish',
            'meta_input'    => array(
                'email' => $params['email'],
                'phone' => $params['phone']
            )
        ]);
        if($post_id){  
            return new WP_REST_Response('Thanks for email', 200);
        }
    }
    // load admin js
    function admin_style_script(){
        if(isset($_GET['action']) == "edit"){
            wp_enqueue_script( 'admin_js', plugin_dir_url(__FILE__) . 'assets/js/admin_script.js', array('jquery'), 1, true );
        }
    }
    // Create metabox
    function wp_create_meta_box_func(){
        add_meta_box('email', 'Advance form field', array($this, 'wp_get_form_field'), 'simple_contact_form', 'normal');
    }
    // Metabox callback function
    function wp_get_form_field($post){
        $email = get_post_meta($post->ID, 'email', true);
        $phone = get_post_meta($post->ID, 'phone', true);
        ?>
        <label for="email">Email Address</label>
        <input type="email" name="email" id="email" value="<?php echo esc_attr($email); ?>" disabled>
        <label for="phone">Phone Number</label>
        <input type="number" name="phone" id="phone" value="<?php echo esc_attr($phone); ?>" disabled>
        <?php
    }
    // save metabox values in database
    function save_custom_meta_box($post_id, $post, $update){
        if(!current_user_can("edit_post", $post_id))
            return $post_id;
        if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
            return $post_id;
        $slug = "simple_contact_form";
        if($slug != $post->post_type)
            return $post_id;
        $meta_box_email_value = "";
        $meta_box_phone_value = "";
        if(isset($_POST["email"])){
            $meta_box_email_value = $_POST["email"];
        }   
        update_post_meta($post_id, "email", $meta_box_email_value);

        if(isset($_POST["phone"])){
            $meta_box_phone_value = $_POST["phone"];
        }   
        update_post_meta($post_id, "phone", $meta_box_phone_value);
    }
}
new SimpleContactForm;
?>