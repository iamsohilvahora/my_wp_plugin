<?php
/**  
 * Allow add to cart product programatically 
 */ 
function wc_allow_add_to_cart_product_func(){
    if(!class_exists('WC_Form_Handler') || empty($_REQUEST['custom_product_id'])){
        return;
    } 
    remove_action('wp_loaded', array('WC_Form_Handler', 'add_to_cart_action'), 20);
    $product_id = $_REQUEST['custom_product_id']; // get current product id
    if($product_id){
        $product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($product_id));
        $was_added_to_cart = false;
        $adding_to_cart = wc_get_product($product_id);
        if(!$adding_to_cart){
            return;
        } 
        $add_to_cart_handler = apply_filters('woocommerce_add_to_cart_handler', $adding_to_cart->product_type, $adding_to_cart);
        $quantity = 1;
        $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
        if($passed_validation && false !== WC()->cart->add_to_cart($product_id, $quantity)){
            wc_add_to_cart_message(array($product_id => $quantity), true);
            WC()->cart->add_to_cart($product_id); // this adds the product with the ID; we can also add a second variable which will be the variation ID
		    wp_safe_redirect(wc_get_cart_url()); // redirects to the cart page
		    exit(); // safely closes the function
        }
    }
}
// Run before the WC_Form_Handler::add_to_cart_action callback
add_action('wp_loaded', 'wc_allow_add_to_cart_product_func', 15);
?>