/* global jQuery */

// jQuery(document).on( 'submit', '#ff', function() {
//     var $form = jQuery(this);
//     var $input = $form.find('input[name="ff_keywords_filter"]');
//     var query = $input.val();
//     var $content = jQuery('#content');
//     jQuery.ajax({
//         type: 'post',
//         url: myAjax.ajaxurl,
//         dataType: 'html',
//         data: {
//             action: 'load_search_results',
//             query : query
//         },
//     success: function( response ) {
//         $content.html( response );
//     }
//     });

//     return false;
// })

jQuery(function ($) {
    var nextPageToRetrieve = 1;
    $('.more-btn').on('click', function (e) {
        e.preventDefault();
        nextPageToRetrieve++;

        jQuery.ajax({
            type: 'post',
            url: myAjax.ajaxurl,
            dataType: 'html',
            data: {
                action: 'load_search_results',
                nextPageToRetrieve: nextPageToRetrieve
            },
            success: function( response ) {
                $('#load_more').html(response);
            }
        });

        return false;
        
    });
});