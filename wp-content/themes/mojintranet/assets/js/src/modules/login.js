/** Register form
 */
(function($) {
  "use strict";

  var App = window.App;

  App.LoginForm = function() {
    this.$top = $('.login-form');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.LoginForm.prototype = {
    init: function() {
      this.applicationUrl = $('head').data('application-url');
      this.validation = new App.tools.Validation(this.$top);

      this.cacheEls();
      this.bindEvents();

      this.$submitCta.attr('data-original-label', this.$submitCta.val());
    },

    cacheEls: function() {
      this.$emailField = this.$top.find('[name="email"]');
      this.$passwordField = this.$top.find('[name="password"]');
      this.$submitCta = this.$top.find('.cta[type="submit"]');
    },

    bindEvents: function() {
      var _this = this;

      this.$top.on('submit', $.proxy(this.submit, this));
    },

    submit: function(e) {
      var _this = this;

      e.preventDefault();

      this.validate();

      if(!this.validation.hasErrors()) {
        this.toggleState('loading');

        //**/ window.setTimeout(function() {
          $.ajax({
            url: window.location.href,
            method: 'post',
            data: _this.getData(),
            success: $.proxy(_this.submitSuccess, _this),
            error: $.proxy(_this.submitError, _this),
            complete: $.proxy(_this.submitComplete, _this)
          });
        //**/}, 2000);
      }
      else {
        this.validation.displayErrors();
      }
    },

    getData: function() {
      return {
        email: this.$emailField.val(),
        password: this.$passwordField.val()
      };
    },

    submitSuccess: function(data) {
      if(data.success) {
        window.location.href = this.applicationUrl;
      }
      else {
        this.validation.displayErrors(data.validation.errors);
      }
    },

    submitError: function() {
    },

    submitComplete: function() {
      this.toggleState();
    },

    validate: function() {
      this.validation.reset();

      this.validation.isFilled(this.$emailField, 'email');
      this.validation.isFilled(this.$passwordField, 'password');
    },

    toggleState: function(state) {
      if(state === 'loading') {
        this.$submitCta.val('Loading...');
        this.$submitCta.addClass('loading');
        this.$submitCta.attr('disabled', 'disabled');
      }
      else {
        this.$submitCta.val(this.$submitCta.attr('data-original-label'));
        this.$submitCta.removeClass('loading');
        this.$submitCta.removeAttr('disabled');
      }
    }
  };

}(jQuery));
