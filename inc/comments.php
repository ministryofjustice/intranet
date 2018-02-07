<?php
function convert_to_time_ago( $date, $d, $comment ) {

    return human_time_diff( get_comment_time( 'U' ), current_time('timestamp') ) . ' ago';

}
add_filter( 'get_comment_date', 'convert_to_time_ago', 10,3 );

function format_comment($comment, $args, $depth) {

       $GLOBALS['comment'] = $comment; ?>

        <li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">

            <div id="div-comment-8741" class="comment-body">
				<div class="comment-author vcard">
                    <cite class="fn">
                        <span class="author"><?php printf(__('%s'), get_comment_author_link()) ?></span>
                        <span class="dash">—</span>
                        <span class="date"><?php printf(__('%1$s'), get_comment_date(), get_comment_time()) ?></span>
                    </cite>
                    <?php comment_text(); ?>

                    <div class="reply">
                        <?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
                    </div>
			</div>

        <?php
}

function format_comment_closed($comment, $args, $depth)
{
   $GLOBALS['comment'] = $comment; ?>
    <li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">

    <div id="div-comment-8741" class="comment-body">
		<div class="comment-author vcard">
        <cite class="fn">
            <span class="author"><?php printf(__('%s'), get_comment_author_link()) ?></span>
            <span class="dash">—</span>
            <span class="date"><?php printf(__('%1$s'), get_comment_date(), get_comment_time()) ?></span>
        </cite>
        <?php comment_text(); ?>
        <p>Comments are now closed</p>
	  </div>
    <?php
}
