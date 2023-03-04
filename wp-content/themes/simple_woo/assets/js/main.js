jQuery(document).on('ready', function(){
	var variable_product_size,variable_product_id, data, site_url, urlParams;
	urlParams = new URLSearchParams(window.location.search);
	if(urlParams.has('size')){
		variable_product_size = urlParams.getAll('size')[0];
		jQuery('.single-product #pa_size').val(variable_product_size);
		var url = window.location.href;
		var a = url.indexOf("?");
		var b =  url.substring(a);
		var c = url.replace(b,"");
		url = c;
		window.history.pushState('', '', url);
	}

	// On submit product form (add new product)
	const validImageTypes = ['image/gif', 'image/jpeg', 'image/jpg', 'image/png']; // define image types
	jQuery(".product_form").on('submit', function(e){
		e.preventDefault();
  		var fd = new FormData(jQuery('.product_form')[0]); // get form data
     	fd.append("action", "add_product_data"); // add action
     	let product_price = jQuery('#product_price').val(); // get product price
     	// Product featured image validation
     	var image_file = jQuery("#fileToUpload");
     	if(image_file[0].files.length){
     		var image_name = image_file[0].files[0].name;
     		var image_type = image_file[0].files[0].type;
     		var image_size = image_file[0].files[0].size;
     	}
     	// Regular expressiion for price
        var priceReg1 = /^\d+$/;
        var priceReg2 = /\d+?\.\d+/;
        if(!(priceReg1.test(product_price) || priceReg2.test(product_price))){
            alert("Please entered valid product price");
        }
        else if(!validImageTypes.includes(image_type)){
            alert("Please upload image which contain jpg, jpeg, png, gif extension" + ' (Filename: ' + image_name + ' ) ');
        }
        else if(image_size > 100000){
            alert("File size is larger than 100kb, Please upload below 100kb" + ' (Filename: ' + image_name + ' ) ');
        }
		else{
			// Product gallery image validation
	     	let image_gallery = jQuery('#galleryToUpload');
			let imageGalleryValidation = true;
			if(image_gallery[0].files.length > 3){
				alert("You can upload upto 3 image in product gallery.");
				imageGalleryValidation = false;
			}
			else{
		     	for(var i = 0; i < image_gallery[0].files.length; i++){
		 	        var file_name = image_gallery[0].files[i].name;
		 	        var file_type = image_gallery[0].files[i].type;
		 	        var file_size = image_gallery[0].files[i].size;
		 	        if(!validImageTypes.includes(file_type)){
						alert("Please upload image which contain jpg, jpeg, png, gif extension" + ' (Filename: ' + file_name + ' ) ');
						imageGalleryValidation = false;
					}
					else if(file_size > 100000){
						alert("File size is larger than 100kb, Please upload below 100kb" + ' (Filename: ' + file_name + ' ) ');
						imageGalleryValidation = false;
					}
				}
			}	
			if(imageGalleryValidation){
				jQuery.ajax({
				    method: "POST",
				    dataType: "json",
				    url: pyramid.ajaxurl,
				    data: fd,
				    processData: false,
	        		contentType: false,
				    success: function(response){
				        if(response.status == true){
				        	alert("The product has been created");
				        	window.location.reload();
				        }
				    },
				});
			}
        }
        return false;
	});

	// On submit product form (Edit existing product)
	jQuery(".edit_product_form").on('submit', function(e){
		e.preventDefault();
  		var fd = new FormData(jQuery('.edit_product_form')[0]); // get form data
     	fd.append("action", "edit_product_data"); // add action
     	let product_price = jQuery('#new_product_price').val();
     	// const validImageTypes = ['image/gif', 'image/jpeg', 'image/jpg', 'image/png']; // define image types
     	// Product featured image validation
     	var image_file = jQuery("#newFileToUpload");
     	if(image_file[0].files.length){
     		var image_name = image_file[0].files[0].name;
     		var image_type = image_file[0].files[0].type;
     		var image_size = image_file[0].files[0].size;
     	}
     	// Regular expressiion for price
        var priceReg1 = /^\d+$/;
        var priceReg2 = /\d+?\.\d+/;
        if(!(priceReg1.test(product_price) || priceReg2.test(product_price))){
            alert("Please entered valid product price");
        }
        else if(image_file[0].files.length){
			if(!validImageTypes.includes(image_type)){
				alert("Please upload image which contain jpg, jpeg, png, gif extension" + ' (Filename: ' + image_name + ' ) ');
			}
			else if(image_size > 100000){
            	alert("File size is larger than 100kb, Please upload below 100kb" + ' (Filename: ' + image_name + ' ) ');
			}
			else{
				productFormData(fd);
        	}
        }
        else{
        	productFormData(fd);
        }
        return false;
	});

	// Send product edit form data
	function productFormData(fd){
		// Product gallery image validation
     	let image_gallery = jQuery('#newProductGallery');
		let imageGalleryValidation = true;
		if(image_gallery[0].files.length > 3){
			alert("You can upload upto 3 image in product gallery.");
			imageGalleryValidation = false;
		}
		else{
	     	for(var i = 0; i < image_gallery[0].files.length; i++){
	 	        var file_name = image_gallery[0].files[i].name;
	 	        var file_type = image_gallery[0].files[i].type;
	 	        var file_size = image_gallery[0].files[i].size;
	 	        if(!validImageTypes.includes(file_type)){
					alert("Please upload image which contain jpg, jpeg, png, gif extension" + ' (Filename: ' + file_name + ' ) ');
					imageGalleryValidation = false;
				}
				else if(file_size > 100000){
					alert("File size is larger than 100kb, Please upload below 100kb" + ' (Filename: ' + file_name + ' ) ');
					imageGalleryValidation = false;
				}
			}
		}
		if(imageGalleryValidation){
			jQuery.ajax({
			    method: "POST",
			    dataType: "json",
			    url: pyramid.ajaxurl,
			    data: fd,
			    processData: false,
        		contentType: false,
			    success: function(response){
			        if(response.status == true){
			        	alert("The product has been updated");
			        	window.location.reload();
			        }
			    },
			});
		}
    }
	
	// on click of product delete button for customer
	jQuery(".delete_product").on('click', function(e){
		let delete_product = confirm("Are you sure want to delete this product ?");
		if(delete_product){
			let product_id = jQuery(this).attr('data-id');
			jQuery.ajax({
			    method: "POST",
			    dataType: "json",
			    url: pyramid.ajaxurl,
			    data: {
			    	'action': 'delete_customer_product',
			    	'product_id': product_id
			    },
			    success: function(response){
			        console.log(response);
			        if(response.status == true && response.message == "The product has been removed"){
			        	alert("The product has been removed.");
			        	window.location.reload();
			        }
			        else if(response.status == false && response.message == "There is an error to remove product"){
			        	alert("There is an error to remove product");
			        }     
			    },
			});
		}
	});

	// Custom google recaptcha validation
    grecaptcha.ready(function(){
        grecaptcha.execute(pyramid.sitekey, { action: 'contact_form7_submission' }).then(function (token) {
          	jQuery('#recaptchaResponse').val(token);
      		jQuery('#contact_form_submission').click(function(event){
            	event.preventDefault(); // Prevent direct form submission
            	var formdata = new FormData(jQuery('.wpcf7-form')[0]); // get form data
            	formdata.append("action", "save_contact_form_7_data"); // add action
	            jQuery.ajax({
                    url: pyramid.ajaxurl,
                    type: 'post',
                    data: formdata,
                    dataType: 'json',
                    processData: false,
                 	contentType: false,
                    success: function(_response){
                        // The Ajax request is a success. _response is a JSON object
                        var error = _response.error;
                        var success = _response.success;
                        if(error != ""){
                            alert(error);
                        }
                        else{
                            // In case of success, display it to user
                            alert(success);
                            jQuery("#contact_form_submission").hide();
                            jQuery('.wpcf7-form')[0].reset();
                            jQuery('#show_result').html(_response.data);
                        }
                    },
                    error: function(jqXhr, json, errorThrown){
                        // In case of Ajax error too, display the result for demo purpose
                        var error = jqXhr.responseText;
          			   	alert(error);
                    }
	            });
			});
       });
    });	


});