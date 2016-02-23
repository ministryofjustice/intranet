jQuery(function() {
  "use strict";

  var App = window.App;

  //Early
  App.ins.breakpoint = new App.Breakpoint();

  //Mid
  App.ins.mobileHeader = new App.MobileHeader();
  App.ins.stickyNews = new App.StickyNews();
  App.ins.azIndex = new App.AZIndex();
  App.ins.emergencyMessage = new App.EmergencyMessage();
  App.ins.tableOfContents = new App.TableOfContents();
  //App.ins.childrenPages = new App.ChildrenPages();
  App.ins.tabbedContent = new App.TabbedContent();
  App.ins.news = new App.News();
  App.ins.blog = new App.Blog();
  App.ins.events = new App.Events();
  App.ins.shareViaEmail = new App.ShareViaEmail();
  App.ins.like = new App.Like();
  App.ins.searchResults = new App.SearchResults();
  App.ins.searchAutocomplete = new App.SearchAutocomplete();
  App.ins.floaters = new App.Floaters();
  App.ins.collapsibleBlock = new App.CollapsibleBlock();
  App.ins.departmentDropdown = new App.DepartmentDropdown();
  App.ins.skipToContent = new App.SkipToContent();
  App.ins.pageFeedback = new App.PageFeedback();
  App.ins.navigation = new App.Navigation();
  App.ins.accessibility = new App.Accessibility();
  App.ins.myMoj = new App.MyMoj();
  App.ins.postsWidget = new App.PostsWidget();
  App.ins.eventsWidget = new App.EventsWidget();

  //Late
  App.ins.breakpoint.trigger();
});
