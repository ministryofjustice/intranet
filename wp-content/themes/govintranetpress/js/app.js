/*! mojintranet 2015-01-16 */
!function(){"use strict";window.App={tools:{},ins:{}}}(jQuery),function(){"use strict";var a=window.App,b={sizeUnits:["B","KB","MB","GB","TB","PB"]};a.tools={round:function(a,b){var c;return b?(c=b?Math.pow(10,b):1,Math.round(a*c)/c):Math.round(a)},formatSize:function(a){for(var c=0;a>=1024;)a/=1024,c++;return(c>0?this.round(a,2):a)+b.sizeUnits[c]}}}(jQuery),function(a){"use strict";var b=window.App;b.AZIndex=function(){this.$top=a(".a-z"),this.$top.length&&(this.init(),this.goToLetter(null))},b.AZIndex.prototype={init:function(){this.applicationUrl=a("head").data("application-url"),this.serviceUrl=this.applicationUrl+"/service/search",this.pageBase=this.applicationUrl+"/"+this.$top.data("top-level-slug"),this.itemTemplate=this.$top.find('template[data-name="a-z-result-item"]').html(),this.serviceXHR=null,this.cacheEls(),this.bindEvents()},cacheEls:function(){this.$categoryInput=this.$top.find('[name="category"]'),this.$keywordsInput=this.$top.find('[name="keywords"]'),this.$letters=this.$top.find(".letter"),this.$results=this.$top.find(".results")},bindEvents:function(){var b=this;this.$letters.click(function(c){c.preventDefault();{var d=a(this);d.data("letter")}b.$letters.removeClass("selected"),d.addClass("selected"),b.loadResults()}),this.$keywordsInput.keyup(function(){b.loadResults()})},loadResults:function(a){a=this.getRequestData(a),this.stopLoadingResults(),this.requestResults(a)},stopLoadingResults:function(){this.serviceXHR&&(this.serviceXHR.abort(),this.serviceXHR=null)},requestResults:function(b){var c=[];a.each(b,function(a,b){c.push(b)}),this.serviceXHR=a.getJSON(this.serviceUrl+"/"+c.join("/"),a.proxy(this.displayResults,this))},clearResults:function(){this.$results.empty()},displayResults:function(b){var c,d=this;this.clearResults(),a.each(b.data,function(b,e){a.each(e.results,function(a,b){c=d.buildResultRow(b),d.$results.append(c)})})},getSelectedInitial:function(){return this.$letters.filter(".selected").data("letter")},buildResultRow:function(b){var c=a(this.itemTemplate);return c.find(".title").html(b.title),c.find(".description").html(b.excerpt),c},goToLetter:function(a){a=a?a.toUpperCase():"All",this.$letters.removeClass("selected"),this.$letters.filter('[data-letter="'+a+'"]').addClass("selected"),this.loadResults()},getRequestData:function(b){var c=this,d={type:"",category:"",keywords:c.$keywordsInput.val(),initial:c.getSelectedInitial(),page:1,resultsPerPage:20};return b&&a.each(b,function(a,b){d[a]=b}),d}}}(jQuery),function(a){"use strict";var b=window.App;b.EmergencyMessage=function(){this.$top=a(".message"),this.$top.length&&this.init()},b.EmergencyMessage.prototype={init:function(){this.cacheEls(),this.bindEvents()},cacheEls:function(){this.$closeButton=this.$top.find(".close")},bindEvents:function(){this.$closeButton.on("click",a.proxy(this.close,this))},close:function(){this.$top.slideUp(200)}}}(window.jQuery),function(a){"use strict";var b=window.App;b.Floaters=function(){this.$floaters=a(".js-floater"),this.$floaters.length&&this.init()},b.Floaters.prototype={init:function(){this.cacheEls(),this.bindEvents(),this.setUpFloaters()},cacheEls:function(){},bindEvents:function(){a(window).on("scroll",a.proxy(this.scrollHandler,this))},setUpFloaters:function(){this.$floaters.each(function(){var c=a(this);c.attr("data-start-position",b.tools.round(c.offset().top,0))})},scrollHandler:function(){this.$floaters.each(function(){var c=a(this),d=a(c.attr("data-floater-limiter-selector")),e=c.outerHeight(),f=b.tools.round(d.offset().top,0),g=d.outerHeight(),h=a(window).scrollTop();c.css(h>f?{marginTop:Math.min(h-f,g-e-100)+"px"}:{marginTop:""})})}}}(jQuery),function(a){"use strict";var b=window.App;b.GuidanceAndSupportContent=function(){this.$top=a(".guidance-and-support-content"),this.$top.length&&this.init()},b.GuidanceAndSupportContent.prototype={init:function(){this.redirectUrl=this.$top.attr("data-redirect-url"),this.redirectEnabled=this.$top.attr("data-redirect-enabled"),this.redirectUrl&&"1"===this.redirectEnabled&&this.redirect(this.redirectUrl)},redirect:function(a){window.location.href=a}}}(window.jQuery),function(a){"use strict";var b=window.App;b.GuidanceAndSupport=function(){this.$top=a(".guidance-and-support"),this.$top.length&&this.init()},b.GuidanceAndSupport.prototype={init:function(){this.applicationUrl=a("head").data("application-url"),this.serviceUrl=this.applicationUrl+"/service/children",this.pageBase=this.applicationUrl+"/"+this.$top.data("top-level-slug"),this.itemTemplate=this.$top.find('template[data-name="guidance-and-support-category-item"]').html(),this.serviceXHR=null,this.cacheEls(),this.bindEvents(),this.prepopulateColumns()},cacheEls:function(){this.$tree=this.$top.find(".tree"),this.$columns=this.$tree.find(".item-container"),this.$sortList=this.$top.find(".tabbed-filters"),this.$sortPopular=this.$sortList.find('[data-sort-type="popular"]'),this.$sortAlphabetical=this.$sortList.find('[data-sort-type="alphabetical"]'),this.$allCategoriesLink=this.$tree.find(".all-categories")},bindEvents:function(){var b=this;this.$sortAlphabetical.on("click","a",function(c){c.preventDefault(),b.sort("alphabetical"),b.$sortList.find(".filter-item").removeClass("selected"),a(this).parent().addClass("selected")}),this.$sortPopular.on("click","a",function(c){c.preventDefault(),b.sort("popular"),b.$sortList.find(".filter-item").removeClass("selected"),a(this).parent().addClass("selected")}),this.$allCategoriesLink.on("click",function(a){a.preventDefault(),b.toggleTopLevelCategories(!0),b.collapseTopLevelColumn(!1)}),this.$tree.hammer().on("swipeleft",a.proxy(this.swipeMobileColumns,this,"left")),this.$tree.hammer().on("swiperight",a.proxy(this.swipeMobileColumns,this,"right")),a(document).on("keydown",a.proxy(this.swipeMobileColumns,this,null))},swipeMobileColumns:function(a,b){var c,d;if("keydown"===b.type)if(37===b.keyCode)a="right";else{if(39!==b.keyCode)return;a="left"}c=parseInt(this.$tree.attr("data-show-column"),10),d="left"===a?c+1:c-1,this.$columns.filter(".level-"+d).is(":visible")&&this.$tree.attr("data-show-column",d)},categoryClick:function(a,b,c){var d=this.$columns.filter(".level-"+b),e=d.find("[data-page-id="+a+"]");c.preventDefault(),e.hasClass("selected")||(this.markItem(e),this.loadChildren(a,b+1),1===b&&this.toggleTopLevelCategories(!1))},updateUrl:function(){var b,c=[this.pageBase];this.$columns.each(function(){return b=a(this).find(".item.selected"),b.length?(c.push(b.data("slug")),!0):!1}),history.pushState({},"",c.join("/")+"/")},markItem:function(a){a.closest(".item-list").find(".selected").removeClass("selected"),a.addClass("selected")},toggleTopLevelCategories:function(a){if(void 0===a)throw new Error("toggle parameter must be set (boolean)");this.$columns.filter(".level-1").find(".item:not(.selected)").slideToggle(a),this.$allCategoriesLink.toggleClass("visible",!a)},collapseTopLevelColumn:function(a){this.$tree.toggleClass("collapsed",a)},prepopulateColumns:function(){var b,c,d=this,e={},f=0;this.$columns.each(function(){b=a(this),e=b.data("items"),c=b.data("selected-id"),b.removeAttr("data-items"),b.removeAttr("data-selected-id"),e&&(f++,d.populateColumn(f,e),d.markItem(b.find('[data-page-id="'+c+'"]')))}),f>1&&this.toggleTopLevelCategories(!1),f>2&&this.collapseTopLevelColumn()},loadChildren:function(a,b){this.stopLoadingChildren(),this.$tree.find('[data-page-id="'+a+'"]').addClass("loading"),this.requestChildren(a,b)},stopLoadingChildren:function(){this.$tree.find(".item.loading").removeClass("loading"),this.serviceXHR&&(this.serviceXHR.abort(),this.serviceXHR=null)},requestChildren:function(b,c){var d=this;d.serviceXHR=a.getJSON(d.serviceUrl+"/"+b,a.proxy(d.populateColumn,d,c))},populateColumn:function(b,c){var d,e,f=this,g=this.$columns.filter(".level-"+b),h=this.$columns.filter(".level-"+(b+1)),i=g.find(".item-list");for(this.helpers.toggleElement(h,!1),e=b;3>=e;e++)this.$columns.filter(".level-"+e).find(".item-list").empty();b>1&&g.find(".category-name").html(c.title),a.each(c.items,function(a,c){d=f.buildChild(c,b),i.append(d)}),this.sort(),this.helpers.toggleElement(g,!0),this.stopLoadingChildren(),this.$columns.removeClass("current"),this.$columns.filter(".level-"+b).addClass("current"),this.updateUrl(),this.$tree.attr("data-show-column",b)},buildChild:function(b,c){var d=this,e=a(this.itemTemplate);return e.attr("data-page-id",b.id),e.attr("data-popularity-order",b.id),e.attr("data-name",b.title),e.attr("data-slug",b.slug),e.find(".title").html(b.title),e.find("a").attr("href",b.url),e.on("click","a",a.proxy(this.collapseTopLevelColumn,this,1!==c)),b.is_external&&!b.child_count&&e.find("a").attr("rel","external"),3>c&&b.child_count>0&&(e.find(".description").html(b.excerpt),e.on("click","a",a.proxy(d.categoryClick,d,b.id,c))),e},sort:function(a){var b,c,d,e,f;for(a||(a=this.$sortList.find(".selected").data("sort-type")),d="alphabetical"===a?this.helpers.alphabeticalComparator:this.helpers.popularComparator,e="alphabetical"===a?"A to Z":"Popular",b=1;3>=b;b++)f=this.$columns.filter(".level-"+b).find(".item-list"),c=f.find("li").toArray(),c.sort(d),f.append(c);this.$tree.find(".sort-order").text(e)},helpers:{alphabeticalComparator:function(b,c){var d=a(b).data("name"),e=a(c).data("name");return e>d?-1:d>e?1:0},popularComparator:function(b,c){var d=a(b).data("popularity-order"),e=a(c).data("popularity-order");return e>d?-1:d>e?1:0},toggleElement:function(a,b){a.toggleClass("visible",b),a.toggleClass("hidden",!b)}}}}(window.jQuery),function(a){"use strict";var b=window.App;b.MobileMenu=function(){this.$top=a(".header"),this.$top.length&&(this.config={menuToggleClass:"mobile-menu-enabled"},this.init())},b.MobileMenu.prototype={init:function(){this.cacheEls(),this.bindEvents()},cacheEls:function(){this.$menuButton=this.$top.find(".mobile-nav button")},bindEvents:function(){this.$top.on("click","button",a.proxy(this.toggleMenu,this))},toggleMenu:function(){this.$top.toggleClass(this.config.menuToggleClass)}}}(window.jQuery),function(a){"use strict";var b=window.App;b.News=function(){this.$top=a(".page-news"),this.$top.length&&this.init()},b.News.prototype={init:function(){this.settings={dateDropdownMonths:12},this.applicationUrl=a("head").data("application-url"),this.serviceUrl=this.applicationUrl+"/service/news",this.pageBase=this.applicationUrl+"/"+this.$top.data("top-level-slug"),this.itemTemplate=this.$top.find('template[data-name="news-item"]').html(),this.resultsPageTitleTemplate=this.$top.find('template[data-name="news-results-page-title"]').html(),this.filteredResultsTitleTemplate=this.$top.find('template[data-name="news-filtered-results-title"]').html(),this.serviceXHR=null,this.months=["January","February","March","April","May","June","July","August","September","October","November","December"],this.currentPage=null,this.cacheEls(),this.bindEvents(),this.populateDateFilter(),this.setFilters(),this.loadResults()},cacheEls:function(){this.$dateInput=this.$top.find('[name="date"]'),this.$keywordsInput=this.$top.find('[name="keywords"]'),this.$results=this.$top.find(".results"),this.$prevPage=this.$top.find(".previous"),this.$nextPage=this.$top.find(".next")},bindEvents:function(){var b=this;this.$dateInput.on("change",function(){b.loadResults({page:1})}),this.$keywordsInput.on("input",function(){b.loadResults({page:1})}),this.$prevPage.click(function(c){c.preventDefault(),b.loadResults({page:a(this).attr("data-page")})}),this.$nextPage.click(function(c){c.preventDefault(),b.loadResults({page:a(this).attr("data-page")})})},populateDateFilter:function(){var b,c,d,e,f,g=new Date,h=g.getFullYear(),i=g.getMonth(),j=1;for(f=0;f<this.settings.dateDropdownMonths;f++)b=new Date(h,i-f,j),d=b.getMonth(),c=b.getFullYear(),e=a("<option>"),e.text(this.months[d]+" "+c),e.val(c+"-"+(d+1)),this.$dateInput.append(e)},setFilters:function(){var a,b=this.getSegmentsFromUrl();b[2]&&(a=b[2].replace("+"," "),a&&this.$keywordsInput.val("-"===a?"":a)),b[3]&&this.$dateInput.val(b[3])},loadResults:function(b){var c=this.$top.find(".news-results-page-title");c.length||(c=a(this.resultsPageTitleTemplate),this.$results.append(c)),b=this.getDataObject(b),this.stopLoadingResults(),this.$top.addClass("loading-results"),this.$top.find(".news-results-title").remove(),this.$results.prepend(a(this.resultsPageTitleTemplate).text("Loading results...")),this.$results.find(".news-item").addClass("faded"),this.requestResults(b)},stopLoadingResults:function(){this.$top.removeClass("loading-results"),this.$top.find(".news-group-separator.loading"),this.serviceXHR&&(this.serviceXHR.abort(),this.serviceXHR=null)},requestResults:function(b){var c=this,d=[];a.each(b,function(a,b){d.push(b)}),c.serviceXHR=a.getJSON(c.serviceUrl+"/"+d.join("/"),a.proxy(c.displayResults,c))},clearResults:function(){this.$results.empty()},displayResults:function(b){var c,d=this;this.clearResults(),this.setResultsHeading(b),a.each(b.results,function(a,b){c=d.buildResultRow(b),d.$results.append(c)}),this.updatePagination(b),this.updateUrl(),this.stopLoadingResults()},setResultsHeading:function(b){var c,d,e=a(this.resultsPageTitleTemplate),f=a(this.filteredResultsTitleTemplate),g=parseInt(b.totalResults,10),h=parseInt(b.urlParams.page,10);this.hasKeywords()||this.$dateInput.val()?(this.$results.append(f),f.find(".results-count").text(g),f.find(".results-count-description").text(1===g?"result":"results"),this.hasKeywords()?f.find(".keywords").text(this.getSanitizedKeywords()):(f.find(".containing").hide(),f.find(".keywords").hide()),this.$dateInput.val()?(c=this.parseDate(this.$dateInput.val()),d=this.months[c.getMonth()]+" "+c.getFullYear(),f.find(".date").text(d)):(f.find(".for-date").hide(),f.find(".date").hide())):(e.text(1===h?"Latest":"Archive"),this.$results.append(e))},hasKeywords:function(){return this.getSanitizedKeywords().length>0},getSanitizedKeywords:function(){var a=this.$keywordsInput.val();return a=a.replace(/^\s+|\s+$/g,""),a=a.replace(/\s+/g," "),a=a.replace(/[^a-zA-Z0-9\s]+/g,"")},buildResultRow:function(b){var c=a(this.itemTemplate),d=this.parseDate(b.timestamp);return b.thumbnail_url?c.find(".thumbnail").attr("src",b.thumbnail_url):c.find(".thumbnail").remove(),c.find(".title").html(b.title),c.find(".news-link").attr("href",b.url),c.find(".date").html(this.formatDate(d)),c.find(".excerpt").html(b.excerpt),c},getDataObject:function(b){{var c=this.getSanitizedKeywords(),d=this.getSegmentsFromUrl();d[1]||1}c=c.replace(/\s+/g,"+");var e={category:"",date:this.$dateInput.val(),keywords:c,page:d[1]||1};return b&&a.each(b,function(a,b){e[a]=b}),e},parseDate:function(a){var b=a.split("-");return 2===b.length&&b.push("01"),new Date(b.join("/"))},formatDate:function(a){return a.getDate()+" "+this.months[a.getMonth()]+" "+a.getFullYear()},updatePagination:function(a){this.currentPage=parseInt(a.urlParams.page,10);var b=parseInt(a.urlParams.per_page,10),c=parseInt(a.totalResults,10),d=b>0?Math.ceil(c/b):1,e=Math.max(this.currentPage-1,1),f=Math.min(this.currentPage+1,d);this.$prevPage.toggleClass("disabled",this.currentPage<=1),this.$nextPage.toggleClass("disabled",this.currentPage>=d),this.$prevPage.attr("data-page",e),this.$nextPage.attr("data-page",f),this.$prevPage.find(".prev-page").text(e),this.$nextPage.find(".next-page").text(f),this.$top.find(".total-pages").text(d)},getSegmentsFromUrl:function(){var a=window.location.href,b=a.substr(this.pageBase.length);return b=b.replace(/^\/|\/$/g,""),b.split("/")},updateUrl:function(){var a=[this.pageBase],b=this.getSanitizedKeywords();b=b.replace(/\s/g,"+"),a.push("page"),a.push(this.currentPage),a.push(b||"-"),a.push(this.$dateInput.val()||"-"),history.pushState({},"",a.join("/")+"/")}}}(jQuery),function(a){"use strict";var b=window.App;b.SearchResults=function(){this.$top=a(".page-search-results"),this.$top.length&&this.init()},b.SearchResults.prototype={init:function(){this.applicationUrl=a("head").data("application-url"),this.serviceUrl=this.applicationUrl+"/service/search",this.pageBase=this.applicationUrl+"/"+this.$top.data("top-level-slug"),this.itemTemplate=this.$top.find('template[data-name="search-item"]').html(),this.resultsPageTitleTemplate=this.$top.find('template[data-name="search-results-page-title"]').html(),this.filteredResultsTitleTemplate=this.$top.find('template[data-name="search-filtered-results-title"]').html(),this.serviceXHR=null,this.months=["January","February","March","April","May","June","July","August","September","October","November","December"],this.currentPage=null,this.cacheEls(),this.bindEvents(),this.$keywordsInput.focus(),this.setFilters(),this.loadResults()},cacheEls:function(){this.$searchForm=this.$top.find("#search-form"),this.$typeInput=this.$top.find('[name="type"]'),this.$categoryInput=this.$top.find('[name="category"]'),this.$keywordsInput=this.$top.find('[name="keywords"]'),this.$results=this.$top.find(".results"),this.$prevPage=this.$top.find(".previous"),this.$nextPage=this.$top.find(".next")},bindEvents:function(){var b=this;this.$keywordsInput.on("input",function(){b.loadResults({page:1})}),this.$prevPage.click(function(c){c.preventDefault(),b.loadResults({page:a(this).attr("data-page")})}),this.$nextPage.click(function(c){c.preventDefault(),b.loadResults({page:a(this).attr("data-page")})}),this.$searchForm.on("submit",function(a){a.preventDefault()})},setFilters:function(){var a,b=this.getSegmentsFromUrl();b[0]&&this.$typeInput.val(b[0]),b[1]&&(a=b[1],a=decodeURI(a),a=a.replace("+"," "),a=a.replace(/[^a-zA-Z0-9\s']+/g,""),a&&this.$keywordsInput.val("-"===a?"":a))},loadResults:function(b){b=this.getDataObject(b),this.stopLoadingResults(),this.$top.addClass("loading-results"),this.$top.find(".search-results-title").remove(),this.$results.prepend(a(this.resultsPageTitleTemplate).text("Loading results...")),this.$results.find(".search-item").addClass("faded"),this.requestResults(b)},stopLoadingResults:function(){this.$top.removeClass("loading-results"),this.$top.find(".search-group-separator.loading"),this.serviceXHR&&(this.serviceXHR.abort(),this.serviceXHR=null)},requestResults:function(b){var c=this,d=[];a.each(b,function(a,b){d.push(b)}),c.serviceXHR=a.getJSON(c.serviceUrl+"/"+d.join("/"),a.proxy(c.displayResults,c))},clearResults:function(){this.$results.empty()},displayResults:function(b){var c,d=this;this.clearResults(),this.setResultsHeading(b),a.each(b.results,function(a,b){c=d.buildResultRow(b),d.$results.append(c)}),this.updatePagination(b),this.updateUrl(),this.stopLoadingResults()},setResultsHeading:function(b){{var c=(a(this.resultsPageTitleTemplate),a(this.filteredResultsTitleTemplate)),d=parseInt(b.totalResults,10);parseInt(b.urlParams.page,10)}this.$results.append(c),c.find(".results-count").text(d),c.find(".results-count-description").text(1===d?"result":"results"),this.hasKeywords()?c.find(".keywords").text(this.getSanitizedKeywords()):(c.find(".containing").hide(),c.find(".keywords").hide())},hasKeywords:function(){return this.getSanitizedKeywords().length>0},getSanitizedKeywords:function(){var a=this.$keywordsInput.val();return a=a.replace(/^\s+|\s+$/g,""),a=a.replace(/[^a-zA-Z0-9\s']+/g," "),a=a.replace(/\s+/g," ")},buildResultRow:function(c){var d=a(this.itemTemplate),e=this.parseDate(c.timestamp);return c.thumbnail_url?d.find(".thumbnail").attr("src",c.thumbnail_url):d.find(".thumbnail").remove(),d.find(".title").html(c.title),d.find(".search-link").attr("href",c.url),d.find(".date").html(this.formatDate(e)),d.find(".excerpt").html(c.excerpt),c.file_url?(d.find(".file-link").html(c.file_name).attr("href",c.file_url),d.find(".file-size").html(b.tools.formatSize(c.file_size)),d.find(".file-length").html(c.file_pages)):d.find(".file").hide(),d},getDataObject:function(b){var c=this.getSanitizedKeywords(),d=this.getSegmentsFromUrl(),e=d[2]||1;c=c.replace(/\s+/g,"+");var f={type:"",category:"",keywords:c,page:e,resultsPerPage:10};return b&&a.each(b,function(a,b){f[a]=b}),f},parseDate:function(a){var b=a.split("-");return 2===b.length&&b.push("01"),new Date(b.join("/"))},formatDate:function(a){return a.getDate()+" "+this.months[a.getMonth()]+" "+a.getFullYear()},updatePagination:function(a){this.currentPage=parseInt(a.urlParams.page,10);var b=parseInt(a.urlParams.per_page,10),c=parseInt(a.totalResults,10),d=b>0?Math.ceil(c/b):1,e=Math.max(this.currentPage-1,1),f=Math.min(this.currentPage+1,d);this.$prevPage.toggleClass("disabled",this.currentPage<=1),this.$nextPage.toggleClass("disabled",this.currentPage>=d),this.$prevPage.attr("data-page",e),this.$nextPage.attr("data-page",f),this.$prevPage.find(".prev-page").text(e),this.$nextPage.find(".next-page").text(f),this.$top.find(".total-pages").text(d)},getSegmentsFromUrl:function(){var a=window.location.href,b=a.substr(this.pageBase.length);return b=b.replace(/^\/|\/$/g,""),b.split("/")},updateUrl:function(){var a=[this.pageBase],b=this.getSanitizedKeywords();a.push(this.$typeInput.val()||"All"),b=b.replace(/\s/g,"+"),b=encodeURI(b),a.push(b||"-"),a.push(this.currentPage),history.pushState({},"",a.join("/")+"/")}}}(jQuery),function(a){"use strict";var b=window.App;b.StickyNews=function(){this.$top=a("#need-to-know"),this.$top.length&&this.init()},b.StickyNews.prototype={init:function(){this.cacheEls(),this.bindEvents(),this.showItem(1)},cacheEls:function(){this.$pages=this.$top.find(".need-to-know-list > li"),this.$pageLinks=this.$top.find(".page-list > li")},bindEvents:function(){this.$pageLinks.on("click",a.proxy(this.showItem,this,null))},showItem:function(b,c){b||(b=a(c.target).data("page-id")),this.$pages.hide(),this.$pageLinks.removeClass("selected"),this.$pages.filter('[data-page="'+b+'"]').show(),this.$pageLinks.filter('[data-page-id="'+b+'"]').addClass("selected")}}}(window.jQuery),function(a){"use strict";var b=window.App;b.TabbedContent=function(){this.$tabs=a(".content-tabs li"),this.$tabs.length&&this.init()},b.TabbedContent.prototype={init:function(){this.cacheEls(),this.bindEvents(),this.cacheTemplates(),this.$tabs.eq(0).click()},cacheEls:function(){this.$tabContent=a(".tab-content")},cacheTemplates:function(){var b=this;this.templates=[],a("template[data-template-type]").each(function(){var c=a(this);b.templates[c.attr("data-content-name")]=c.html()})},bindEvents:function(){this.$tabs.on("click",a.proxy(this.switchTab,this))},switchTab:function(c){var d=a(c.currentTarget),e=d.attr("data-content");this.$tabContent.html(this.templates[e]),this.$tabs.removeClass("current-menu-item"),d.addClass("current-menu-item"),c.preventDefault(),b.ins.tableOfContents.generate()}}}(jQuery),function(a){"use strict";var b=window.App;b.TableOfContents=function(){this.$tableOfContents=a(".table-of-contents"),this.$tableOfContents.length&&this.init()},b.TableOfContents.prototype={init:function(){this.cacheEls(),this.bindEvents(),this.generate(),this.initialized=!0},cacheEls:function(){this.$contentContainer=a(this.$tableOfContents.attr("data-content-selector"))},bindEvents:function(){},generate:function(){var b=this;this.initialized&&(this.$tableOfContents.empty(),this.$contentContainer.find("h2").each(function(){var c,d=a(this),e=a("<li><a></a></li>");d.filter("[id]").length||(c=d.text().toLowerCase(),c=c.replace(/[^A-Za-z0-9\s-]/g,""),c=c.replace(/[\s+]/g,"-"),d.attr("id",c)),e.find("a").text(d.text()).attr("href","#"+d.attr("id")),e.appendTo(b.$tableOfContents)}))}}}(jQuery),jQuery(function(){"use strict";var a=window.App;a.ins.mobileMenu=new a.MobileMenu,a.ins.stickyNews=new a.StickyNews,a.ins.guidanceAndSupport=new a.GuidanceAndSupport,a.ins.guidanceAndSupportContent=new a.GuidanceAndSupportContent,a.ins.azIndex=new a.AZIndex,a.ins.emergencyMessage=new a.EmergencyMessage,a.ins.tableOfContents=new a.TableOfContents,a.ins.tabbedContent=new a.TabbedContent,a.ins.news=new a.News,a.ins.searchResults=new a.SearchResults,a.ins.floaters=new a.Floaters});