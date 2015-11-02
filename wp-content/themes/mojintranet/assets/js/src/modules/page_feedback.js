/** Tabbed content
 */
//(function($) {
//  "use strict";
//
//  var App = window.App;
//
//  App.PageFeedback = function() {
//    this.$link = $('.page-feedback-link');
//    if(!this.$link.length) { return; }
//    this.init();
//  };
//
//  App.PageFeedback.prototype = {
//    init: function() {
//      this.cacheEls();
//      this.bindEvents();
//    },
//
//    cacheEls: function() {
//    },
//
//    bindEvents: function() {
//      this.$link.click($.proxy(this.prepareEmail, this));
//    },
//
//    prepareEmail: function(e) {
//      var email = this.$link.attr('href');
//      var id = 'T' + new Date().getTime();
//      var subject = 'Page feedback - ' + $('title').text() + ' [' + id + ']';
//      var body = [];
//      var nl = '\n';
//
//      e.preventDefault();
//
//      body.push(new Array(71).join('-'));
//      body.push('This information will help us a lot with resolving the issue. Please do not delete.');
//      body.push('Page URL: ' + window.location.href);
//      body.push('User agent: ' + window.navigator.userAgent);
//      body.push('Screen resolution: ' + window.screen.availWidth + 'x' + window.screen.availHeight);
//      body.push(new Array(71).join('-'));
//
//      body = nl + nl + body.join(nl) + nl;
//
//      window.location.href = email + '?subject='+encodeURIComponent(subject)+'&body='+encodeURIComponent(body);
//    }
//  };
//}(jQuery));



(function($) {
  "use strict";

  var App = window.App;

  App.PageFeedback = function() {
    this.$top = $('.page-feedback-container');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.PageFeedback.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();
    },

    cacheEls: function() {
      this.$formToggleLink = this.$top.find('.report-problem-link');
      this.$form = this.$top.find('.feedback-form');
      this.$cta = this.$top.find('.report-cta');
    },

    bindEvents: function() {
      this.$formToggleLink.on('click', $.proxy(this.toggleForm, this));
      this.$cta.on('click', $.proxy(this.sendForm, this));
    },

    toggleForm: function(e) {
      e.preventDefault();
      this.$top.toggleClass('expanded');
    },

    getClientData: function() {
      var dwTag = $('.template-container').data('dw-tag');
      var email = dwTag === 'search-results' ? this.$top.data('alt-email') : this.$top.attr('email');
      var id = 'T' + new Date().getTime();

      return {
        url: window.location.href,
        userAgent: window.navigator.userAgent,
        resolution: window.screen.availWidth + 'x' + window.screen.availHeight,
        subject: 'Page feedback - ' + $('title').text() + ' [' + id + ']',
        email: email
      };
    },

    sendForm: function() {
    }
  };
}(jQuery));
