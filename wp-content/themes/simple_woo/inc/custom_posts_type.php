<?php 
	// REGISTER CUSTOM POST TYPES	
	if(!function_exists('create_post_type')){
		function create_post_type(){
		  $labels = array(
		    'name'                  => 'Projects',
		    'singular_name'         => 'Projects',
		    'menu_name'             => 'Projects',
		    'name_admin_bar'        => 'Projects',
		    'archives'              => 'Project Archives',
		    'attributes'            => 'Project Attributes',
		    'parent_item_colon'     => 'Parent Item:',
		    'all_items'             => 'All Projects',
		    'add_new_item'          => 'Add New Project',
		    'add_new'               => 'Add New Project',
		    'new_item'              => 'New Project',
		    'edit_item'             => 'Edit Project',
		    'update_item'           => 'Update Project',
		    'view_item'             => 'View Project',
		    'view_items'            => 'View Project',
		    'search_items'          => 'Search Project',
		    'not_found'             => 'Not found',
		    'not_found_in_trash'    => 'Not found in Trash',
		    'featured_image'        => 'Featured Image',
		    'set_featured_image'    => 'Set featured image',
		    'remove_featured_image' => 'Remove featured image',
		    'use_featured_image'    => 'Use as featured image',
		    'insert_into_item'      => 'Insert into project',
		    'uploaded_to_this_item' => 'Uploaded to this project',
		    'items_list'            => 'Projects list',
		    'items_list_navigation' => 'Projects list navigation',
		    'filter_items_list'     => 'Filter projects list',
		  );
		  $args = array(
		    'label'                 => 'Projects',
		    'description'           => 'Projects Description',
		    'labels'                => $labels,
		    'supports'              => array( 'title','page template', 'custom-fields', 'page-attributes', 'thumbnail','editor','excerpt','slug'),
		    'hierarchical'          => false,
		    'public'                => true,
		    'show_ui'               => true,
		    'show_in_menu'          => true,
		    'menu_position'         => 25,
		    'menu_icon' => "dashicons-portfolio",
		    'rewrite' => array ( 'slug' => __( 'projects' ) ), // change the name
		    'show_in_admin_bar'     => true,
		    'show_in_nav_menus'     => true,
		    'can_export'            => true,
		    'has_archive'           => true,
		    'exclude_from_search'   => false,
		    'publicly_queryable'    => true,
		    'capability_type'       => 'post',
		  );
		  register_post_type('projects', $args);
		}
	}
	//set custom post types
	add_action('init', 'create_post_type');

	//Register Custom Taxonomy
	function custom_taxonomy(){
		$labels = array(
			'name'                       => _x( 'Types', 'taxonomy general name' ),
			'singular_name'              => _x( 'Types', 'taxonomy singular name' ),
			'menu_name'                  => __( 'Types'),
			'all_items'                  => __( 'All Types', 'pyramidtimesystems' ),
			'parent_item'                => __( 'Parent Type', 'pyramidtimesystems' ),
			'parent_item_colon'          => __( 'Parent Type:', 'pyramidtimesystems' ),
			'new_item_name'              => __( 'New Type Name', 'pyramidtimesystems' ),
			'add_new_item'               => __( 'Add New Type', 'pyramidtimesystems' ),
			'edit_item'                  => __( 'Edit Type', 'pyramidtimesystems' ),
			'update_item'                => __( 'Update Type', 'pyramidtimesystems' ),
			'view_item'                  => __( 'View Type', 'pyramidtimesystems' ),
			'separate_items_with_commas' => __( 'Separate types with commas', 'pyramidtimesystems' ),
			'add_or_remove_items'        => __( 'Add or remove types', 'pyramidtimesystems' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'pyramidtimesystems' ),
			'popular_items'              => __( 'Popular Types', 'pyramidtimesystems' ),
			'search_items'               => __( 'Search Types', 'pyramidtimesystems' ),
			'not_found'                  => __( 'Not Found', 'pyramidtimesystems' ),
			'no_terms'                   => __( 'No Types', 'pyramidtimesystems' ),
			'items_list'                 => __( 'Types list', 'pyramidtimesystems' ),
			'items_list_navigation'      => __( 'Types list navigation', 'pyramidtimesystems' ),
		);
		$args = array(
			'labels' => $labels,
			// Hierarchical taxonomy (like categories)
	        'hierarchical' => true,
			'public' => true,
			'show_ui' => true,
			'show_admin_column' => false,
			'show_in_nav_menus' => true,
			'show_tagcloud' => true,	
			'query_var' => true,
	        'capabilities' => array(),

	        // Control the slugs used for this taxonomy
	        'rewrite' => array(
	          'slug' => 'project-type', // This controls the base slug that will display before each term
	          'with_front' => true, // Don't display the category base before "/locations/"
	          'hierarchical' => true // This will allow URL's like "/locations/boston/cambridge/"
	        ),
		);
		register_taxonomy('project-type', array('projects'), $args);
	}
	add_action('init', 'custom_taxonomy');
?>