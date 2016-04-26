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

      this.$agencyLinkList = $('.my-moj .agency-link-list');
      this.$agencyLinkItem = this.$agencyLinkList.find('.agency');
      this.$agencyLinkLabel = this.$agencyLinkList.find('.label');
      this.$agencyLink = this.$agencyLinkList.find('.agency-link');

      this.$tooltip = this.$myIntranetForm.find('.my-agency-tooltip');
    },

    bindEvents: function() {
      $(document).on('click', $.proxy(this.outsideClickHandle, this));
      this.$departmentTrigger.on('click', $.proxy(this.triggerClick, this));
      this.$departmentList.find('a').on('click', $.proxy(this.itemClick, this));
      this.$tooltip.on('click', $.proxy(this.toggleTooltip, this, false));
    },

    outsideClickHandle: function(e) {
      if(!$(e.target).closest(this.$myIntranetForm).length) {
        this.toggleList(false);
      }
    },

    initDropdown: function() {
      var _this = this;
      var department = this.readState();
      var text;
      var $defaultItem = this.$departmentList.find('li[data-default="1"]');
      var $selectedDepartment = this.$departmentList.find('li[data-department="' + department + '"]');

      if(!department || !$selectedDepartment.length) {
        department = $defaultItem.attr('data-department');
        text = $defaultItem.text();
        this.toggleTooltip(true);
      }
      else {
        text = $selectedDepartment.text();
      }

      this.updateLabels(text, department);
      this.saveState();

      window.setTimeout(function() {
        _this.toggleTooltip(false);
      }, 20000);
    },

    toggleTooltip: function(toggle) {
      this.$tooltip.toggleClass('visible', toggle);
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
      var department = $item.closest('li').data('department');
      var text = $item.text();

      e.preventDefault();

      this.$departmentList.removeClass('visible');
      this.updateLabels(text, department);

      this.saveState();
      this.toggleTooltip(false);
    },

    updateLabels: function(text, department) {
      var link = this.$departmentList.find('.agency[data-department="' + department + '"]').attr('data-url') || "";

      this.$departmentLabel.html(text);
      this.$agencyLinkLabel.html(text);
      this.$departmentList.attr('data-department', department);
      this.$agencyLinkItem.attr('data-department', department);
      this.$agencyLink.attr('href', link);
      this.$agencyLinkList.toggleClass('hidden', !link.length);
    },

    saveState: function(e) {
      var department = this.$departmentList.attr('data-department');
      App.tools.setCookie(this.settings.cookieName, department, 3650);
      App.ins.personalisation.update();
    },

    readState: function() {
      return App.tools.getCookie(this.settings.cookieName);
    }
  };
}(window.jQuery));
