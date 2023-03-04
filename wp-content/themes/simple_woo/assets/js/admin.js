jQuery(document).on('ready', function(){
	// Datepicker
	jQuery('.datepicker').datepicker();

	// For classic editor plugin
	// jQuery("<span style='color:red;'>Maximum character limit is 300 characters</span>").
	// insertAfter(".wp-admin #post #title");	
	// jQuery("<span style='color:red;'>Minimum character limit is 300 characters and then publish/update post.</span>").
	// insertAfter(".wp-admin #post #post-status-info");
	

	// // Set character limit for post title
	// jQuery('.wp-admin #post #title').on('keyup', function(e){
	// 	jQuery(this).attr('maxLength', 300);
	// });

	// // Set character limit for post content
	// jQuery('.wp-admin #post #content').on('keyup', function(e){
	// 	jQuery(this).attr('minLength', 300);
	// 	jQuery("#publish").prop('disabled', true);

	// 	if(jQuery(this).val().length > 300){
	// 		jQuery("#publish").prop('disabled', false);
	// 	}
	// });

});