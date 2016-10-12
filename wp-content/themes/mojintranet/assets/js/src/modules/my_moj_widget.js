(function($) {
  "use strict";

  var App = window.App;

  App.MyMojWidget = function(data) {
    this.data = data;
    this.$top = $('.template-home .my-moj');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.MyMojWidget.prototype = {
    init: function() {
      this.agency = App.tools.helpers.agency.get();

      this.agencyData = App.tools.helpers.agency.getData(this.agency);

      this.applicationUrl = $('head').data('application-url');
      this.templateUri = $('head').data('template-uri');
      this.pageBase = this.applicationUrl + '/' + this.$top.data('top-level-slug');

      this.agencyLinkTemplate = this.$top.find('[data-name="widget-agency-link"]').html();
      this.appTemplate = this.$top.find('[data-name="widget-app-item"]').html();
      this.quickLinkTemplate = this.$top.find('[data-name="widget-quick-link-item"]').html();

      this.resultsLoaded = false;

      this.cacheEls();

      this.displayData(this.data);
      this.setHeading();
      this.addFeaturedLinks();
    },

    cacheEls: function() {
      this.$quickLinksList = this.$top.find('.quick-links-list');
      this.$appsList = this.$top.find('.apps-list');
    },

    displayData: function(data) {
      var _this = this;

      App.ins.skeletonScreens.remove(this.$quickLinksList);
      App.ins.skeletonScreens.remove(this.$appsList);

      $.each(data.apps, function(index, app) {
        _this.$appsList.append(_this.buildAppItem(app));
      });

      $.each(data.quick_links, function(index, quickLink) {
        _this.$quickLinksList.append(_this.buildQuickLinkItem(quickLink));
      });

      this.resultsLoaded = true;
      this.$top.removeClass('loading');
    },

    setHeading: function() {
      var $agencyAbbreviation = $('.agency-abbreviation');

      $agencyAbbreviation.text(this.agencyData.abbreviation);
    },

    addFeaturedLinks: function() {
      var _this = this;
      var links = this.agencyData.links || [];
      var $agencyLinkList = $('.agency-link-list');
      var $agencyLinkItem;

      $agencyLinkList.toggleClass('hidden', links.length);

      $.each(links, function(index, link) {
        $agencyLinkItem = $(_this.agencyLinkTemplate);

        $agencyLinkItem.find('a').attr('href', link.url);
        $agencyLinkItem.find('.label').html(link.label);

        if (link.is_external) {
          $agencyLinkItem.find('.agency-link').attr('rel', 'external');
        }

        if (link.classes) {
          $agencyLinkItem.addClass(link.classes);
        }

        $agencyLinkItem.appendTo($agencyLinkList);
      });
    },

    buildAppItem: function(data) {
      var $child = $(this.appTemplate);

      $child.find('.app-link').attr('href', data.url);
      $child.find('.app-name').html(data.title);

      if(data.external) {
        $child.find('.app-link').attr('rel', 'external');
      }

      $child.find('.app-icon-inner').addClass(data.icon + '-icon');

      return $child;
    },

    buildQuickLinkItem: function(data) {
      var $child = $(this.quickLinkTemplate);

      $child.find('.quick-link-link')
        .attr('href', data.url)
        .html(data.title);

      return $child;
    }
  };
}(jQuery));
