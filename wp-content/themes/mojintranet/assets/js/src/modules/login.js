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
      this.validation = new App.tools.Validation(this.$top);

      this.cacheEls();
      this.bindEvents();
    },

    cacheEls: function() {
      this.$firstNameField = this.$top.find('[name="first_name"]');
      this.$passwordField = this.$top.find('[name="password"]');
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
      this.validation.reset();

      this.validation.isFilled(this.$firstNameField, 'email');
      this.validation.isFilled(this.$passwordField, 'password');
    }
  };

}(jQuery));
