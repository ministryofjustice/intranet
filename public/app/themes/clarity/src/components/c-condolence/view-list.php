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
<article class="c-condolence-list-item<?= $workplace_class ?>">

    <?php if (!empty($thumbnail)) { ?>
        <div class="c-condolence-list-item__photo-wrapper">
            <a href="<?= get_permalink() ?>"><img class="c-condolence-list-item__photo""
                src="<?= $thumbnail[0] ?>"
                alt="Photo of <?= get_the_title() ?>"></a>
        </div>
    <?php } ?>

    <div class="c-condolence-list-item__details">
        <h2><a href="<?= get_permalink() ?>"><?= get_the_title() ?></a></h2>
        <?php if (!empty($job_title)) { ?>
            <p class="c-condolence-list-item__job-title"><?= $job_title ?></p>
        <?php } ?>
        <?php if (!empty($workplace)) { ?>
            <p class="c-condolence-list-item__workplace"><?= $workplace ?></p>
        <?php } ?>
        <div class="c-condolence-list-item__excerpt">
            <?php the_excerpt(); ?>
        </div>
    </div>
</article>
