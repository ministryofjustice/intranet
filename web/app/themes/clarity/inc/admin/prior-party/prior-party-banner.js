/**
 * default data object
 */
const MOJ_PPB = {
    excluded: []
}

jQuery(document).ready(function ($) {
    $('.ppb-banners__row').on('click', function (e) {
        // redirect to post preview...
        window.location.href = window.location.href+"&"+$.param({'ref':$(this).data('reference')})
    })
})
