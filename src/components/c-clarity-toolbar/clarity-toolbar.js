/* global jQuery */

(function ($) {
  $.fn.clarityToolbar = () => {
    // Don't do anything unless document.location.search exists
    if (document.location.search.indexOf('devtools=') >= 0) {
      // Append the new information to the url and refresh the page
      const updateUrl = (param) => {
        document.location.search = '?devtools=true&show=' + param
      }

        // If a user clicks on an item, perform the appropriate actions
      const triggerAction = (event) => {
        updateUrl($(event.currentTarget).data('show'))
      }

      // Decide which (if any) tool should appear depending on the query string
      const showItem = (item) => {
        if (document.location.search.indexOf('show=') === -1) return
        // Trim the '?' from the querystring
        item = item.substring(1)
        let toolStatus = item.split('&')[0].split('=')[1]
        let show = item.split('&')[1].split('=')[1]
        if (toolStatus === 'true') {
          $('body').addClass('show_' + show)
          getClassType(show)
        }
      }

      // Loops through each item and displays the relevant class
      const showClass = (type) => {
        // BUG: AF: Some elements are not showing the class for some reason. I don't have time to investigate at the moment.
        let elementList
        if (type === 'c') {
          elementList = $(
          "section[class*='c-']" || "section[class^=' c-'-']" ||
          "header[class*='c-']" || "header[class^=' c-']" ||
          "article[class*='c-']" || "article[class^=' c-']" ||
          "nav[class*='c-']" || "nav[class^=' c-']" ||
          "aside[class*='c-']" || "aside[class^=' c-']")
        } else {
          elementList = $("[class*='" + type + "-']" || "[class^='" + type + "-']")
        }
        elementList.each(() => {
          let classList = $(this).attr('class').split(' ')
          for (let i = classList.length - 1; i >= 0; i--) {
            if (classList[i].indexOf(type) === 0) $('<div class="item_name">' + classList[i] + '</div>').prependTo($(this))
          }
        })
      }

      // Converts a query into a class for the 'showClass()' function
      const getClassType = (query) => {
        switch (query) {
          case 'components' :
            showClass('c')
            break
          case 'objects' :
            showClass('o')
            break
          case 'layouts' :
            showClass('l')
            break
          case 'utilities' :
            showClass('u')
            break
          case 'javascript' :
            showClass('js')
            break
        }
      }

      this.find('ul > li').on('click', triggerAction)
      showItem(document.location.search)

      return this
    } else {
      console.info('Did you know you can add a developer toolbar to the top of this page? just add ?devtools=true to the url!')
    }
  }
}(jQuery))
