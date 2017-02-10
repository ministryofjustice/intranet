/** Campaign banner
 */
(function($) {
  "use strict";
  var App = window.App;
  App.campaignBanner = function(data) {
    this.data = data;
    this.$top = $('.template-home');
    if (!this.$top.length) { return; }
    this.init();
  };
  App.campaignBanner.prototype = {
    init: function() {
      this.cacheEls();
      this.displayResults(this.data);
    },
    cacheEls: function() {
      this.$closeButton = this.$top.find('.close');
    },
    displayResults: function(data) {
      if (data.visible) {
        this.$top.addClass('visible');
      }

      if (no url) {
        this.$topBanner.find('img').after();
      }

      this.$topBanner.find('a').attr('href', data.url);
      this.$topBanner.find('img').attr('src', data.image_src);
    }
  };
}(window.jQuery));
