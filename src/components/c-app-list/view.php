<?php
use MOJ\Intranet\MyMOJ;

$app_array = MyMOJ::get_apps(get_intranet_code());
?>
<ul class="c-app-list">
    <?php
        foreach ($app_array as $app ) {
            echo '<li><a class="u-icon u-icon--'.$app['icon'].'" href="'.$app['url'].'" title="'.$app['title'].'"><span>'.$app['title'].'</span></a></li>';
        }
    ?>
</ul>
