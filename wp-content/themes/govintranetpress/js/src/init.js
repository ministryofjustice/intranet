jQuery(function($) {
  "use strict";

  var App = window.App;

  App.ins.mobileMenu = new App.MobileMenu();
  App.ins.stickyNews = new App.StickyNews();
  App.ins.guidanceAndSupport = new App.GuidanceAndSupport();
  App.ins.guidanceAndSupportContent = new App.GuidanceAndSupportContent();
  App.ins.azIndex = new App.AZIndex();
  App.ins.emergencyMessage = new App.EmergencyMessage();
  App.ins.tableOfContents = new App.TableOfContents();
  App.ins.tabbedContent = new App.TabbedContent();
  App.ins.news = new App.News();
  App.ins.searchResults = new App.SearchResults();
  App.ins.floaters = new App.Floaters();
  App.ins.collapsibleBlock = new App.CollapsibleBlock();
});
