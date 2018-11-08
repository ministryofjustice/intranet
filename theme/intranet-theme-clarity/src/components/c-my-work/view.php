<?php
use MOJ\Intranet\MyMOJ;

$mywork_array = MyMOJ::get_my_work_links(get_intranet_code());

if (is_array($mywork_array)) {
?>

<section class="c-my-work">
  <h1 class="o-title o-title--section">My Work</h1>
  <ul>
    <?php
    	foreach ($mywork_array as $link) {
    		echo '<li><a href="'.$link['url'].'">'.$link['title'].'</a></li>';
    	}
    ?>
  </ul>
</section>
<?php }
