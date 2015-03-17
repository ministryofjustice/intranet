/* DEBUG ONLY */
/*
(function($) {
  'use strict';

  window.Line = function(color) {
    this.$el = $('<div></div>')
      .appendTo(document.body)
      .css({
        backgroundColor: '#' + color,
        width: '100%',
        height: '1px',
        position: 'absolute',
        top: 0,
        left: 0,
        right: 0,
        zIndex: 99999999999
      })
    ;
  };

  window.Line.prototype.top = function(top) {
    this.$el.css('top', top + 'px');
  };
}(jQuery));
/**/


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
      /* DEBUG ONLY */
      /*
      this.limiterTopLine = new window.Line('00f');
      this.limiterBottomLine = new window.Line('0f0');
      this.maxFloaterTopLine = new window.Line('f00');
      /**/

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
      var _this = this;

      this.$floaters.each(function() {
        var $floater = $(this);
        var $container = $($floater.attr('data-floater-limiter-selector'));
        var floaterHeight = $floater.outerHeight() + 20;
        var limiterHeight = $container.outerHeight();
        var scrollTop = $(window).scrollTop();
        var limiterTop = App.tools.round($container.offset().top, 0);
        var absLimiterBottom = limiterTop + limiterHeight;
        var maxFloaterTop = absLimiterBottom - floaterHeight;

        /* DEBUG ONLY */
        /*
        _this.limiterTopLine.top(limiterTop);
        _this.limiterBottomLine.top(absLimiterBottom);
        _this.maxFloaterTopLine.top(maxFloaterTop);

        console.log({
          absLimiterBottom: absLimiterBottom,
          //scrollTop: scrollTop,
          'scrollTop - limiterTop': scrollTop - limiterTop,
          maxFloaterTop: maxFloaterTop
        });
        /**/

        if(scrollTop > limiterTop) {
          $floater.css({
            marginTop: Math.min(scrollTop, maxFloaterTop) - limiterTop + 'px'
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
