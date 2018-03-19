/* global jQuery */

jQuery(function ($) {

    jQuery(document).on('submit', '#ff', function (e) {
        e.preventDefault();

        var nextPageToRetrieve = jQuery('#ff').data('page') + 1;
        jQuery('.more-btn').attr('data-page', nextPageToRetrieve);

        var $input = jQuery(this).find('input[name="ff_keywords_filter"]');
        var query = $input.val();
        var $content = jQuery('#content');
        var addval = $input.attr('value', query);

        var optionSelected = jQuery(this).find("#ff_date_filter option:selected");
        var valueSelected = optionSelected.val();

        var postType = jQuery('.data-type').data('type');

        jQuery.ajax({
            type: 'post',
            url: myAjax.ajaxurl,
            dataType: 'html',
            data: {
                action: 'load_search_results',
                query : query,
                valueSelected: valueSelected,
                postType: postType
            },
            success: function( response ) {
                $('.c-article-item').remove();
                $content.html( response );
            }
        });

        jQuery.ajax({
            type: 'post',
            url: myAjax.ajaxurl,
            dataType: 'html',
            data: {
                action: 'load_search_results_total',
                query: query,
                valueSelected: valueSelected,
                postType: postType
            },
            success: function (response) {
                jQuery('#title-section').html(response);
            }
        });

        jQuery.ajax({
            type: 'post',
            url: myAjax.ajaxurl,
            dataType: 'html',
            data: {
                action: 'load_page_total',
                query: query,
                nextPageToRetrieve: nextPageToRetrieve,
                valueSelected: valueSelected,
                postType: postType
            },
            success: function (response) {
                $('.c-pagination').html(response);
            }
        });
        return false;
    });

    jQuery('.c-pagination').on('click', function () {

        var nextPageToRetrieve = jQuery('.more-btn').data('page') + 1;
        jQuery('.more-btn').attr('data-page', nextPageToRetrieve);
        var query = jQuery('#ff_keywords_filter').val();
        var valueSelected = jQuery('.more-btn, .nomore-btn').data('date');
        var postType = jQuery('.data-type').data('type');

        jQuery.ajax({
            type: 'post',
            url: myAjax.ajaxurl,
            dataType: 'html',
            data: {
                action: 'load_next_results',
                query: query,
                nextPageToRetrieve: nextPageToRetrieve,
                valueSelected: valueSelected,
                postType: postType
            },

            success: function( response ) {
                $('#load_more').append(response);
            }
        });

        jQuery.ajax({
            type: 'post',
            url: myAjax.ajaxurl,
            dataType: 'html',
            data: {
                action: 'load_page_total',
                query: query,
                nextPageToRetrieve: nextPageToRetrieve,
                valueSelected: valueSelected,
                postType: postType
            },
            success: function (response) {
                $('.c-pagination').html(response);
            }
        });

        return false;

    });

    jQuery(document).on('submit', '#ff_events', function (e) {
        e.preventDefault();

        var nextPageToRetrieve = jQuery('#ff').data('page') + 1;
        jQuery('.more-btn').attr('data-page', nextPageToRetrieve);

        var $input = jQuery(this).find('input[name="ff_keywords_filter"]');
        var query = $input.val();
        var $content = jQuery('#content');
        var addval = $input.attr('value', query);

        var optionSelected = jQuery(this).find("#ff_date_filter option:selected");
        var valueSelected = optionSelected.val();

        var postType = jQuery('.data-type').data('type');

        jQuery.ajax({
            type: 'post',
            url: myAjax.ajaxurl,
            dataType: 'html',
            data: {
                action: 'load_events_filter_results',
                query: query,
                valueSelected: valueSelected,
                postType: postType
            },
            success: function (response) {
                $('.c-article-item').remove();
                $content.html(response);
            }
        });
        return false;
    });
});
