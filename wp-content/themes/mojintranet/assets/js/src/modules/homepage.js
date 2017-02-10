(function($) {
  "use strict";

  var App = window.App;

  App.Homepage = function() {
    this.$top = $('.template-home');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.Homepage.prototype = {
    init: function() {
      this.applicationUrl = $('head').data('application-url');
      this.templateUri = $('head').data('template-uri');
      this.serviceUrl = this.applicationUrl + '/service/widgets/all/' + App.tools.helpers.agency.getForContent() + '//';

      this.requestData();
    },

    requestData: function() {
      var _this = this;

      /* use the timeout for dev/debugging purposes */
      //**/window.setTimeout(function() {
        _this.serviceXHR = $.getJSON(_this.serviceUrl, $.proxy(_this.initialiseWidgets, _this));
      //**/}, 2000);
    },

    initialiseWidgets: function(data) {
      App.ins.myMojWidget = new App.MyMojWidget(data.my_moj);
      App.ins.followUsWidget = new App.FollowUsWidget(data.follow_us);
      App.ins.postsWidget = new App.PostsWidget(data.posts);
      App.ins.eventsWidget = new App.EventsWidget(data.events);
      App.ins.featuredNewsWidget = new App.FeaturedNewsWidget(data.featured_news);
      App.ins.newsListWidget = new App.NewsListWidget(data.news_list);
      App.ins.needToKnowWidget = new App.NeedToKnowWidget(data.need_to_know);
      App.ins.emergencyMessage = new App.EmergencyMessage(data.emergency_message);
      App.ins.campaignBanner = new App.campaignBanner(data.campaign_banner);
    }
  };
}(jQuery));
