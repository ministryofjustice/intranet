<?php
use MOJ\Intranet\Events;

if (!defined('ABSPATH')) {
    die();
}

$oEvents = new Events();

$options = array(
    'page' => 1,
    'per_page' => 2,
);

$eventsList = $oEvents->getEvents($options);
$singleEvent = $eventsList[0];
?>
<!-- c-article events starts here -->
<article class="c-article">
    <h1 class="o-title o-title--page"><?php echo get_the_title();?></h1>
    <?php if (!empty($singleEvent)): ?>
    <div class="u-wrapper">
    <div class="c-events">
        <?php get_component('c-events-item', $singleEvent); ?>
    </div>
  </div>
    <?php endif ?>
    <?php get_template_part('src/components/c-rich-text-block/view'); ?>
    <?php get_template_part('src/components/c-share-post/view'); ?>
</article>
<!-- c-article events ends here -->
