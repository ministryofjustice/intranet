(function($) {
  "use strict";

  var App = window.App;

  App.SelectAgency = function() {
    this.$top = $('.select-agency-trigger-container');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.SelectAgency.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();

      this.preselect();
      this.initTooltip();
    },

    cacheEls: function() {
      this.$selectAgencyTrigger = this.$top.find('.select-agency-trigger');
      this.$agencyOverlay = $('.agency-overlay');
      this.$agencyItems = this.$agencyOverlay.find('.agency-item');
      this.$form = this.$agencyOverlay.find('.select-agency-form');
      this.$tooltip = this.$top.find('.my-agency-tooltip');
    },

    bindEvents: function() {
      this.$selectAgencyTrigger.click($.proxy(this.triggerClick, this));
      this.$agencyItems.click($.proxy(this.agencyItemClick, this));
      this.$form.submit($.proxy(this.formSubmit, this));
      this.$tooltip.on('click', $.proxy(this.toggleTooltip, this, false));
    },

    initTooltip: function() {
      var _this = this;

      if(!App.tools.helpers.agency.getCookie()) {
        this.toggleTooltip(true);
      }

      window.setTimeout(function() {
        _this.toggleTooltip(false);
      }, 20000);
    },

    preselect: function() {
      var agency = App.tools.helpers.agency.get();

      this.$agencyItems.filter('[data-agency="' + agency + '"]').addClass('selected');
      this.$selectAgencyTrigger.text(this.getCurrentAgencyName());
    },

    getCurrentAgencyName: function() {
      var $selectedAgency = this.$agencyItems.filter('.selected');

      return $selectedAgency.find('.label').text();
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

    formSubmit: function(e) {
      var $selectedItem = this.$agencyItems.filter('.selected');

      e.preventDefault();

      App.tools.helpers.agency.set($selectedItem.attr('data-agency'));
      window.location.href = window.location.href;
    },

    toggleOverlay: function() {
      $('html').toggleClass('agency-overlay-visible');
    },

    selectItem: function($item) {
      this.$agencyItems.removeClass('selected');
      $item.addClass('selected');
    }
  };
}(window.jQuery));
