<?php
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

// Make a Column Sortable
function wp_sortable_projects_column($columns){
    $columns['taxonomy'] = 'Taxonomy';
    $columns['author'] = 'Author';
    //To make a column 'un-sortable' remove it from the array
    unset($columns['date']);
    return $columns;
}
add_filter('manage_edit-projects_sortable_columns', 'wp_sortable_projects_column');

function projects_taxonomy_orderby($query){
    if(!is_admin())
        return;
    $orderby = $query->get('orderby');

    if('taxonomy' == $orderby){
        $query->set('meta_key','taxonomy');
        $query->set('orderby','meta_value'); 
        // "meta_value_num" is used for numeric sorting
		// "meta_value"     is used for Alphabetically sort.
        // We can use any query params which used in WP_Query.
    }
}
add_action('pre_get_posts', 'projects_taxonomy_orderby');
?>