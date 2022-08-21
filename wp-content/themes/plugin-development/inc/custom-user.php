<?php 
// Show custom fields on user profile page
function plugin_development_add_extra_user_fields($user){ ?>
	<h3>User Birthday</h3>
	<table class="form-table">
		<tr>
			<th>User Birthday</th>
			<td><input type="date" name="user_birthday" class="regular-text" value="<?= get_user_meta($user->ID, '_user_birthday', true) ?>" id=""></td>
		</tr>
	</table>
<?php }
add_action('show_user_profile', 'plugin_development_add_extra_user_fields', 10);
add_action('edit_user_profile', 'plugin_development_add_extra_user_fields', 10);

// Update user profile fields.
function plugin_development_update_user_profile($user_id){
	update_user_meta($user_id, '_user_birthday', $_POST['user_birthday']);
}
add_action('personal_options_update', 'plugin_development_update_user_profile', 10);
add_action('edit_user_profile_update', 'plugin_development_update_user_profile', 10);

// Search for dile (wp-admin/includes/schema.php)
// add or remove user role  
add_action('init', function(){
	// remove_role('simple_role');
	add_role('simple_role', __('Simple Role', 'plugin_development'),
    array(
        'read'         => true,  // true allows this capability
        'edit_posts'   => true,
        'delete_posts' => false, // Use false to explicitly deny
    ));
    
    // user_can($user, $capability); // Returns whether a particular user has the specified capability.

	$role = get_role('simple_role');
	$role->add_cap('edit_pages');
	$role->add_cap('edit_others_posts');
	$role->add_cap('edit_published_posts');
	// $role->remove_cap('upload_files');

});

?>