/** Floaters
 * Handles floating elements which have minimum and maximum position determined by its container
 */
(function($) {
  "use strict";

  var App = window.App;

  App.Floaters = function() {
    this.$floaters = $('.js-floater');
    if(!this.$floaters.length) { return; }
    this.init();
  };

  App.Floaters.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();
      this.setUpFloaters();
    },

    cacheEls: function() {
    },

    bindEvents: function() {
      $(window).on('scroll', $.proxy(this.scrollHandler, this));
    },

    setUpFloaters: function() {
      this.$floaters.each(function() {
        var $floater = $(this);
        $floater.attr('data-start-position', App.tools.round($floater.offset().top, 0));
      });
    },

    scrollHandler: function() {
      this.$floaters.each(function() {
        var $floater = $(this);
        var $container = $($floater.attr('data-floater-limiter-selector'));
        var floaterHeight = $floater.outerHeight();
        var limiterTop = App.tools.round($container.offset().top, 0);
        var limiterHeight = $container.outerHeight();
        var scrollTop = $(window).scrollTop();

        if(scrollTop > limiterTop) {
          $floater.css({
            marginTop: Math.min(scrollTop - limiterTop, limiterHeight - floaterHeight - 100) + 'px'
          });
        }
        else {
          $floater.css({
            marginTop: '' //restore original
          });
        }
      });
    }
  };
}(jQuery));
