(function($) {
  "use strict";

  var TestForm = function() {
    this.$form = $('.standard.test');
    this.validation = new App.tools.Validation(this.$form);
    this.bindEvents();
  };

  TestForm.prototype = {
    bindEvents: function() {
      var _this = this;

      this.$form.submit($.proxy(_this.submitForm, _this));
    },

    submitForm: function(e) {
      e.preventDefault();

      this.validation.isFilled('[name="first-name"]', 'first name');
      this.validation.isFilled('[name="surname"]', 'surname');
      this.validation.isFilled('[name="login-email"]', 'email');

      this.validation.displayErrors();
    }
  };

  window.testForm = new TestForm();
}(jQuery));
