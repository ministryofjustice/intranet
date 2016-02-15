/** Reset password form
 */
(function($) {
  "use strict";

  var App = window.App;

  App.ResetPasswordForm = function() {
    this.$top = $('.reset-password-form');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.ResetPasswordForm.prototype = {
    init: function() {
      this.validation = new App.tools.Validation(this.$top);

      this.cacheEls();
      this.bindEvents();
    },

    cacheEls: function() {
      this.$passwordField = this.$top.find('[name="password"]');
      this.$reenterPasswordField = this.$top.find('[name="reenter_password"]');
    },

    bindEvents: function() {
      var _this = this;

      this.$top.on('submit', $.proxy(this.submit, this));
    },

    submit: function(e) {
      e.preventDefault();

      this.validate();

      if(!this.validation.hasErrors()) {
        this.displayConfirmationMessage();
      }
      else {
        this.validation.displayErrors();
      }
    },

    displayConfirmationMessage: function() {
      $('.template-container').addClass('confirmation');
    },

    validate: function() {
      var passwordVal1, passwordVal2;

      this.validation.reset();

      this.validation.isFilled(this.$passwordField, 'password');
      this.validation.isFilled(this.$reenterPasswordField, 're-enter password');

      passwordVal1 = this.$passwordField.val();
      passwordVal2 = this.$reenterPasswordField.val();

      if(passwordVal1 && passwordVal2 && passwordVal1 !== passwordVal2) {
        this.validation.error(this.$reenterPasswordField, 're-enter password address', 'Passwords don\'t match');
      }
    }
  };

}(jQuery));
