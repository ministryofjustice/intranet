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
      this.itemTemplate = $('.template-partial[data-name="select-agency-item"]').html();

      this.cacheEls();
      this.bindEvents();

      this.initAgencyList();
      this.initTooltip();
    },

    cacheEls: function() {
      this.$selectAgencyTrigger = this.$top.find('.select-agency-trigger');
      this.$tooltip = this.$top.find('.my-agency-tooltip');
      this.$agencyOverlay = $('.agency-overlay');
      this.$form = this.$agencyOverlay.find('.select-agency-form');
      this.$agencyList = this.$agencyOverlay.find('.agency-list');
      this.$agencyItems = $([]);
    },

    bindEvents: function() {
      this.$selectAgencyTrigger.click($.proxy(this.triggerClick, this));
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

    initAgencyList: function() {
      var _this = this;
      var $item;
      var agencies = App.tools.helpers.agency.agencies;
      var agency = App.tools.helpers.agency.get();

      //populate agency list
      $.each(agencies, function(name, item) {
        $item = _this.buildItem(name, item);
        _this.$agencyList.append($item);
      });

      this.$agencyItems = this.$agencyList.find('.agency-item');

      //select agency
      this.$agencyItems.filter('[data-agency="' + agency + '"]').addClass('selected');
      this.$selectAgencyTrigger.html('Change');
    },

    buildItem: function(name, item) {
      var $item = $(this.itemTemplate);

      $item.find('.label').html(item.label);
      $item.attr('data-agency', name);
      $item.click($.proxy(this.agencyItemClick, this));

      return $item;
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

      if($selectedItem.length) {
        App.tools.helpers.agency.set($selectedItem.attr('data-agency'));
        window.location.href = window.location.href;
      }
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
