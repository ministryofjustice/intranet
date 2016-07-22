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
      this.applicationUrl = $('head').data('application-url');
      this.templateUri = $('head').data('template-uri');
      this.pageBase = this.applicationUrl + '/' + this.$top.data('top-level-slug');

      this.appTemplate = this.$top.find('[data-name="widget-app-item"]').html();
      this.quickLinkTemplate = this.$top.find('[data-name="widget-quick-link-item"]').html();

      this.resultsLoaded = false;

      this.cacheEls();

      this.displayData(this.data);
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
