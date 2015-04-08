(function($) {
  "use strict";

  App.Navigation = function() {
    this.$top = $('.guidance-and-support-content');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.Navigation.prototype = {
    init: function() {
      this.data = JSON.parse(this.$top.attr('data-children-data'));

      this.cacheEls();
      this.cacheTemplates();
      this.bindEvents();

      this.buildMenu();
    },

    cacheTemplates: function() {
      this.menuItemTpl = $('.template-partial[data-name="menu-item"]').html();
      this.childItemTpl = $('.template-partial[data-name="child-item"]').html();
    },

    cacheEls: function() {
      this.$menu = $('.menu-list');
    },

    bindEvents: function() {
    },

    buildMenu: function() {
      var _this = this;
      var $menuItem;
      var current = false;

      $.each(this.data, function(index, data) {
        $menuItem = _this.buildMenuItem(data);
        $menuItem.appendTo(_this.$menu);
        $menuItem.addClass(index === _this.data.length - 1 ? 'current' : 'collapsed');

        if(!$menuItem.find('.children-list .child-item').length) {
          $menuItem.addClass('no-children');
        }
      });

      this.highlightCurrentInParent();
    },

    buildMenuItem: function(data) {
      var _this = this;
      var $menuItem = $(this.menuItemTpl);
      var $childrenList = $menuItem.find('.children-list');

      $menuItem.attr('data-id', data.id);

      $menuItem.find('.menu-item-link')
        .click($.proxy(_this.toggle, _this))
        .html(data.title);

      $.each(data.items, function(index, data) {
        _this.buildChildItem(data).appendTo($childrenList);
      });

      return $menuItem;
    },

    buildChildItem: function(data) {
      var $childItem = $(this.childItemTpl);

      $childItem.attr('data-id', data.id);

      $childItem.find('.child-item-link')
        .html(data.title)
        .attr('href', data.url);

      return $childItem;
    },

    clear: function($item) {
      if($item) {
        $item.find('.item-list').empty();
      }
      else {
        this.$menu.empty();
      }
    },

    toggle: function(e) {
      var $element = $(e.target);
      var $item = $element.closest('.menu-item');
      e.preventDefault();

      $item.toggleClass('collapsed');

      this.$menu.find('.menu-item').each(function() {
        var $this = $(this);
        if($this.get(0) !== $item.get(0)) {
          $this.addClass('collapsed');
        }
      });
    },

    highlightCurrentInParent: function() {
      var currentId = this.$menu.find('.current').attr('data-id');
      var $currentPageLink = this.$menu.find('.child-item[data-id="' + currentId + '"]');
      $currentPageLink.addClass('highlight');
      $currentPageLink.off('click');
      $currentPageLink.click(function(e) {
        e.preventDefault();
      });
    }
  };
}(window.jQuery));
