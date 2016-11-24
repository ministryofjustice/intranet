(function($) {
  "use strict";

  var App = window.App;

  App.RegionalLanding = function() {
    this.$top = $('.template-regional-landing .template-container');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.RegionalLanding.prototype = {
    init: function() {
      this.applicationUrl = $('head').data('application-url');
      this.templateUri = $('head').data('template-uri');
      this.region = this.$top.attr('data-region');
      this.serviceUrl = this.applicationUrl + '/service/widgets/regional/' + App.tools.helpers.agency.getForContent() + '/region=' + this.region + '/';

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
      App.ins.eventsWidget = new App.EventsWidget(data.events);
      App.ins.newsListWidget = new App.NewsListWidget(data.news_list);
    }
  };
}(jQuery));
