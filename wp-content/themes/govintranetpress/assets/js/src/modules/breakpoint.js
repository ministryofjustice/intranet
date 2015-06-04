/**
 * breakpoint.js
 * triggers events on window and changes a class on html element when certain breakpoints are reached
 * it also restricts the amount of resize events getting triggered during resizing which improves performance
 */
(function($) {
  "use strict";

  var App = window.App;

  App.Breakpoint = function() {
    this.settings = {
      delay: 200,
      breakpoints: {
        mobile: 0,
        tablet: 768,
        desktop: 1024
      }
    };

    this.isLocked = false;
    this.unlockHandle = null;
    this.currentBreakpoint = null;

    this.init();
  };

  App.Breakpoint.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();
    },

    cacheEls: function() {
    },

    bindEvents: function() {
      $(window).on('resize', $.proxy(this.resizeHandle, this));
    },

    resizeHandle: function() {
      if(!this.isLocked) {
        this.trigger();
        this.lock();
      }
    },

    trigger: function() {
      var width = window.innerWidth || document.body.clientWidth;
      var breakpointName;

      if(width < this.settings.breakpoints.tablet) {
        this.triggerBreakpoint('mobile', true);
      }

      if(width >= this.settings.breakpoints.tablet && width < this.settings.breakpoints.desktop) {
        this.triggerBreakpoint('tablet', true);
      }

      if(width >= this.settings.breakpoints.desktop) {
        this.triggerBreakpoint('desktop', true);
      }
    },

    triggerBreakpoint: function(breakpointName) {
      var classNames = ['breakpoint-mobile', 'breakpoint-tablet', 'breakpoint-desktop', 'breakpoint-gte-tablet', 'breakpoint-lte-tablet'];
      var $html = $('html');
      var eventName;

      if(this.currentBreakpoint !== breakpointName) {
        $html.removeClass(classNames.join(' '));

        eventName = 'breakpoint-' + breakpointName;
        $(window).trigger(eventName);
        $html.addClass(eventName);

        if(breakpointName === 'tablet' || breakpointName === 'desktop') {
          eventName = 'breakpoint-gte-tablet';
          $(window).trigger(eventName);
          $html.addClass(eventName);
        }

        if(breakpointName === 'tablet' || breakpointName === 'mobile') {
          eventName = 'breakpoint-lte-tablet';
          $(window).trigger(eventName);
          $html.addClass(eventName);
        }

        this.currentBreakpoint = breakpointName;
      }
    },

    lock: function() {
      var _this = this;
      if(this.isLocked) {
        window.clearTimeout(this.unlockHandle);
        this.unlockHandle = null;
      }

      this.isLocked = true;

      this.unlockHandle = window.setTimeout(function() {
        _this.isLocked = false;
        _this.trigger();
      }, this.settings.delay);
    }
  };
}(jQuery));
