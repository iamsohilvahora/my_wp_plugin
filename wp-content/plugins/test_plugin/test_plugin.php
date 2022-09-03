<?php 
/**
 * Plugin Name:       Test Plugin
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Handle the basics with this plugin.
 * Version:           1.10.3
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Sohil Vahora
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       test-plugin
 * Domain Path:       /languages
 */

/*** Register the "book" custom post type */
function test_plugin_setup_post_type(){
    // register cpt
    register_post_type( 'books', array(
            'labels' => array(
                'name' => __( 'All Books' ),
                'singular_name' => __( 'book' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'books'),
            'show_in_rest' => true,
 
        ));

    // register taxonomy
    $labels = array(
         'name'                       => _x( 'Courses', 'Taxonomy General Name', 'text_domain' ),
         'singular_name'              => _x( 'Course', 'Taxonomy Singular Name', 'text_domain' ),
         'menu_name'                  => __( 'Courses', 'text_domain' ),
         'all_items'                  => __( 'All Items', 'text_domain' ),
         'parent_item'                => __( 'Parent Item', 'text_domain' ),
         'parent_item_colon'          => __( 'Parent Item:', 'text_domain' ),
         'new_item_name'              => __( 'New Item Name', 'text_domain' ),
         'add_new_item'               => __( 'Add New Item', 'text_domain' ),
         'edit_item'                  => __( 'Edit Item', 'text_domain' ),
         'update_item'                => __( 'Update Item', 'text_domain' ),
         'view_item'                  => __( 'View Item', 'text_domain' ),
         'separate_items_with_commas' => __( 'Separate items with commas', 'text_domain' ),
         'add_or_remove_items'        => __( 'Add or remove items', 'text_domain' ),
         'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
         'popular_items'              => __( 'Popular Items', 'text_domain' ),
         'search_items'               => __( 'Search Items', 'text_domain' ),
         'not_found'                  => __( 'Not Found', 'text_domain' ),
         'no_terms'                   => __( 'No items', 'text_domain' ),
         'items_list'                 => __( 'Items list', 'text_domain' ),
         'items_list_navigation'      => __( 'Items list navigation', 'text_domain' ),
      );
      $args   = array(
          'hierarchical'      => true, // make it hierarchical (like categories)
          'labels'            => $labels,
          'public' => true,
          'show_ui'           => true,
          'show_admin_column' => true,
          'show_in_nav_menus' => true,
          'show_tagcloud' => true,
          'show_in_rest' => true,
          'query_var'         => true,
          'rewrite'           => [ 'slug' => 'course' ],
      );
      register_taxonomy( 'course', [ 'post', 'books' ], $args );

      // add user role 
      add_role('simple_role',
        'Simple Role',
        array(
            'read'         => true,
            'edit_posts'   => true,
            'upload_files' => true,
        )
      );
    
    // if ( is_admin() ) {
    if(is_user_logged_in()){
        $user = wp_get_current_user();
        // if ( in_array( 'administrator', (array) $user->roles ) ) {
        if(current_user_can('administrator')){   
            //The user has the "administrator" role
            require_once __DIR__ . '/admin/test_plugin-admin.php';
        }
    }
    register_setting('wporg_options', 'My_plugin_header_logo');
    add_shortcode('wporg', 'wporg_shortcode');
} 
add_action('init', 'test_plugin_setup_post_type'); 

/*** Activate the plugin. */
function test_plugin_activate(){ 
    // Trigger our function that registers the custom post type plugin.
    // test_plugin_setup_post_type(); 
    // Clear the permalinks after the post type has been registered.

    // register_custom_taxonomy_course();

    flush_rewrite_rules(); 
}
register_activation_hook(__FILE__, 'test_plugin_activate');

/*** Deactivation hook. */
function test_plugin_deactivate() {
    // Unregister the post type, so the rules are no longer in memory.
    unregister_post_type( 'books' );
    // Clear the permalinks to remove our post type's rules from the database.
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'test_plugin_deactivate');

register_uninstall_hook(__FILE__, 'test_plugin_function_to_run');

//admin menu page
function wporg_options_page(){
    add_menu_page(
        'WPOrg',
        'WPOrg Options',
        'manage_options',
        'wporg',
        'wporg_options_page_html',
        'dashicons-admin-network',
        20
    );
    add_submenu_page(
        'tools.php',
        'WPOrg Options',
        'WPOrg Options',
        'manage_options',
        'wporg',
        'wporg_options_page_html'
    );
}
add_action('admin_menu', 'wporg_options_page');

function wporg_options_page_html(){
    ?>
    <div class="wrap">
      <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
      <form action="options.php" method="post">
        <?php
        // output security fields for the registered setting "wporg_options"
        settings_fields('wporg_options');
        // output setting sections and their fields
        // (sections are registered for "wporg", each field is registered to a specific section)
        do_settings_sections('wporg');
        // update_option('My_plugin_header_logo','Logo');
        // output save settings button
        submit_button(__('Save Settings', 'textdomain'));
        ?>
      </form>
    </div>
    <?php
}

//display shortcodes
function wporg_shortcode($atts = [], $content = null, $tag = ''){
    $content = "The girls on the train";
    return $content;
}

//Settings api  
function wporg_settings_init() {
    // register a new setting for "reading" page
    register_setting('reading', 'wporg_setting_name');
    // register a new section in the "reading" page
    add_settings_section(
        'wporg_settings_section',
        'WPOrg Settings Section', 'wporg_settings_section_callback',
        'reading'
    );
    // register a new field in the "wporg_settings_section" section, inside the "reading" page
    add_settings_field(
        'wporg_settings_field',
        'WPOrg Setting', 'wporg_settings_field_callback',
        'reading',
        'wporg_settings_section'
    );
}
 
/**
 * register wporg_settings_init to the admin_init action hook
 */
add_action('admin_init', 'wporg_settings_init');
 
/**
 * callback functions
 */
 
// section content cb
function wporg_settings_section_callback() {
    echo '<p>WPOrg Section Introduction.</p>';
}
 
// field content cb
function wporg_settings_field_callback() {
    // get the value of the setting we've registered with register_setting()
    $setting = get_option('wporg_setting_name');
    // output the field
    ?>
    <input type="text" name="wporg_setting_name" value="<?php echo isset($setting) ? esc_attr($setting) : ''; ?>">
    <?php
}

//add meta box to screen
function add_custom_meta_box() {
    $screens = [ 'post', 'books' ];
    foreach ( $screens as $screen ) {
        add_meta_box(
            'wporg_box_id',                 // Unique ID
            'Custom Meta Box Title',      // Box title
            'custom_meta_box_html',  // Content callback, must be of type callable
            $screen                            // Post type
        );
    }
}

function custom_meta_box_html( $post ) {
    $value = get_post_meta($post->ID, '_wporg_meta_key', true);
    ?>
    <label for="wporg_field">Description for this field</label>
    <select name="wporg_field" id="wporg_field" class="postbox">
        <option value="">Select something...</option>
        <option value="something" <?php selected($value, 'something'); ?>>Something</option>
        <option value="else" <?php selected($value, 'else'); ?>>Else</option>
    </select>
    <?php
}
add_action('add_meta_boxes', 'add_custom_meta_box');

function wporg_save_postdata($post_id){
    if(array_key_exists('wporg_field', $_POST)){
        update_post_meta(
            $post_id,
            '_wporg_meta_key',
            $_POST['wporg_field']
        );
    }
}
add_action('save_post', 'wporg_save_postdata');

//User profile data update
/**
 * The field on the editing screens.
 *
 * @param $user WP_User user object
 */
function wporg_usermeta_form_field_birthday($user)
{
    ?>
    <h3>It's Your Birthday</h3>
    <table class="form-table">
        <tr>
            <th>
                <label for="birthday">Birthday</label>
            </th>
            <td>
                <input type="date"
                       class="regular-text ltr"
                       id="birthday"
                       name="birthday"
                       value="<?= esc_attr(get_user_meta($user->ID, 'birthday', true)) ?>"
                       title="Please use YYYY-MM-DD as the date format."
                       pattern="(19[0-9][0-9]|20[0-9][0-9])-(1[0-2]|0[1-9])-(3[01]|[21][0-9]|0[1-9])"
                       required>
                <p class="description">
                    Please enter your birthday date.
                </p>
            </td>
        </tr>
    </table>
    <?php
}
  
/**
 * The save action.
 *
 * @param $user_id int the ID of the current user.
 *
 * @return bool Meta ID if the key didn't exist, true on successful update, false on failure.
 */
function wporg_usermeta_form_field_birthday_update($user_id)
{
    // check that the current user have the capability to edit the $user_id
    if(! current_user_can('edit_user', $user_id)){
        return false;
    }
    // create/update user meta for the $user_id
    return update_user_meta(
        $user_id,
        'birthday',
        $_POST['birthday']
    );
}
  
// Add the field to user's own profile editing screen.
add_action('show_user_profile', 'wporg_usermeta_form_field_birthday');
  
// Add the field to user profile editing screen.
add_action('edit_user_profile', 'wporg_usermeta_form_field_birthday');
  
// Add the save action to user's own profile editing screen update.
add_action('personal_options_update', 'wporg_usermeta_form_field_birthday_update');
  
// Add the save action to user profile editing screen update.
add_action('edit_user_profile_update', 'wporg_usermeta_form_field_birthday_update');
?>