/** Children pages
 * Note: it's designed to work with only one instance per page
 */
(function($) {
  "use strict";

  var App = window.App;

  App.ChildrenPages = function() {
    this.settings = {
      serviceUrl: '/service/children/'
    };

    this.$childrenPages = $('.children-pages');
    this.$pageContainer = $('.guidance-and-support-content');
      this.pageId = this.$pageContainer.attr('data-page-id');
    if(!this.$childrenPages.length || !this.pageId) { return; }
    this.init();
  };

  App.ChildrenPages.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();
      this.getChildren();
    },

    cacheEls: function() {
      this.$childrenPagesBox = $('.children-pages-box');
    },

    bindEvents: function() {
    },

    getChildren: function() {
      var _this = this;

      $.ajax({
        url: this.settings.serviceUrl + this.pageId,
        type: 'json',
        success: function(data) {
          _this.populateChildrenList(data);
        },
        error: function() {
        }
      });
    },

    populateChildrenList: function(data) {
      var _this = this;
      var $child;

      $.each(data.items, function(index, child) {
        $child = _this.constructChildLink(child);
        $child.appendTo(_this.$childrenPages);
      });

      this.updateVisibility();
    },

    constructChildLink: function(childData) {
      var $child = $('<li></li>');
      var $link = $('<a></a>');

      $link.attr('href', childData.url);
      $link.text(childData.title);
      if(childData.isExternal) {
        $link.attr('rel', 'external');
      }
      $link.appendTo($child);

      return $child;
    },

    updateVisibility: function() {
      this.$childrenPagesBox.toggleClass('visible', this.$childrenPages.find('li').length > 0);
    }
  };
}(jQuery));
