<h1>This is custom page template</h1>
<?php
	global $wpdb;
	$table_name = $wpdb->prefix."users";
	
	// get_results - return results of whole table
	// $user_details = $wpdb->get_results(
	// 	$wpdb->prepare("SELECT * FROM $table_name ORDER BY id DESC"), ARRAY_A);
	// ARRAY_A - Associative array
	// ARRAY_N - Indexed array

	// get_row - return row of table based on condition
	// $user_details = $wpdb->get_row(
	// 	$wpdb->prepare("SELECT * FROM $table_name WHERE id = '%d'", 2), ARRAY_A);

	// get_var - return single scalar value based on condition
	$user_details = $wpdb->get_var(
		$wpdb->prepare("SELECT user_email FROM $table_name WHERE id = '%d'", 2));

	echo "<pre>";		
	print_r($user_details);
	echo "</pre>";	

	/********************************************/
	// Ways to get User details 
	/********************************************/
	// $user_info = wp_get_current_user();
	// $user_id = get_current_user_id();
	global $user_ID;
	// $user_info = new WP_User($user_ID); 
	// get_currentuserinfo(); // depracated
	$user_info = get_userdata($user_ID);

	echo "<pre>";		
	print_r($user_info);
	echo "</pre>";
?>