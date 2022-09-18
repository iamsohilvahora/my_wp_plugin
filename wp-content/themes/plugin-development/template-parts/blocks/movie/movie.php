<?php
	// Create class attribute allowing for custom "className" and "align" values.in

	// echo "<pre>";
	// print_r($block);
	// echo "</pre>";

	$className = 'movies';
	if( !empty($block['className']) ) {
	    $className .= ' ' . $block['className'];
	}
	if( !empty($block['align']) ) {
	    $className .= ' align' . $block['align'];
	}

	// Load values and handle defaults.
	$field = get_fields();
	$movie_name = $field['name_of_movie'];
	$release_date = $field['release_date'];
?>
<div class="<?php echo esc_attr($className); ?>">
    <h1><?php echo $movie_name; ?></h1>
    <p><?php echo $release_date; ?></p>
</div>