<?php
    // Movie block
    // Check function exists.
    if(function_exists('acf_register_block_type')){
        add_action('acf/init', 'my_acf_blocks_init');
    }
    function my_acf_blocks_init(){    
            // Register a Movie block.
            acf_register_block_type(array(
                'name'              => 'movie',
                'title'             => __('Movie'),
                'description'       => __('A custom movie block.'),
                'render_template'   => 'template-parts/blocks/movie/movie.php',
                'category'          => 'formatting',
                'keywords' => array('cinema', 'movie'),
                // 'icon' => 'book-alt',
                // 'render_template'   => get_template_directory() . '/template-parts/blocks/testimonial/testimonial.php',
                // 'enqueue_style'     => get_template_directory_uri() . '/template-parts/blocks/testimonial/testimonial.css',
                // 'enqueue_script'    => get_template_directory_uri() . '/template-parts/blocks/testimonial/testimonial.js',
            ));
    }
?>