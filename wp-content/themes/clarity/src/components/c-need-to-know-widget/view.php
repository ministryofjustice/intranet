<?php

use MOJ\Intranet\Agency;
$agency = get_intranet_code();

for ($i = 1; $i <= 3; $i++) {
  $slideArray[] = array(
    'headline'  => get_field($agency.'_homepage_slide_headline_'.$i, 'option'),
    'url'       => get_field($agency.'_homepage_slide_url_'.$i, 'option'),
    'image'     => get_field($agency.'_homepage_slide_image_'.$i, 'option'),
    'alt_text'  => get_field($agency.'_homepage_slide_alt_text_'.$i, 'option'),
  );
}

if(!empty( $slideArray[0]['headline']) || !empty( $slideArray[1]['headline']) || !empty( $slideArray[2]['headline']) ) {
?>
  <section class="c-need-to-know-widget js-need-to-know-widget" role="marquee">
    <?php
    foreach ($slideArray as $slides => $values) {
      ?>
      <div class="c-need-to-know-widget__slide js-slide">
        <a href="<?php echo $values['url'];?>" class="news-carousel">
          <img src="<?php echo $values['image'];?>" alt="<?php echo $slides['alt_text'];?>">
          <div>
              <p>
                <?php echo $values['headline'];?>
              </p>
          </div>
        </a>
      </div>
      <?php
    }
    ?>
  </section>
<?php
}
