/* global console */
/* jshint esversion: 6 */
export default (function ($) {
    const tabbed = {
        panels: {},
        tabs: {},
        /**
         * Hash object used to track and perform interactions
         */
        hash: {
            fragment: null,
            /**
             * On the current page, take the user to a specified hash
             * @param hash
             */
            goto: (hash) => {
                window.location.hash = hash;
                tabbed.hash.fragment = null; // reset the hash
                tabbed.search.locate();
            },
            get: () => tabbed.hash.fragment || (tabbed.hash.fragment = window.location.hash.substring(1)),
            check: (string) => string.charAt(0) === '#'
        },
        defined: (test) => typeof test !== 'undefined',
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
         * Search for hash fragments, locates named anchors or elements with IDs
         * Switches the tab and activates scroll
         */
        search: {
            target: null,
            locate: () => {
                if (tabbed.hash.get()) {
                    tabbed.search.target = $("a[name='" + tabbed.hash.get() + "']");

                    // test target ~ .get(0) reduces object to DOM element
                    if (!tabbed.defined(tabbed.search.target.get(0))) {
                        tabbed.search.target = $("#" + tabbed.hash.get());
                    }

                    // attempt to match the tab title
                    tabbed.search.title = tabbed.search.target.closest('.c-tabbed-content').data('tab-title');

                    // try and goto the content
                    tabbed.search.goto();
                }
            },
            goto: () => {
                if (tabbed.defined(tabbed.search.title)) {
                    tabbed.tabs.each(function () {
                        if ($(this).text() === tabbed.search.title) {

                            // show the right tab
                            tabbed.show($(this));

                            // scroll to the item
                            $('html,body').animate({
                                scrollTop: (tabbed.search.target.offset().top - 20)
                            }, 'slow');
                            return false;
                        }
                    });
                }
            }
        }
    };

    $.fn.mojTabbedContent = function () {
        // populate panels object then assign u-current to first panel to display first panel
        tabbed.panels = $('.js-tabbed-content');
        tabbed.panels.filter(':first-of-type').show().addClass('u-current');

        // populate tabs object then assign u-current to first tab to display first tab
        tabbed.tabs = $('.c-tabbed-content__nav li');
        tabbed.tabs.attr("tabindex", "0").attr('aria-selected', 'false');
        $('.c-tabbed-content__nav li:first-child').addClass('u-current').attr('aria-selected', 'true');

        // remove nav styling if there is less than 1 tab
        if (tabbed.tabs.length < 1) {
            $('.c-tabbed-content__nav').hide();
        }

        // try and locate hash fragments
        tabbed.search.locate();

        /**
         * Set up user interactions
         */
        tabbed.tabs
            .on("click", function () {
                tabbed.show($(this));
            })
            .on("keyup", function (event) {
                // https://api.jquery.com/event.which/
                if (event.which === 13) {
                    tabbed.show($(this));
                }
            });

        // track link clicks in panels
        tabbed.panels.find("a")
            .on("click", function () {
                let url = $(this).attr("href");

                if (tabbed.defined(url)) {
                    // do we have a URL or a hash?
                    if (tabbed.hash.check(url)) {
                        // it's a hash
                        tabbed.hash.goto(url);
                        return false; // intercept click
                    }

                    // only act on valid URLs
                    try {
                        url = new URL(url);
                    } catch (_) {
                        return true; // hand back to browser
                    }

                    // still here?... validate same-page hashes
                    if (url.pathname === window.location.pathname && url.hash) {
                        tabbed.hash.goto(url.hash);
                        return false; // intercept click
                    }
                }
                return true;
            });
    };

    return null;
})(jQuery);
