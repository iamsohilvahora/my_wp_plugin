jQuery(document).ready(function(){
    jQuery('#example').DataTable(); // Show datatable

    // form validation 
    jQuery('#playlist_form').validate({
        submitHandler: function(){
            var formdata = new FormData(jQuery('#playlist_form')[0]); // get form data
            formdata.append("action", "save_playlist_details"); // add action
            jQuery.ajax({
                method: "POST",
                dataType: "json",
                url: admin_ajax_custom.ajaxurl,
                data: formdata,
                processData: false,
                contentType: false,
                success: function(response) {
                    if(response.status == true){
                        alert(response.message);
                        window.location.reload();
                    }
                    else{
                        alert(response.message);
                    }
                }
            });
        }
    });

    // Upload media image
    jQuery('#media-upload').on('click', function(){
        var image = wp.media({
            title: 'Upload playlist image',
            multiple: false
        }).open().on("select", function(){
            var files = image.state().get("selection").first();
            var jsonFiles = files.toJSON();
            jQuery('#media-image').attr('src', jsonFiles.url);
            jQuery('#image-url').val(jsonFiles.url);
        });
    });

    // Upload media image
    jQuery('.playlist-delete').on('click', function(){
        var delete_confirm = confirm("Are you sure want to delete ?");

        if(delete_confirm){ // true
            let delete_id = jQuery(this).attr('data-id');
            jQuery.ajax({
                method: "POST",
                dataType: "json",
                url: admin_ajax_custom.ajaxurl,
                data: {
                    'action': 'delete_playlist_details',
                    'delete_id' : delete_id
                },
                success: function(response){
                    if(response.status == true){
                        alert(response.message);
                        jQuery("#table-playlist").html(response.template);
                    }
                    else{
                        alert(response.message);
                    }
                }
            }); 
        } 
    });
    

});