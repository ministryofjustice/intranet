/* global jQuery */

;(function ($) {
  $.fn.moji_leftHandMenu = function () {
    var listItem = this.find('li')
    listItem.each(function () {
      // If the list item has children and is not the active item then hide its children.
      if ($(this).hasClass('page_item_has_children') && !$(this).hasClass('active')) {
        $(this).addClass('u-closed')
      }
      // If the list item has children, attach a click event to each directly descended anchor element
      $(this).filter('.page_item_has_children').find('> a').on('click', function () {
        if ($(this).parent().hasClass('u-closed')) {
          $(this).parent().removeClass('u-closed')
        } else {
          $(this).parent().addClass('u-closed')
        }
      })
    })
    return this
  }
})(jQuery)
