/** This file will be broken down into separate modules and then concatenated
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

  /** A-Z page index
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
        this.applicationUrl = $('head').data('application-url');
        this.serviceUrl = this.applicationUrl+'/service/children';
        this.pageBase = this.applicationUrl+'/'+this.$top.data('top-level-slug');

        this.itemTemplate = this.$top.find('template[data-name="a-z-category-item"]').html();
        this.serviceXHR = null;

        this.cacheEls();
        this.bindEvents();

        this.prepopulateColumns();
      },

      cacheEls: function(){
        this.$tree = this.$top.find('.tree');
        this.$columns = this.$tree.find('.item-container');

        this.$sortList = this.$top.find('.sort');
        this.$sortPopular = this.$sortList.find('[data-sort-type="popular"]');
        this.$sortAlphabetical = this.$sortList.find('[data-sort-type="alphabetical"]');

        this.$allCategoriesLink = this.$tree.find('.all-categories');
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

        this.$allCategoriesLink.on('click', function(e){
          e.preventDefault();
          _this.toggleTopLevelCategories(true);
          _this.collapseTopLevelColumn(false);
        });
      },

      /** Performs tasks after user clicks on a category link
       * @param {Number} categoryId Category ID
       * @param {Number} level Level number (1-3)
       * @param {Event} e Event
       */
      categoryClick: function(categoryId, level, e){
        var $container = this.$columns.filter('.level-'+level);
        var $parent = $container.find('[data-page-id='+categoryId+']');

        e.preventDefault();

        this.markItem($parent);
        this.loadChildren(categoryId, level+1);

        if(level===1){
          this.toggleTopLevelCategories(false);
        }
      },

      /** Updates the url based on the selected categories
       */
      updateUrl: function(){
        var $item;
        var urlParts = [this.pageBase];

        this.$columns.each(function(){
          $item = $(this).find('.item.selected');
          if($item.length){
            urlParts.push($item.data('slug'));
            return true;
          }

          return false;
        });

        history.pushState({}, "", urlParts.join('/')+'/');
      },

      /** Marks a specified item as selected
       * @param {jQuery} item Item element
       */
      markItem: function($item){
        $item.closest('.item-list').find('.selected').removeClass('selected');
        $item.addClass('selected');
      },

      /** Toggles all top-level categories
       * @param {Boolean} toggle Whether to show or hide categories
       */
      toggleTopLevelCategories: function(toggle){
        if(toggle===undefined){
          throw new Error('toggle parameter must be set to boolean');
        }
        this.$columns.filter('.level-1').find('.item:not(.selected)').slideToggle(toggle);
        this.$allCategoriesLink.toggle(!toggle);
      },

      /** Collapses the level 1 column (CSS-controlled)
       * @param {Boolean} toggle Toggle state
       */
      collapseTopLevelColumn: function(toggle){
        this.$tree.toggleClass('collapsed', toggle);
      },

      /** Populates all columns based on data properties generated server-side
       */
      prepopulateColumns: function(){
        var _this = this;
        var $column;
        var selectedId;
        var data = {};
        var level = 0;

        this.$columns.each(function(){
          $column = $(this);
          data = $column.data('items');
          selectedId = $column.data('selected-id');
          if(data){
            level++;
            _this.populateColumn(level, data);
            _this.markItem($column.find('[data-page-id="'+selectedId+'"]'));
          }
        });

        if(level>1){
          this.toggleTopLevelCategories(false);
        }
        if(level>2){
          this.collapseTopLevelColumn();
        }
      },

      /** Load children based on category ID
       * @param {Number} categoryId Category ID
       * @param {Number} level Level of the child container [1-3]
       */
      loadChildren: function(categoryId, level){
        this.stopLoadingChildren();
        this.$tree.find('[data-page-id="'+categoryId+'"]').addClass('loading');
        this.requestChildren(categoryId, level);
      },

      /** Stops loading children elements
       */
      stopLoadingChildren: function(){
        this.$tree.find('.item.loading').removeClass('loading');
        if(this.serviceXHR){
          this.serviceXHR.abort();
          this.serviceXHR = null;
        }
      },

      /** Performs an XHR to get children and adds the children to the relevant column
       * @param {Number} categoryId ID of the category we request the children of
       * @param {Number} level Level of the child container [1-3]
       */
      requestChildren: function(categoryId, level){
        this.serviceXHR = $.getJSON(this.serviceUrl+'/'+categoryId, $.proxy(this.populateColumn, this, level));
      },

      /** Populates a specified column (based on level) with children specified in data object
       * @param {Number} level Item level (1-3)
       * @param {Object} data Children data
       */
      populateColumn: function(level, data){
        var _this = this;
        var $thisLevelContainer = this.$columns.filter('.level-'+level); //this level = the child level
        var $nextLevelContainer = this.$columns.filter('.level-'+(level+1)); //next level = the grandchild level
        var $thisItemList = $thisLevelContainer.find('.item-list');
        var $child;
        var a;

        this.helpers.toggleElement($nextLevelContainer, false);

        //clear all subcolumns
        for(a=level;a<=3;a++){
          this.$columns.filter('.level-'+a).find('.item-list').empty();
        }

        if(level>1){
          $thisLevelContainer.find('.category-name').html(data.title);
        }

        $.each(data.items, function(index, item){
          $child = _this.buildChild(item, level);
          $thisItemList.append($child);
        });

        this.sort();
        this.helpers.toggleElement($thisLevelContainer, true);
        this.stopLoadingChildren();
        this.$columns.removeClass('current');
        this.$columns.filter('.level-'+level).addClass('current');

        this.updateUrl();
      },

      /** Sets up and returns a child element
       * @param {Object} data Child data model
       * @param {Number} level Item level (1-3)
       * @returns {jQuery} Populated child element
       */
      buildChild: function(data, level){
        var _this = this;
        var $child = $(this.itemTemplate);
        $child.attr('data-page-id', data.id);
        $child.attr('data-popularity-order', data.id); //!!! for now the order will be based on IDs
        $child.attr('data-name', data.title);
        $child.attr('data-slug', data.slug);
        $child.find('.title').html(data.title);
        $child.find('a').attr('href', data.url);
        $child.on('click', 'a', $.proxy(this.collapseTopLevelColumn, this, level!==1));

        if(level<3){
          $child.find('.description').html(data.excerpt);
          $child.on('click', 'a', $.proxy(_this.categoryClick, _this, data.id, level));
        }

        return $child;
      },

      /** Sorts items in all columns alphabetically or by popularity
       * @param {String} type Type of sort [alphabetical/popular]
       */
      sort: function(type){
        var level;
        var items;
        var sortMethod;
        var sortLabel;
        var $list;

        if(!type){
          type = this.$sortList.find('.selected').data('sort-type');
        }

        sortMethod = type==='alphabetical' ? this.helpers.alphabeticalComparator : this.helpers.popularComparator;
        sortLabel = type==='alphabetical' ? 'A to Z' : 'Popular';

        for(level=1; level<=3; level++){
          $list = this.$columns.filter('.level-'+level).find('.item-list');
          items = $list.find('li').toArray();
          items.sort(sortMethod);
          $list.append(items);
        }

        //update sort labels
        this.$tree.find('.sort-order').text(sortLabel);
      },

      helpers: {
        /** Sort comparator for alphabetical order
         */
        alphabeticalComparator: function(a, b){
          var label1 = $(a).data('name');
          var label2 = $(b).data('name');
          return (label1 < label2) ? -1 : (label1 > label2) ? 1 : 0;
        },

        /** Sort comparator for popular order
         */
        popularComparator: function(a, b){
          var label1 = $(a).data('popularity-order');
          var label2 = $(b).data('popularity-order');
          return (label1 < label2) ? -1 : (label1 > label2) ? 1 : 0;
        },

        /** Generic method which toggles classes indicating visibility on an element
         * N.B. due to the generic nature of this method it could and should be moved
         * to become a part of the app toolkit (doesn't exist yet)
         * @param {jQuery} $element Subject element
         * @param {Boolean} toogle Toggle state
         */
        toggleElement: function($element, toggle){
          $element.toggleClass('visible', toggle);
          $element.toggleClass('hidden', !toggle);
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
