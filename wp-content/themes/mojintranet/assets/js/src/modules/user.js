(function($) {
  "use strict";

  var App = window.App;

  App.User = function() {
    this.init();
  };

  App.User.prototype = {
    init: function() {
      this.applicationUrl = $('head').data('application-url');
      this.userApiUrl = this.applicationUrl + '/service/user/status/';
      this.userApiXHR = null;
      this.isLoggedIn = false;

      this.cacheEls();

      this.start();
    },

    cacheEls: function() {
      this.$userMenu = $('.header .user-menu');
      this.$signIn = $('.sign-in');
    },

    start: function() {
      this.userApiXHR = $.getJSON(this.userApiUrl, $.proxy(this.saveData, this));
    },

    saveData: function(data) {
      this.isLoggedIn = data.is_logged_in;
      this.displayName = data.name;

      $(window).trigger('user-initialised');
      $('html').removeClass('user-not-initialised');
      $('html').toggleClass('user-logged-in', this.isLoggedIn === true);
      $('html').toggleClass('user-not-logged-in', this.isLoggedIn === false);
    },
  };
}(window.jQuery));
