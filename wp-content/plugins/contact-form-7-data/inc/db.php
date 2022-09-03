<?php
    // Create table (wp_contact_form_7_data) for store contact form 7 data
    function wp_contact_form_7_table(){
        global $wpdb;

        $table_name = $wpdb->prefix . "contact_form_7_data";

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `name` varchar(255) NOT NULL,
                    `email` varchar(255) NOT NULL,
                    `subject` varchar(255) NOT NULL,
                    `message` varchar(255) NOT NULL,
                    `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
                    ) $charset_collate";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    // Save contact form 7 data into (wp_contact_form_7_data) table
    function save_form($wpcf7){
        global $wpdb;
        $submission = WPCF7_Submission::get_instance();
        if($submission){
            $submission = $submission->get_posted_data();
            $name = $submission['your-name'];
            $email = $submission['your-email'];
            $subject = $submission['your-subject'];
            $message = $submission['your-message'];
        }        
        else{
            echo 'error';
        }
        $table_name = $wpdb->prefix . "contact_form_7_data";
        $result = $wpdb->insert($table_name, array("name" => $name, "email" => $email, "subject" => $subject, "message" => $message), array("%s", "%s", "%s", "%s"));
        if(!$result){
            die('Invalid query: $insert_query : ' . mysqli_error($db));
        }
    }
    // remove_all_filters('wpcf7_before_send_mail');
    // remove_filter('wpcf7_skip_mail');
    // add_action('wpcf7_before_send_mail', 'save_form', 10, 1);
    add_action('wpcf7_mail_sent', 'save_form');

    // skip sending cf7 email
    // function mycustom_wpcf7_skip_mail($skip_mail, $contact_form){
    //     if(999999 == $contact_form->id()) {$skip_mail = true;}
    //     return $skip_mail;
    // }
    // add_filter('wpcf7_skip_mail', 'mycustom_wpcf7_skip_mail', 10, 2);
?>