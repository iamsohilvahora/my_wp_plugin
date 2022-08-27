<?php
	/**
	 * Register a custom menu page.
	 */
	function register_my_custom_menu_page(){
	    // add_menu_page( page_title, menu_title, capability, menu_slug, function, icon_url, position )
	    add_menu_page('Like Dislike System',
	        'Like System',
	        'manage_options',
	        'like_dislike_post',
	        'like_system_page_html',
	        'dashicons-thumbs-up',
	        6
	    );
	    // add_submenu_page( parent_slug, page_title, menu_title, capability, menu_slug, function, position)
	    // add_submenu_page( 'tools.php', 'Like Data Title', 'Like Data', 'manage_options', 'add_like_system_slug', 'add_like_data' );
	    // add_submenu_page( 'options-general.php', 'Like Data Title', 'Like Data', 'manage_options', 'add_like_system_slug', 'add_like_data' );
	    //add_submenu_page( 'like_system_slug', 'Like Data Title', 'Like Data', 'manage_options', 'add_like_system_slug', 'add_like_data' );
	}
	add_action('admin_menu', 'register_my_custom_menu_page');
	function like_system_page_html(){
	    if(!is_admin()){
	        return;
	    } ?>
	    <div class="wrap">
	        <form action="options.php" method="post">
	            <h1><?= esc_html(get_admin_page_title()); ?></h1>
	        	<?php 
	            	settings_fields('plugin-settings');
	            	do_settings_sections('plugin-settings');
	            	submit_button('Save Changes');
	        	?>          
	        </form>
	    </div>
	<?php
	}
	function add_like_data(){
	    echo "<p>Like data not found</p>";
	}
	function like_label_field_cb(){
	    //get the value of setting we've registered with register_setting()
	    $setting = get_option('like_button_label');   
	    //Output the field ?>
	    <input type="text" name="like_button_label" value="<?php echo isset($setting) ? esc_attr($setting) : ""; ?>">
	<?php
	}
	function dislike_label_field_cb(){
	    //get the value of setting we've registered with register_setting()
	    $setting = get_option('dislike_button_label');   
	    //Output the field ?>
	    <input type="text" name="dislike_button_label" value="<?php echo isset($setting) ? esc_attr($setting) : ""; ?>">
	    <?php
	}
	//Settings api
	function like_dislike_plugin_setting(){
	    // register_setting(option_group, option_name, args);
	    // add_settings_section(id, title, callback, page);
	    // add_settings_field(id, title, callback, page, section, args);
	    register_setting('plugin-settings' , 'like_button_label');
	    register_setting('plugin-settings' , 'dislike_button_label');
	    
	    add_settings_section('label_setting_section', 'Button Labels', 'plugin_settings_section_cb', 'plugin-settings');
	    add_settings_field('like_label_field', 'Like Button Label', 'like_label_field_cb', 'plugin-settings', 'label_setting_section');
	    add_settings_field('dislike_label_field', 'Dislike Button Label', 'dislike_label_field_cb', 'plugin-settings', 'label_setting_section');
	}
	function plugin_settings_section_cb($args){
		echo "Define button labels";
	}
	add_action('admin_init', 'like_dislike_plugin_setting');
?>