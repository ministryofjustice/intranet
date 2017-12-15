/* global jQuery */

jQuery(document).on( 'submit', '#ff', function() {
    var $form = jQuery(this);
    var $input = $form.find('input[name="ff_keywords_filter"]');
    var query = $input.val();
    var $content = jQuery('#content');
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

    return false;
})