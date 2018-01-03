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

        var post_id = jQuery(this).data('id');
        $.ajax({
              
            url: '/wp-json/wp/v2/posts/?per_page=5&page=' + nextPageToRetrieve,
            success: function (data) {
                //console.log(data);
                $.each(data, function (key, value) {
                    $('#load_more').append('<article class="c-article-item js-article-item">');
                    $('#load_more').append('<h1>' + value.title.rendered + '</h1>');
                    $('#load_more').append('<div class="c-article-exceprt"><p>' + value.excerpt.rendered + '</p></div>');
                    $('#load_more').append('</article>');
                });
            }
            
        });

        return false;
        
    });
});