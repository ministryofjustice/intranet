(function($) {
  "use strict";

  var App = window.App;

  App.Feeds = function() {
    this.$top = $('.feeds');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.Feeds.prototype = {
    init: function() {
      this.scheme = /^http:/.test(window.location.href) ? 'http' : 'https'; //can't we just use scheme-agnostic URL's? to be tested on IE7

      this.initializeTwitter();
      this.initializeYammer();
    },

    initializeTwitter: function() {
      App.tools.inject(this.scheme + '://platform.twitter.com/widgets.js');
    },

    initializeYammer: function() {
      App.tools.inject(this.scheme + '://assets.yammer.com/assets/platform_embed.js', function() {
        window.yam.connect.embedFeed({
          container: '#yammer-feed',
          network: 'justice.gsi.gov.uk',
          feedType: 'group',
          feedId: 'all'
        });

        $('#embed-feed').css({
          height: '600px'
        });
      });
    }
  };
}(window.jQuery));
