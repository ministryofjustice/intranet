/** Homepage Banner
 */
(function($) {
  "use strict";
  var App = window.App;
  App.HomepageBanner = function(data) {
    this.data = data;
    this.$top = $('.content-container');
    if (!this.$top.length) { return; }
    this.init();
  };
  App.HomepageBanner.prototype = {
    init: function() {
      this.cacheEls();
      this.displayResults(this.data.results);
    },
    cacheEls: function() {
      this.$image = this.$top.find('img');
    },
    displayResults: function(data) {
      if (data.visible) {
        this.$top.addClass('visible');
        this.$image.addClass('campaign-banner');
        this.$image.attr('src', data.image_url);
        this.$image.attr('alt', data.alt);
      }

      if (data.url) {
        this.$image.wrap("<a href='" + data.url + "'></a>");
      }

      if (data.visible === 0) {
        this.$top.addClass('banner-hide');
      }
    }
  };
}(window.jQuery));
