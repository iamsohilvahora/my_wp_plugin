<?php
class ElementorPostsWidget extends \Elementor\Widget_Base{
	public function get_name(){
		return 'wp_bytes_posts';
	}
	public function get_title(){
		return 'Posts with thumbnail';
	}
	public function get_icon(){
		return 'eicon-post-list';
	}
	public function get_categories(){
		return ['general'];
	}
	protected function _register_controls(){
		// Register content tab
		// Display title field
		$this->start_controls_section(
			'content_section',
			[
				'label' => 'Bytes Content',
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'list_title', [
				'label' => 'Enter Title',
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'Default Title',
				'label_block' => true,
			]
		);
		$this->end_controls_section();
		// Display color field
		$this->start_controls_section(
			'content_section',
			[
				'label' => 'WP Style',
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'list_color',
			[
				'label' => 'Change Color',
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#fff',
			]
		);
		$this->end_controls_section();
	}
	protected function render(){
		$attr = $this->get_settings_for_display();
		$title = $attr['list_title'];
		$color = $attr['list_color'];
		echo "<h2 style='color: {$color}'>{$title}<h2>";

		// display any 3 product 
		$args = [
		    'post_type' => 'product',
		    'post_status' => 'publish',
		    'posts_per_page' => 3,
		    'ignore_sticky_posts' => true
		];
		$query = new WP_Query($args);

		if($query->have_posts()):
			while($query->have_posts()) : $query->the_post(); ?> 
				<div style="display: flex;align-items: center;" class="elementor__posts">
					<figure class="img-fluid" style="margin-right: 1em;">
						<?php echo get_the_post_thumbnail($query->ID, 'thumbnail' ); ?>
					</figure>	
					<?php echo get_the_title(); ?>
				</div>
			<?php endwhile;
		else:
			echo __('No products found', 'textdomain');
    	endif;    
		wp_reset_postdata();



	}

}

?>