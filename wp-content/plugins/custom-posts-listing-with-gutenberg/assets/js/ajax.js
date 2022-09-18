jQuery(document).ready(function(){
    /******   jQuery-AJAX Load More Button  *******/
    jQuery('.load-btn').on('click' , function(){
		postListFilter(this);
    });
    var pageNumber = 1;
    function postListFilter(obj){
        var post_type = jQuery(obj).attr('data-post');
        var post_per_page = jQuery(obj).attr('data-total-post');
        var loadMoreButton = jQuery('.load-more');
        var loadMoreText = jQuery(obj).text();
        if(loadMoreText !=''){
            pageNumber++;
        }
        else{
            pageNumber = 1;
        }
        var post_admin_URL = post_list_admin_URL_NAME.ajaxurl;
        var str = '&pageNumber=' + pageNumber + '&action=get_more_posts' + '&post_type=' + post_type + '&post_per_page=' + post_per_page;
        jQuery.ajax({
            type: "POST",
            dataType: "html",
            url: post_admin_URL,
            data: str,
            success: function(data){
                var obj = JSON.parse(data);
                // console.log(obj);
                if(loadMoreText !=''){  
                    jQuery(".append-post .row").append(obj.content);
                    if (obj.page == obj.max_pages){
                        loadMoreButton.hide(); //if last page, HIDE the button
                    } 
                }
                else{
                    jQuery(".append-post .row").append(obj.content);
                    if(obj.page == obj.max_pages){
                        loadMoreButton.hide(); //if last page, HIDE the button
                    } 
                    else{
                        loadMoreButton.show();
                    } 
                }
            },
        });
        return false;　
    }
});

// Code for infinite scroll
var canBeLoaded = true, // this param allows to initiate the AJAX call only if necessary
bottomOffset = 1000; // the distance (in px) from the page bottom when you want to load more posts
var pageNumber = 1;
jQuery(window).scroll(function(){
    var post_type = jQuery('.inifinite-post').attr('data-post');
    var post_per_page = jQuery('.inifinite-post').attr('data-total-post');
    if(jQuery(document).scrollTop() > (jQuery(document).height() - bottomOffset ) && canBeLoaded == true ){
        pageNumber = pageNumber + 1;
        var str = '&pageNumber=' + pageNumber + '&action=infinite_scroll_post' + '&post_type=' + post_type + '&post_per_page=' + post_per_page;
        jQuery.ajax({
            url : post_list_admin_URL_NAME.ajaxurl,
            type:'POST',
            data:str,
            beforeSend: function(xhr){
                // you see, the AJAX call is in process, we shouldn't run it again until complete
                canBeLoaded = false; 
            },
            success:function(data){
                var obj = JSON.parse(data);
                if(obj.content){
                    if(obj.page == obj.max_pages){
                        canBeLoaded = false; 
                        jQuery('.infinite-post-container .row').append(obj.content); // where to insert posts
                    } 
                    else{
                        jQuery('.infinite-post-container .row').append(obj.content); // where to insert posts
                        canBeLoaded = true; // the ajax is completed, now we can run it again
                    }
                }
            }
        });
    }
});