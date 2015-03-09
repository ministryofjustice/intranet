/*jslint browser: true, white: true */
/*global console,jQuery,megamenu,window,navigator*/

/**
 * Mega Menu jQuery Plugin
 */
(function ($) {
    "use strict";

    $.fn.megaMenu = function (options) {

        var menu = $(this);

        menu.settings = $.extend({
            event: menu.attr('data-event'),
            effect: menu.attr('data-effect')
        }, options);

        function isTouchDevice() {
            return ('ontouchstart' in window || navigator.maxTouchPoints > 0 || navigator.msMaxTouchPoints > 0);
        }

        function closePanels() {
            $('li', menu).removeClass('mega-toggle-on');
            $('li.mega-menu-megamenu > ul.mega-sub-menu, li.mega-menu-flyout ul.mega-sub-menu', menu).hide();
        }

        function hidePanel(anchor) {
            anchor.parent().removeClass('mega-toggle-on');
            anchor.siblings('.mega-sub-menu').hide();
        }

        function showPanel(anchor) {
            anchor.parent().addClass('mega-toggle-on').siblings().children('a').each(function() {
                hidePanel($(this));
            });

            if (menu.settings.effect === 'fade') {
                anchor.siblings('.mega-sub-menu').css('display', 'none').fadeIn(megamenu.fade_speed);
            } else if (menu.settings.effect === 'slide') {
                anchor.siblings('.mega-sub-menu').css('display', 'none').slideDown(megamenu.slide_speed);
            } else {
                anchor.siblings('.mega-sub-menu').show();
            }
        }

        function openOnClick() {
            // hide menu when clicked away from
            $(document).on('click', function(event) {
                if (!$(event.target).closest('.mega-menu li').length) {
                    closePanels();
                }
            });

            $('li.mega-menu-megamenu.mega-menu-item-has-children > a, li.mega-menu-flyout.mega-menu-item-has-children > a, li.mega-menu-flyout li.mega-menu-item-has-children > a', menu).on({
                click: function (e) {

                    // check for second click
                    if ( $(this).parent().hasClass("mega-click-click-go") ) {
                        
                        if ( ! $(this).parent().hasClass("mega-toggle-on") ) {
                            e.preventDefault();
                            showPanel($(this));
                        }

                    } else {
                        e.preventDefault();

                        if ( $(this).parent().hasClass("mega-toggle-on") ) {
                            hidePanel($(this));                            
                        } else {
                            showPanel($(this));
                        }

                    }
                }
            });
        }

        function openOnHover() {
            $('li.mega-menu-megamenu.mega-menu-item-has-children, li.mega-menu-flyout.mega-menu-item-has-children, li.mega-menu-flyout li.mega-menu-item', menu).hoverIntent({
                over: function () {
                    showPanel($(this).children('a'));
                },
                out: function () {
                    hidePanel($(this).children('a'));
                },
                timeout: megamenu.timeout
            });
        }

        function init() {
            menu.removeClass('mega-no-js');

            if (isTouchDevice() || menu.settings.event === 'click') {
                openOnClick();
            } else {
                openOnHover();
            }

        }

        init();
    };

}(jQuery));

jQuery(document).ready(function(){
    "use strict";
    jQuery('.mega-menu').each(function() {
        jQuery(this).megaMenu();
    });
});