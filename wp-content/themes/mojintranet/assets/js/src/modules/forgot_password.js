/** Forgot password form
 */
(function($) {
  "use strict";

  var App = window.App;

  App.ForgotPasswordForm = function() {
    this.$top = $('.forgot-password-form');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.ForgotPasswordForm.prototype = {
    init: function() {
      this.validation = new App.tools.Validation(this.$top);

      this.cacheEls();
      this.bindEvents();

      this.$submitCta.attr('data-original-label', this.$submitCta.val());
    },

    cacheEls: function() {
      this.$emailField = this.$top.find('[name="email"]');
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
        email: this.$emailField.val()
      };
    },

    submitSuccess: function(data) {
      if(data.success) {
        this.displayConfirmationMessage();
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

    displayConfirmationMessage: function() {
      $('.template-container').addClass('confirmation');
    },

    validate: function() {
      this.validation.reset();

      this.validation.isFilled(this.$emailField, 'email');
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
