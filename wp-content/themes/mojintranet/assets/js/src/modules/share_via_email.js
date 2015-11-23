(function($) {
  "use strict";

  var App = window.App;

  App.ShareViaEmail = function() {
    this.$link = $('.share-via-email');
    if(!this.$link.length) { return; }
    this.init();
  };

  App.ShareViaEmail.prototype = {
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

      body.push(this.$link.attr('data-body'));
      body.push(window.location.href);

      body = body.join(nl) + nl;

      window.location.href = 'mailto:?subject='+encodeURIComponent(subject)+'&body='+encodeURIComponent(body);
    }
  };
}(jQuery));
