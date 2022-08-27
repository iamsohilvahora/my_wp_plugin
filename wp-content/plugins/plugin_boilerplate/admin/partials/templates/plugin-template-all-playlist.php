<?php
	global $wpdb;
	$table_name = $wpdb->prefix."playlist";
	$all_playlist = $wpdb->get_results(
		$wpdb->prepare("SELECT * FROM $table_name ORDER BY id DESC"), ARRAY_A);

	if(!empty($all_playlist)){
		$i = 1;
		foreach($all_playlist as $index => $data){ ?>
		<tr>
		    <td><?php echo $i++; ?></td>
		    <td><?php echo $data['name']; ?></td>
		    <td><img src="<?php echo $data['thumbnail']; ?>" style="width:80px;height:80px;"></td>
		    <td><?php $user_levels = (array) json_decode($data['playlist_for']); 
		    	$user_level = "";
		    	foreach($user_levels as $level){
		    		$user_level .= ucfirst($level). ", ";
		    	}
		    	echo rtrim($user_level,', ');
		    	?>
		    </td>
		    <td>
		    <a href="javascripy:void(0)" class="btn btn-info">Edit</a>
		    <a href="javascripy:void(0)" class="btn btn-danger playlist-delete" data-id="<?php echo $data['id']; ?>">Delete</a>
			</td>
		</tr>			
		<?php }
	}
?>