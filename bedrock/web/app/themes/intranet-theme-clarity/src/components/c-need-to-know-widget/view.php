<?php

use MOJ\Intranet\NeedToKnow;

$config = array (
        'agency' => get_intranet_code(),
);
$oNeedToKnow = new NeedToKnow();
$needToKnowSlides = $oNeedToKnow->getNeedToKnow($config);

if (!empty($needToKnowSlides)) {
?>
    <section class="c-need-to-know-widget js-need-to-know-widget" role="marquee">
<?php foreach ($needToKnowSlides as $slide) { ?>
        <div class="c-need-to-know-widget__slide js-slide">
            <a href="<?php echo $slide['url'];?>">
                <img src="<?php echo $slide['image_url'];?>" alt="<?php echo $slide['image_alt'];?>">
                <div>
                    <p>
                        <?php echo $slide['title'];?>
                    </p>
                </div>
            </a>
        </div>
<?php } ?>
    </section>
<?php
}

