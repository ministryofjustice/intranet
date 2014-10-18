/** This file will be broken down to separate modules and then concatenated
 * to app.js during a build process (which we don't have at the moment).
 * The individual modules are already built to work on their own.
 */

jQuery(function(){
  "use strict";

  var App = {};

  /** Mobile menu
   */
  (function($){
    "use strict";

    App.MobileMenu = function(){
      this.$top = $('#masthead');
      if(!this.$top.length){ return; }

      this.config = {
        menuToggleClass: 'mobile-menu-enabled'
      };

      this.init();
    };

    App.MobileMenu.prototype = {
      init: function(){
        this.cacheEls();
        this.bindEvents();
      },

      cacheEls: function(){
        this.$menuButton = this.$top.find('.mobile-nav button');
      },

      bindEvents: function(){
        this.$top.on('click', 'button', $.proxy(this.toggleMenu, this));
      },

      toggleMenu: function(){
        this.$top.toggleClass(this.config.menuToggleClass);
      }
    };
  }(window.jQuery));

  /** Sticky news
   */
  (function($){
    "use strict";

    App.StickyNews = function(){
      this.$top = $('#need-to-know');
      if(!this.$top.length){ return; }

      this.init();
    };

    App.StickyNews.prototype = {
      init: function(){
        this.cacheEls();
        this.bindEvents();
      },

      cacheEls: function(){},

      bindEvents: function(){
        var _this = this;
        this.$top.on('click', '.close-icon', function(){
          _this.collapse();
        });
      },

      collapse: function(){
        this.$top.slideUp(200);
      }
    };
  }(window.jQuery));

  /** A-Z
   */
  (function($){
    "use strict";

    App.PageIndex = function(){
      this.$top = $('.a-z');
      if(!this.$top.length){ return; }
      this.init();
    };

    App.PageIndex.prototype = {
      init: function(){
        this.config = {
          serviceUrl: 'http://localhost/mojintranet/service/children' //hard-coding for now
        };

        this.topPageId = this.$top.data('page-id');
        this.itemTemplate = this.$top.find('template[data-name="a-z-category-item"]').html();

        this.cacheEls();
        this.bindEvents();

        this.populateChildren(this.topPageId, 1);
      },

      cacheEls: function(){
        this.$categoriesContainer = this.$top.find('.categories ul');
        this.$subcategoriesContainer = this.$top.find('.subcategories');
        this.$linksContainer = this.$top.find('.links');

        this.$categories = this.$categoriesContainer.find('li');
      },

      bindEvents: function(){
      },

      categoryClick: function(parentId, level, e){
        e.preventDefault();
        this.populateChildren(parentId, level);
      },

      populateChildren: function(parentId, level){
        var _this = this;
        var $container = this.$top.find('.level-'+level);
        var $el;

        $.getJSON(this.config.serviceUrl+'/'+parentId, function(data){
          $container.empty();

          $.each(data.items, function(index, item){
            $el = $(_this.itemTemplate);
            $el.attr('data-page-id', item.id);
            $el.attr('data-order', item.order); //the API doesnt supply
            $el.find('a').html(item.title);
            if(level<3){
              $el.on('click', 'a', $.proxy(_this.categoryClick, _this, item.id, level+1));
              $el.find('.description').html(item.excerpt);
            }
            $container.append($el);
          });
        });
      },

      getId: function($el){
        $el = $($el);
        var id = $el.data('page-id');

        return id;
      }
    };
  }(window.jQuery));

  /** init section - this should be in a separate file - init.js
   */
  var mobileMenu = new App.MobileMenu();
  var stickyNews = new App.StickyNews();
  var pageIndex = new App.PageIndex();
});
