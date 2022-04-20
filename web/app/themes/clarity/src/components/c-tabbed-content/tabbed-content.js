export default (function ($) {
    $.fn.moji_tabbedContent = function () {
        // create panels var then assign u-current to first panel to display first panel
        var panels = $('.js-tabbed-content')
        panels.filter(':first-of-type').show().addClass('u-current')

        // create tabs var then assign u-current to first tab to display first tab
        var tabs = $('.c-tabbed-content__nav li')
        tabs.attr("tabindex", "0").attr('aria-selected', 'false')
        $('.c-tabbed-content__nav li:first-child').addClass('u-current').attr('aria-selected', 'true')

        // remove  nav styling if there is less than 1 tab
        if (tabs.length < 1) {
            $('.c-tabbed-content__nav').hide()
        }
        function showhidetab (tab) {
            // Hide panel
            tabs.removeClass('u-current').attr('aria-selected', 'false')
            panels.removeClass('u-current')
            panels.hide()

            // show panel
            tab.addClass('u-current').attr('aria-selected', 'true')
            var thisTab = tab.text()
            panels.filter('[data-tab-title="' + thisTab + '"]').show().addClass('u-current')
        }
        tabs.click(
            function () {
                showhidetab($(this)); 
            }
        ).keyup(function (quay){
            if (quay.keyCode && quay.keyCode == "13" ){
                showhidetab($(this));
            }
        }) 
    }
})(jQuery);
