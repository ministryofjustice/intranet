jQuery(function($) {
  $(".colour_check input").each(function() {
    checkContrast($(this));
  });

  $(".colour_check input").on('keyup change', function(e) {
        checkContrast($(this));
  });

  function checkContrast(input) {
      var colour = input.val();

      if (colour.length >= 6) {
          if(colour.charAt(0) != '#') {
              colour = '#' + colour;
          }

          if(/(^#[0-9A-F]{6}$)/i.test(colour)) {
              $.ajax({
                  url : params.ajax_url,
                  dataType: "json",
                  data : {
                    action : 'check_colour_contrast',
                    colour1 : '#ffffff',
                    colour2 : colour
                  },
                  success : function(data) {
                      if(data.success) {
                          if(data.contrast >= 500) {
                              input.parents('.acf-input').children('.contrast_invalid_message').hide();
                              input.parents('.acf-input').children('.contrast_valid_message').css("display", "inline-block");
                          }
                          else {
                              input.parents('.acf-input').children('.contrast_invalid_message').css("display", "inline-block");
                              input.parents('.acf-input').children('.contrast_valid_message').hide();
                          }
                      }
                      else {
                          hideContrastMessages(input);
                      }
                  }
              });
          }
          else {
              hideContrastMessages(input);
          }

      }
      else {
          hideContrastMessages(input);
      }

      function hideContrastMessages(input) {
          input.parents('.acf-input').children('.contrast_invalid_message').hide();
          input.parents('.acf-input').children('.contrast_valid_message').hide();
      }

  }

    
})
