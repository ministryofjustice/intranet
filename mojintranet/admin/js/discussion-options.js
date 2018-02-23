jQuery(function($) {
    $("#comments_on").change(function() {
        if($(this).is(':checked')) {
            $('#comment_status').prop('checked', true);
            $(".comment_status_option").removeClass('status_hidden');
        }
        else {
            $('#comment_status').prop('checked', false);
            $(".comment_status_option").addClass('status_hidden');
        }
    });
})
