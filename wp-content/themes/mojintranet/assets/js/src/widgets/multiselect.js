/** multiselect.js
 * Converts the native multi-select box into a more user-friendly variant which uses checkboxes
 */
(function($) {
  "use strict";

  var App = window.App;

  App.MultiSelect = function() {
    this.$top = $('select[multiple]');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.MultiSelect.prototype = {
    init: function() {
      this.upgradeAll();
    },

    cacheEls: function() {
    },

    bindEvents: function() {
    },

    upgradeAll: function() {
      var _this = this;

      this.$top.each(function(index, element) {
        _this.upgrade(element);
      });
    },

    upgrade: function(select) {
      var _this = this;
      var $select = $(select);
      var $customSelect = $('<ul></ul>');
      var options = this.getOptions($select);
      var $item;
      var $checkbox;
      var $label;

      $.each(options, function(index, option) {
        $item = $('<li></li>');

        $label = $('<label></label>')
          .html(option.label)
          .appendTo($item);

        $checkbox = $('<input type="checkbox">')
          .prop('checked', option.selected)
          .val(option.value)
          .prependTo($label)
          .on('change', $.proxy(_this.checkboxChange, this));

        $customSelect
          .attr('data-type', 'multi-select')
          .data('original-element', $select)
          .append($item);
      });

      $select
        .addClass('invisible')
        .on('change', $.proxy(this.selectChange, this))
        .data('custom-element', $customSelect)
        .after($customSelect);

      $select.off('change', $.proxy(this.selectChange, this));
      $select.on('change', $.proxy(this.selectChange, this));
    },

    getOptions: function($select) {
      var options = [];
      var $option;

      $select.find('option').each(function(index, option) {
        $option = $(option);
        options.push({
          label: $option.html(),
          value: $option.val(),
          selected: $option.prop('selected')
        });
      });

      return options;
    },

    checkboxChange: function(e) {
      var $element = $(e.target);
      var $customSelect = $element.closest('[data-type="multi-select"]');
      var $originalSelect = $customSelect.data('original-element');
      var isChecked = $element.prop('checked');

      //synchronise with the original select
      $originalSelect.find('[value="' + $element.val() + '"]').prop('selected', isChecked);
    },

    selectChange: function(e) {
      $(e.target).data('custom-element').remove();
      this.upgrade(e.target);
    }
  };
}(jQuery));
