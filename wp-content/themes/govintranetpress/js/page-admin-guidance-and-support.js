jQuery(function($) {
  // Add links
  $('.quick_links-container').on('click','.add-link',function(e){
    container = $(this).closest('div');
    table = container.find('table');
    // Get namespace
    namespace = container.attr('class').replace(/-container$/,'');
    // Check if max links reached
    totalLinks = $('.'+namespace+'-line').size();
    if(totalLinks<5) {
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
            <input class='" + namespace + "-url " + namespace + "-url" + linkNumber + " regular-text' id='" + namespace + "-url" + linkNumber + "' name='" + namespace + "-url" + linkNumber + "' type='url' placeholder='Link URL'>\
          </td>\
          <td>\
            <a href='#' class='hide-if-no-js delete-link' tabindex='-1'>Delete</a>\
          </td>\
        </tr>\
        ").insertBefore($(this).closest('tr'));
      if (linkNumber==5) {
        $(this).closest('tr').hide();
      }
    } else {
      alert('No more than 5 quick links allowed');
    }
    e.preventDefault();
  })

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
    // Bring back Add Link if less than 5
    if(totalLinks=5) {
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
    gsTabs.find(".ui-tabs-nav").append("<li><a href='#tabs-" + tabCount + "'>Tab " + tabCount + "</a><span class='ui-icon ui-icon-close' role='presentation'>Remove Tab</span></li>");
    gsTabs.append("\
      <div id='tabs-" + tabCount + "'>\
        <table class='form-table'>\
          <tbody>\
            <tr class='form-field'>\
              <th>\
                <label>Tab Title</label>\
              </th>\
              <td>\
                <input class='regular-text tab-title' id='' name='' type='text'>\
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
                            <input type='text'>\
                          </td>\
                        </tr>\
                        <tr class='form-field'>\
                          <td colspan='2'>\
                            <textarea id='tab-" + tabCount + "-section-1-content'></textarea>\
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
    $('#tabs-' + tabCount + ' .accordion').accordion({
      heightStyle: 'content'
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
                <textarea id='tab-" + currentTab + "-section-" + sectionCount + "-content'></textarea>\
              </td>\
            </tr>\
          </tbody>\
        </table>\
      </div>\
    ");
    $('#tab-' + currentTab + '-section-count').val(sectionCount);
    $("#tab-" + currentTab + "-section-" + sectionCount + "-content").wp_editor();
    sectionContainer.accordion("refresh")
    sectionContainer.accordion({
      active: -1
    });
    e.preventDefault();
  });
});