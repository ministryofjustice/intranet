/** Homepage banner (side)
 */
(function($) {
  "use strict";
  var App = window.App;
  App.HomepageBannerSide = function(data) {
    this.data = data;
    this.$top = $('.side-banner');
    if (!this.$top.length) { return; }
    this.init();
  };
  App.HomepageBannerSide.prototype = {
    init: function() {
      this.cacheEls();
      this.displayResults(this.data.results);
    },
    cacheEls: function() {
      this.$image = this.$top.find('img');
      this.$link = this.$top.find('a');
    },
    displayResults: function(data) {

      if (data.visible) {
        this.$top.addClass('visible');
        this.$image.addClass('campaign-banner-side');
        this.$image.attr('src', data.image_url);
        this.$link.attr('href', data.url);
        this.$image.attr('alt', data.alt);
      } else {
        this.$top.addClass('banner-hide');
      }

      if (data.url) {
        this.$image.wrap("<a href='" + data.url + "'></a>");
      }

      if (data.text) {
        this.$top.find('h2.title').html(data.text);
      } else {
        this.$top.find('h2.title').addClass('banner-hide');
      }
    }
  };
}(window.jQuery));
