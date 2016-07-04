(function($) {
  "use strict";

  var App = window.App;

  App.Navigation = function() {
    if(!$('.menu-list-container').length) { return; }
    this.$top = $('.template-container');
    this.init();
  };

  App.Navigation.prototype = {
    init: function() {
      this.applicationUrl = $('head').data('application-url');
      this.templateUri = $('head').data('template-uri');
      this.postId = this.$top.attr('data-page-id');
      this.serviceUrl = this.applicationUrl + '/service/page_tree/ancestors/' + App.tools.helpers.agency.getForContent()+ '//' + this.postId + '/';

      this.cacheEls();
      this.cacheTemplates();
      this.bindEvents();

      this.requestChildren();
    },

    cacheTemplates: function() {
      this.menuItemTpl = $('.template-partial[data-name="menu-item"]').html();
      this.childItemTpl = $('.template-partial[data-name="child-item"]').html();
    },

    cacheEls: function() {
      this.$menu = $('.menu-list');
    },

    requestChildren: function() {
      var _this = this;

      /* use the timeout for dev/debugging purposes */
      //**/window.setTimeout(function() {
        _this.serviceXHR = $.getJSON(_this.serviceUrl, $.proxy(_this.buildMenu, _this));
      //**/}, 2000);
    },

    bindEvents: function() {
    },

    buildMenu: function(data) {
      var _this = this;
      var $menuItem;

      $.each(data, function(index, child) {
        $menuItem = _this.buildMenuItem(child);
        $menuItem.appendTo(_this.$menu);
        $menuItem.addClass(index === data.length - 1 ? 'current' : 'collapsed');

        if(!$menuItem.find('.children-list .child-item').length) {
          $menuItem.addClass('no-children');
        }
      });

      this.highlightInAncestors();
    },

    buildMenuItem: function(data) {
      var _this = this;
      var $menuItem = $(this.menuItemTpl);
      var $childrenList = $menuItem.find('.children-list');

      $menuItem.attr('data-id', data.id);
      $menuItem.find('.menu-item-link')
        .attr('href', data.url)
        .html(data.title);

      $menuItem.find('.dropdown-button')
        .click($.proxy(_this.toggle, _this));

      $.each(data.children, function(index, data) {
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

      this.$menu.find('.menu-item').not('.current').each(function() {
        var $this = $(this);
        if($this.get(0) !== $item.get(0)) {
          $this.addClass('collapsed');
        }
      });
    },

    highlightInAncestors: function() {
      var _this = this;
      var $categoryItem;
      var $link;
      var id;

      this.$menu.find('.menu-item').each(function() {
        $categoryItem = $(this);
        id = $categoryItem.attr('data-id');

        $link = _this.$menu.find('.child-item[data-id="' + id + '"]');
        $link.addClass('highlight');
      });
    }
  };
}(window.jQuery));
