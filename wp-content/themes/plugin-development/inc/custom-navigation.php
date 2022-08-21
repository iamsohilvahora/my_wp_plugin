<?php
/**
 * add extra item to navigation menu.
 */
function plugin_development_add_extra_item_to_nav_menu($items, $args){
    if(is_user_logged_in() && $args->theme_location == 'menu-1'){
        $items .= '<li class="menu-item">'
                . '<form role="search" method="get" class="search-form" action="'.home_url( '/' ).'">'
                . '<label>'
                . '<span class="screen-reader-text">' . _x( 'Search for:', 'label' ) . '</span>'
                . '<input type="search" class="search-field" placeholder="' . esc_attr_x( 'Search …', 'placeholder' ) . '" value="' . get_search_query() . '" name="s" title="' . esc_attr_x( 'Search for:', 'label' ) . '" />'
                . '</label>'
                . '<input type="submit" class="search-submit" value="'. esc_attr_x('Search', 'submit button') .'" />'
                . '</form>'
                . '</li>';
    }
    return $items;
}
add_filter('wp_nav_menu_items', 'plugin_development_add_extra_item_to_nav_menu', 10, 2);

/**
 * show different navigation menu for logged in and non-logged in users.
 */
function plugin_development_wp_nav_menu_populate_theme_location($args){
	$args['theme_location'] = is_user_logged_in() ? 'menu-2' : 'menu-1';
    return $args;
}
add_filter('wp_nav_menu_args', 'plugin_development_wp_nav_menu_populate_theme_location');

/**
 * add_extra_menu_item_fields.
 */
function plugin_development_add_extra_menu_item_fields($item_id){
	wp_nonce_field(-1, '_wpnonce', true, true); 
	$checked = get_post_meta($item_id, "conditional_menu_$item_id", true) ? 'checked' : ''; ?>
	<p><label for="conditional-menu-item"><input type="checkbox" name="conditional_menu_<?= $item_id; ?>" id="conditional-menu-item" <?= $checked; ?>>Conditional Menu</label></p>
<?php }
add_action('wp_nav_menu_item_custom_fields', 'plugin_development_add_extra_menu_item_fields');

/**
 * Update nav menu item fields.
 */
function plugin_development_wp_update_nav_menu_items($menu_id, $item_id){
	if(!wp_verify_nonce($_POST['_wpnonce'], -1)){
		return $menu_id;
	}
	if(isset($_POST["conditional_menu_$item_id"])){
		update_post_meta($item_id, "conditional_menu_$item_id", "checked");
	}
	else{
		delete_post_meta($item_id, "conditional_menu_$item_id");
	}
}
add_action('wp_update_nav_menu_item', 'plugin_development_wp_update_nav_menu_items', 10, 2);

/**
 * add css class to nav menu link.
 */
function plugin_development_wp_nav_menu_link_attributes($atts, $item){
	$condition = get_post_meta($item->ID, "conditional_menu_$item->ID", true);
	if($condition){
		$atts['class'] = $atts['class'] . ' text-danger';
	}
	return $atts;
}

add_filter('nav_menu_link_attributes', 'plugin_development_wp_nav_menu_link_attributes', 10, 2);

/**
 * add css class to nav menu item.
 */
function plugin_development_wp_nav_menu_css_class($classes, $item){
	$condition = get_post_meta($item->ID, "conditional_menu_$item->ID", true);
	if($condition){
		$classes[] = 'd-none';
	}
	return $classes;
}
add_filter('nav_menu_css_class', 'plugin_development_wp_nav_menu_css_class', 10, 2);
?>