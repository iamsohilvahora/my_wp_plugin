jQuery(document).ready(function () {
    jQuery('#list_of_book').DataTable();
    jQuery('#list_of_shelf_book').DataTable();

    // click on first ajax request
    jQuery("#first_ajax_request").on("click", function(){
        jQuery.ajax({
            method: "POST",
            dataType: "json",
            url: tech_book.ajax_url,
            data: {
              'action': 'admin_ajax_request',
              'param' : 'first_simple_ajax',
            },
            success: function(response){
                console.log(response);
            },
        });
    });

    // Create bookshelf
    jQuery("#frm_add_book_shelf").validate({
        submitHandler: function(){
            var formdata = new FormData(jQuery('#frm_add_book_shelf')[0]); // get form data
            formdata.append("action", "admin_ajax_request"); // add action
            formdata.append("param", "create_book_shelf"); // add param
            jQuery.ajax({
                method: "POST",
                dataType: "json",
                url: tech_book.ajax_url,
                data: formdata,
                processData: false,
                contentType: false,
                success: function(response){
                    if(response.status == 1){
                        alert(response.message);
                        window.location.reload();
                    }
                    else{
                        alert(response.message);
                        window.location.reload();
                    }
                }
            });
        }
    });

    // Delete bookshelf
    jQuery(".delete-book-shelf").on('click', function(){
        let con = confirm("Are you sure want to delete ?");
        if(con){
            let form_id = jQuery(this).attr("data-id"); // get form id
            jQuery.ajax({
                method: "POST",
                dataType: "json",
                url: tech_book.ajax_url,
                data: {
                    'action' : 'admin_ajax_request',
                    'param' : "delete_book_shelf",
                    'form_id' : form_id
                },
                success: function(response){
                    if(response.status == 1){
                        alert(response.message);
                        window.location.reload();
                    }
                    else{
                        alert(response.message);
                        window.location.reload();
                    }
                }
            });
        }
    });

    // create book
    jQuery("#frm_create_book").validate({
        submitHandler: function(){
            var formdata = new FormData(jQuery('#frm_create_book')[0]); // get form data
            formdata.append("action", "admin_ajax_request"); // add action
            formdata.append("param", "frm_create_book"); // add param
            jQuery.ajax({
                method: "POST",
                dataType: "json",
                url: tech_book.ajax_url,
                data: formdata,
                processData: false,
                contentType: false,
                success: function(response){
                    if(response.status == 1){
                        alert(response.message);
                        window.location.reload();
                    }
                    else{
                        alert(response.message);
                        window.location.reload();
                    }
                }
            });
        }
    });

    // Upload book image
    jQuery(document).on('click','#book_image', function(){
        var image = wp.media({
            title: "Upload book image",
            multiple: false
        }).open().on("select", function(e){
            let uploaded_image = image.state().get("selection").first();
            let image_data = uploaded_image.toJSON();
            jQuery("#show_book_image").attr('src', image_data.url);
            jQuery("#book_cover_image").val(image_data.url);
        });
    });

     // Delete bookshelf
     jQuery(".delete-book").on('click', function(){
        let con = confirm("Are you sure want to delete this book ?");
        if(con){
            let form_id = jQuery(this).attr("data-id"); // get form id
            jQuery.ajax({
                method: "POST",
                dataType: "json",
                url: tech_book.ajax_url,
                data: {
                    'action' : 'admin_ajax_request',
                    'param' : "delete_book",
                    'form_id' : form_id
                },
                success: function(response){
                    if(response.status == 1){
                        alert(response.message);
                        window.location.reload();
                    }
                    else{
                        alert(response.message);
                        window.location.reload();
                    }
                }
            });
        }
    });
});