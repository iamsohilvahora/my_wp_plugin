<?php
    /**
     * Register a custom menu page.
     */
    function register_cf7_menu_page() {
        add_menu_page('Contact Form 7 Data',
            'CF7DB',
            'manage_options',
            'cfdb7_slug',
            'cfdb7_page_html',
            'dashicons-database-view',
            25
        );
    }
    add_action('admin_menu', 'register_cf7_menu_page');
    function cfdb7_page_html(){
        if(!is_admin()){
            return;
        }
        ?>
        <h1>User Contact Details</h1>
        <?php
            global $wpdb;
            $table_name = $wpdb->prefix . "contact_form_7_data";
            $posts = $wpdb->get_results("SELECT name, email, subject, message, time FROM $table_name");
            if($posts):
            ?>
            <table class="table">
                <thead class="thead-dark">
                    <tr class="text-center">
                        <th scope="col">id</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Subject</th>
                        <th scope="col">Message</th>
                        <th scope="col">Date</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $id = 1;
                    foreach($posts as $post):
                    ?>
                    <tr class="text-center">
                        <td><?= $id++; ?></td>
                        <td><?= $post->name; ?></td>
                        <td><?= $post->email; ?></td>
                        <td><?= $post->subject; ?></td>
                        <td><?= $post->message; ?></td>
                        <td><?= $post->time; ?></td>
                    </tr>
                    <?php 
                    endforeach;
                    ?>    
                </tbody>
            </table>
            <?php
                else:
            ?>
                <p>User Contact Details Not Found.</p>
            <?php        
                endif;    
    }
?>