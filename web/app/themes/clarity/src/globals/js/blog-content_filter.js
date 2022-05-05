export default (function ($) {
    $.fn.moji_ajaxFilter = function () {
        jQuery(document).on('submit', '#ff', function (e) {
            e.preventDefault()

            var nextPageToRetrieve = jQuery('#ff').data('page') + 1
            jQuery('.more-btn').attr('data-page', nextPageToRetrieve)

            var $input = jQuery(this).find('input[name="ff_keywords_filter"]')
            var query = $input.val()
            var $content = jQuery('#content')
            var nonce = $('#_search_filter_wpnonce').val()

            var optionSelected = jQuery(this).find('#ff_date_filter option:selected, #ff_region_news_date_filter option:selected')
            var valueSelected = optionSelected.val()
            var newsCategorySelected = jQuery(this).find('#ff_categories_filter_e-news, input[name="ff_categories_filter_regions"]')
            var newsCategoryValue = newsCategorySelected.val()

            var postType = jQuery('.data-type').data('type')
            var termID = jQuery('.l-secondary').data('termid')

            if (newsCategoryValue === 'undefined') {
                newsCategoryValue = 0
            }

            jQuery.ajax({
                type: 'post',
                url: mojAjax.ajaxurl,
                dataType: 'html',
                data: {
                    action: 'load_search_results',
                    query: query,
                    valueSelected: valueSelected,
                    postType: postType,
                    newsCategoryValue: newsCategoryValue,
                    termID: termID,
                    nonce_hash: nonce
                },
                success: function (response) {
                    $('.c-article-item').remove()
                    $content.html(response)
                }
            })

            jQuery.ajax({
                type: 'post',
                url: mojAjax.ajaxurl,
                dataType: 'html',
                data: {
                    action: 'load_search_results_total',
                    query: query,
                    valueSelected: valueSelected,
                    postType: postType,
                    newsCategoryValue: newsCategoryValue,
                    termID: termID,
                    nonce_hash: nonce
                },
                success: function (response) {
                    jQuery('#title-section').html(response)
                }
            })

            jQuery.ajax({
                type: 'post',
                url: mojAjax.ajaxurl,
                dataType: 'html',
                data: {
                    action: 'load_page_total',
                    query: query,
                    nextPageToRetrieve: nextPageToRetrieve,
                    valueSelected: valueSelected,
                    postType: postType,
                    newsCategoryValue: newsCategoryValue,
                    termID: termID,
                    nonce_hash: nonce
                },
                success: function (response) {
                    $('.c-pagination').html(response)
                }
            })
            return false
        })

        jQuery('.c-pagination').on('click', function () {
            $("#load_more div.data-type").addClass("shown-item")
            var nonce = $('#_search_filter_wpnonce').val()
            var nextPageToRetrieve = jQuery('.more-btn').data('page') + 1
            jQuery('.more-btn').attr('data-page', nextPageToRetrieve)
            var query = jQuery('#ff_keywords_filter').val()
            var valueSelected = jQuery('.more-btn, .nomore-btn').data('date')
            var postType = jQuery('.data-type').data('type')
            var newsCategorySelected = jQuery('input[name="ff_categories_filter_news-category"]:checked')
            var newsCategoryValue = newsCategorySelected.val()
            if (newsCategoryValue === 'undefined') {
                newsCategoryValue = 0
            }

            jQuery.ajax({
                type: 'post',
                url: mojAjax.ajaxurl,
                dataType: 'html',
                data: {
                    action: 'load_next_results',
                    query: query,
                    nextPageToRetrieve: nextPageToRetrieve,
                    valueSelected: valueSelected,
                    postType: postType,
                    newsCategoryValue: newsCategoryValue,
                    nonce_hash: nonce
                },

                success: function (response) {
                    $('#load_more').append(response)
                    $("#load_more div.data-type:not('.shown-item')+article div.content a").focus()
                }
            })

            jQuery.ajax({
                type: 'post',
                url: mojAjax.ajaxurl,
                dataType: 'html',
                data: {
                    action: 'load_page_total',
                    query: query,
                    nextPageToRetrieve: nextPageToRetrieve,
                    valueSelected: valueSelected,
                    postType: postType,
                    newsCategoryValue: newsCategoryValue,
                    nonce_hash: nonce
                },
                success: function (response) {
                    $('.c-pagination').html(response)
                }
            })
            return false
        })

        jQuery(document).on('submit', '#ff_events', function (e) {
            e.preventDefault()

            var nextPageToRetrieve = jQuery('#ff').data('page') + 1
            jQuery('.more-btn').attr('data-page', nextPageToRetrieve)

            var $input = jQuery(this).find('input[name="ff_keywords_filter"]')
            var query = $input.val()
            var $content = jQuery('#content')

            var optionSelected = jQuery(this).find('#ff_date_filter option:selected')
            var valueSelected = optionSelected.val()

            var postType = jQuery('.data-type').data('type')
            var termID = jQuery('.l-secondary').data('termid')

            var nonce = $('#_search_filter_wpnonce').val()

            jQuery.ajax({
                type: 'post',
                url: mojAjax.ajaxurl,
                dataType: 'html',
                data: {
                    action: 'load_events_filter_results',
                    query: query,
                    valueSelected: valueSelected,
                    postType: postType,
                    termID: termID,
                    nonce_hash: nonce
                },
                success: function (response) {
                    $('.c-article-item').remove()
                    $content.html(response)
                }
            })
            return false
        })
    }
})(jQuery)
