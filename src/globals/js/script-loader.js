/* global $ */

/*

Script loader

In order to avoid performance issues, scripts are not automatically
loaded when a component is generated. You must explicitly execute your scripts here.

*/

$(document).ready(function () {
  // Tell the css that JavaScript has loaded successfully
  $('html').removeClass('no-js').addClass('js')

  $('.js-clarity-toolbar').moji_clarityToolbar()
  $('.js-left-hand-menu').moji_leftHandMenu()
  $('.js-need-to-know').moji_slider(true)
  $('.c-news-list > .js-article-item').moji_equaliser()
})
