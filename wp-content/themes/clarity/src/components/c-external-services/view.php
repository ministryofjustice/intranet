<?php
use MOJ\Intranet\Agency;
$agency = get_intranet_code();
?>

<!-- c-external-services starts here -->
<section class="c-external-services">
  <ul>
    <?php
    for ($i = 0; $i <= 10; $i++) {
      $external_services[] = array(
        'title'  => get_field($agency.'_external_services_title_'.$i, 'option'),
        'url'    => get_field($agency.'_external_services_link_'.$i, 'option'),
      );
      if(!empty($external_services[$i]['title'])){
        echo '<li><a href="'.$external_services[$i]['url'].'">'.$external_services[$i]['title'].'</a></li>';
      }
    }
    ?>
  </ul>
</section>
<!-- c-external-services ends here -->
