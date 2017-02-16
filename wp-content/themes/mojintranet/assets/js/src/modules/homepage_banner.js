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
      this.$link = this.$top.find('a');
    },
    displayResults: function(data) {

      if (data.image_url) {
        this.$top.addClass('visible');
        this.$image.addClass('campaign-banner');
        this.$image.attr('src', data.image_url);
        this.$link.attr('href', data.url);
        this.$image.attr('alt', data.alt);
      } else {
        this.$top.addClass('template-partial');
      }

      if (data.url) {
        this.$image.wrap("<a href='" + data.url + "' </a>");
      }

      if (data.visible === 0) {
        this.$top.addClass('template-partial');
      }
      
      // if (data.alt !=='') {
      //   this.$top.addClass('template-partial');
      // }
    }
  };
}(window.jQuery));
