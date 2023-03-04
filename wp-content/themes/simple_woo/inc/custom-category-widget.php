<?php
	// create category list widget
	class Category_List_Widget extends WP_Widget{
		// php classnames and widget name/description added
		function __construct(){
			$widget_options = array(
			  'classname' => 'category_list_widget',
			  'description' => 'Add list of categories to your sidebar'
			);
			parent::__construct( 
			  'category_list_widget', 
			  'Simple Blog Category List', 
			  $widget_options 
			);
		}

		// create the widget output
		function widget($args, $instance){
			$title = apply_filters('widget_title', $instance['title']);
			$categories = get_categories(array(
				'orderby' => 'name',
				'order'   => 'ASC'
			  ));

			$cat_count = 0;
			$cat_col_one = [];
			$cat_col_two = [];
			foreach($categories as $category){
				$cat_count ++;
				$category_link = sprintf( 
			    	'<li class="list-unstyled"><a href="%1$s" alt="%2$s">%3$s</a></li>',
			    	esc_url(get_category_link($category->term_id)),
			    	esc_attr(sprintf(__('View all posts in %s', 'textdomain'), $category->name)),
			    	esc_html($category->name)
			    );
				if($cat_count % 2 != 0){
				    $cat_col_one[] = $category_link;
				}
				else{
				    $cat_col_two[] = $category_link;
				}
			}
			echo $args['before_widget'] . $args['before_title'] . $title . $args['after_title'];
			?>
			<div class="row">
				<div class="col-lg-6"><?php
			    	foreach($cat_col_one as $cat_one){
			      		echo $cat_one;
			    	} ?>
			  	</div>

			  	<div class="col-lg-6"><?php 
			    	foreach($cat_col_two as $cat_two){
			      		echo $cat_two;
			    	} ?>
			  	</div>
			</div><?php
			echo $args['after_widget'];
		}

		function form($instance){ 
			$title = !empty($instance['title']) ? $instance['title'] : ''; ?>
			<p>
		  		<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
		  		<input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>" />
			</p>
			<p>This widget displays all of your post categories as a two-column list (or a one-column list when rendered responsively).</p>
		<?php }

		// Update database with new info
		function update($new_instance, $old_instance){ 
			$instance = $old_instance;
			$instance['title'] = strip_tags($new_instance['title']);
			return $instance;
		}
	}

?>