(function($){
    $('#fastajax-searchboxform #fastinput').on('keyup', function(){
        var searchcontent = $('#fastinput').val();
        var searchNonce = $(this).attr('data-nonce');
        
        $.ajax({
            type: 'post',
            url: fastAjaxSearch.ajaxurl,
            data: {
                action: 'fast_ajax_searchresult',
                search_data: searchcontent,
                searchNonce: searchNonce,
            },
            beforeSend: function(){
                $('.ajax-searchloading').addClass('lds-hourglass');
            },
            success:function(data){
                if(searchcontent.length > 0){
                    $('.blog_default').hide();
                }else{
                    $('.blog_default').show();
                }
                $('.ajax-searchloading').removeClass('lds-hourglass');
                $('#searchOutput').html(data);
            }
        });

        return false;
    })
    $('#fastajax-searchboxform').on('submit', function(){
        return false;
    })

    $('#fast_cat').on('change', function(){
        var catin = $(this).val();
        $.ajax({
            type: 'post',
            url: fastAjaxcategory.ajaxurl,
            data: {
                action: 'fast_ajax_catin',
                catin: catin,
            },
            beforeSend: function(){
                $('.ajax-searchloading').addClass('lds-hourglass');
            },
            success:function(data){
                $('.blog_default').hide();
                $('.ajax-searchloading').removeClass('lds-hourglass');
                $('#searchOutput').html(data);
            }
        });

        return false;
    })

    $('#fast_author').on('change', function(){
        var authorin = $(this).val();
        $.ajax({
            type: 'post',
            url: fastAjaxauthor.ajaxurl,
            data: {
                action: 'fast_ajax_authorin',
                authorin: authorin,
            },
            beforeSend: function(){
                $('.ajax-searchloading').addClass('lds-hourglass');
            },
            success:function(data){
                $('.blog_default').hide();
                $('.ajax-searchloading').removeClass('lds-hourglass');
                $('#searchOutput').html(data);
            }
        });

        return false;
    })



})(jQuery);