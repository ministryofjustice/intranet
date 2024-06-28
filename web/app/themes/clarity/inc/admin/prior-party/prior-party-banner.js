/**
 * default data object
 */
const MOJ_PPB = {
    excluded: []
}

jQuery(document).ready(function ($) {
    /**
     * React to clicks on banners, redirect to preview
     */
    $('.ppb-banners__row').on('click', function (e) {
        // redirect to post preview...
        window.location.href = window.location.href+"&"+$.param({'ref':$(this).data('reference')})
    });

    /**
     * react to clicks on Post table, add the ID to the exclude array
     */
    $('.ppb-posts__row').on('click', function (e) {
        const post_id = $(this).data('id');
        const index = $.inArray(post_id, MOJ_PPB.excluded);

        if (index !== -1) {
            MOJ_PPB.excluded.splice(index, 1);
        } else {
            MOJ_PPB.excluded.push(post_id);
        }

        console.log(MOJ_PPB.excluded);
    })
})
