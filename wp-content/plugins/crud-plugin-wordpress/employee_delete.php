<?php
function employee_delete(){
    if(isset($_GET['id'])){
        global $wpdb;
        $table_name = $wpdb->prefix.'employee_list';
        $id = $_GET['id'];
        $wpdb->delete(
            $table_name,
            array('id' => $id)
        );
    }
    $site = get_site_url() .'/wp-admin/admin.php?page=Employee_Listing';
    ?>
    <meta http-equiv="refresh" content="0; url=<?= $site ?>" />
    <?php
    // wp_redirect(admin_url('admin.php?page=Employee_List'), 301);
    // exit;
    // header("location:http://localhost/wordpressmyplugin/wordpress/wp-admin/admin.php?page=Employee_Listing");
}
?>