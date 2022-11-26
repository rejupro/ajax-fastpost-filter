(function($){

    $('#fastinput').val('');
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

    // Post by Publisher
    // Publication by Publisher 
	$('#fast_postpublisher').on('change', function(){
		var publisherin = $(this).val();
		$.ajax({
            type: 'post',
            url: fastAjaxpostpublisher.ajaxurl,
            data: {
                action: 'fast_ajax_postpublisher',
                publisherin: publisherin,
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
	})

    $('.month_filter').hide();
    $('#fast_year').on('change', function(){
        var fastYear = $(this).val();
        $.ajax({
            type: 'post',
            url: fastAjaxyear.ajaxurl,
            data: {
                action: 'fast_ajax_fastYear',
                fastYear: fastYear,
            },
            beforeSend: function(){
                $('.ajax-searchloading').addClass('lds-hourglass');
            },
            success:function(data){
                $('.blog_default').hide();
                $('.month_filter').show();
                $('.ajax-searchloading').removeClass('lds-hourglass');
                $('#searchOutput').html(data);
            }
        });
        return false;
    })
    // Month
    $('#fast_month').on('change', function(){
        var fastMonthin = $(this).val();
        var fastYearin = $('#fast_year').val();
        $.ajax({
            type: 'post',
            url: fastAjaxmonth.ajaxurl,
            data: {
                action: 'fast_ajax_ajaxMonthin',
                fastMonthin: fastMonthin,
                fastYearin: fastYearin,
            },
            beforeSend: function(){
                $('.ajax-searchloading').addClass('lds-hourglass');
            },
            success:function(data){
                $('.blog_default').hide();
                $('.month_filter').show();
                $('.ajax-searchloading').removeClass('lds-hourglass');
                $('#searchOutput').html(data);
            }
        });

        return false;
    })



})(jQuery);