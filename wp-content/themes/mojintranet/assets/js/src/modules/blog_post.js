/** Blog Post
 */
(function($) {
  "use strict";

  var App = window.App;

  App.BlogPost = function() {
    this.$top = $('.template-blog-post .template-container');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.BlogPost.prototype = {
    init: function() {
      this.applicationUrl = $('head').data('application-url');
      this.serviceUrl = this.applicationUrl+'/service/post/siblings';

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
        'post_id': this.postID
      };

      return base;
    }
  };
}(jQuery));
