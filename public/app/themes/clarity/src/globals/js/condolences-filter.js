export default (function ($) {
    $.fn.moji_condolencesFilter = function () {
        jQuery(document).on('submit', '#filter_condolences', function (e) {
            e.preventDefault();

            var workplace = $('#ff_workplace_filter').val();

            if (workplace.length > 0) {
                $('.c-condolence-list-item').hide();
                $('.c-condolence-list-item.agency-' + workplace).show();
            } else {
                $('.c-condolence-list-item').show();
            }


        });

        $(".view-by-grid").click(function () {
            $(".c-condolences-list").addClass('grid-view');
            $(".view-by-grid").addClass('current');
            $(".view-by-list").removeClass('current');
        });

        $(".view-by-list").click(function () {
            $(".c-condolences-list").removeClass('grid-view');
            $(".view-by-list").addClass('current');
            $(".view-by-grid").removeClass('current');
        });


    }
})(jQuery);
