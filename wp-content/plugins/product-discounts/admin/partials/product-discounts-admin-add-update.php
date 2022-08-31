<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://author.example.com/
 * @since      1.0.0
 *
 * @package    Product_Discounts
 * @subpackage Product_Discounts/admin/partials
 */        
if(isset($_GET['edit_id'])){
    $edit_id = $_GET['edit_id'];    
    global $wpdb;
    $table_name = $wpdb->prefix."product_discounts";
    // get product discount detail using id
    $product_discount = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE ID = '$edit_id'")); ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Update Product Discount</h1>
        <hr class="wp-header-end">
   
        <form method="post" id="update-woocommerce-discount-form" enctype="multipart/form-data">
            <input type="hidden" name="edit_id" value="<?php echo $product_discount->id; ?>">
            <table class="form-table" role="presentation">
                <tbody>
                    <tr class="user-user-login-wrap">
                        <th>
                            <label for="discount_name">Discount Name <span class="description">(required)</span>
                            </label>
                        </th>
                        <td>
                            <input name="discount_name" type="text" id="discount_name" autocorrect="off" autocomplete="off" value="<?= $product_discount->discount_name; ?>" class="regular-text" required>
                        </td>
                    </tr>
                    <tr class="user-user-login-wrap">
                        <th>
                            <label for="discount_value">Discount Value <span class="description">(required)</span></label>
                        </th>
                        <td>
                            <input name="discount_value" type="number" id="discount_value" value="<?= $product_discount->discount_value; ?>" class="regular-text" required>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p class="submit"><input type="submit" class="button button-primary" value="Update Discount"></p>
        </form>
    </div>
<?php }
else{ ?>
    <div class="wrap" id="profile-page">
        <h1 class="wp-heading-inline">Add Product Discount</h1>
        <hr class="wp-header-end">
    
        <form method="post" id="woocommerce-discount-form" enctype="multipart/form-data">
            <table class="form-table" role="presentation">
                <tbody>
                    <tr class="user-user-login-wrap">
                        <th>
                            <label for="discount_name">Discount Name <span class="description">(required)</span>
                            </label>
                        </th>
                        <td>
                            <input name="discount_name" type="text" id="discount_name" autocorrect="off" autocomplete="off" class="regular-text" required>
                        </td>
                    </tr>
                    <tr class="user-user-login-wrap">
                        <th>
                            <label for="discount_value">Discount Value <span class="description">(required)</span></label>
                        </th>
                        <td>
                            <input name="discount_value" type="number" id="discount_value" class="regular-text" required>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p class="submit"><input type="submit" class="button button-primary" value="Add New Discount"></p>
        </form>
    </div>
<?php } ?>