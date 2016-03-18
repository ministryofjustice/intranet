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
    },

    cacheEls: function() {
      this.$emailField = this.$top.find('[name="email"]');
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
        $.ajax({
          url: window.location.href,
          method: 'post',
          data: this.getData(),
          success: $.proxy(this.submitSuccess, _this),
          error: $.proxy(this.submitError, _this)
        });
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

    displayConfirmationMessage: function() {
      $('.template-container').addClass('confirmation');
    },

    validate: function() {
      this.validation.reset();

      //this.validation.isFilled(this.$emailField, 'email');
    }
  };

}(jQuery));
