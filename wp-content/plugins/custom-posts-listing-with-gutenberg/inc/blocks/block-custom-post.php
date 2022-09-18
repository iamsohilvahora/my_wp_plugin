<?php
	// Create category for gutenber editor block
	function wp_plugin_block_categories($categories){
	    return array_merge(
	        $categories,
	        [
	            [
	                'slug'  => 'bytes-tab',
	                'title' => __( 'Bytes Tab', 'txtdomain' ),
	            ],
	        ]
	    );
	}
	add_action('block_categories', 'wp_plugin_block_categories', 10, 2);
	// Create custom block using acf
	function acf_custom_posts_item_block(){
	    // check function exists
	    if(function_exists('acf_register_block')){
	        // register a custom posts item block
	        acf_register_block(array(
	            'name'              => 'custom-posts',
	            'title'             => __('Custom Posts'),
	            'description'       => __('A custom block for diffrent post types.'),
	            'render_template'   => plugin_dir_path(__FILE__).'/block-custom-posts-data.php',
	            'category'          => 'bytes-tab',
	            'icon'              => 'excerpt-view',
	            'keywords'          => array('post'),
	        ));
	    }
	    // Show acf field group to custom-posts block
	    if(function_exists('acf_add_local_field_group')):
	        acf_add_local_field_group(array(
	            'key' => 'group_1',
	            'title' => 'Custom Posts Block',
	            'fields' => array(
	                array(
	                    'key' => 'field_1',
	                    'label' => 'Select Posts',
	                    'name' => 'select_posts',
	                    'type' => 'select',
	                    'instructions' => '',
	                    'required' => 0,
	                    'conditional_logic' => 0,
	                    'wrapper' => array(
	                        'width' => '',
	                        'class' => '',
	                        'id' => '',
	                    ),
	                    'choices' => array(
	                    ),
	                    'default_value' => false,
	                    'allow_null' => 1,
	                    'multiple' => 0,
	                    'ui' => 0,
	                    'return_format' => 'value',
	                    'ajax' => 0,
	                    'placeholder' => '',
	                ),
	                array(
	                    'key' => 'field_2',
	                    'label' => 'Load Post',
	                    'name' => 'load_post',
	                    'type' => 'select',
	                    'instructions' => '',
	                    'required' => 0,
	                    'conditional_logic' => 0,
	                    'wrapper' => array(
	                        'width' => '',
	                        'class' => '',
	                        'id' => '',
	                    ),
	                    'choices' => array(
	                        'load_more' => 'Load More',
	                        'infinite_loading' => 'Infinite Loading',
	                    ),
	                    'default_value' => false,
	                    'allow_null' => 1,
	                    'multiple' => 0,
	                    'ui' => 0,
	                    'return_format' => 'value',
	                    'ajax' => 0,
	                    'placeholder' => '',
	                ),
	                array(
	                    'key' => 'field_3',
	                    'label' => 'Posts Per Page',
	                    'name' => 'posts_per_page',
	                    'type' => 'number',
	                    'instructions' => '',
	                    'required' => 0,
	                    'conditional_logic' => array(
	                        array(
	                            array(
	                                'field' => 'field_2',
	                                'operator' => '==contains',
	                                'value' => 'load_more',
	                            ),
	                        ),
	                    ),
	                    'wrapper' => array(
	                        'width' => '',
	                        'class' => '',
	                        'id' => '',
	                    ),
	                    'default_value' => 3,
	                    'placeholder' => '',
	                    'prepend' => '',
	                    'append' => '',
	                    'min' => '',
	                    'max' => '',
	                    'step' => '',
	                ),
	            ),
	            'location' => array(
	                array(
	                    array(
	                        'param' => 'block',
	                        'operator' => '==',
	                        'value' => 'acf/custom-posts',
	                    ),
	                ),
	            ),
	            'menu_order' => 0,
	            'position' => 'normal',
	            'style' => 'default',
	            'label_placement' => 'top',
	            'instruction_placement' => 'label',
	            'hide_on_screen' => '',
	            'active' => true,
	            'description' => '',
	        ));
	    endif;
	}
	add_action('acf/init', 'acf_custom_posts_item_block');
	// add different post types to select posts field 
	function acf_load_select_posts_field_choices($field){
	    // reset choices
	    $field['choices'] = array();
	    // get list of post type
		$args = array(
			'public' => true,
		);
		$output = 'names'; // names or objects, here names is the default
		$operator = 'and'; // 'and' or 'or'
		$choices = get_post_types($args, $output, $operator); 
		// remove any unwanted white space
	    $choices = array_map('trim', $choices);
	    // loop through array and add to field 'choices'
	    if(is_array($choices)){
	        foreach($choices as $choice){
	        	if($choice == 'page' || $choice == 'attachment') continue;
				$field['choices'][ $choice ] = $choice;
	        }
	    }
	    return $field; 
	}
	add_filter('acf/load_field/name=select_posts', 'acf_load_select_posts_field_choices');
?>