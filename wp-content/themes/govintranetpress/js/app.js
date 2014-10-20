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
          serviceUrl: $('head').data('application-url')+'/service/children'
        };

        this.topPageId = this.$top.data('page-id');
        this.itemTemplate = this.$top.find('template[data-name="a-z-category-item"]').html();
        this.serviceXHR = null;

        this.cacheEls();
        this.bindEvents();

        this.addChildren(this.topPageId, 1);
      },

      cacheEls: function(){
        this.$tree = this.$top.find('.tree');
        this.$categoriesContainer = this.$tree.find('.categories');
        this.$subcategoriesContainer = this.$tree.find('.subcategories');
        this.$linksContainer = this.$tree.find('.links');

        this.$sortList = this.$top.find('.sort');
        this.$sortPopular = this.$sortList.find('[data-sort-type="popular"]');
        this.$sortAlphabetical = this.$sortList.find('[data-sort-type="alphabetical"]');
      },

      bindEvents: function(){
        var _this = this;

        this.$sortAlphabetical.on('click', 'a', function(e){
          e.preventDefault();
          _this.sort('alphabetical');
          _this.$sortList.find('> li').removeClass('selected');
          $(this).parent().addClass('selected');
        });

        this.$sortPopular.on('click', 'a', function(e){
          e.preventDefault();
          _this.sort('popular');
          _this.$sortList.find('> li').removeClass('selected');
          $(this).parent().addClass('selected');
        });
      },

      categoryClick: function(parentId, level, e){
        var $container = this.$top.find('.level-'+level);
        var $parent = $container.find('[data-page-id='+parentId+']');
        e.preventDefault();

        if($container.find('[data-page-id='+parentId+']').hasClass('selected')){ return; }

        $container.find('.selected').removeClass('selected');
        $parent.addClass('selected');
        this.addChildren(parentId, level+1);
      },

      slideCategories: function(toggle){
        this.$tree.toggleClass('contracted', toggle);
      },

      addChildren: function(parentId, level){
        var $parent = this.$tree.find('[data-page-id="'+parentId+'"]'); //the clicked element which is a parent to the children we populate the container with
        this.loading($parent);

        if(this.serviceXHR){
          this.serviceXHR.abort();
        }

        this.requestChildren(parentId, level);
      },

      requestChildren: function(parentId, level){
        var _this = this;
        var $container = this.$tree.find('.level-'+level);
        var $child;

        this.serviceXHR = $.getJSON(_this.config.serviceUrl+'/'+parentId, function(data){
          _this.$tree.find('.level-'+(level+1)).parent().hide();
          $container.empty();

          if(level<3){
            _this.$tree.find('.level-3').empty();
          }

          if(level>1){
            $container.parent().find('> .title').html(data.title);
          }

          $.each(data.items, function(index, item){
            $child = _this.setUpChild(item, level);
            $container.append($child);
          });

          _this.sort();
          $container.parent().show();
          _this.stopLoading();
          _this.serviceXHR = false;
        });
      },

      setUpChild: function(data, level){
        var _this = this;
        var $child = $(_this.itemTemplate);
        $child.attr('data-page-id', data.id);
        $child.attr('data-popularity-order', data.id); //!!! for now the order will be based on IDs
        $child.attr('data-name', data.title);
        $child.find('h3').html(data.title);
        $child.find('a').attr('href', data.url);
        $child.on('click', 'a', $.proxy(_this.slideCategories, _this, level!==1));

        if(level<3){
          $child.on('click', 'a', $.proxy(_this.categoryClick, _this, data.id, level));
          $child.find('.description').html(data.excerpt);
        }

        return $child;
      },

      loading: function($item){
        this.stopLoading();
        $item.addClass('loading');
      },

      stopLoading: function(){
        this.$tree.find('.item.loading').removeClass('loading');
      },

      getId: function($el){
        $el = $($el);
        var id = $el.data('page-id');

        return id;
      },

      /** sorts items in all columns alphabetically or by popularity depending on type param
       * @param {String} type Type of sort [alphabetical/popular]
       */
      sort: function(type){
        var level;
        var items;
        var sortMethod;
        var $container;

        if(!type){
          type = this.$sortList.find('.selected').data('sort-type');
        }

        for(level=1; level<=3; level++){
          $container = this.$top.find('.level-'+level);
          items = $container.find(' > li').toArray();

          sortMethod = type==='alphabetical' ? this.helpers.alphabeticalComparator : this.helpers.popularComparator;
          items.sort(sortMethod);
          $container.append(items);
        }
      },

      helpers: {
        alphabeticalComparator: function(a, b){
          var label1 = $(a).data('name');
          var label2 = $(b).data('name');
          return (label1 < label2) ? -1 : (label1 > label2) ? 1 : 0;
        },
        popularComparator: function(a, b){
          var label1 = $(a).data('popularity-order');
          var label2 = $(b).data('popularity-order');
          return (label1 < label2) ? -1 : (label1 > label2) ? 1 : 0;
        }
      }
    };
  }(window.jQuery));

  /** init section - this should be in a separate file - init.js
   */
  var mobileMenu = new App.MobileMenu();
  var stickyNews = new App.StickyNews();
  var pageIndex = new App.PageIndex();
});
