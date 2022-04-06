export default (function ($) {
    $.fn.moji_leftHandMenu = function () {
        var listItem = this.find('li')
        listItem.each(
            function () {
                // If the list item has children and is not the active item then hide its children.
                if ($(this).hasClass('page_item_has_children') && !$(this).hasClass('current_page_item')) {
                    $(this).addClass('u-closed')
                }
                // If the list item has children, attach a click event to each directly descended anchor element
                $(this).filter('.page_item_has_children').find('> a ').on(
                    'click',
                    function (e) {
                        if ($(this).parentsUntil('ul').hasClass('u-closed')) {
                            e.preventDefault()
                            $(this).parentsUntil('ul').removeClass('u-closed')
                            $(this).attr('aria-expanded', 'true')    
                        } else {
                            e.preventDefault()
                            $(this).parentsUntil('ul').addClass('u-closed')
                            $(this).attr('aria-expanded', 'false')
                        }
                    }
                ).attr('aria-expanded', 'false').attr('aria-haspopup', 'true')
            }
        )
        return this
    }
})(jQuery);
