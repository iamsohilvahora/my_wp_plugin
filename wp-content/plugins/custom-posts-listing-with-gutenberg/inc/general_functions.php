<?php
	/* jQuery-AJAX Load More function */
	function wp_loadmore_ajax_handler(){
		$post_html;
		$post_type = (isset($_POST['post_type'])) ? $_POST['post_type'] : '';
		$post_per_page = (isset($_POST['post_per_page'])) ? $_POST['post_per_page'] : '-1';

		$page = (isset($_POST['pageNumber'])) ? $_POST['pageNumber'] : 1;
		$pagedata = $post_per_page * $page - $post_per_page;

		$args = array(
			'post_status' => 'publish',
			'post_type' => $post_type,
			'posts_per_page' => $post_per_page,
			'order' => 'DESC',
			'paged' => $page,
			'offset' => $pagedata,          
		);

		$argscount = array(
			'post_status' => 'publish',
			'post_type' => $post_type,
			'posts_per_page' => -1,           
		);

		$the_query = new WP_Query($args);
		$count_post = new WP_Query($argscount);  
		$count = count($count_post->posts );
		// $post_html .= '<div class="row my-5">';

		if($the_query->have_posts()):
			while($the_query->have_posts()):
				$the_query->the_post();
				$post_id = $latest_news_blog_query->ID; 

				$post_html .= '<div class="col-md-4 my-5 articles-post">';
				$post_html .= '<h3><a href="'.get_the_permalink().'">'.get_the_title().'</a></h3>';
				$post_html .= '<div class="articles-inner"> 
				     <div class="articles-img">';

				if(has_post_thumbnail($post_id)):
					$thumb_img = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'full');

					$post_html .= '<img src="'.$thumb_img[0].'"  />';
				else: 
					$post_html .= "<p>No image available</p>"; 
				endif;  
				            
				$post_html .= '</div>';
				$post_html .= '<div class="articles-text">';
				     $excerpt_content = get_the_excerpt();
				     $contentText = substr($excerpt_content, 0, 100);
				        
				if(!empty($contentText)): 
					$post_html .= '<p>'.$contentText.'</p>';

					$post_html .= '<a class="read-more-button" href="'.get_the_permalink().'" style="color: red;font-size: 20px;">Read More</a>';
				endif;
				$post_html .= '</div>';
				$post_html .= '</div>';
				$post_html .= '</div>';
			endwhile;
		endif;
		// $post_html .= '</div>';

		echo json_encode(array(  
				'max_pages' => $the_query->max_num_pages,
				'page' => $page,
				'total_post'=>$count,
				'content' => $post_html,
				'posts' => json_encode($the_query->query_vars) // everything about your loop is here
		));
		exit;
	}

	// For infinite scroll and display post
	function wp_infinite_scroll_ajax_handler(){
		$post_html;
		$post_type = (isset($_POST['post_type'])) ? $_POST['post_type'] : '';
		$post_per_page = (isset($_POST['post_per_page'])) ? $_POST['post_per_page'] : '-1';
		$page = (isset($_POST['pageNumber'])) ? $_POST['pageNumber'] : 1;
		$pagedata = $post_per_page * $page - $post_per_page;

		$args = array(
			'post_status' => 'publish',
			'post_type' => $post_type,
			'posts_per_page' => $post_per_page,
			'order' => 'DESC',
			'paged' => $page,
			'offset' => $pagedata,          
		);

		$argscount = array(
			'post_status' => 'publish',
			'post_type' => $post_type,
			'posts_per_page' => -1,           
		);

		$the_query = new WP_Query($args);
		$count_post = new WP_Query($argscount);  
		$count = count($count_post->posts );
		// $post_html .= '<div class="row my-5">';

		if($the_query->have_posts()):
			while($the_query->have_posts()):
				$the_query->the_post();
				$post_id = $latest_news_blog_query->ID; 

				$post_html .= '<div class="col-md-4 my-5 articles-post">';
				$post_html .= '<h3><a href="'.get_the_permalink().'">'.get_the_title().'</a></h3>';
				$post_html .= '<div class="articles-inner"> 
				     <div class="articles-img">';

				if(has_post_thumbnail($post_id)):
					$thumb_img = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'full');

					$post_html .= '<img src="'.$thumb_img[0].'"  />';
				else: 
					$post_html .= "<p>No image available</p>"; 
				endif;  
				            
				$post_html .= '</div>';
				$post_html .= '<div class="articles-text">';
				$excerpt_content = get_the_excerpt();
				$contentText = substr($excerpt_content, 0, 100);
				        
				if(!empty($contentText)): 
					$post_html .= '<p>'.$contentText.'</p>';

					$post_html .= '<a class="read-more-button" href="'.get_the_permalink().'" style="color: red;font-size: 20px;">Read More</a>';
				endif;
				$post_html .= '</div>';
				$post_html .= '</div>';
				$post_html .= '</div>';
			endwhile;
		endif;
		// $post_html .= '</div>';

		echo json_encode(
		array(  
		'max_pages' => $the_query->max_num_pages,
		'page' => $page,
		'total_post'=>$count,
		'content' => $post_html,
		'posts' => json_encode($the_query->query_vars) // everything about your loop is here
		));
		exit;
	}



?>