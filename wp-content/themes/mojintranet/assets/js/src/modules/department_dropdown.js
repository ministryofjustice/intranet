(function($) {
  "use strict";

  var App = window.App;

  App.DepartmentDropdown = function() {
    this.$myIntranetForm = $('.my-intranet-form');
    if(!this.$myIntranetForm.length) { return; }
    this.init();
  };

  App.DepartmentDropdown.prototype = {
    init: function() {
      this.settings = {
        cookieName: 'department_dropdown'
      };

      this.cacheEls();
      this.bindEvents();
      this.initDropdown();
    },

    cacheEls: function() {
      this.$departmentList = this.$myIntranetForm.find('.department-list');
      this.$departmentTrigger = this.$myIntranetForm.find('.department-dropdown-trigger');
      this.$departmentLabel = this.$departmentTrigger.find('.label');
    },

    bindEvents: function() {
      $(document).on('click', $.proxy(this.outsideClickHandle, this));
      this.$departmentTrigger.on('click', $.proxy(this.triggerClick, this));
      this.$departmentList.find('a').on('click', $.proxy(this.itemClick, this));
    },

    outsideClickHandle: function(e) {
      if(!$(e.target).closest(this.$myIntranetForm).length) {
        this.toggleList(false);
      }
    },

    initDropdown: function() {
      var department = this.readState();
      var text;

      if(!department) {
        text = this.$departmentList.find('li:first').text();
      }
      else {
        text = this.$departmentList.find('li[data-department="' + department + '"]').text();
      }

      this.$departmentList.attr('data-department', department);
      this.$departmentLabel.html(text);
    },

    triggerClick: function(e) {
      e.preventDefault();
      this.toggleList();
    },

    toggleList: function(toggle) {
      this.$departmentList.toggleClass('visible', toggle);
    },

    itemClick: function(e) {
      var $item = $(e.target);

      e.preventDefault();
      this.$departmentLabel.html($item.text());
      this.$departmentList.attr('data-department', $item.closest('li').data('department'));
      this.$departmentList.removeClass('visible');

      this.saveState();
    },

    saveState: function(e) {
      var department = this.$departmentList.attr('data-department');
      App.tools.setCookie(this.settings.cookieName, department, 3650);
    },

    readState: function() {
      return App.tools.getCookie(this.settings.cookieName);
    }
  };
}(window.jQuery));
