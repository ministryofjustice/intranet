export default (function ($) {
  $.fn.filterFollowUser = function () {
    /**
     * jQuery object store
     * Provides an organised way to cache objects
     *
     * @type {jQuery|HTMLElement|*}
     */
    const JQ = {
      filter: $('.c-search-results-filter'),
      follow: $('.c-search-results-filter form'),
      content: $('#content'),
      pagination: $('.c-pagination__main')
    }

    if (JQ.filter.length === 0) {
      return;
    }

    const position = {
      top: JQ.follow.offset().top - 10,
      bottom: JQ.content.outerHeight(),
      stick: JQ.pagination.outerHeight(),
      width: JQ.filter.parent().outerWidth() + 'px'
    }

    // define the absolute bottom where fixing stops
    position.absolute = (position.bottom - (JQ.follow.offset().top - position.stick)) - 40;

    /**
     * Feature functions
     * Provides an organised way to store functions to execute on the application
     *
     * @type Object
     */
    const MOJ_Filter = {
      style: {
        fixed: { position: 'fixed', top: 10, width: position.width },
        bottomed: {
          position: 'absolute',
          bottom: position.stick,
          top: 'auto',
          width: position.width
        },
        normal: { position: '', top: '', bottom: '', width: '' },
      },
      on: {
        scroll: function () {
          $(window).bind('scroll', function () {
            const offset = $(this).scrollTop()

            if (offset >= position.top && offset < position.absolute) {
              JQ.follow.css(MOJ_Filter.style.fixed)
            } else if (offset >= position.absolute) {
              JQ.follow.css(MOJ_Filter.style.bottomed)
            } else if (offset < position.top) {
              JQ.follow.css(MOJ_Filter.style.normal)
            }
          })
        },
      },
    }

    MOJ_Filter.on.scroll();
  }
})(jQuery)
