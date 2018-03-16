(function($) {
  "use strict";

  var App = window.App;

  App.SkeletonScreens = function() {
    this.$top = $('[data-skeleton-screen-count]');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.SkeletonScreens.prototype = {
    init: function() {
      this.applicationUrl = $('head').data('application-url');

      this.generate();
    },

    generate: function() {
      var _this = this;
      var $skeletonScreen;
      var $list;
      var template;
      var count;
      var classes;
      var type;
      var a;

      this.$top.each(function(index, list) {
        $list = $(list);
        count = parseInt($list.attr('data-skeleton-screen-count'), 10);
        classes = $list.attr('data-skeleton-screen-classes');
        type = $list.attr('data-skeleton-screen-type') || 'standard';

        //remove the skeleton attributes
        $list.removeAttr('data-use-skeleton-screens');
        $list.removeAttr('data-skeleton-screen-count');
        $list.removeAttr('data-skeleton-screen-type');
        $list.removeAttr('data-skeleton-screen-classes');

        for (a = 0; a < count; a++) {
          template = $('script[data-name="skeleton-screen"][data-type="' + type + '"]').html();
          $skeletonScreen = $(template);
          $skeletonScreen.find('[data-size]').each(_this.setElementWidth);

          $skeletonScreen.addClass(classes);
          $skeletonScreen.appendTo($list);
        }
      });
    },

    setElementWidth: function(index, element) {
      var $element;
      var size;
      var min, max;
      var width;

      $element = $(element);
      size = $element.attr('data-size').split(':');

      if(size.length > 1) {
        min = parseInt(size[0], 10) || 0;
        max = parseInt(size[1], 10) || 100;
        width = App.tools.rand(min, max);
      }
      else {
        width = size[0];
      }

      $element.css({
        width: '' + width + '%'
      });
    },

    remove: function($container) {
      $container.find('.skeleton-screen').remove();
    }
  };

  App.ins.skeletonScreens = new App.SkeletonScreens();
}(jQuery));

