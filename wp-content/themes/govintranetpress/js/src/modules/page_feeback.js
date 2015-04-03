/** Tabbed content
 */
(function($) {
  "use strict";

  var App = window.App;

  App.PageFeedback = function() {
    this.$link = $('.page-feedback-link');
    if(!this.$link.length) { return; }
    this.init();
  };

  App.PageFeedback.prototype = {
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
      var email = this.$link.attr('href');
      var subject = 'MoJ Intranet - Report an issue';
      var body = [];
      var nl = '\n';

      e.preventDefault();

      body.push(new Array(71).join('-'));
      body.push('This information will help us a lot with resolving the issue. Please do not delete.');
      body.push('Page URL: ' + window.location.href);
      body.push('User agent: ' + window.navigator.userAgent);
      body.push('Screen resolution: ' + window.screen.availWidth + 'x' + window.screen.availHeight);
      body.push(new Array(71).join('-'));

      body = nl + nl + body.join(nl) + nl;

      window.location.href = email + '?subject='+encodeURIComponent(subject)+'&body='+encodeURIComponent(body);
    }
  };
}(jQuery));
