(function($) {
  "use strict";

  var App = window.App;

  App.AboutUsIndex = function() {
    this.$top = $('.template-about-us .template-container');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.AboutUsIndex.prototype = {
    init: function() {
      this.agency = App.tools.helpers.agency.getForContent();
      this.agencyData = App.tools.helpers.agency.getData(this.agency);
      this.applicationUrl = $('head').data('application-url');
      this.serviceUrl = this.applicationUrl + '/service/page_tree/children-by-tag/about-' + this.agency + '/2';

      this.categoryItemTemplate = this.$top.find('[data-name="widget-about-us-item"]').html();
      this.childItemTemplate = this.$top.find('[data-name="widget-about-us-child-item"]').html();

      this.resultsLoaded = false;

      this.cacheEls();
      this.bindEvents();

      this.$top.find('.agency-name-heading').html(this.agencyData.label);
      this.requestData();
    },

    cacheEls: function() {
      this.$globalCategoriesList = this.$top.find('.global-categories-list');
      this.$agencyCategoriesList = this.$top.find('.agency-categories-list');
      this.$globalCategoriesBox = this.$top.find('.global-categories-box');
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
      var $category;
      var categoryList = App.tools.sortByKey(data.children, 'title');
      var childrenList = [];

      //temporary measure to display the old layout for agencies that have no content of their own
      if (categoryList.length) {
        this.$top.addClass('has-own-categories');
      }
      else {
        this.$top.find('.category-item')
          .removeClass('col-lg-12 col-md-12')
          .addClass('col-lg-4 col-md-4');
        this.$top.find('.about-column')
          .removeClass('col-lg-6 col-md-6')
          .addClass('col-lg-12 col-md-12');
      }

      $.each(categoryList, function(index, category) {
        $category = _this.buildCategoryItem(category);
        _this.$agencyCategoriesList.append($category);
        childrenList = App.tools.sortByKey(category.children, 'title');

        $.each(childrenList, function(index, child) {
          $category.find('> .children-list').append(_this.buildChildItem(child));
        });
      });

      this.resultsLoaded = true;
      this.$top.removeClass('loading');
    },

    buildCategoryItem: function(data) {
      var $category = $(this.categoryItemTemplate);

      $category.find('a')
        .attr('href', data.url)
        .html(data.title);

      return $category;
    },

    buildChildItem: function(data) {
      var $child = $(this.childItemTemplate);

      $child.find('a')
        .attr('href', data.url)
        .html(data.title);

      return $child;
    }
  };
}(jQuery));
