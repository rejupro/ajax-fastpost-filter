(function($){
    $('#fastajax-searchboxform #fastinput').bind('keyup change', function(){
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
                $('.ajax-searchloading').removeClass('lds-hourglass');
                $('#searchOutput').html(data);
            }
        });

        return false;
    })
    $('#fastajax-searchboxform').on('submit', function(){
        var searchcontent = $('#fastinput').val();
        $.ajax({
            type: 'post',
            url: fastAjaxSearch.ajaxurl,
            data: {
                action: 'fast_ajax_searchresult',
                search_data: searchcontent
            },
            beforeSend: function(){
                $('.ajax-searchloading').addClass('lds-hourglass');
            },
            success:function(data){
                $('.ajax-searchloading').removeClass('lds-hourglass');
                $('#searchOutput').html(data);
            }
        });

        return false;
    })
})(jQuery);