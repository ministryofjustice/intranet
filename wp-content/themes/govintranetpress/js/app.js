/** This file will be broken down to separate modules and then concatenated
 * to app.js during a build process (which we don't have at the moment).
 */

jQuery(function(){
  "use strict";

  var App = {};

  /** Mobile menu
   */
  (function($){
    "use strict";

    App.MobileMenu = function(){
      this.config = {
        menuToggleClass: 'mobile-menu-enabled'
      };

      this.init();
      this.cacheEls();
      this.bindEvents();
    };

    App.MobileMenu.prototype = {
      init: function(){
        this.$top = $('#masthead');
      },

      cacheEls: function(){
        this.$menuButton = this.$top.find('.mobile-nav button');
      },

      bindEvents: function(){
        var _this = this;
        this.$top.on('click', 'button', $.proxy(this.toggleMenu, this));
      },

      toggleMenu: function(){
        this.$top.toggleClass(this.config.menuToggleClass);
      }
    };
  }(window.jQuery));

  /** Mobile menu
   */
  (function($){
    "use strict";

    App.StickyNews = function(){
      this.init();
      this.cacheEls();
      this.bindEvents();
    };

    App.StickyNews.prototype = {
      init: function(){
        this.$top = $('#need-to-know');
      },

      cacheEls: function(){
      },

      bindEvents: function(){
        var _this = this;
        this.$top.on('click', '.close-icon', function(){
          _this.collapse();
        });
      },

      collapse: function(){
        this.$top.hide();
      }
    };
  }(window.jQuery));

  /** init section
   * This section should remain in this file
   */
  var mobileMenu = new App.MobileMenu();
  var stickyNews = new App.StickyNews();
});
