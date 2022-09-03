<?php
	// Load css and js
	function load_assets(){
	    wp_enqueue_style('bootstrap_css', PLUGIN_DIR_PATH . 'assets/css/bootstrap.min.css', array(), 1, 'all');
	    wp_enqueue_script('bootstrap_js', PLUGIN_DIR_PATH . 'assets/js/bootstrap.min.js', array('jquery'), 1, true);
	}

	// Update CSS and JS within in Admin        
	add_action('admin_enqueue_scripts', 'load_assets');
?>