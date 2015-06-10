/** Guidance and Support content template
 */
(function($) {
  "use strict";

  var App = window.App;

  App.GuidanceAndSupportContent = function() {
    this.$top = $('.template-guidance-and-support-content .template-container');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.GuidanceAndSupportContent.prototype = {
    init: function() {
      this.redirectUrl = this.$top.attr('data-redirect-url');
      this.redirectEnabled = this.$top.attr('data-redirect-enabled');
      this.isImported = this.$top.attr('data-is-imported');

      if(this.redirectUrl && this.redirectEnabled==="1") {
        this.redirect(this.redirectUrl);
      }
    },

    redirect: function(url) {
      window.location.href = url;
    }
  };
}(window.jQuery));
