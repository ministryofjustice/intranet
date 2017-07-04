<?php
use MOJ\Intranet\MyMOJ;

$app_array = MyMOJ::get_apps(get_intranet_code());
?>
<ul class="c-app-list">
    <?php
        foreach ($app_array as $app ) {
            echo '<li><a href="'.$app['url'].'" title="'.$app['title'].'"><span class="u-icon u-icon--'.$app['icon'].'"></span><span class="c-app-list__app-name">'.$app['title'].'</span></a></li>';
        }
    ?>
</ul>
