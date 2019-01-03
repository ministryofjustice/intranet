<?php

use MOJ\Intranet\Agency;
$agency = get_intranet_code();

$posttype_list_left = get_field($agency . '_post_type_list', 'option');
$agencies_list_left = get_field($agency . '_agency_list', 'option');

$posttype_list_center = get_field($agency . '_post_type_list_center', 'option');
$agencies_list_center = get_field($agency . '_agency_list_center', 'option');

$posttype_list_right = get_field($agency . '_post_type_list_right', 'option');
$agencies_list_right = get_field($agency . '_agency_list_right', 'option');

$buildfeatleft = $agency . '_feature_item_left_'.$posttype_list_left.'__'. $agencies_list_left;
$buildfeatcenter = $agency . '_feature_item_center_'.$posttype_list_center.'__'. $agencies_list_center;
$buildfeatright = $agency . '_feature_item_right_'.$posttype_list_right.'__'. $agencies_list_right;

$feature_left = get_field($buildfeatleft, 'option');
$feature_center = get_field($buildfeatcenter, 'option');
$feature_right = get_field($buildfeatright, 'option');

$feature_array =  [$feature_left, $feature_center, $feature_right];


  if(!empty($feature_left) || !empty($feature_center) || !empty($feature_right)){
    ?>
    <section class="c-news-list">
      <?php
      foreach ($feature_array as $post) {
        get_template_part('src/components/c-article-item/view', 'date_excerpt');
      }
      ?>
    </section>
    <?php
  }
?>
