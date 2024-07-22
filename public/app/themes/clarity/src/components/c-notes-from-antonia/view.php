<!-- c-note-from-antonia starts here -->
<div class="c-notes-from-antonia">
    <div class="content">
        <?php
        $show_title = get_field('display_title_notes_from_antonia', $post->ID);
        if ($show_title === '1' || $show_title =='yes') {
            echo '<p><strong>' . get_gmt_from_date($post->post_date, 'l j F Y') . ' &ndash; ' . $post->post_title . '</strong></p>';
        }

        do_action('before_note_from_antonia', $post->ID);
        ?>

        <div class="c-article-content">
            <p><?= apply_filters('the_content', $post->post_content) ?></p>
        </div>

        <?php
        if (current_user_can('edit_post', $post->ID)) {
            echo '<a href="' . get_edit_post_link($post->ID) .'" class="button" title="Click to edit ' . $post->post_title . '">Edit Note</a><br>';
        }
        ?>
    </div>
</div>
<!-- c-note-from-antonia ends here -->
