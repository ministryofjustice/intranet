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
      var email = dwTag === 'search-results' ? this.$top.data('alt-email') : this.$top.data('email');
      var id = 'T' + new Date().getTime();

      return {
        url: window.location.href,
        userAgent: window.navigator.userAgent,
        resolution: window.screen.availWidth + 'x' + window.screen.availHeight,
        subject: 'Page feedback - ' + $('title').text() + ' [' + id + ']',
        email: email,
        description: this.$top.find('.feedback-field').val()
      };
    },

    sendForm: function(e) {
      e.preventDefault();
    }
  };
}(jQuery));
