<?php
/**
 * plugin development functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package plugin_development
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function plugin_development_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on plugin development, use a find and replace
		* to change 'plugin-development' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'plugin-development', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'plugin-development' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'plugin_development_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'plugin_development_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function plugin_development_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'plugin_development_content_width', 640 );
}
add_action( 'after_setup_theme', 'plugin_development_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function plugin_development_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'plugin-development' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'plugin-development' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'plugin_development_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function plugin_development_scripts() {
	wp_enqueue_style( 'plugin-development-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'plugin-development-style', 'rtl', 'replace' );

	wp_enqueue_script( 'plugin-development-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'plugin_development_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
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
function wp_show_peoject_type_func($atts){
    $atts = shortcode_atts(
        array(
            'id' => '1',
            'title' => 'default title',
        ), $atts, 'show-project');
    return 'id: '.esc_html($atts['id']).' - title: '.esc_html($atts['title']);
}
add_shortcode('show-project', 'wp_show_peoject_type_func');