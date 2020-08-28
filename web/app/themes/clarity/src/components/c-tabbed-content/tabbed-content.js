export default (function ($) {
    $.fn.moji_tabbedContent = function () {
        // create panels var then assign u-current to first panel to display first panel
        var panels = $('.js-tabbed-content')
        panels.filter(':first-of-type').show().addClass('u-current')

        // create tabs var then assign u-current to first tab to display first tab
        var tabs = $('.c-tabbed-content__nav li')
        $('.c-tabbed-content__nav li:first-child').addClass('u-current')

        // remove  nav styling if there is less than 1 tab
        if (tabs.length < 1) {
            $('.c-tabbed-content__nav').hide()
        }

        tabs.click(
            function () {
                // Hide panel
                tabs.removeClass('u-current')
                panels.removeClass('u-current')
                panels.hide()

                // show panel
                $(this).addClass('u-current')
                var thisTab = $(this).text()
                panels.filter('[data-tab-title="' + thisTab + '"]').show().addClass('u-current')
            }
        )
    }
})(jQuery);
