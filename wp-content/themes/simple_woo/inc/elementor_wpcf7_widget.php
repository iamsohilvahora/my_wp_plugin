<?php
class ElementorWPCF7Widget extends \Elementor\Widget_Base{
	public function get_name(){
		return 'wpdev_wpcf7';
	}
	public function get_title(){
		return 'Contact form 7';
	}
	public function get_icon(){
		return 'eicon-price-list';
	}
	public function get_categories(){
		return ['general'];
	}
	protected function _register_controls(){
		$args = [
		    'post_type' => 'wpcf7_contact_form',
		    'post_status' => 'publish',
		    'posts_per_page' => -1,
		    'nopaging' => true
		];
		$query = new WP_Query($args);

		if($query->have_posts()):
			while($query->have_posts()) : $query->the_post();  
				$cf7_id_title = get_the_id().'_'.get_the_title();
				$options[$cf7_id_title] = get_the_title();	
			endwhile;
    	endif;    
		wp_reset_postdata();
		// Register content tab
		// Display contact form 7
		$this->start_controls_section(
			'content_section',
			[
				'label' => 'Contact form 7',
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'cf7form', [
				'label' => 'Select contact form 7',
				'type' => \Elementor\Controls_Manager::SELECT2,
				'options' => $options,
			]
		);
		$this->end_controls_section();
	}
	protected function render(){
		$attr = $this->get_settings_for_display();
		$cf7form = $attr['cf7form'];
		$cf7 = explode('_', $cf7form);
		if(!empty($cf7[0]) && !empty($cf7[1])){
			echo "[contact-form-7 id='{$cf7[0]}' title='$cf7[1]']";
		}
	}
}
?>