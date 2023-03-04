<?php
	// Creating the widget
	class demo_widget extends WP_Widget{
		// The construct part
		function __construct(){
			parent::__construct(
				// widget ID
				'demo_widget',
				// widget name
				__('Sample Widget', 'wp_widget_domain'),
				// widget description
				array('description' => __('This is a widget', 'wp_widget_domain'), 
			));
		}

		// Creating widget front-end
		public function widget($args, $instance){
			$title = apply_filters('widget_title', $instance['title']);
			echo $args['before widget'];
			//if title is present
			if(!empty($title))
				echo $args['before_title'] . $title . $args['after_title'];
			//output
			echo __('Greetings from Bytestechnolab', 'wp_widget_domain');
			echo $args['after_widget'];
		}

		// Creating widget Backend
		public function form($instance){
			if(isset($instance['title']))
				$title = $instance['title'];
			else
				$title = __('Default Title', 'wp_widget_domain');
			?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
			</p>
		<?php
		}

		// Updating widget replacing old instances with new
		public function update($new_instance, $old_instance){
			$instance = array();
			$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
			return $instance;
		}
	}
?>