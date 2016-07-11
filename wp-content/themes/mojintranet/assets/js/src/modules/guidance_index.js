(function($) {
  "use strict";

  var App = window.App;

  App.GuidanceIndexWidget = function() {
    this.$top = $('.template-guidance-and-support-index .template-container');
    if (!this.$top.length) { return; }
    this.init();
  };

  App.GuidanceIndexWidget.prototype = {
    init: function() {
      this.agency = App.tools.helpers.agency.getForContent();
      this.applicationUrl = $('head').data('application-url');
      this.serviceUrl = this.applicationUrl + '/service/page_tree/guidance-index/' + this.agency + '//';

      this.categoryItemTemplate = this.$top.find('[data-name="widget-guidance-item"]').html();
      this.childItemTemplate = this.$top.find('[data-name="widget-guidance-child-item"]').html();

      this.resultsLoaded = false;

      this.cacheEls();

      this.requestData();
    },

    cacheEls: function() {
      this.$topCategoriesList = this.$top.find('.most-visited .guidance-categories-list');
      this.$allCategoriesList = this.$top.find('.a-to-z .guidance-categories-list');
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
      var childrenList;
      var $category;
      //var childrenList;

      //get most visited
      if (this.agency === 'hq') {
        $.each(data.most_visited, function(index, category) {
          $category = _this.buildCategoryItem(category);
          _this.$topCategoriesList.append($category);

          childrenList = App.tools.sortByKey(category.children, 'title');

          $.each(childrenList, function(index, child) {
            $category.find('> .children-list').append(_this.buildChildItem(child));
          });
        });
      }

      $.each(data.categories, function(index, category) {
        _this.$allCategoriesList.append(_this.buildCategoryItem(category));
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
