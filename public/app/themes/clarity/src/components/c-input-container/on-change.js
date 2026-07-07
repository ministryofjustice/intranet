export default (function ($) {
    $.fn.mojRadiosOnChange = function () {
        $("input[type=radio].js-radios-onChange").on('change', function() {
            $(this).closest("form").trigger('submit');
        });
    };
})(jQuery);
