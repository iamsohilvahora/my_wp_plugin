<?php 
// Adds widget: Product
class Product_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'product_widget',
			esc_html__( 'Product Widget', 'textdomain' )
		);
	}

	private $widget_fields = array(
		array(
			'label' => 'Limit Product',
			'id' => 'limitproduct_number',
			'type' => 'number',
		),
	);

	public function widget( $args, $instance ) {
		echo $args['before_widget'];

       if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

        $args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => $instance['limitproduct_number'],
            'order' => 'ASC',
        );

        $query = new WP_Query($args);
        $product = new WC_Product(get_the_id());
        if($query->have_posts()):
            while($query->have_posts()): $query->the_post(); ?>
                <div class="product-list">
                    <div class="product-thumb">
                        <?php the_post_thumbnail(); ?>
                    </div>
                    <div class="product-title">
                        <a href="<?php echo get_the_permalink(get_the_id()); ?>"><?php the_title(); ?></a>
                    </div>
                    <div class="product-price">
                        <?php echo $product->get_price_html(); ?>
                    </div>
                    <div class="product-btn">
                        <?php woocommerce_template_loop_add_to_cart(); ?>
                    </div>
                </div>
    
            <?php endwhile;
        else: 
            echo "No product found";
        endif;


		// if ( ! empty( $instance['title'] ) ) {
		// 	echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		// }

		// // Output generated fields
		// echo '<p>'.$instance['limitproduct_number'].'</p>';
		
		echo $args['after_widget'];
	}

	public function field_generator( $instance ) {
		$output = '';
		foreach ( $this->widget_fields as $widget_field ) {
			$default = '';
			if ( isset($widget_field['default']) ) {
				$default = $widget_field['default'];
			}
			$widget_value = ! empty( $instance[$widget_field['id']] ) ? $instance[$widget_field['id']] : esc_html__( $default, 'textdomain' );
			switch ( $widget_field['type'] ) {
				default:
					$output .= '<p>';
					$output .= '<label for="'.esc_attr( $this->get_field_id( $widget_field['id'] ) ).'">'.esc_attr( $widget_field['label'], 'textdomain' ).':</label> ';
					$output .= '<input class="widefat" id="'.esc_attr( $this->get_field_id( $widget_field['id'] ) ).'" name="'.esc_attr( $this->get_field_name( $widget_field['id'] ) ).'" type="'.$widget_field['type'].'" value="'.esc_attr( $widget_value ).'">';
					$output .= '</p>';
			}
		}
		echo $output;
	}

	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( '', 'textdomain' );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'textdomain' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php
		$this->field_generator( $instance );
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		foreach ( $this->widget_fields as $widget_field ) {
			switch ( $widget_field['type'] ) {
				default:
					$instance[$widget_field['id']] = ( ! empty( $new_instance[$widget_field['id']] ) ) ? strip_tags( $new_instance[$widget_field['id']] ) : '';
			}
		}
		return $instance;
	}
}

function register_product_widget() {
	register_widget( 'Product_Widget' );
}
add_action( 'widgets_init', 'register_product_widget' );

?>