jQuery(function($) {
  maxLinks = $('.quick_links-container').attr('data-max-links');
  // Add links
  $('.quick_links-container').on('click','.add-link',function(e){
    container = $(this).closest('div');
    table = container.find('table');
    // Get namespace
    namespace = container.attr('class').replace(/-container$/,'');
    // Check if max links reached
    totalLinks = $('.'+namespace+'-line').size();
    // if(totalLinks<maxLinks) {
      linkNumber = totalLinks + 1;
      // Add new link fields
      // Note: I added tabindex='-1' to Delete links to remove them from taborder to prevent accidental triggering
      $("\
        <tr class='" + namespace + "-line " + namespace + "-line[" +  linkNumber + "]'>\
          <!--<td>\
            <span class='dashicons dashicons-sort'></span>\
          </td>-->\
          <td>\
            <input class='" + namespace + "-link-text " + namespace + "-link-text" + linkNumber + " regular-text' id='" + namespace + "-link-text" + linkNumber + "' name='" + namespace + "-link-text" + linkNumber + "' type='text' placeholder='Link text'>\
          </td>\
          <td>\
            <input class='" + namespace + "-url " + namespace + "-url" + linkNumber + " regular-text' id='" + namespace + "-url" + linkNumber + "' name='" + namespace + "-url" + linkNumber + "' type='text' placeholder='Link URL'>\
          </td>\
          <td>\
            <input class='" + namespace + "-qlink " + namespace + "-qlink" + linkNumber + "' id='" + namespace + "-qlink" + linkNumber + "' name='" + namespace + "-qlink" + linkNumber + "' type='checkbox'>\
          </td>\
          <td>\
            <input class='" + namespace + "-firsttab " + namespace + "-firsttab" + linkNumber + "' id='" + namespace + "-firsttab" + linkNumber + "' name='" + namespace + "-firsttab" + linkNumber + "' type='checkbox'>\
          </td>\
          <td>\
            <input class='" + namespace + "-secondtab " + namespace + "-secondtab" + linkNumber + "' id='" + namespace + "-secondtab" + linkNumber + "' name='" + namespace + "-secondtab" + linkNumber + "' type='checkbox'>\
          </td>\
          <td>\
            <a href='#' class='hide-if-no-js delete-link' tabindex='-1'>Delete</a>\
          </td>\
        </tr>\
        ").insertBefore($(this).closest('tr'));
      // if (linkNumber==maxLinks) {
        // $(this).closest('tr').hide();
      // }
    // } else {
      // alert('No more than ' + maxLinks + ' quick links allowed');
    // }
    e.preventDefault();
  });

  // Limit quick links to 7
  $('.quick_links-container').on('change','.quick_links-qlink',function(e) {
    qlCount = $('.quick_links-qlink:checked').size();
    if(qlCount>maxLinks) {
      alert("No more than " + maxLinks + " Quick Links allowed");
      $(this).attr('checked',null);
    }
  });

  // Remove links
  $('.quick_links-container').on('click','.delete-link',function(e){
    container = $(this).closest('div');
    // Get namespace
    namespace = container.attr('class').replace(/-container$/,'');
    clickedLink = Number($(this).closest('tr').attr('class').match(/\[(.+)\]/)[1]);
    totalLinks = $('.'+namespace+'-line').size();
    // Cascade changes through remaining links
    for(var i=(clickedLink); i<=(totalLinks); i++) {
      oldText = $('input.' + namespace + '-link-text' + (i+1)).val();
      oldUrl = $('input.' + namespace + '-link-text' + (i+1)).val();
      $('input.' + namespace + '-link-text' + i).val(oldText);
      $('input.' + namespace + '-link-url' + i).val(oldUrl);
    }
    // Remove link
    // $(this).closest('tr').remove();
    $(this).closest('tr').fadeOut(250, function() {
      $('tr.quick_links-line').last().remove();
      $(this).show();
    });
    // Bring back Add Link if less than maxLinks
    if(totalLinks==maxLinks) {
      $('.quick_links-container .add-link').closest('tr').show();
    }
    e.preventDefault();
  });

  // Drag and drop
  $('.quick_links-container tr.draggable').draggable({
      helper: "clone",
      handle: ".dashicons.dashicons-sort"
  });

  // Tabs
  var gsTabs = $('#content_tabs .tabs').tabs();
  // Add tab
  $('#content_tabs').on('click','.add-tab',function(e){
    tabCount = gsTabs.find(".ui-tabs-nav li").size();
    tabCount++;
    gsTabs.find(".ui-tabs-nav").append("<li><a href='#tabs-" + tabCount + "'>Tab " + tabCount + "</a><a href='#' class='delete-tab'><span class='ui-icon ui-icon-close' role='presentation'>Remove Tab</span></a></li>");
    gsTabs.append("\
      <div id='tabs-" + tabCount + "'>\
        <table class='form-table'>\
          <tbody>\
            <tr class='form-field'>\
              <th>\
                <label>Tab Title</label>\
              </th>\
              <td>\
                <input class='regular-text tab-title' id='tab-" + tabCount + "-title' name='tab-" + tabCount + "-title' type='text'>\
              </td>\
            </tr>\
            <tr>\
              <th scope='row' valign='top' colspan='2'>\
                <a class='hide-if-no-js add-section' href='#'>+ Add Section</a>\
              </th>\
            </tr>\
            <tr>\
              <td colspan='2'>\
                <div class='accordion'>\
                  <h3>Section 1</h3>\
                  <div>\
                    <table>\
                      <tbody>\
                        <tr class='form-field'>\
                          <th>\
                            <label>Section Title</label>\
                          </th>\
                          <td>\
                            <input type='text' id='tab-" + tabCount + "-section-1-title' name='tab-" + tabCount + "-section-1-title'>\
                          </td>\
                        </tr>\
                        <tr class='form-field'>\
                          <td colspan='2'>\
                            <textarea id='tab-" + tabCount + "-section-1-content' name='tab-" + tabCount + "-section-1-content'></textarea>\
                          </td>\
                        </tr>\
                      </tbody>\
                    </table>\
                  </div>\
                </div>\
              </td>\
            </tr>\
          </tbody>\
        </table>\
      </div>\
    ");
    $('#tab-count').val(tabCount);
    $("#tab-" + tabCount + "-section-1-content").wp_editor();
    gsTabs.tabs("refresh");
    gsTabs.tabs({
      active: -1
    });
    $('#tabs-' + tabCount + ' .accordion').accordion({
      heightStyle: 'content'
    });
    e.preventDefault();
  });
  // Delete tabs
  $('#content_tabs').on('click','.delete-tab',function(e){
    var clickedLink = this;
    // parent = $(this).parent();
    // currentTab = $(parent).attr('aria-controls').match(/^tabs-(.+)/)[1];
    $("body").append("<div id='dialog-confirm-delete-tab' class='hidden' title='Confirm'><p>Are you sure you want to delete this tab?</p></div>");
    $("#dialog-confirm-delete-tab").dialog({
      autoOpen: true,
      draggable: false,
      height: 'auto',
      width: 'auto',
      modal: true,
      resizable: false,
      open: function(){
        jQuery('.ui-widget-overlay').bind('click',function(){
          jQuery('#ourID').dialog('close');
        })
      },
      buttons: {
        Cancel: function() {
          $(this).dialog("close");
        },
        "Delete": function() {
          var panelId = deletedTab = $( clickedLink ).closest( "li" ).fadeOut('slow',function(){$(this).remove();}).attr( "aria-controls" );
          var tabCount = Number($('#tab-count').attr('value'));
          var deletedTab = Number(panelId.match(/tabs-(\d+)/)[1]);
          $( "#" + panelId ).fadeOut('slow',function(){$(this).remove();});
          gsTabs.tabs( "refresh" );
          // Loop through remaining tabs and associated sections so tab n+1 becomes tab n
          for (i=deletedTab+1;i<=tabCount;i++) {
            var elements = ['id','name','class'];
            for (var j=0; j<=elements.length; j++) {
              el = elements[j];
              $("[" + el + "*=tab-"+i+"]").prop(el, function(index,id) {
                return id.replace(/(.*)tab-(\d+)(.*)/g,"$1tab-"+(i-1)+"$3");
              });
            }
          }
          $('#tab-count').attr('value',tabCount-1);
          $(this).dialog("close");
        }
      }
    });
    e.preventDefault();
  });

  // Change tab title on field edit


  // Sections
  $('.accordion').accordion({
    heightStyle: 'content'
  });
  // Add Section
  $('#content_tabs').on('click','.add-section',function(e){
    // Get tab number
    currentTab = $(this).closest('.ui-tabs-panel').attr('id').match(/^tabs-(.+)/)[1];
    sectionContainer = $(this).closest("table").find(".accordion");
    sectionCount = sectionContainer.find(".ui-accordion-header").size();
    sectionCount++;
    sectionContainer.append("\
      <h3>Section " + sectionCount + "</h3>\
      <div>\
        <table>\
          <tbody>\
            <tr class='form-field'>\
              <th>\
                <label>Section Title</label>\
              </th>\
              <td>\
                <input type='text' id='tab-" + currentTab + "-section-" + sectionCount + "-title' name='tab-" + currentTab + "-section-" + sectionCount + "-title'>\
              </td>\
            </tr>\
            <tr class='form-field'>\
              <td colspan='2'>\
                <textarea id='tab-" + currentTab + "-section-" + sectionCount + "-content' name='tab-" + currentTab + "-section-" + sectionCount + "-content'></textarea>\
              </td>\
            </tr>\
            <tr>\
              <td colspan='2'>\
                <a href='#' class='delete-section'>Delete this section<span class='ui-icon ui-icon-close' role='presentation'></span></a>\
              </td>\
            </tr>\
          </tbody>\
        </table>\
      </div>\
    ");
    $('#tab-' + currentTab + '-section-count').val(sectionCount);
    $("#tab-" + currentTab + "-section-" + sectionCount + "-content").wp_editor();
    sectionContainer.accordion("refresh");
    sectionContainer.accordion({
      active: -1
    });
    e.preventDefault();
  });
  // Delete Section
  $('#content_tabs').on('click','.delete-section',function(e){
    var parent = $(this).closest('div');
    var head = parent.prev('h3');
    var sectionContainer = $(parent).parent();
    var sectionCount = sectionContainer.find(".ui-accordion-header").size();
    var inputName = $(parent).find('input').attr('name');
    var sectionDetails = inputName.match(/tab-(\d+)-section-(\d+)-title/);
    var currentTab = Number(sectionDetails[1]);
    var deletedSection = Number(sectionDetails[2]);
    // Set up confirmation dialog
    $("body").append("<div id='dialog-confirm-delete-section' class='hidden' title='Confirm'><p>Are you sure you want to delete this section?</p></div>");
    $("#dialog-confirm-delete-section").dialog({
      autoOpen: true,
      draggable: false,
      height: 'auto',
      width: 'auto',
      modal: true,
      resizable: false,
      open: function(){
        jQuery('.ui-widget-overlay').bind('click',function(){
          jQuery('#ourID').dialog('close');
        })
      },
      buttons: {
        Cancel: function() {
          $(this).dialog("close");
        },
        "Delete": function() {
          parent.add(head).fadeOut('slow',function(){$(this).remove();});
          // Loop through remaining sections so section n+1 becomes section n
          for (i=deletedSection+1;i<=sectionCount;i++) {
            var elements = ['id','name','class'];
            for (var j=0; j<=elements.length; j++) {
              el = elements[j];
              $("[" + el + "*=tab-"+currentTab+"-section-"+i+"]").prop(el, function(index,id) {
                return id.replace(/(.*)tab-(\d+)-section-\d+(.*)/g,"$1tab-$2-section-"+(i-1)+"$3");
              });
            }
          }
          $('#tab-'+currentTab+'-section-count').attr('value',sectionCount-1);
          $(this).dialog("close");
        }
      }
    });
    e.preventDefault();
  });
});

function debugTabs() {
  jQuery(function($) {
    var gsTabs = $('#content_tabs .tabs .ui-tabs-panel');
    var tabCount = gsTabs.size();
    var logMessage = "TOTAL TABS = "+tabCount+"\n";
    for(var i=0; i<tabCount; i++) {
      var sectionCount = $("#content_tabs .tabs .ui-tabs-panel:eq(" + i + ") .ui-accordion-header").size();
      logMessage += "Tab " + i + " - " + sectionCount + " section(s)\n";
    }
    console.log(logMessage);
  });

  // return true;
}