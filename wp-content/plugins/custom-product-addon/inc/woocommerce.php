<?php
// WooCommerce support in theme
function wc_theme_setup_func(){
    add_theme_support('woocommerce');
    remove_theme_support('wc-product-gallery-lightbox');
    remove_theme_support("wc-product-gallery-slider");
    remove_theme_support("wc-product-gallery-zoom");
}
add_action('after_setup_theme', 'wc_theme_setup_func', 100);

// Remove default content from single product detail page
function wc_add_or_remove_product_page_content(){
	if(is_single()){
    	remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
    	add_action('woocommerce_single_product_summary', 'woocommerce_template_customize_button', 31);
    	remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
    }
}
add_action('template_redirect', 'wc_add_or_remove_product_page_content');
// load product addon form on product detail page on "customize"button click
function woocommerce_template_customize_button(){ 
	global $product;
	$product_id = $product->id; // get current product id 
    $username_enable_disable = get_post_meta($product_id, '_username_enable_disable', true);
    $username_char_limit = get_post_meta($product_id, '_username_char_length_limit', true); ?>
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#customize_form">Customize</button>
    <!-- Modal -->
    <div class="modal fade" id="customize_form" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Fillout Forms</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post" class="product_form" enctype='multipart/form-data'>
                        <input type="hidden" name="custom_product_id" value="<?php echo $product_id; ?>" />
                        <?php if($username_enable_disable != 'no'): ?>
                        <div class="mb-3">
                            <label for="username" class="col-form-label">Username:</label>
                            <input type="text" class="form-control" name="username" id="username" maxLength="<?php echo $username_char_limit; ?>" required>
                        </div>
                        <?php endif; ?>
                        <div class="mb-3">
                            <label for="email" class="col-form-label">Email Address:</label>
                            <input type="email" class="form-control" name="email_address" id="email_address" required>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Add to cart" /> 
                    </form>      
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>  
            </div>
        </div>
    </div>
<?php }
// Add engraving text to cart item
function wc_add_engraving_text_to_cart_item($cart_item_data, $product_id, $variation_id){
    $username = filter_input(INPUT_POST, 'username');	
    $email_address = filter_input(INPUT_POST, 'email_address');
    if(!empty($username)){
        $cart_item_data['username'] = $username;
    }
    if(!empty($email_address)){
        $cart_item_data['email_address'] = $email_address;
    }
    return $cart_item_data;
}
add_filter('woocommerce_add_cart_item_data', 'wc_add_engraving_text_to_cart_item', 10, 3);
// Display engraving text on cart page
function wc_display_engraving_text_cart($item_data, $cart_item){
    if(!empty($cart_item['username']) || !empty($cart_item['email_address'])){
        $values = "<ul>";
        if(!empty($cart_item['username'])){
            $values .= "<li>Username: ".wc_clean($cart_item['username'])."</li>";
        }
        if(!empty($cart_item['email_address'])){
            $values .= "<li>Email: ".wc_clean($cart_item['email_address'])."</li>";
        }    
        $values .= "</ul>";
        $item_data['user_data'] = array(
            'key'     => 'User Data',
            'value'   => $values
        );
    }  
    return $item_data;
}
add_filter('woocommerce_get_item_data', 'wc_display_engraving_text_cart', 10, 2);
// Display engraving data on order - detail page, email and admin side.
function wc_wrap_order_meta_handler($item_id, $values, $cart_item_key){
    if(isset($values["username"]) || isset($values["email_address"])){
        $user_data = "";
        if(!empty($values["username"])){
            $username = "Username: ".$values["username"]."<br />";
            $user_data .= $username; 
        }
        if(!empty($values["email_address"])){
            $email = "Email: ".$values["email_address"];
            $user_data .= $email;
        }
        wc_add_order_item_meta($item_id, "User Data", $user_data);
    }
}
add_action('woocommerce_add_order_item_meta', 'wc_wrap_order_meta_handler', 99, 3);
// Add tab to Product Data area of Edit Product page
function wc_product_addon_tab_func($tabs){
    // Product addon tab
    $tabs['product_addon'] = array(
                    'label'    => 'Product Addon',
                    'target'   => 'product_addon_data',
                    'priority' => 90,
                );
    return $tabs;
}
add_filter('woocommerce_product_data_tabs', 'wc_product_addon_tab_func');
// Add 'Product addon' tab contents
function wc_product_addon_data_panel_func(){ ?>
    <div id="product_addon_data" class="panel woocommerce_options_panel">
        <div class="options_group">
        <?php
            echo '<div class="options_group">';
            // Checkbox field
            $username_enable_disable = get_post_meta(get_the_id(), '_username_enable_disable', true);
            $checked = $username_enable_disable ? $username_enable_disable : 'yes';
            woocommerce_wp_checkbox( 
                array( 
                    'id' => '_username_enable_disable', 
                    'label' => __('Enable/Disable Username', 'woocommerce' ),
                    'value' => $checked,
                ));

            // Number field
            woocommerce_wp_text_input(array(
                'id' => '_username_char_length_limit',
                'label' => __('Username Character Limit'), 
                'placeholder' => 'Enter maximum length for username field', 
                'type' => 'number',
                'custom_attributes' => array('step' => 'any', 'min' => '0')));
            echo '</div>';
        ?>
        </div>
    </div>
    <?php
}
add_action('woocommerce_product_data_panels', 'wc_product_addon_data_panel_func', 100);
// Save 'Product addon' data to postmeta.
function wc_save_product_addon_data_func($product_id){
    global $pagenow, $typenow;
    if('post.php' !== $pagenow || 'product' !== $typenow) return;
    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    
    // Save Product addon data
    if(isset($_POST['_username_enable_disable'])){
        if($_POST['_username_enable_disable']){
            update_post_meta($product_id, '_username_enable_disable', $_POST['_username_enable_disable']);
        }
    } 
    else{
        update_post_meta($product_id, '_username_enable_disable','no');
    }

    if(isset($_POST['_username_char_length_limit'])){
        if($_POST['_username_char_length_limit'] || $_POST['_username_char_length_limit'] == 0){
            update_post_meta($product_id, '_username_char_length_limit', $_POST['_username_char_length_limit']);
        }
    } 
    else{
        delete_post_meta($product_id, '_username_char_length_limit');
    }
}
add_action('save_post_product', 'wc_save_product_addon_data_func');
?>