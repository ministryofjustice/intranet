(function($) {
  "use strict";

  var App = window.App;

  App.tools.Validation = function($form) {
    this.$form = $form;
    this.errors = [];

    this.messageTemplate = $('.template-partial[data-name="validation-message"]').html();
    this.summaryTemplate = $('.template-partial[data-name="validation-summary"]').html();
    this.summaryItemTemplate = $('.template-partial[data-name="validation-summary-item"]').html();
  };

  App.tools.Validation.prototype = {
    error: function($element, fieldName, message) {
      if($.type($element) === 'string') {
        $element = $($element);
      }

      this.errors.push({
        element: $element,
        fieldName: fieldName,
        message: message
      });
    },

    isFilled: function($element, fieldName, message) {
      var value;

      if($.type($element) === 'string') {
        $element = $($element);
      }

      value = $element.val();

      if(!message) {
        message = 'Please enter ' + fieldName;
      }

      if(value === '') {
        this.error($element, fieldName, message);
      }

      return value;
    },

    hasErrors: function() {
      return this.errors.length === 0;
    },

    getErrors: function() {
      return this.errors;
    },

    displayErrors: function() {
      var $element, $message, $container;
      var error;
      var index, count;

      this.resetValidation();
      this.displaySummary();

      for(index = 0, count = this.errors.length; index < count; index++) {
        error = this.errors[index];
        $element = error.element;
        $message = this.createMessage(error.message);
        $element.closest('.form-row').addClass('validation-error');
        $element.before($message);
      }

      this.errors = [];
    },

    resetValidation: function() {
      this.$form.find('.validation-summary').remove();
      this.$form.find('.validation-message').remove();
      this.$form.find('.form-row.validation-error').removeClass('validation-error');
    },

    createMessage: function(message) {
      var $message = $(this.messageTemplate);

      $message.html(message);

      return $message;
    },

    displaySummary: function() {
      var $summary = $(this.summaryTemplate);
      var $summaryItem;
      var $list = $summary.find('.errors');
      var index, count;

      for(index = 0, count = this.errors.length; index < count; index++) {
        $summaryItem = $(this.summaryItemTemplate);
        $summaryItem.html(App.tools.ucfirst(this.errors[index].fieldName));
        $list.append($summaryItem);
      }

      $summary.prependTo(this.$form);
    }
  };
}(jQuery));
