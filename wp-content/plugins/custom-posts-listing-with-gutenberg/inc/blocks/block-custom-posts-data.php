<?php
	/**********************************************/
	/*** Code for display posts using load more ***/
	/**********************************************/
	// Get acf field of custom posts Block field group 
	$post_type = get_field('select_posts');
	$count_args = array('post_type' => $post_type);
	$the_query = new WP_Query($count_args);
	$totalpost = $the_query->found_posts;

	// Get option for load post (load more or infinite scroll)
	$load_post = get_field('load_post');
	
	// posts per page by default set to 3
	$posts_per_page = 3;

	if($load_post == 'load_more'){
		$posts_per_page = get_field('posts_per_page'); 
	
		// check if post type is not empty
		if(!empty($post_type)){ ?>
			<div class="container latest-articles-container append-post">
				<div class="row">
			<?php
				$custom_posts_args = array(
					'post_type' => $post_type,
					'posts_per_page' => $posts_per_page,
					'post_status' => 'publish',
					'orderby'=> 'post_date', 
					'order' => 'DESC',
				);
			$custom_posts_query = new WP_Query($custom_posts_args);

			if($custom_posts_query->have_posts()):	   
				/* Start the Loop */
		        while($custom_posts_query->have_posts()):
					$custom_posts_query->the_post();
					$postID = get_the_ID();

					$title = get_the_title(); ?>

					<div class="col-md-4 my-5 articles-post">
						<h3><a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title();?></a></h3>
						<div class="articles-inner"> 
							<div class="articles-img">
								<?php 
									if(has_post_thumbnail($post_id)):
										$thumb_img = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'full'); 
								?>
										<img src="<?php echo $thumb_img[0]; ?>" alt="<?php echo get_the_title(); ?>" />
								<?php
									else: 
										echo "<p>No image available</p>"; 
									endif;	
								?>	
							</div>
							<div class="articles-text">
								<?php
									$excerpt_content = get_the_excerpt();
									$contentText = substr($excerpt_content, 0, 100);
								?>
								<?php if(!empty($contentText)): ?>
									<p><?php echo $contentText; ?></p>

									<a class="read-more-button" href="<?php echo get_the_permalink(); ?>" style="color: red;font-size: 20px;">Read More</a>
								<?php endif; ?>
							</div>
						</div>
					</div>	
					<!-- <hr /> -->
		        <?php
			        endwhile; 

				else:
					echo "<p>No post found</p>";
				endif;
				/* Restore original Post Data */
				wp_reset_postdata();
			?>
				</div>
			</div>

			<?php if($totalpost >= $posts_per_page && $posts_per_page > 0): ?>
			<div class="load-more text-center">
			    <a class="btn btn-primary load-btn my-5" href="javascript:void(0);" data-post='<?php echo $post_type; ?>' data-total-post='<?php echo $posts_per_page; ?>'>Load more</a>
			</div>
			<?php endif; ?>
		<?php }
	} ?>

	<?php
	/*****************************************************/
	//*** Code for display posts using infinite scroll ***/
	/*****************************************************/
	if($load_post == 'infinite_loading'){
		// get default posts_per_page value
		$posts_per_page = get_option('posts_per_page'); 
	
		// check if post type is not empty
		if(!empty($post_type)){ ?>
			<div class="container infinite-post-container append-post">
				<div class="row">
			<?php
				$custom_posts_args = array(
					'post_type' => $post_type,
					'posts_per_page' => $posts_per_page,
					'post_status' => 'publish',
					'orderby'=> 'post_date', 
					'order' => 'DESC',
				);
			$custom_posts_query = new WP_Query($custom_posts_args);

			if($custom_posts_query->have_posts()):	   
				/* Start the Loop */
		        while($custom_posts_query->have_posts()):
					$custom_posts_query->the_post();
					$postID = get_the_ID();

					$title = get_the_title(); ?>

				  <div class="col-md-4 my-5 articles-post">
					<h3><a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title();?></a></h3>
					<div class="articles-inner"> 
						<div class="articles-img">
							<?php 
								if(has_post_thumbnail($post_id)):
									$thumb_img = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'full'); 
							?>
									<img src="<?php echo $thumb_img[0]; ?>" alt="<?php echo get_the_title(); ?>" />
							<?php
								else: 
									 echo "<p>No image available</p>"; 
								endif;	
							?>	
						</div>
						<div class="articles-text">
							<?php
								$excerpt_content = get_the_excerpt();
								$contentText = substr($excerpt_content, 0, 100);
							?>
							<?php if(!empty($contentText)): ?>
								<p><?php echo $contentText; ?></p>

								<a class="read-more-button" href="<?php echo get_the_permalink(); ?>" style="color: red;font-size: 20px;">Read More</a>
							<?php endif; ?>
						</div>
					</div>
				</div>

		        <?php
			        endwhile;
			else:
				echo "<p>No post found</p>";
			endif;
			/* Restore original Post Data */
			wp_reset_postdata();
			?>
				</div>
			</div>
			<input type="hidden" class="inifinite-post" data-post='<?php echo $post_type; ?>' data-total-post='<?php echo $posts_per_page; ?>'>
		<?php }
	} 