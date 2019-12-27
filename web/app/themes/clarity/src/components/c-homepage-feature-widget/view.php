<?php

use MOJ\Intranet\Agency;

$agency = get_intranet_code();

$posttype_list_left = get_field($agency . '_post_type_list', 'option');
$agencies_list_left = get_field($agency . '_agency_list', 'option');

$posttype_list_right = get_field($agency . '_post_type_list_right', 'option');
$agencies_list_right = get_field($agency . '_agency_list_right', 'option');

$buildfeatleft  = $agency . '_feature_item_left_' . $posttype_list_left . '__' . $agencies_list_left;
$buildfeatright = $agency . '_feature_item_right_' . $posttype_list_right . '__' . $agencies_list_right;

$feature_left  = get_field($buildfeatleft, 'option');
$feature_right = get_field($buildfeatright, 'option');

$feature_array = [ $feature_left, $feature_right ];


if (! empty($feature_left) || ! empty($feature_right)) {
    ?>
    <!-- c-homepage-feature-widget ends here -->
    <section class="c-homepage-feature-widget">
    <?php
    foreach ($feature_array as $post) {
        get_template_part('src/components/c-article-item-feature/view');
    }
    ?>
    </section>
    <!-- c-homepage-feature-widget ends here -->
    <?php
}
?>
