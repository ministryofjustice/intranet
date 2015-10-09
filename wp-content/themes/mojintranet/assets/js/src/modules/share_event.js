(function($) {
  "use strict";

  var App = window.App;

  App.ShareEvent = function() {
    this.$link = $('.share-event');
    if(!this.$link.length) { return; }
    this.init();
  };

  App.ShareEvent.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();
    },

    cacheEls: function() {
    },

    bindEvents: function() {
      this.$link.click($.proxy(this.prepareEmail, this));
    },

    prepareEmail: function(e) {
      var subject = this.$link.attr('data-title') + ' - ' + this.$link.attr('data-date');
      var body = [];
      var nl = '\n';

      e.preventDefault();

      body.push('Hi there,\n\nI thought you might be interested in this event I\'ve found on the MoJ intranet:\n');
      body.push(window.location.href);

      body = body.join(nl) + nl;

      window.location.href = 'mailto:?subject='+encodeURIComponent(subject)+'&body='+encodeURIComponent(body);
    }
  };
}(jQuery));
