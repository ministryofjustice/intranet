/** Single News
 */
(function($) {
  "use strict";

  var App = window.App;

  App.SingleNews = function() {
    this.$top = $('.template-single-news .template-container');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.SingleNews.prototype = {
    init: function() {
      this.applicationUrl = $('head').data('application-url');
      this.serviceUrl = this.applicationUrl+'/service/news/siblings';

      this.loadNavLinks();
    },

    loadNavLinks: function() {
      this.postID = $('.template-container').data('post-id');

      if ($.isNumeric(this.postID)) {
        var requestData = this.getDataObject();
        this.requestNavLinks(requestData);
      }
    },

    requestNavLinks: function(data) {
      var _this = this;
      var dataArray = [];

      $.each(data, function(key, value) {
        dataArray.push(value);
      });

      _this.serviceXHR = $.getJSON(_this.serviceUrl+'/'+dataArray.join('/'), $.proxy(_this.displayNavLinks, _this));
    },

    displayNavLinks: function(data) {
      if(data.prev_link.length > 0) {
        $(".previous a").attr("href", data.prev_link);
      }
      else {
        $(".previous").html($(".previous a").html());
      }

      if(data.next_link.length > 0) {
        $( ".next a" ).attr("href", data.next_link);
      }
      else {
        $(".next").html($(".next a").html());
      }

      $(".content-nav").removeClass('nav-hidden');
    },

    getDataObject: function() {
      var base = {
        'agency': App.tools.helpers.agency.getForContent(),
        'additional_filters': '',
        'post_id': this.postID,
      };

      return base;
    }
  };
}(jQuery));
