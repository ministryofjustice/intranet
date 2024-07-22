<?php
/** Condolence - Single view **/

$job_title = get_field('job_title');

$workplace_terms = get_the_terms(get_the_ID(), 'workplace');
$workplace = '';
if (!empty($workplace_terms)) {

    $count = 0;
    foreach ($workplace_terms as $workplace_term) {

        if ($count > 0) {
            $workplace .= ", ";
        }

        $workplace .= $workplace_term->name;
        $count++;

    }
}

$thumbnail_id = get_post_thumbnail_id();
$thumbnail = '';
if (!empty($thumbnail_id)) {
    $thumbnail = wp_get_attachment_image_src($thumbnail_id, 'square-feature');
}
?>

<!-- c-condolence starts here -->
<article class="c-condolence">


    <div class="c-condolence-header">
        <div class="l-primary">
            <p class="c-condolence-header__intro">In Memory of</p>
            <?php if (!empty($thumbnail)) { ?>
                <img class="c-condolence-header__photo"" src="<?= $thumbnail[0] ?>"
                alt="Photo of <?= get_the_title() ?>">
            <?php } ?>
            <div class="c-condolence-header__details">
                <h1 class="o-title o-title--page"><?= get_the_title() ?></h1>
                <?php if (!empty($job_title)) { ?>
                    <p class="c-condolence-header__job-title"><?= $job_title ?></p>
                <?php } ?>
                <?php if (!empty($workplace)) { ?>
                    <p class="c-condolence-header__workplace"><?= $workplace ?></p>
                <?php } ?>
                <?php the_excerpt(); ?>
            </div>
        </div>
    </div>
    <div class="l-primary">
        <?php
        get_template_part('src/components/c-rich-text-block/view');
        ?>
    </div>


</article>
<!-- c-condolence ends here -->
