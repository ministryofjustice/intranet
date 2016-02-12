/** Register form
 */
(function($) {
  "use strict";

  var App = window.App;

  App.RegisterForm = function() {
    this.$top = $('.register-form');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.RegisterForm.prototype = {
    init: function() {
      this.validation = new App.tools.Validation(this.$top);

      this.cacheEls();
      this.bindEvents();
    },

    cacheEls: function() {
      this.$firstNameField = this.$top.find('[name="first_name"]');
      this.$surnameField = this.$top.find('[name="surname"]');
      this.$emailField = this.$top.find('[name="email"]');
      this.$reenterEmailField = this.$top.find('[name="reenter_email"]');
      this.$displayNameField = this.$top.find('[name="display_name"]');
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
      var emailVal1, emailVal2;

      this.validation.reset();

      this.validation.isFilled(this.$firstNameField, 'first name');
      this.validation.isFilled(this.$surnameField, 'surname');
      this.validation.isFilled(this.$emailField, 'email');
      this.validation.isFilled(this.$reenterEmailField, 're-enter email');
      this.validation.isFilled(this.$displayNameField, 'display name');

      emailVal1 = this.$emailField.val();
      emailVal2 = this.$reenterEmailField.val();

      if(emailVal1) {
        this.validation.isValidEmail(this.$emailField, 'email');
      }

      if(emailVal1 && emailVal2 && emailVal1 !== emailVal2) {
        this.validation.error(this.$reenterEmailField, 're-enter email address', 'Email addresses don\'t match');
      }
    }
  };

}(jQuery));
