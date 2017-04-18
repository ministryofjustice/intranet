<?php
use MOJ\Intranet\Agency;

$app_array = Agency::getApps(get_intranet_code());
?>
<ul class="c-app-list">
    <?php
        foreach ($app_array as $app ) {
            echo '<li><a class="u-icon u-icon--'.$app['icon'].'" href="'.$app['url'].'" title="'.$app['title'].'"><span>'.$app['title'].'</span></a></li>';
        }
    ?>
</ul>
