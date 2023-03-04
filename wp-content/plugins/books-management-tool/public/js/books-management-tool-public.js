jQuery(function(){
	// click on first ajax request
    jQuery("#btn-frontend-ajax").on("click", function(){
        jQuery.ajax({
            method: "POST",
            dataType: "json",
            url: techy_book.ajaxurl,
            data: {
              'action': 'public_ajax_request',
              'param' : 'first_simple_ajax',
            },
            success: function(response){
                console.log(response);
            },
        });
    });
});