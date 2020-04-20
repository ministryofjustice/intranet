<?php
/** Condolence - List View **/

$job_title = get_field('job_title');

$workplace_terms = get_the_terms(get_the_ID(), 'workplace');
$workplace = '';
$workplace_class = '';
if (!empty($workplace_terms)) {

    $count = 0;
    foreach ($workplace_terms as $workplace_term) {

        if ($count > 0) {
            $workplace .= ", ";
        }

        $workplace .= $workplace_term->name;
        $count++;

        $workplace_class .= " agency-" . $workplace_term->term_id;

    }
}

$thumbnail_id = get_post_thumbnail_id();
$thumbnail = '';
if (!empty($thumbnail_id)) {
    $thumbnail = wp_get_attachment_image_src($thumbnail_id, 'square-feature');
}
?>
<article class="c-condolence-list-item<?php echo $workplace_class; ?>">

    <?php if (!empty($thumbnail)) { ?>
        <div class="c-condolence-list-item__photo-wrapper">
            <a href="<?php echo get_permalink(); ?>"><img class="c-condolence-list-item__photo""
                src="<?php echo $thumbnail[0]; ?>"
                alt="Photo of <?php echo get_the_title(); ?>"></a>
        </div>
    <?php } ?>

    <div class="c-condolence-list-item__details">
        <h2><a href="<?php echo get_permalink(); ?>"><?php echo get_the_title(); ?></a></h2>
        <?php if (!empty($job_title)) { ?>
            <p><?php echo $job_title; ?></p>
        <?php } ?>
        <div class="c-condolence-list-item__excerpt">
            <?php the_excerpt(); ?>
        </div>
    </div>
</article>
