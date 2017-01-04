(function($) {
  "use strict";

  var App = window.App;

  App.CampaignLanding = function() {
    this.$top = $('.template-campaign-landing .template-container');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.CampaignLanding.prototype = {
    init: function() {
      this.applicationUrl = $('head').data('application-url');
      this.templateUri = $('head').data('template-uri');
      this.region = this.$top.attr('data-region');
      this.campaignCategory = this.$top.attr('data-campaign-category');
      this.serviceUrl = this.getServiceUrl();

      this.requestData();
    },

    requestData: function() {
      var _this = this;

      /* use the timeout for dev/debugging purposes */
      //**/window.setTimeout(function() {
        _this.serviceXHR = $.getJSON(_this.serviceUrl, $.proxy(_this.initialiseWidgets, _this));
      //**/}, 2000);
    },

    getServiceUrl: function() {
      var base = this.applicationUrl + '/service/widgets/campaign-landing/' + App.tools.helpers.agency.getForContent() + '/';

      if (this.campaignCategory) {
        return base + 'campaign_category=' + this.campaignCategory + '/';
      }

      return base;
    },

    initialiseWidgets: function(data) {
      App.ins.eventsWidget = new App.EventsWidget(data.events);
      App.ins.newsListWidget = new App.NewsListWidget(data.news_list);
      App.ins.postsWidget = new App.PostsWidget(data.posts);
    }
  };
}(jQuery));
