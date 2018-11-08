/** multiselect.js
 *
 * Converts all native <select[multiple> elements on the page into a more user-friendly
 * element which uses checkboxes. It also keeps the fake select and the original one (hidden)
 * in sync (two-way binding).
 *
 * You can also convert selects manually at any time by using:
 *    App.ins.multiSelect.replace({DOM Node|JQuery Object}).
 *
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
      this.replaceAll();
    },

    replaceAll: function() {
      var _this = this;

      this.$top.each(function(index, element) {
        _this.replace(element);
      });
    },

    replace: function(select) {
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
        .data('custom-element', $customSelect)
        .off('change', $.proxy(this.selectChange, this))
        .on('change', $.proxy(this.selectChange, this))
        .after($customSelect);
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
      $originalSelect.trigger('change');
    },

    selectChange: function(e) {
      $(e.target).data('custom-element').remove();
      this.replace(e.target);
    }
  };
}(jQuery));
