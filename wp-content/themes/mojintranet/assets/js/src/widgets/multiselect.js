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
      var $customSelect = this.buildCustomSelect();
      var $list = $customSelect.find('ul');
      var $item;
      var $oldCustomSelect = $select.data('custom-element') || $([]);
      var options = this.getOptions($select);

      //remove the old custom select (if exists)
      $oldCustomSelect.remove();

      $.each(options, function(index, option) {
        $item = _this.buildItem(option);

        $list
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

    buildCustomSelect: function() {
      var $customSelect = $('<div></div>');
      var $list = $('<ul></ul>');
      var $clearLink = $('<a></a>');
      var $clearLinkBox = $('<p></p>');

      $customSelect.addClass('multi-select-box');
      $clearLinkBox.addClass('clear-link-box');

      $clearLink
        .html('Clear')
        .addClass('clear-link')
        .click($.proxy(this.clearClick, this));

      $clearLinkBox.append($clearLink);
      $customSelect.append($clearLinkBox);
      $customSelect.append($list);

      return $customSelect;
    },

    buildItem: function(data) {
      var $item = $('<li></li>');

      var $label = $('<label></label>')
        .html(data.label)
        .appendTo($item);

      var $checkbox = $('<input type="checkbox">')
        .prop('checked', data.selected)
        .val(data.value)
        .prependTo($label)
        .on('change', $.proxy(this.checkboxChange, this));

      return $item;
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
      this.replace(e.target);
      $(e.target).trigger('multi-select-change');
    },

    clearClick: function(e) {
      this.clear($(e.target).closest('.multi-select-box').find('[data-type="multi-select"]').data('original-element'));
    },

    clear: function(select) {
      var $originalSelect = $(select);
      var $customSelect = $originalSelect.data('custom-element');

      $originalSelect.find('option:selected').prop('selected', false);
      $originalSelect.trigger('change');

      this.replace($originalSelect);
    }
  };
}(jQuery));
