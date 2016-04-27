(function($) {
  "use strict";

  var App = window.App;

  App.SelectAgency = function() {
    this.$myIntranetForm = $('.my-intranet-form');
    if(!this.$myIntranetForm.length) { return; }
    this.init();
  };

  App.SelectAgency.prototype = {
    init: function() {
      this.settings = {
        cookieName: 'dw_agency'
      };

      this.cacheEls();
      this.bindEvents();
    },

    cacheEls: function() {
      this.$selectAgencyTrigger = this.$myIntranetForm.find('.select-agency-trigger');
      this.$agencyOverlay = $('.agency-overlay');
      this.$agencyItems = this.$agencyOverlay.find('.agency-item');

    },

    bindEvents: function() {
      this.$selectAgencyTrigger.click($.proxy(this.triggerClick, this));
      this.$agencyItems.click($.proxy(this.agencyItemClick, this));
    },

    toggleTooltip: function(toggle) {
      this.$tooltip.toggleClass('visible', toggle);
    },

    triggerClick: function(e) {
      e.preventDefault();

      this.toggleOverlay();
    },

    agencyItemClick: function(e) {
      e.preventDefault();

      this.selectItem($(e.currentTarget));
    },

    toggleOverlay: function() {
      this.$agencyOverlay.toggleClass('visible');
    },

    selectItem: function($item) {
      this.$agencyItems.removeClass('selected');
      $item.addClass('selected');
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
