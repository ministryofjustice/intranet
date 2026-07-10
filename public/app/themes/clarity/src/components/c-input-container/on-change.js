export default (function ($) {
    $.fn.mojRadiosOnChange = function () {
        $("input[type=radio].js-radios-onChange").change(function() {
            $(this).closest("form").submit();
        });
    };
})(jQuery);
