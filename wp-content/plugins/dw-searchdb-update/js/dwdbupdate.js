jQuery(document).ready(function($) {
  $('#dwdbupdate-optimise').on('click',function(e) {
    $("#dwdbupdate-feedback").html("<h3>Optimisation started...</h3>");

    $.ajax({
      type: 'POST',
      url: ajaxurl,
      dataType: 'json',
      data: { action: "getgspages" },
      success: function(posts) {
        totalPosts = posts.length;
        $.each(posts, function(index, value) {
          $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: { action: "optimisedb", postId: value, totalPosts: totalPosts, currentPost: (index+1)},
            success: function(response) {
              $("#dwdbupdate-feedback").html(response);
            },
            error: function(response) {
              $("#dwdbupdate-feedback").html(response);
            }
          });
        });
      },
      error: function(response) {
        $("#dwdbupdate-feedback").html(response);
      },
      complete: function() {
        $.ajax({
          type: 'POST',
          url: ajaxurl,
          data: { action: "rebuildindex"},
          success: function(response) {
            $("#dwdbupdate-feedback").html(response);
          },
          error: function(response) {
            $("#dwdbupdate-feedback").html(response);
          }
        });
      }
    });
    e.preventDefault();
  });
});