<!-- c-emergency-banner starts here -->
<section class="c-emergency-banner c-emergency-banner--<?php echo $data['type']; ?>">
  <div class="c-emergency-banner__meta">
    <h1><?php echo $data['title'];?></h1>
    <time datetime="2016-12-22"><?php echo $data['date'];?></time>
  </div>
  <div class="c-emergency-banner__content ie_content">
    <?php echo $data['message'];?>
  </div>
</section>
<!-- c-emergency-banner ends here -->