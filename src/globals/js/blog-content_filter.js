/* global jQuery */

jQuery(document).on( 'submit', '#ff', function() {
    var nextPageToRetrieve = 1;
    var $input = jQuery(this).find('input[name="ff_keywords_filter"]');
    var query = $input.val();
    var $content = jQuery('#content');
    //console.log ('submit' + query);
    var addval = $input.attr("value", query);
    //console.log(addval);
    jQuery.ajax({
        type: 'post',
        url: myAjax.ajaxurl,
        dataType: 'html',
        data: {
            action: 'load_search_results',
            query : query
        },
        success: function( response ) {
            $content.html( response );
        }
    });

    jQuery.ajax({
        type: 'post',
        url: myAjax.ajaxurl,
        dataType: 'html',
        data: {
            action: 'load_search_results_total',
            query: query
        },
        success: function (response) {
            jQuery('#title-section').html(response);
        }
    });

    return false;
})

jQuery(function ($) {
    var nextPageToRetrieve = 1;
    $('.more-btn').on('click', function (e) {
        e.preventDefault();
        nextPageToRetrieve++;
        var query = jQuery('#ff_keywords_filter').val();

        jQuery.ajax({
            type: 'post',
            url: myAjax.ajaxurl,
            dataType: 'html',
            data: {
                action: 'load_next_results',
                query: query,
                nextPageToRetrieve: nextPageToRetrieve
            },
            success: function( response ) {
                $('#load_more').html(response);
            }
        });

        jQuery.ajax({
            type: 'post',
            url: myAjax.ajaxurl,
            dataType: 'html',
            data: {
                action: 'load_page_total',
                query: query,
                nextPageToRetrieve: nextPageToRetrieve
            },
            success: function (response) {
                $('.c-pagination__count').html(response);
            }
        });
        return false;
        
    });
});