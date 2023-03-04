<?php 
    // If user is not admin
	if(!is_admin()){
		return;
	}
	// get current admin page url
	$current_page = admin_url("admin.php?page=".$_GET["page"]); ?>

	<!-- Display datepicker form -->
	<form method="get" action="<?php echo $current_page; ?>">
		<input type="hidden" name="page" value="post_db_slug" />
		<div class="container">
			<h1 class="text-center text-success">Default Post Details</h1>
			<div class="row">
				<div class="col-md-5">
					<input type="text" name="start_date" placeholder="Select start date" class="form-control alertform datepicker" id="start_post_datepicker" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ""; ?>">
				</div>
				<div class="col-md-5">
					<input type="text" name="end_date" placeholder="Select end date" class="form-control alertform datepicker" id="end_post_datepicker" value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ""; ?>">
				</div>
				<div class="col-md-2">
					<input type="submit" class="btn btn-primary" value="Filter">
				</div>
			</div>
		</div>
	</form>

	<?php
		global $post;
		$perpage = 10;
		$curpage = isset($_GET['pagenum']) ? intval($_GET['pagenum']) : 1;
		if(isset($_GET['start_date']) && isset($_GET['end_date'])){
			$start_date = date('Y-m-d', strtotime($_GET['start_date']));
			$end_date = date('Y-m-d', strtotime($_GET['end_date']));

			// query for get total post using date filter
			$post_total_query = new WP_Query(
				array(
					'post_type' => 'post',
					'post_status' => 'publish',
					'posts_per_page' => -1,
				    'date_query' => array(
				        array(
				            'after'     => $start_date,
				            'before'    => $end_date,
				            'inclusive' => true,
				        ),
				    ),
				)
			);
			$total = $post_total_query->post_count;
			$pages = ceil($total/$perpage);
			$post_args = array(
				'post_type' => 'post',
				'post_status' => 'publish',
				'orderby'=> 'post_date', 
				'order' => 'DESC',
				'posts_per_page' => $perpage,
				'offset' => $perpage*($curpage-1),
			    'date_query' => array(
			        array(
			            'after'     => $start_date,
			            'before'    => $end_date,
			            'inclusive' => true,
			        ),
			    ),
			);
		}
		else{
			// query for get total post
			$post_total_query = new WP_Query(
				array(
					'post_type' => 'post',
					'post_status' => 'publish',
					'posts_per_page' => -1,
			));
			$total = $post_total_query->post_count;
			$pages = ceil($total/$perpage);
			$post_args = array(
					'post_type' => 'post',
					'post_status' => 'publish',
					'orderby'=> 'post_date', 
					'order' => 'DESC',
					'posts_per_page' => $perpage,
					'offset' => $perpage*($curpage-1)
				);
		}
		// execute query
		$post_query = new WP_Query($post_args);
		// Display post details
		if($post_query->have_posts()): ?>
			<table class="table table-bordered mt-5">
			    <thead class="thead-dark">
			        <tr class="text-center">
			            <th scope="col">id</th>
			            <th scope="col">Post Title</th>
			            <th scope="col">Post Excerpt</th>
			            <th scope="col">Post Status</th>
			            <th scope="col">Post Date</th>
			            <th scope="col">Post Author</th>
			        </tr>
			    </thead>
			    <tbody>
		    <?php
		    	$id = (($perpage * ($curpage - 1)) + 1);
		    	while($post_query->have_posts()):
		    		$post_query->the_post();
		    		$post_id = $post_query->ID; ?>
		    		<tr class="text-center">
		    		    <td><?= $id++; ?></td>
		    		    <td><?= $post->post_title; ?></td>
		    		    <td><?= substr($post->post_excerpt, 0, 50); ?></td>
		    		    <td><?= $post->post_status; ?></td>
		    		    <td><?= date("F j, Y", strtotime($post->post_date)); ?></td>
		    		    <td><?= get_the_author_meta('display_name', $post->post_author); ?></td>
		    		</tr>
		    	<?php
				endwhile;
				?>
			    </tbody>
			</table>
			<?php 
				// Display pagination 
				$pagin = array();
				for($i = 1; $i <= $pages; $i++){
					if($_GET['pagenum'] == $i): 
						$class = 'active'; 
					else:
					 	$class = '';
					endif;
			    	$url = $_SERVER['REQUEST_URI'] . "&pagenum=$i";
			    	$link = "<li class='page-item $class'><a class='page-link' href='$url'>$i</a></li>";
			    	if ($curpage != $i) $link = str_replace( '~', '', $link );
			    	$pagin[] = $link;
				}
				if($pages > 1):
					echo '<div id="post_pagination"><ul class="pagination justify-content-center">'. implode( '', $pagin ) .'</ul></div>';
				endif; ?>
				<form method="get" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
					<?php
						$url = $_SERVER['REQUEST_URI'];
						$components = parse_url($url);
						parse_str($components['query'], $results);
						foreach($results as $k=>$v): ?>
							<input type="hidden" name="<?php echo $k; ?>" class="button button-primary" value="<?php echo $v; ?>" /> 
						<?php endforeach; ?>
						<!-- Export button for csv file -->
						<input type="submit" name="export_all_posts" class="button button-primary" value="<?php _e('Export All Posts'); ?>" />
				</form>
		<?php		
		else:
			echo '<h3 class="text-danger">Post not found.</h3>';
		endif;	
?>