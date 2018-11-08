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
      this.applicationUrl = $('head').data('application-url');

      this.cacheEls();
      this.bindEvents();
    },

    cacheEls: function() {
      this.$formContainer = this.$top.find('.form-container');
      this.$formToggleLink = this.$top.find('.report-problem-link');
      this.$form = this.$top.find('.feedback-form');
      this.$cta = this.$top.find('.report-cta');
      this.$message = this.$top.find('.message');

      this.$nameField = this.$top.find('.name-field');
      this.$emailField = this.$top.find('.email-field');
      this.$feedbackField = this.$top.find('.feedback-field');
    },

    bindEvents: function() {
      this.$formToggleLink.on('click', $.proxy(this.toggleForm, this, true, undefined));
      this.$cta.on('click', $.proxy(this.sendForm, this));
      $('.jump-to-problem-form').on('click', $.proxy(this.toggleForm, this, false, true));
    },

    toggleForm: function(prevent, toggle, e) {
      if (prevent) {
        e.preventDefault();
      }
      
      this.$top.toggleClass('expanded', toggle);
    },

    getClientData: function() {
      var dwTag = $('.template-container').data('dw-tag');
      var id = 'T' + new Date().getTime();

      return {
        username: this.$nameField.val(),
        email: this.$emailField.val(),
        url: window.location.href,
        referrer: document.referrer,
        user_agent: window.navigator.userAgent,
        resolution: window.screen.availWidth + 'x' + window.screen.availHeight,
        subject: 'Page feedback - ' + $('title').text() + ' [' + id + ']',
        tag: dwTag,
        description: this.$feedbackField.val(),
        agency: App.tools.helpers.agency.get()
      };
    },

    validateForm: function() {
      this.resetValidation();

      this.validateIsEmpty(this.$nameField);
      this.validateIsEmpty(this.$emailField);
      this.validateIsEmpty(this.$feedbackField);
      this.validateIsEmail(this.$emailField);

      return !this.$top.find('.form-row.error').length;
    },

    resetValidation: function() {
      this.$top.find('.form-row').removeClass('error');
    },

    validateIsEmpty: function($element) {
      if(!$element.val()) {
        $element.closest('.form-row').addClass('error');
      }
    },

    validateIsEmail: function($element) {
      if(!/@.+\./.test($element.val())) {
        $element.closest('.form-row').addClass('error');
      }
    },

    sendForm: function(e) {
      var passed = this.validateForm();

      e.preventDefault();

      if(!passed) {
        return;
      }

      $.ajax({
        url: this.applicationUrl + '/submit-feedback/',
        method: 'post',
        success: $.proxy(this.submitSuccess, this),
        error: $.proxy(this.submitError, this),
        complete: $.proxy(this.submitComplete, this),
        dataType: 'json',
        data: this.getClientData()
      });
    },

    showMessage: function(message) {
      this.$message
        .addClass('visible')
        .html(message);
    },

    hideMessage: function() {
      this.$message.removeClass('visible');
    },

    submitSuccess: function() {
      this.showMessage('Feedback sent. Thank you.');
      this.$form.removeClass('visible');
    },

    submitError: function() {
      this.showMessage('Error: couldn\'t submit the form. Please try again later.');
      this.$form.addClass('visible');
    },

    submitComplete: function() {
    }
  };
}(jQuery));
