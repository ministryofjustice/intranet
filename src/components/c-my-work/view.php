<?php
use MOJ\Intranet\MyMOJ;

$quicklink_array = MyMOJ::get_quick_links(get_intranet_code());

if (is_array($quicklink_array)) {
?>

<section class="c-my-work">
  <h1 class="o-title o-title--section">My Work</h1>
  <ul>
    <?php
    	foreach ($quicklink_array as $link) {
    		echo '<li><a href="'.$link['url'].'">'.$link['title'].'</a></li>';
    	}
    ?>
  </ul>
</section>
<?php }
