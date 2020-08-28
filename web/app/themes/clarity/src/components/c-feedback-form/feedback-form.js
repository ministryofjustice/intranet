export default (function ($) {
    $.fn.moji_feedbackForm = function () {
        let form = $('.js-reveal-target')
        let trigger = $('.js-reveal-trigger')
        trigger.click(
            function (e) {
                e.preventDefault()
                form.toggle()
            }
        )
    }
})(jQuery);
