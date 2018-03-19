/* global casper jQuery */

var settings = require('../settings.js')

casper.start(settings.domain)

console.log('## Testing page header')
casper.then(function checkHeaderExists () {
  casper.test.assertExists('.c-header-container', 'The header container exists on the page')
  casper.test.assertExists('.c-header-container .c-logo-bar', 'The logo bar exists in the header')
  casper.test.assertExists('.c-header-container .c-search-bar', 'The search bar exists in the header')
  casper.test.assertExists('.c-header-container .c-main-nav-bar', 'The main navigation bar exists in the header')
})

// TODO: This page only works on HQ at the moment, also it would be nice if it worked using a less verbose syntax
casper.then(function checkNavigationElements () {
  console.log('## Testing primary navigation')
  casper.test.assertEvalEquals(function testHomeLink () {
    return jQuery('.c-main-nav-bar li:nth-child(1) a').text()
  }, 'Home', 'Check Home link exists and says "Home"')
  casper.test.assertEvalEquals(function testNewsLink () {
    return jQuery('.c-main-nav-bar li:nth-child(2) a').text()
  }, 'News', 'Check News link exists and says "News"')
  casper.test.assertEvalEquals(function testEventsLink () {
    return jQuery('.c-main-nav-bar li:nth-child(3) a').text()
  }, 'Events', 'Check Events link exists and says "Events"')
  casper.test.assertEvalEquals(function testGuidanceLink () {
    return jQuery('.c-main-nav-bar li:nth-child(4) a').text()
  }, 'Guidance & forms', 'Check Guidance link exists and says "Guidance & forms"')
  casper.test.assertEvalEquals(function testAboutLink () {
    return jQuery('.c-main-nav-bar li:nth-child(5) a').text()
  }, 'About Us', 'Check About Us link exists and says "About Us"')
  casper.test.assertEvalEquals(function testBlogLink () {
    return jQuery('.c-main-nav-bar li:nth-child(6) a').text()
  }, 'Blog', 'Check Blog link exists and says "Blog"')
})

casper.run(function () {
  casper.test.done()
})
