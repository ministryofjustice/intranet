/** Login
 */
(function($) {
  "use strict";

  var App = window.App;

  App.Login = function() {
    this.$top = $('#registerform');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.Login.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();
    },

    cacheEls: function() {
      this.$regEmail = this.$top.find('.user_email');
      this.$regUsername = this.$top.find('.user_login');
    },

    bindEvents: function() {
      var _this = this;

      this.$regEmail.on('input propertychange', function(e) {
        var updatedEmail = e.currentTarget.value;
        _this.updateUsername(updatedEmail);
      });
    },

    updateUsername: function(data) {
      this.$regUsername.attr("value",data);
    }
  };

}(jQuery));