jQuery(document).on('ready', function(){
	// Add discount form data
	jQuery("#woocommerce-discount-form").on('submit', function(e){
		e.preventDefault();
		let discount_name = jQuery("#discount_name").val();
		let discount_value = jQuery("#discount_value").val();
		// Check discount name and discount value is not empty
		if(discount_name && discount_name != '' && discount_value && discount_value != ''){
			var formdata = new FormData(jQuery('#woocommerce-discount-form')[0]); // get form data
			formdata.append("action", "save_product_discounts_details"); // add action
			// send ajax request 
			jQuery.ajax({
			    method: "POST",
			    dataType: "json",
			    url: plugin_ajax_object.ajaxurl,
			    data: formdata,
			    processData: false,
			    contentType: false,
			    success: function(response){
			        if(response.status == true){
			         	jQuery('#product-discount-status').remove();
			         	jQuery("#wpbody-content .wrap .wp-header-end").after(response.message);
			        	jQuery("#discount_name").val('');
						jQuery("#discount_value").val('');
						//setTimeout(wp_load_page, 3000); // load page after 3 second
			        }
			        if(response.status == false){
			        	jQuery('#product-discount-status').remove();
			        	jQuery("#wpbody-content .wrap .wp-header-end").after(response.message);
			        }
			    }
			});
		}
	});

	// Add discount form data
	jQuery(".delete-discount").on('click', function(e){
		e.preventDefault();
		let delete_confirm = confirm('Are you sure want to delete ?'); 

		if(delete_confirm){
			let delete_id = jQuery(this).attr('data-id');
			// Check id exists or not
			if(delete_id && delete_id != ''){
				// send ajax request 
				jQuery.ajax({
				    method: "POST",
				    dataType: "json",
				    url: plugin_ajax_object.ajaxurl,
				    data: {
				    	'action': 'delete_product_discounts_details',
				    	'delete_id': delete_id
				    },
				    success: function(response){
				        if(response.status == true){
				        	jQuery('#product-discount-status').remove();
				        	jQuery("#wpbody-content .wrap .wp-header-end").after(response.message);
				        }
				        if(response.status == false){
				        	jQuery('#product-discount-status').remove();
				        	jQuery("#wpbody-content .wrap .wp-header-end").after(response.message);
				        }
				    }
				});
			}
		}
	});

	// Edit discount form
	jQuery("#update-woocommerce-discount-form").on('submit', function(e){
		e.preventDefault();
		let discount_name = jQuery("#discount_name").val();
		let discount_value = jQuery("#discount_value").val();
		// Check discount name and discount value is not empty
		if(discount_name && discount_name != '' && discount_value && discount_value != ''){
			var formdata = new FormData(jQuery('#update-woocommerce-discount-form')[0]); // get form data
			formdata.append("action", "update_product_discounts_details"); // add action
			// send ajax request 
			jQuery.ajax({
			    method: "POST",
			    dataType: "json",
			    url: plugin_ajax_object.ajaxurl,
			    data: formdata,
			    processData: false,
			    contentType: false,
			    success: function(response){
			        if(response.status == true){
			        	jQuery('#product-discount-status').remove();
						jQuery("#wpbody-content .wrap .wp-header-end").after(response.message);
			        }
			        if(response.status == false){
			        	jQuery('#product-discount-status').remove();
			        	jQuery("#wpbody-content .wrap .wp-header-end").after(response.message);
			        }
			    }
			});
		}
	});

	// Remove notice element
	jQuery('body').on('click', '#product-discount-status .notice-dismiss', function(){
		jQuery('#product-discount-status').remove();
	});	
});