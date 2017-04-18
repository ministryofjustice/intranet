<?php
    // Mock data, the proper version will come from WP and contain a link for each item as well
    $app_array = ['People finder', 'Travel booking', 'Jobs', 'Pensions', 'SOP', 'Civil Service Learning', 'IT Portal', 'MoJ Webchat', 'Room Booking'];
?>
<ul class="c-app-list">
    <?php
        foreach ($app_array as $key => $value) {
            echo '<li><a class="u-icon u-icon--'.slugify($value).'" href=""><span>'.$value.'</span></a></li>';
        }
    ?>
</ul>
