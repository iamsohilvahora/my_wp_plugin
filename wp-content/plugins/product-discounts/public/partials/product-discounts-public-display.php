<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://author.example.com/
 * @since      1.0.0
 *
 * @package    Product_Discounts
 * @subpackage Product_Discounts/public/partials
 */

if($discount = get_post_meta(get_the_id(), '_product_discounts', true)){
    echo "<br /><br />";
    echo "<b>The product discount</b> is: ".$discount. "%";
}
?>

