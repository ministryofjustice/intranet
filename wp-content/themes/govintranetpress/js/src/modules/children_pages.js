/** Children pages
 * Note: it's designed to work with only one instance per page
 */
(function($) {
  "use strict";

  var App = window.App;

  App.ChildrenPages = function() {
    this.$childrenPages = $('.children-pages');
    this.isImported = !!$('.guidance-and-support-content[data-is-imported="1"]').length;
    if(!this.$childrenPages.length || this.isImported) { return; }
    this.init();
  };

  App.ChildrenPages.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();
      this.generate();
      this.initialized = true;
    },

    cacheEls: function() {
      this.$childrenPagesBox = $('.children-pages-box');
    },

    bindEvents: function() {
    },

    generate: function() {
      var _this = this;

      if(!this.initialized) { return; }
    },

    getChildren: function() {
    },

    populateChildrenList: function() {
    }
  };
}(jQuery));
