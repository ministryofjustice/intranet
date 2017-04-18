<?php
    // Mock data, the proper version will come from WP and contain a link for each item as well
    $quicklink_array = ['Buildings and facilities','Contact Shared Services','Ministers','Pay and benefits'];
?>

<section class="c-quick-links">
  <h1 class="o-title o-title--section">Quick Links</h1>
  <ul>
    <?php
    	foreach ($quicklink_array as $key => $value) {
    		echo '<li><a href="">'.$value.'</a></li>';
    	}
    ?>
  </ul>
</section>
