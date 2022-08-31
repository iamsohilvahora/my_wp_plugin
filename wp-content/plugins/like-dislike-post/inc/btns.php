<?php 
	function wp_like_dislike_buttons($content){
		if(is_single()){
		    global $wpdb;
		    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		    $table_name = $wpdb->prefix . "likes_system";
		    $like_button_label = get_option('like_button_label');
		    $dislike_button_label = get_option('dislike_button_label');
		    $post_id = get_the_id();
		    $user_id = get_current_user_id(); 
		    // echo 'User IP Address - '.$_SERVER['REMOTE_ADDR'];  
		    // function getUserIpAddrs(){
		    //     if(!empty($_SERVER['HTTP_CLIENT_IP'])){
		    //         // ip from share internet
		    //         $ip = $_SERVER['HTTP_CLIENT_IP'];
		    //     }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		    //         // ip pass from proxy
		    //         $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		    //     }else{
		    //         $ip = $_SERVER['REMOTE_ADDR'];
		    //     }
		    //     return $ip;
		    // }
		    //echo 'User Real IP - '.getUserIpAddrs();
		    $like_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE user_id = $user_id AND post_id = $post_id AND like_count = 1");
		    $like_class = ($like_count > 0) ? "wp-like-btn-click" : "";
	        $dislike_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE user_id = $user_id AND post_id = $post_id AND dislike_count = 1");
	        $dislike_class = ($dislike_count > 0) ? "wp-dislike-btn-click" : "";
		    $like_button_wrap = '<div class="wp-buttons-container">';
		    $like_button = '<a href="javascript:;" onclick="wp_like_button_ajax('.$post_id.', '. $user_id.')" class="wp-btn wp-like-btn '.$like_class.'"><i class="fa fa-thumbs-up"></i>'.$like_button_label.'</a>';
		    $dislike_button = '<a href="javascript:;" onclick="wp_dislike_button_ajax('.$post_id.', '. $user_id.')" class="wp-btn wp-dislike-btn '.$dislike_class.'">'.$dislike_button_label.' <i class="fa fa-thumbs-down"></i></a>';
		    $like_button_wrap_end = '</div>';
		    $wp_ajax_response = '<div id="wpAjaxResponse" class="wp-ajax-response"><span></span></div>';
		    $content .= $like_button_wrap;
		    $content .= $like_button;
		    $content .= $dislike_button;
		    $content .= $like_button_wrap_end;
		    $content .= $wp_ajax_response;
		}
		return $content;
	}
	add_filter('the_content', 'wp_like_dislike_buttons');
?>