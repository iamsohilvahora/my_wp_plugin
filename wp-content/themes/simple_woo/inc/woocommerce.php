<?php
// add woocommerce support
function add_woocommerce_support(){
    add_theme_support( 'woocommerce', array(
        'thumbnail_image_width' => 150,
        'single_image_width'    => 300,
        'product_grid'          => array(
            'default_rows'    => 3,
            'min_rows'        => 2,
            'max_rows'        => 8,
            'default_columns' => 4,
            'min_columns'     => 2,
            'max_columns'     => 5,
        )
    ));
    // add_theme_support('wc-product-gallery-zoom');
    // add_theme_support('wc-product-gallery-lightbox');
    // add_theme_support('wc-product-gallery-slider');
    // remove_theme_support( 'wc-product-gallery-zoom' );
    // remove_theme_support( 'wc-product-gallery-lightbox' );
    // remove_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'add_woocommerce_support');

/**
 * Shop page 
*/
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar');
// Start wrapper
function wc_start_wrapper_func(){
    echo "<div class='product_main_wrapper'>";
    echo "<div class='product_wrap'>";
}
add_action('woocommerce_before_shop_loop', 'wc_start_wrapper_func', 31);
// Show sidebar after shop loop
add_action('woocommerce_after_shop_loop', 'woocommerce_get_sidebar', 12);
// End wrapper
function wc_end_wrapper_func(){
    echo "</div>";
}
add_action('woocommerce_after_shop_loop', 'wc_end_wrapper_func', 11);
add_action('woocommerce_after_shop_loop', 'wc_end_wrapper_func', 13);


/**
 * Single product page 
*/
// Removing elements
// remove title
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
// remove rating stars
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
// remove product meta 
remove_action( 'woocommerce_single_product_summary','woocommerce_template_single_meta', 40 );
// remove description
remove_action( 'woocommerce_single_product_summary','woocommerce_template_single_excerpt', 20 );
// remove images
// remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
// remove related products
// remove_action( 'woocommerce_after_single_product_summary','woocommerce_output_related_products',20);
// remove additional information tabs
// remove_action('woocommerce_after_single_product_summary','woocommerce_output_product_data_tabs',10);

// Reorder elements
// change order of description
// remove_action('woocommerce_single_product_summary','woocommerce_template_single_excerpt', 20);
// add_action('woocommerce_single_product_summary','woocommerce_template_single_excerpt', 6 );
add_action('woocommerce_single_product_summary','woocommerce_template_single_title', 11);

// Add elements
/**
 * Add some text after the product summary text.
 *
 * Hook: woocommerce_after_single_product_summary
 */
add_action('woocommerce_after_single_product_summary','text_after_single_product_summary', 21); // don't forget to add priority
function text_after_single_product_summary(){
    echo '<div class="text-after-product-summary">
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                </p>
          </div>';
}

// Edit product tabs
add_filter('woocommerce_product_tabs', 'remove_product_tabs', 98);
function remove_product_tabs($tabs){
    unset( $tabs['description'] ); // Remove the Description tab
    $tabs['reviews']['title'] = __( 'Ratings' ); // Rename the Reviews tab
    $tabs['additional_information']['priority'] = 5; // Additional information at first
    return $tabs;
}

// Customizing the product page with CSS scripts
function custom_product_style(){
    if(is_shop()){
        wp_register_style('product_css', get_template_directory_uri() . '/product.css', false, '1.0.0', 'all');
        wp_enqueue_style('product_css');
    }

    if(is_product() && is_single()){
        wp_register_style('single_product_css', get_template_directory_uri() . '/single-product.css', false, '1.0.0', 'all');
        wp_enqueue_style('single_product_css');
    }
}
add_action('wp_enqueue_scripts', 'custom_product_style');

// Unhook the WooCommerce wrappers
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

// Hook in your own functions to display the wrappers
// add_action('woocommerce_before_main_content', 'my_theme_wrapper_start', 10);
// add_action('woocommerce_after_main_content', 'my_theme_wrapper_end', 10);
function my_theme_wrapper_start(){
    echo '<section id="main">';
}
function my_theme_wrapper_end(){
    echo '</section>';
}

// get usr info in single product page
add_action('woocommerce_before_single_product','wp_get_user_info');
function wp_get_user_info(){
    if(is_user_logged_in()){
        $user = wp_get_current_user(); 
        printf ('<h1>Howdy ' .$user->user_nicename.'!</h1>');
        $roles = ( array ) $user->roles;
        if($roles[0]=='administrator'){
            echo "<h4><b>You are $roles[0]</h4></b>";
        }     
    }
    else{
        return array();
    }     
}

// Add new tab in single product page  
function woo_new_product_tab($tabs){ 
    // Adds the new tab 
    $tabs['test_tab'] = array(
        'title'     => __( 'New Product Tab', 'woocommerce' ),
        'priority'  => 50,
        'callback'  => 'woo_new_product_tab_content'
    );
    return $tabs;
}
add_filter('woocommerce_product_tabs', 'woo_new_product_tab');
function woo_new_product_tab_content(){
    echo '<h2>New Product Tab</h2><p>Here\'s your new product tab content</p>.'; 
}

// Alter Product Price Programmatically @ WooCommerce Frontend
function bbloomer_alter_price_display($price_html, $product){
    // ONLY ON FRONTEND
    if ( is_admin() ) return $price_html;
    
    // ONLY IF PRICE NOT NULL
    if ('' === $product->get_price()) return $price_html;
    
    // IF CUSTOMER LOGGED IN, APPLY 20% DISCOUNT   
    if(wc_current_user_has_role('customer')){
        $orig_price = wc_get_price_to_display($product);
        $price_html = wc_price($orig_price * 0.80);
    }
    return $price_html;
}
add_filter('woocommerce_get_price_html', 'bbloomer_alter_price_display', 9999, 2);

function bbloomer_alter_price_cart($cart){
    if(is_admin() && ! defined( 'DOING_AJAX')) return;
 
    if(did_action('woocommerce_before_calculate_totals') >= 2) return;
 
    // IF CUSTOMER NOT LOGGED IN, DONT APPLY DISCOUNT
    if(!wc_current_user_has_role('customer')) return;
 
    // LOOP THROUGH CART ITEMS & APPLY 20% DISCOUNT
    foreach($cart->get_cart() as $cart_item_key => $cart_item){
        $product = $cart_item['data'];
        $price = $product->get_price();
        $cart_item['data']->set_price($price * 0.80);
    }
}
add_action('woocommerce_before_calculate_totals', 'bbloomer_alter_price_cart', 9999);

/**********************************************************************************/
/************************ Add extra field to checkout page ************************/
/**********************************************************************************/
// Add text field to checkout page
function custom_checkout_text_field($checkout){
    echo '<div id="custom_checkout_field"><h2>' . __('Your experience with us') . '</h2>';

    woocommerce_form_field('custom_experience', array(
        'type' => 'text',
        'class' => array(
            'my-field-class form-row-wide'
        ),
        'label' => __('(1)Good (2)Very Good (3)Excited'),
        'placeholder' => __('Add Value Here'),
    ),
    $checkout->get_value('custom_experience'));
    echo '</div>';
}
add_action('woocommerce_after_order_notes', 'custom_checkout_text_field');

// Validation
function checkout_text_field_process(){
    // Show an error message if the field is not set.
    if(!$_POST['custom_experience']) wc_add_notice(__('Please Enter Your Experience Value!') , 'error');
}
add_action('woocommerce_checkout_process', 'checkout_text_field_process', 9999);

// Update value given in custom meta field
function custom_checkout_text_field_update_order_meta($order_id){
    if(!empty($_POST['custom_experience'])){
        update_post_meta($order_id, '_billing_experience_value',sanitize_text_field($_POST['custom_experience']));
    }
}
add_action('woocommerce_checkout_update_order_meta', 'custom_checkout_text_field_update_order_meta');

// Display field value on the order edit page
function custom_checkout_text_field_display_admin_order_meta($order){
    echo '<p><strong>'.__('Experience Field').':</strong> ' . get_post_meta($order->id, '_billing_experience_value', true) . '</p>';
}
add_action('woocommerce_admin_order_data_after_billing_address', 'custom_checkout_text_field_display_admin_order_meta', 10, 1);

// Checkout page phone number validation
function custom_checkout_phone_field_process(){
    global $woocommerce;
    // Check if error
    if(!(preg_match('/^[0-9-]{12}$/D', $_POST['billing_phone']))){ 
        wc_add_notice("Incorrect Phone Number! Please enter valid 10 digits phone number", 'error');
    }
}
add_action('woocommerce_checkout_process', 'custom_checkout_phone_field_process');

// Phone Placeholder and Input Mask
function checkout_phone_format($fields){
    $fields['billing']['billing_phone']['placeholder'] = '123-456-7890';
    $fields['billing']['billing_phone']['maxlength'] = 12; // 123-456-7890 is 12 chars long
    return $fields;
}
add_filter('woocommerce_checkout_fields', 'checkout_phone_format');

function custom_phone_mask(){
    wc_enqueue_js("
      $('#billing_phone')
      .keydown(function(e){
         var key = e.which || e.charCode || e.keyCode || 0;
         var phone = $(this);         
         if(key !== 8 && key !== 9){
           if(phone.val().length === 3){
            phone.val(phone.val() + '-'); // add dash after char #3
           }
           if(phone.val().length === 7){
            phone.val(phone.val() + '-'); // add dash after char #7
           }
         }
         return (key == 8 ||
           key == 9 ||
           key == 46 ||
           (key >= 48 && key <= 57) ||
           (key >= 96 && key <= 105));
        });
    ");
}
add_action('woocommerce_after_checkout_form', 'custom_phone_mask');

/******* Show Conditional Message Upon Country Selection @ WooCommerce Checkout **********/
// Add the message notification and place it over the billing section
// The "display:none" hides it by default  
function echo_notice_shipping(){
   echo '<div class="shipping-notice woocommerce-info" style="display:none">Please allow 5-10 business days for delivery after order processing.</div>';
}
add_action('woocommerce_before_checkout_billing_form', 'echo_notice_shipping');

// Show or hide message based on billing country
function show_notice_shipping(){
   wc_enqueue_js("
      // Set the country code that will display the message
      var countryCode = 'IN';
 
      // Get country code from checkout
      selectedCountry = $('select#billing_country').val();
 
      // Function to toggle message
      function toggle_upsell(selectedCountry){   
         if(selectedCountry == countryCode){
            $('.shipping-notice').show();
         }
         else{
            $('.shipping-notice').hide();
         }
      }
      // Call function
      toggle_upsell(selectedCountry);
      $('select#billing_country').change(function(){
         toggle_upsell(this.value);         
      });
   " );
}
add_action('woocommerce_after_checkout_form', 'show_notice_shipping');

// Limit woocommerce product quantity input in woocommerce
function woocommerce_quantity_input($args = array(), $product = null, $echo = true){
    if(is_null($product)){
        $product = $GLOBALS['product'];
    }
    $defaults = array(
      'input_id' => uniqid('quantity_'),
      'input_name' => 'quantity',
      'input_value' => '1',
      'classes' => apply_filters('woocommerce_quantity_input_classes', array( 'input-text', 'qty', 'text' ), $product),
      'max_value' => apply_filters('woocommerce_quantity_input_max', -1, $product),
      'min_value' => apply_filters('woocommerce_quantity_input_min', 0, $product),
      'step' => apply_filters('woocommerce_quantity_input_step', 1, $product),
      'pattern' => apply_filters('woocommerce_quantity_input_pattern', has_filter('woocommerce_stock_amount', 'intval') ? '[0-9]*' : ''),
      'inputmode' => apply_filters('woocommerce_quantity_input_inputmode', has_filter('woocommerce_stock_amount', 'intval') ? 'numeric' : ''),
      'product_name' => $product ? $product->get_title() : '',
    );
    $args = apply_filters('woocommerce_quantity_input_args', wp_parse_args($args, $defaults), $product);

    // get placed order quantity details
    global $post;
    $product_id = $post->ID;
    $customer = wp_get_current_user();
    // Get all customer orders
    $customer_orders = get_posts(array(
        'numberposts' => -1,
        'meta_key' => '_customer_user',
        'orderby' => 'date',
        'order' => 'DESC',
        'meta_value' => get_current_user_id(),
        'post_type' => wc_get_order_types(),
        'post_status' => array_keys(wc_get_order_statuses()), 'post_status' => array('wc-processing'),
    ));
    $Order_Array = [];
    $order_data = [];
    foreach($customer_orders as $customer_order){
        $orderq = wc_get_order($customer_order);
        foreach($orderq->get_items() as $item_id => $item){
            if($item->get_product_id() == $product_id){
                $order_data[] = [
                    "order_id" => $item->get_order_id(),
                    "product_id" => $item->get_product_id(),
                    "product_name" => $item->get_name(),
                    "quantity" => $item->get_quantity(),
                ];
            }
        }
    }
    $count_order_data = count($order_data);
    if($count_order_data == 1){
        $order_id = $order_data[0]['order_id'];
        $product_quantity = $order_data[0]['quantity'];
        // $max_product_quantity = 1;
    }
    else{
        // $max_product_quantity = 2;   
    }
    if(!empty($order_id)){
        $order = wc_get_order($order_id);
    }
    if(!empty($order)){
        $ordered_date_time = $order->get_date_created()->date_i18n('Y-m-d H:i:s');
        $Ordered_date_time_second = strtotime($ordered_date_time);
        $new_date_time = date('Y-m-d H:i:s',strtotime('+24 hours',strtotime($ordered_date_time)));
        $new_date_time_second = strtotime($new_date_time);
        $current_date_time = date('Y-m-d H:i:s');
        $current_date_time_second = strtotime($current_date_time);
    }
    $max_product_quantity = 2;
    if(!empty($product_quantity)){
        if($product_quantity == 1){
            // echo "You can order maximum of 2 product";
            $max_product_quantity = 1;
        }
        else if($product_quantity == 2){
            if($current_date_time_second < $new_date_time_second ){
                // echo "You cannot order this product, because you try to exceed maximum limit of 2, so you can order after 24 hour.";             
                remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
                remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
            }
            else{
                // echo "You can order maximum of 2 product";
                $max_product_quantity = 2;
            }
        }
    }

    // Apply sanity to min/max args - min cannot be lower than 0.
    $args['min_value'] = max( $args['min_value'], 0 );
    // Note: change 2 to whatever you like
    $args['max_value'] = 0 < $args['max_value'] ? $args['max_value'] : $max_product_quantity;

    // Max cannot be lower than min if defined.
    if('' !== $args['max_value'] && $args['max_value'] < $args['min_value']){
        $args['max_value'] = $args['min_value'];
    }

    $options = '';
    for($count = $args['min_value'];$count <= $args['max_value'];$count = $count + $args['step']){
        // Cart item quantity defined?
        if('' !== $args['input_value'] && $args['input_value'] >= 1 && $count == $args['input_value']){
            $selected = 'selected';      
        } 
        else $selected = '';
        $options .= '<option value="' . $count . '"' . $selected . '>' . $count . '</option>';
    }
    $string = '<div class="quantity"><span>Qty</span><select name="' . $args['input_name'] . '">' . $options . '</select></div>';
    if($echo){
        echo $string;
    } 
    else{
        return $string;
    }
}

// Get placed order information in product detail page
function wc_get_placed_order_info(){
    global $post;
    $product_id = $post->ID; // get product id
    $customer = wp_get_current_user(); // get current user
    // Get all customer orders
    $customer_orders = get_posts(array(
        'numberposts' => -1,
        'meta_key' => '_customer_user',
        'orderby' => 'date',
        'order' => 'DESC',
        'meta_value' => get_current_user_id(),
        'post_type' => wc_get_order_types(),
        'post_status' => array_keys(wc_get_order_statuses()), 
        'post_status' => array('wc-processing', 'wc-completed'),
    ));
    // echo "<pre>";
    // print_r($customer_orders);
    // echo "</pre>";
    $Order_Array = [];
    $order_data = [];
    foreach($customer_orders as $customer_order){
        $orderq = wc_get_order($customer_order);
        foreach($orderq->get_items() as $item_id => $item){
            if($item->get_product_id() == $product_id){
                $order_data[] = [
                    "order_id" => $item->get_order_id(),
                    "product_id" => $item->get_product_id(),
                    "product_name" => $item->get_name(),
                    "quantity" => $item->get_quantity(),
                ];
            }
        }
    }
    $count_order_data = count($order_data);
    if($count_order_data == 1){
        $order_id = $order_data[0]['order_id'];
        $product_quantity = $order_data[0]['quantity'];
    }
    else if($count_order_data == 2){
        $order_id = $order_data[0]['order_id'];
        // $product_quantity = $order_data[1]['quantity'];
        $product_quantity = 2;
    }
    if(!empty($order_id)){
        $order = wc_get_order($order_id);
    }
    if(!empty($order)){
        $ordered_date_time = $order->get_date_created()->date_i18n('Y-m-d H:i:s');
        $Ordered_date_time_second = strtotime($ordered_date_time);
        $new_date_time = date('Y-m-d H:i:s',strtotime('+24 hours',strtotime($ordered_date_time)));
        $new_date_time_second = strtotime($new_date_time);
        $current_date_time = date('Y-m-d H:i:s');
        $current_date_time_second = strtotime($current_date_time);
    }
    if(!empty($product_quantity)){
        if($product_quantity == 1){
            echo "You can order maximum of 2 product";
        }
        else if($product_quantity == 2){
            if($current_date_time_second < $new_date_time_second ){
                echo "You cannot order this product, because you try to exceed maximum limit of 2, so you can order after 24 hour.";
                remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
                remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
            }
            else{
                echo "You can order maximum of 2 product";
            }
        }
    }
}
add_action('woocommerce_single_product_summary', 'wc_get_placed_order_info', 29);

/*
 * taxonomy archive page
 */
// Remove taxonomy archive description
remove_action('woocommerce_archive_description', 'woocommerce_taxonomy_archive_description');

// Restrict product search to redirect on single product page
function wc_remove_search_redirect(){
    return false;        
}
add_filter('woocommerce_redirect_single_search_result', 'wc_remove_search_redirect', 10);

// Customize woocommerce shop page for variable product
function wc_template_loop_product_thumbnail(){
    global $post, $wp_query, $woocommerce, $attributes;
    $image_id = wc_get_product()->get_gallery_image_ids()[0]; 
    // Change product thumbnail image on hover
    if($image_id){
        echo wp_get_attachment_image($image_id);
    } 
    else{  //assuming not all products have galleries set
        echo wp_get_attachment_image(wc_get_product()->get_image_id()) ; 
    }
    $product = wc_get_product($wp_query->post);
    $output = "";
    if($product->is_type('variable')){
        if($product->is_in_stock()){
            $output .= '<span class="sizes">Available sizes: </span><br/>';
            // get all variation of products 
            $variations = $product->get_available_variations(); 
            foreach($variations as $variation){
                $variation_id = $variation['variation_id'];
                $variation_obj = new WC_Product_variation($variation_id);
                $stock = $variation_obj->get_stock_quantity();
                $dummy = $variation_obj->get_variation_attributes();
                if((is_int($stock) || ctype_digit($stock)) && (int)$stock > 0){
                    $output .= '<a class="availSize" href="'.get_permalink($post->ID).'?size='.$dummy['attribute_pa_size'].'&product_id='.$post->ID.'" data-size="10" data-product_id="101"><span>' . strtoupper($dummy['attribute_pa_size']) . '</span></a><br />';
                } 
                else{
                    $output .= '<a class="unavailSize" style="color: red;"><span>' . strtoupper($dummy['attribute_pa_size']) . '</span></a><br />';
                }
            }
            // $output .= "<div class='wc_wishlist_share_wrapper'>";
            // $output .= do_shortcode('[ti_wishlists_addtowishlist]');
            // $output .= "</div>";    
        }
    }
    echo $output;
}
add_action('woocommerce_before_shop_loop_item_title', 'wc_template_loop_product_thumbnail', 10);