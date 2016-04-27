(function($) {
  "use strict";

  var App = window.App;

  App.FollowUsWidget = function() {
    this.$top = $('.template-home .social');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.FollowUsWidget.prototype = {
    init: function() {
      this.applicationUrl = $('head').data('application-url');
      this.templateUri = $('head').data('template-uri');
      this.serviceUrl = this.applicationUrl + '/service/widgets/follow-us/' + App.tools.helpers.agency.getForContent() + '/';
      this.pageBase = this.applicationUrl + '/' + this.$top.data('top-level-slug');

      this.followUsItemTemplate = this.$top.find('[data-name="widget-follow-us-item"]').html();

      this.cacheEls();
      this.bindEvents();

      this.requestData();
    },

    cacheEls: function() {
      this.$followUsList = this.$top.find('.social-list');
    },

    bindEvents: function() {
    },

    requestData: function() {
      var _this = this;

      /* use the timeout for dev/debugging purposes */
      //**/window.setTimeout(function() {
        _this.serviceXHR = $.getJSON(_this.serviceUrl, $.proxy(_this.displayData, _this));
      //**/}, 2000);
    },

    displayData: function(data) {
      var _this = this;

      $.each(data.results, function(index, followUsItem) {
        _this.$followUsList.append(_this.buildFollowUsItem(followUsItem));
      });

      this.$top.removeClass('loading');
    },

    buildFollowUsItem: function(data) {
      var $child = $(this.followUsItemTemplate);

      $child.addClass(data.name);
      $child.find('.title').html(data.label);
      $child.find('.social-link').attr('href', data.url);

      return $child;
    }
  };
}(jQuery));
