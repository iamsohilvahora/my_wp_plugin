<?php
// add product author on product list
function prefix_add_author_woocommerce(){
	// add_post_type_support('product', 'author');
}
add_action('after_setup_theme', 'prefix_add_author_woocommerce');

// add account menu items
function wc_account_menu_items_func($menu_links){
	$menu_links = array_slice($menu_links, 0, 5, true)
	+ array('product-list' => 'Show Product List')
	+ array('add-product-form' => 'Customer Product Form')
	+ array_slice($menu_links, 5, NULL, true);
	return $menu_links;
}
add_filter('woocommerce_account_menu_items', 'wc_account_menu_items_func');

// Register Permalink Endpoint
function wc_add_endpoint_func(){
	add_rewrite_endpoint('product-list', EP_PAGES);
	add_rewrite_endpoint('add-product-form', EP_PAGES);
	add_rewrite_endpoint('edit-product-form', EP_PAGES);
}
add_action('init', 'wc_add_endpoint_func');

/*
 * Content for the new page in My Account, woocommerce_account_{ENDPOINT NAME}_endpoint
*/
// Display customer product form
function wc_customer_product_form_func(){ ?> 
	<div id="primary" class="content-area">
		<main id="main" class="site-main">
			<form method="post" class="product_form" enctype='multipart/form-data'>
				<div><h4>Add Product</h4></div>
				<div>
					<div><label>Product Name</label></div>
					<div><input type="text" name="proname" class="product_name" id="product_name" required /></div>
				</div>
				<div>
					<div><label>Product Description</label></div>
					<div><textarea name="prodesc" class="product_desc" id="product_desc" required></textarea></div>
				</div>
				<div>
					<div><label>Product Price</label></div>
					<div><input type="text" name="proprice" class="product_price" id="product_price" required /></div>
				</div>
				<div>
					<div><label>Select Product Categories</label></div>
					<div>
				  	<?php
						$taxonomy     = 'product_cat';
						$orderby      = 'name';  
						$show_count   = 0;      // 1 for yes, 0 for no
						$pad_counts   = 0;      // 1 for yes, 0 for no
						$hierarchical = 1;      // 1 for yes, 0 for no  
						$title        = '';  
						$empty        = 0;
						$args = array(
							'taxonomy'     => $taxonomy,
							'orderby'      => $orderby,
							'show_count'   => $show_count,
							'pad_counts'   => $pad_counts,
							'hierarchical' => $hierarchical,
							'title_li'     => $title,
							'hide_empty'   => $empty
						);
						$all_categories = get_categories($args);
						foreach ($all_categories as $cat){
							if($cat->category_parent == 0){
								$category_id = $cat->term_id; 
							 	echo "<input type='checkbox' name='procat[]' class='product_cat' id='product_cat' value='{$category_id}' />".' '.$cat->name;
							 	echo "<br />";
								$args2 = array(
									'taxonomy'     => $taxonomy,
									'child_of'     => 0,
									'parent'       => $category_id,
									'orderby'      => $orderby,
									'show_count'   => $show_count,
									'pad_counts'   => $pad_counts,
									'hierarchical' => $hierarchical,
									'title_li'     => $title,
									'hide_empty'   => $empty
								);
							 	$sub_cats = get_categories($args2);
							 	if($sub_cats){
							    	foreach($sub_cats as $sub_category){
							        	echo "<input type='checkbox' name='procat[]' class='product_cat' id='product_cat' value='{$sub_category->term_id}' />".' '."<b>$sub_category->name</b>";
							        	echo "<br />";
							     	}   
							 	}
							}       
						}
				    ?>
					</div>
				</div>
				<?php
				$attribute_taxonomies = wc_get_attribute_taxonomies();
				$taxonomy_terms = array();
				if(!empty($attribute_taxonomies)): ?>
				<div>
				    <?php
				  	foreach($attribute_taxonomies as $tax): ?>
						<div>
							<div><label>Select <?php echo $tax->attribute_name; ?> Attributes</label></div>
							<?php 
							if(taxonomy_exists(wc_attribute_taxonomy_name($tax->attribute_name))): ?>
							<div>
								<?php
								echo "<select name='{$tax->attribute_name}[]'>";
								echo "<option value=''>Select Attribute Value</option>";
							    $taxonomy_terms = get_terms(wc_attribute_taxonomy_name($tax->attribute_name), 'orderby=name');
							    foreach($taxonomy_terms as $term){
									echo "<option value='{$term->name}'>$term->name</option>";
							    }
								echo "</select>";
							?>
							</div>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
				<div>
				  <div><label>Uplaod Product Featured Image</label></div>
				  <div><input type="file" name="productFeaturedImg" id="fileToUpload" accept="image/*" required></div>
				</div>
				<div>
				  <div><label>Uplaod Product Gallery</label></div>
				  <div><input type="file" name="productGallery[]" id="galleryToUpload" accept="image/*" multiple required></div>
				</div>
				<input type="submit" value="Submit" class="product_submit" id="add_product" />
			</form>
		</main>
	</div>
<?php }
add_action('woocommerce_account_add-product-form_endpoint', 'wc_customer_product_form_func');

// Show list of product which is added by customer (author)
function wc_show_customer_product_list_func(){ 
	$author = (current_user_can('administrator')) ? '' : get_current_user_id();
	$status = (current_user_can('administrator')) ? 'any' : 'publish';
	$customer_access = get_field('choose_customer_access', 'options');
	$args = array(
	    'post_type' => 'product',
	    'post_status' => $status,
	    'posts_per_page' => -1,
	    'author' => $author,
	);
	$loop = new WP_Query($args);
	
	if($loop->have_posts()): ?>
		<h2>List of Customer's Product</h2>
		<table>
			<tr>
				<th>Id</th>
				<th>Product Name</th>
				<th>Product Price</th>
				<?php if(current_user_can('administrator')): ?>
					<th>Action</th>
				<?php elseif($customer_access): ?>
					<th>Action</th>
				<?php endif; ?>
				<?php if(current_user_can('administrator')): ?>
					<th>Author</th> 
				<?php endif; ?>
				<th>Status</th>
			</tr>
			<?php 
			$i=1;
			while($loop->have_posts()) : $loop->the_post(); 
				// Get $product object from product ID  
				$product = wc_get_product(get_the_id()); ?>
			<tr>
				<td><?php echo $i++; ?></td>
				<td><?php echo $product->get_name(); ?></td>
				<td><?php echo $product->get_price(); ?></td>
				<?php if(current_user_can('administrator')): ?>
					<td><a href="<?php echo get_permalink(wc_get_page_id('myaccount')); ?>edit-product-form/?id=<?php echo get_the_id(); ?>" data-id="<?php echo get_the_id(); ?>" class="edit_product">Edit</a> 
				| <a href="javascript:void(0)" data-id="<?php echo get_the_id(); ?>" class="delete_product">Delete</a></td>
				<?php elseif($customer_access): ?>
					<td><a href="<?php echo get_permalink(wc_get_page_id('myaccount')); ?>edit-product-form/?id=<?php echo get_the_id(); ?>" data-id="<?php echo get_the_id(); ?>" class="edit_product">Edit</a> 
									| <a href="javascript:void(0)" data-id="<?php echo get_the_id(); ?>" class="delete_product">Delete</a></td>
				<?php endif; ?>
				<?php if(current_user_can('administrator')): ?>
					<td><?php echo get_the_author(); ?></td>
				<?php endif; ?>
				<td><?php echo get_post_status(); ?></td>
			</tr>
			<?php endwhile; ?>
		</table>
	<?php
	else:
		echo __('No products found', 'textdomain');
    endif;    
	wp_reset_postdata();
 ?>

<?php }
add_action('woocommerce_account_product-list_endpoint', 'wc_show_customer_product_list_func');

function wc_edit_customer_product_list_func(){
	// Get product ids.
	if(isset($_GET['id'])){
		$product_id = $_GET['id'];
		$product = wc_get_product($product_id);
	}
	$author = (current_user_can('administrator')) ? '' : get_current_user_id();
	$status = (current_user_can('administrator')) ? true : ($product->get_status() == 'publish' ? true : false);
	$customer_access = get_field('choose_customer_access', 'options');
	$edit_access = (current_user_can('administrator')) ? true : ($customer_access == 1 ? true : false);
	$args = array(
	    'return' => 'ids',
	    'limit' => -1,
	    'author' => $author,
	);
	$product_ids = wc_get_products($args);  // get product id of current customer
	if(in_array($product_id, $product_ids) && $status && $edit_access){
		$product = wc_get_product($product_id); ?>
		<div id="primary" class="content-area">
		<main id="main" class="site-main">
			<form method="post" class="edit_product_form" enctype='multipart/form-data'>
				<input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
				<div><h4>Edit Product</h4></div>
				<div>
					<div><label>Product Name</label></div>
					<div><input type="text" name="proname" class="product_name" value="<?php echo $product->get_title(); ?>" required /></div>
				</div>
				<div>
					<div><label>Product Description</label></div>
					<div><textarea name="prodesc" class="product_desc" required><?php echo $product->get_short_description(); ?></textarea></div>
				</div>
				<div>
					<div><label>Product Price</label></div>
					<div><input type="text" name="proprice" class="product_price" id="new_product_price" value="<?php echo $product->get_price(); ?>" required /></div>
				</div>
				<div>
					<?php
						$product_cats_ids = wc_get_product_term_ids( $product->get_id(), 'product_cat' ); ?>
					<div><label>Select Product Categories</label></div>
					<div>
				  	<?php
						$taxonomy     = 'product_cat';
						$orderby      = 'name';  
						$show_count   = 0;      // 1 for yes, 0 for no
						$pad_counts   = 0;      // 1 for yes, 0 for no
						$hierarchical = 1;      // 1 for yes, 0 for no  
						$title        = '';  
						$empty        = 0;
						$args = array(
							'taxonomy'     => $taxonomy,
							'orderby'      => $orderby,
							'show_count'   => $show_count,
							'pad_counts'   => $pad_counts,
							'hierarchical' => $hierarchical,
							'title_li'     => $title,
							'hide_empty'   => $empty
						);
						$all_categories = get_categories($args);
						foreach ($all_categories as $cat){
							if($cat->category_parent == 0){
								$category_id = $cat->term_id; 
								$checked = in_array($category_id, $product_cats_ids) ? 'checked' : '';
							 	echo "<input type='checkbox' name='procat[]' class='product_cat' id='product_cat' value='{$category_id}' {$checked} />".' '.$cat->name;
							 	echo "<br />";
								$args2 = array(
									'taxonomy'     => $taxonomy,
									'child_of'     => 0,
									'parent'       => $category_id,
									'orderby'      => $orderby,
									'show_count'   => $show_count,
									'pad_counts'   => $pad_counts,
									'hierarchical' => $hierarchical,
									'title_li'     => $title,
									'hide_empty'   => $empty
								);
							 	$sub_cats = get_categories($args2);
							 	if($sub_cats){
							    	foreach($sub_cats as $sub_category){
							    		$checked = in_array($sub_category->term_id, $product_cats_ids) ? 'checked' : '';
							        	echo "<input type='checkbox' name='procat[]' class='product_cat' id='product_cat' value='{$sub_category->term_id}' {$checked} />".' '."<b>$sub_category->name</b>";
							        	echo "<br />";
							     	}   
							 	}
							}       
						}
				    ?>
					</div>
				</div>
				<?php
				$attribute_taxonomies = wc_get_attribute_taxonomies();
				$taxonomy_terms = array();
				$product_attributes = $product->get_attributes(); // get attribute of selected product
				if(!empty($attribute_taxonomies)): ?>
				<div>
				    <?php
				  	foreach($attribute_taxonomies as $tax): ?>
						<div>
							<div><label>Select <?php echo $tax->attribute_name; ?> Attributes</label></div>
							<?php 
							if(taxonomy_exists(wc_attribute_taxonomy_name($tax->attribute_name))): ?>
							<div>
								<?php
								echo "<select name='{$tax->attribute_name}[]'>";
								echo "<option value=''>Select Attribute Value</option>";
							    $taxonomy_terms = get_terms(wc_attribute_taxonomy_name($tax->attribute_name), 'orderby=name');
							    foreach($taxonomy_terms as $term){
							    	$selected = ($product_attributes[$tax->attribute_name]['options']['0'] == $term->name) ? 'selected' : '';
									echo "<option value='{$term->name}' {$selected}>$term->name</option>";
							    }
								echo "</select>";
							?>
							</div>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
				<div>
				  <div><label>Uplaod New Product Featured Image</label></div>
				  <div>
				  	<input type="file" name="productFeaturedImg" id="newFileToUpload" accept="image/*">
				  	<?php if(!empty(wp_get_attachment_url($product->get_image_id()))): ?>
				  	<img width="120" height="120" src="<?php echo wp_get_attachment_url($product->get_image_id()); ?>" />
				  	<?php endif; ?>
				  </div>
				</div>
				<div>
				  <div><label>Uplaod New Product Gallery</label></div>
				  <div>
				  	<input type="file" name="productGallery[]" id="newProductGallery" accept="image/*" multiple>
				  	<?php
				  	$attachment_ids = $product->get_gallery_image_ids();
				  	if(!empty($attachment_ids)):
				  		foreach($attachment_ids as $attachment_id){
				  	    	echo "<img width='120' height='120' src='".wp_get_attachment_url($attachment_id)."' />";
						}
					endif;
				  ?>
				  </div>
				</div>
				<input type="submit" value="Update" class="product_submit" id="edit_product" />
			</form>
		</main>
	</div>
	<?php }
	else{
		echo "Unauthorize user access";
	}
}
add_action('woocommerce_account_edit-product-form_endpoint', 'wc_edit_customer_product_list_func');

// action and function for add product data
add_action('wp_ajax_add_product_data', 'wc_add_product_data');
add_action('wp_ajax_nopriv_add_product_data', 'wc_add_product_data');

// action and function for edit product data
add_action('wp_ajax_edit_product_data', 'wc_edit_product_data');
add_action('wp_ajax_nopriv_edit_product_data', 'wc_edit_product_data');

// action and function for delete product data
add_action('wp_ajax_delete_customer_product', 'wc_delete_customer_product_func');
add_action('wp_ajax_nopriv_delete_customer_product', 'wc_delete_customer_product_func');

function wc_add_product_data(){
	if($_POST['action'] == 'add_product_data'){
		$site_url = get_site_url(); // get site url		
		$product_images = []; // empty array
		$product_name = $_POST['proname'];
		$product_desc = $_POST['prodesc'];
		$product_price = $_POST['proprice'];
		$product_cat = $_POST['procat'];
		// Get categories
		if(!empty($product_cat)){
			for($cat=0;$cat<count($product_cat);$cat++){
				$product_category[]['id'] = $product_cat[$cat];
			}
		}
		// Get attributes
		$attribute_taxonomies = wc_get_attribute_taxonomies();
		if(!empty($attribute_taxonomies)):
			$i = 0;
			foreach($attribute_taxonomies as $tax):
				foreach($_POST as $key => $value){
					if($key == $tax->attribute_name ){
						if(!empty($_POST[$tax->attribute_name][0])){
							$product_attr[$i]['name'] = $tax->attribute_name;
							$product_attr[$i]['options'][] = $_POST[$tax->attribute_name][0];
						}
						$i++;
					}
				}
			endforeach;
		endif;
		// Upload product featured image
		if(!empty($_FILES['productFeaturedImg']['name'])){
			$wordpress_upload_dir = wp_upload_dir();
			// $wordpress_upload_dir['path'] is the full server path to wp-content/uploads/2022/06, for multisite works good as well
			// $wordpress_upload_dir['url'] the absolute URL to the same folder, actually we do not need it, just to show the link to file
			$i = 1; // number of tries when the file with the same name is already exists
			$productFeaturedImg = $_FILES['productFeaturedImg'];
			$new_file_path = $wordpress_upload_dir['path'] . '/' . $productFeaturedImg['name'];
			$new_file_mime = mime_content_type($productFeaturedImg['tmp_name']);
			while(file_exists($new_file_path)){
				$i++;
				$new_file_path = $wordpress_upload_dir['path'] . '/' . $i . '_' . $productFeaturedImg['name'];
			}
			$product_image_file_path = "";
			if(move_uploaded_file($productFeaturedImg['tmp_name'], $new_file_path)){
				$upload_id = wp_insert_attachment(array(
					'guid'           => $new_file_path, 
					'post_mime_type' => $new_file_mime,
					'post_title'     => preg_replace( '/\.[^.]+$/', '', $productFeaturedImg['name'] ),
					'post_content'   => '',
					'post_status'    => 'inherit'
				), $new_file_path);
				// wp_generate_attachment_metadata() won't work if you do not include this file
				require_once(ABSPATH . 'wp-admin/includes/image.php');
				// Generate and save the attachment metas into the database
				wp_update_attachment_metadata($upload_id, wp_generate_attachment_metadata($upload_id, $new_file_path));
				// Show the uploaded file in browser
				// wp_redirect($wordpress_upload_dir['url'] . '/' . basename($new_file_path));
				$product_image_file_path = $wordpress_upload_dir['url'] . '/' . basename($new_file_path); // uploaded image
				$product_images[]['src'] = $product_image_file_path; 
			}
		}
		// Upload product gallery
		if(!empty($_FILES['productGallery']['name'][0])){
	        $files = $_FILES['productGallery'];
	        foreach ($files['name'] as $key => $value){
	            if($files['error'][$key] == 1){ continue; }  // Check for error
	            if ($files['name'][$key]){
	                $file = array(
	                'name' => $files['name'][$key],
	                'type' => $files['type'][$key],
	                'tmp_name' => $files['tmp_name'][$key],
	                'error' => $files['error'][$key],
	                'size' => $files['size'][$key]
	                );
	            }
	            $_FILES = array("muti_files" => $file);
	            $i=1;
				require_once(ABSPATH . "wp-admin" . '/includes/image.php');
				require_once(ABSPATH . "wp-admin" . '/includes/file.php');
				require_once(ABSPATH . "wp-admin" . '/includes/media.php');
                foreach($_FILES as $file => $array){
                    	// if ($_FILES[$file]['error'] !== UPLOAD_ERR_OK) continue;
                        if($_FILES[$file]['error'] !== UPLOAD_ERR_OK) __return_false();
                        $attachment_id = media_handle_upload($file, $post_id);
                        $vv .= $attachment_id . ",";
                        $i++;
                        $product_images[]['src'] = wp_get_attachment_image_src($attachment_id, 'medium')[0];
                }
                update_post_meta($post_id, '_product_image_gallery',  $vv);
			}
		}

		// $client_key = "ck_64dc9790e0686fec661acbb2672473263433af33";
		// $client_secret = "cs_ba33d56a42e0dafcc4c2504fb0037f5ac32c699c";
		$user = "pyramidtimesystems";
		$pass = "pyramidtimesystems@123";
		// Insert product details
		$api_response = wp_remote_post("{$site_url}/wp-json/wc/v3/products", array(
		 	'headers' => array(
				'Authorization' => 'Basic ' . base64_encode("{$user}:{$pass}")
			),
			'body' => array(
				'name' => $product_name, // product title
				'status' => get_field('select_customer_status', 'options'), // product status
				'short_description' => $product_desc,
				'regular_price' => $product_price, // product price
				'categories' => $product_category,
				'attributes' => $product_attr,
				"images" => $product_images,
			)
		));
		$body = json_decode($api_response['body']);

		if(wp_remote_retrieve_response_message($api_response) === 'Created'){
			// Update author for product
			$arg = array(
		    	'ID' => $body->id,
		    	'post_author' => get_current_user_id(),
			);
			wp_update_post($arg);

			echo json_encode(array(
				'status' => true,
				'message' => "The product has been created",
			));
			exit;
		}
	}
} 

function wc_edit_product_data(){
	if($_POST['action'] == 'edit_product_data'){
		$site_url = get_site_url(); // get site url
		$product_images = []; // empty array
		$product_id = $_POST['product_id'];
		$product_name = $_POST['proname'];
		$product_desc = $_POST['prodesc'];
		$product_price = $_POST['proprice'];
		$product_cat = $_POST['procat'];
		// Get categories
		if(!empty($product_cat)){
			for($cat=0;$cat<count($product_cat);$cat++){
				$product_category[]['id'] = $product_cat[$cat];
			}
		}
		// Get attributes
		$attribute_taxonomies = wc_get_attribute_taxonomies();
		if(!empty($attribute_taxonomies)):
			$i = 0;
			foreach($attribute_taxonomies as $tax):
				foreach($_POST as $key => $value){
					if($key == $tax->attribute_name ){
						if(!empty($_POST[$tax->attribute_name][0])){
							$product_attr[$i]['name'] = $tax->attribute_name;
							$product_attr[$i]['options'][] = $_POST[$tax->attribute_name][0];
						}
						$i++;
					}
				}
			endforeach;
		endif;
		// Upload product featured image
		if(!empty($_FILES['productFeaturedImg']['name'])){
			$wordpress_upload_dir = wp_upload_dir();
			// $wordpress_upload_dir['path'] is the full server path to wp-content/uploads/2022/06, for multisite works good as well
			// $wordpress_upload_dir['url'] the absolute URL to the same folder, actually we do not need it, just to show the link to file
			$i = 1; // number of tries when the file with the same name is already exists
			$productFeaturedImg = $_FILES['productFeaturedImg'];
			$new_file_path = $wordpress_upload_dir['path'] . '/' . $productFeaturedImg['name'];
			$new_file_mime = mime_content_type($productFeaturedImg['tmp_name']);
			while(file_exists($new_file_path)){
				$i++;
				$new_file_path = $wordpress_upload_dir['path'] . '/' . $i . '_' . $productFeaturedImg['name'];
			}
			$product_image_file_path = "";
			if(move_uploaded_file($productFeaturedImg['tmp_name'], $new_file_path)){
				$upload_id = wp_insert_attachment(array(
					'guid'           => $new_file_path, 
					'post_mime_type' => $new_file_mime,
					'post_title'     => preg_replace( '/\.[^.]+$/', '', $productFeaturedImg['name'] ),
					'post_content'   => '',
					'post_status'    => 'inherit'
				), $new_file_path);
				// wp_generate_attachment_metadata() won't work if you do not include this file
				require_once(ABSPATH . 'wp-admin/includes/image.php');
				// Generate and save the attachment metas into the database
				wp_update_attachment_metadata($upload_id, wp_generate_attachment_metadata($upload_id, $new_file_path));
				// Show the uploaded file in browser
				// wp_redirect($wordpress_upload_dir['url'] . '/' . basename($new_file_path));
				$product_image_file_path = $wordpress_upload_dir['url'] . '/' . basename($new_file_path); // uploaded image
				$product_images[]['src'] = $product_image_file_path;
			}
		}
		else{
			$img_src = get_the_post_thumbnail_url($product_id);
			$product_images[]['src'] = $img_src;
		}
		// Upload product gallery
		if(!empty($_FILES['productGallery']['name'][0])){
	        $files = $_FILES['productGallery'];
	        foreach ($files['name'] as $key => $value){
	            if($files['error'][$key] == 1){ continue; }  // Check for error
	            if($files['name'][$key]){
	                $file = array(
	                'name' => $files['name'][$key],
	                'type' => $files['type'][$key],
	                'tmp_name' => $files['tmp_name'][$key],
	                'error' => $files['error'][$key],
	                'size' => $files['size'][$key]
	                );
	            }
	            $_FILES = array("muti_files" => $file);
	            $i=1;
				require_once(ABSPATH . "wp-admin" . '/includes/image.php');
				require_once(ABSPATH . "wp-admin" . '/includes/file.php');
				require_once(ABSPATH . "wp-admin" . '/includes/media.php');
	            foreach($_FILES as $file => $array){
	                  // if ($_FILES[$file]['error'] !== UPLOAD_ERR_OK) continue;
	                  if($_FILES[$file]['error'] !== UPLOAD_ERR_OK) __return_false();
	                    $attachment_id = media_handle_upload($file, $post_id);
	                    $vv .= $attachment_id . ",";
	                    $i++;
	                    $product_images[]['src'] = wp_get_attachment_image_src($attachment_id, 'medium')[0];
	            }
	            update_post_meta($post_id, '_product_image_gallery',  $vv);
			}
		}
		else{
			$product = wc_get_product($product_id);
			$attachment_ids = $product->get_gallery_image_ids();
			foreach($attachment_ids as $attachment_id){
				$gallery_src = wp_get_attachment_url($attachment_id);
				$product_images[]['src'] = $gallery_src;
			}
		}
		// $client_key = "ck_64dc9790e0686fec661acbb2672473263433af33";
		// $client_secret = "cs_ba33d56a42e0dafcc4c2504fb0037f5ac32c699c";
		$user = "pyramidtimesystems";
		$pass = "pyramidtimesystems@123";
		// Update product details
		$api_response = wp_remote_post("{$site_url}/wp-json/wc/v3/products/{$product_id}", array(
			'method'    => 'PUT',
		 	'headers' => array(
				'Authorization' => 'Basic ' . base64_encode("{$user}:{$pass}")
			),
			'body' => array(
				'name' => $product_name, // product title
				'short_description' => $product_desc,
				'regular_price' => $product_price, // product price
				'categories' => $product_category,
				'attributes' => $product_attr,
				"images" => $product_images,
			)
		));
		$body = json_decode($api_response['body']);
		if(wp_remote_retrieve_response_message($api_response) === 'OK'){
			echo json_encode(array(
				'status' => true,
				'message' => "The product has been updated",
			));
			exit;
		}
	}
}

function wc_delete_customer_product_func(){
	if($_POST['action'] == 'delete_customer_product'){
		$site_url = get_site_url(); // get site url
		$product_id = $_POST['product_id']; // get product id
		$user = "pyramidtimesystems";
		$pass = "pyramidtimesystems@123";
		$api_response = wp_remote_post("{$site_url}/wp-json/wc/v3/products/{$product_id}", array(
			// ?force=true
			'method'    => 'DELETE',
		 	'headers' => array(
				'Authorization' => 'Basic ' . base64_encode("{$user}:{$pass}")
			),
		));
		$body = json_decode($api_response['body']);
		if(wp_remote_retrieve_response_message($api_response) === 'OK'){
			echo json_encode(array(
				'status' => true,
				'message' => "The product has been removed",
			));
			exit;
		}
		else{
			echo json_encode(array(
				'status' => false,
				'message' => "There is an error to remove product",
			));
			exit;	
		}
	}
}



?>