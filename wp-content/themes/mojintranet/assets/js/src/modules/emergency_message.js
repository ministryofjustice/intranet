/** Emergency message
 */
(function($) {
  "use strict";

  var App = window.App;

  App.EmergencyMessage = function() {
    this.$top = $('.emergency-banner .message');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.EmergencyMessage.prototype = {
    init: function() {
      this.applicationUrl = $('head').data('application-url');
      this.serviceUrl = this.applicationUrl + '/service/banner';

      this.resultsLoaded = true;
      this.serviceXHR = null;

      this.cacheEls();
      this.bindEvents();

      this.requestResults();
    },

    cacheEls: function() {
      this.$closeButton = this.$top.find('.close');
    },

    bindEvents: function() {
      this.$closeButton.on('click', $.proxy(this.close, this));
    },

    requestResults: function() {
      var _this = this;
      var dataArray = [];

      /* use the timeout for dev/debugging purposes */
      //**/window.setTimeout(function() {
        _this.serviceXHR = $.getJSON(_this.serviceUrl+'/'+dataArray.join('/'), $.proxy(_this.displayResults, _this));
      //**/}, 2000);
    },

    close: function() {
      this.$top.slideUp(200);
    },

    displayResults: function(data) {
      if(data.visible!==1) {
        this.$top.addClass('hidden');
      }
      this.$top.addClass('message-' + data.type);
      this.$top.find('h3.title').html(data.title);
      this.$top.find('.timestamp').html(data.date);
      this.$top.find('.content').html(data.message);
    }

  };
}(window.jQuery));
