(function($) {
  "use strict";

  var App = window.App;

  App.SkeletonScreens = function() {
    this.$top = $('[data-use-skeleton-screens="true"]');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.SkeletonScreens.prototype = {
    init: function() {
      this.applicationUrl = $('head').data('application-url');

      this.generate();
    },

    generate: function() {
      var $skeletonScreen;
      var $list;
      var count;
      var classes;
      var template;
      var type;
      var a;

      this.$top.each(function(index, list) {
        $list = $(list);
        count = parseInt($list.attr('data-skeleton-screen-count'), 10);
        classes = $list.attr('data-skeleton-screen-classes');
        type = $list.attr('data-skeleton-screen-type') || 'standard';
        template = $('script[data-name="skeleton-screen-' + type + '"]').html();
        console.log(type, template);

        //remove the skeleton attributes
        $list.removeAttr('data-use-skeleton-screens');
        $list.removeAttr('data-skeleton-screen-count');
        $list.removeAttr('data-skeleton-screen-type');
        $list.removeAttr('data-skeleton-screen-classes');

        for (a = 0; a < count; a++) {
          $skeletonScreen = $(template);
          $skeletonScreen.addClass(classes);
          $skeletonScreen.appendTo($list);
        }
      });
    }
  };
}(jQuery));
