/** Homepage Banner
 */
(function($) {
  "use strict";

  var App = window.App;

  App.HomepageBanner = function(data) {
    this.data = data;
    this.$top = $('.homepage-banner');
    if (!this.$top.length) { return; }
    this.init();
  };

  App.HomepageBanner.prototype = {
    init: function() {
      this.applicationUrl = $('head').data('application-url');
      this.serviceUrl = this.applicationUrl + '/service/banner';

      this.resultsLoaded = true;
      this.serviceXHR = null;

      this.cacheEls();
      this.bindEvents();

      this.displayResults(this.data);
    },

    cacheEls: function() {
      this.$closeButton = this.$top.find('.close');
    },

    bindEvents: function() {
      this.$closeButton.on('click', $.proxy(this.close, this));
    },

    close: function() {
      this.$top.slideUp(200);
    },

    displayResults: function(data) {
      if (data.visible) {
        this.$top.addClass('visible');
      }

      this.$top.find('a').attr('href', data.url);
      this.$top.find('img').attr('src', data.image_url);
    }
  };
}(window.jQuery));
