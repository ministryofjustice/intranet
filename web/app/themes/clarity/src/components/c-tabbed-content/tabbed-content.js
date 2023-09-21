/* global console */
/* jshint esversion: 6 */
export default (($) => {
    const tabbed = {
        panels: {},
        tabs: {},
        hash: null,
        /**
         * Get the hash fragment
         * @returns {string}
         */
        getHash: () => {
            return tabbed.hash || (tabbed.hash = window.location.hash.substring(1));
        },
        /**
         * Show a tab and panel
         * Supports accessibility
         * @param tab jquery object
         */
        show: (tab) => {
            // hide first
            tabbed.hide();

            // then show
            tab.addClass('u-current').attr('aria-selected', 'true');
            tabbed.panels.filter('[data-tab-title="' + tab.text() + '"]').show().addClass('u-current');
        },
        /**
         * Hide all tabs
         * Supports accessibility
         */
        hide: () => {
            // Hide panel
            tabbed.tabs.removeClass('u-current').attr('aria-selected', 'false');
            tabbed.panels.removeClass('u-current');
            tabbed.panels.hide();
        },
        /**
         * Handle hash fragments, locates named anchors or elements with IDs
         * Switches the tab and activates scroll
         */
        findAndDeliver: () => {
            /** F I N D */
            let target = $("a[name='" + tabbed.getHash() + "']");

            // test target ~ .get(0) reduces object to DOM element
            if (typeof target.get(0) === 'undefined') {
                target = $("#" + tabbed.getHash());
            }

            // attempt to match the tab title
            const title = target.closest('.c-tabbed-content').data('tab-title');

            /** D E L I V E R */
            if (typeof title !== 'undefined') {
                tabbed.tabs.each(function(){
                    if ($(this).text() === title) {

                        // show the right tab
                        console.log("The tab is", $(this));
                        tabbed.show($(this));

                        // scroll to the item
                        $('html,body').animate({
                            scrollTop: (target.offset().top - 20)
                        }, 'slow');
                        return false;
                    }
                });
            }
        }
    };

    $.fn.mojTabbedContent = () => {
        // create panels var then assign u-current to first panel to display first panel
        tabbed.panels = $('.js-tabbed-content');
        tabbed.panels.filter(':first-of-type').show().addClass('u-current');

        // create tabs var then assign u-current to first tab to display first tab
        tabbed.tabs = $('.c-tabbed-content__nav li');
        tabbed.tabs.attr("tabindex", "0").attr('aria-selected', 'false');
        $('.c-tabbed-content__nav li:first-child').addClass('u-current').attr('aria-selected', 'true');

        // remove nav styling if there is less than 1 tab
        if (tabbed.tabs.length < 1) {
            $('.c-tabbed-content__nav').hide();
        }

        // try and follow hash fragments
        tabbed.findAndDeliver();

        // set up user interaction
        tabbed.tabs.click(
            function () {
                tabbed.show($(this));
            }
        ).keyup(function (event) {
            // https://api.jquery.com/event.which/
            if (event.which === 13) {
                tabbed.show($(this));
            }
        });
    };

    return null;
})(jQuery);
