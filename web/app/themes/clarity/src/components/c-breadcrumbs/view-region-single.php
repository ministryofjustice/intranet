<?php
// Get the correct region name
$post_id   = get_the_ID();
$region_id = get_the_terms($post_id, 'region');

// Loop through using the region id and get current region name
if ($region_id) :
    foreach ($region_id as $region) :
        $current_region = $region->name;
    endforeach;
endif;

$current_region_url_formated  = strtolower(preg_replace('#[ -]+#', '-', $current_region));
$current_region_name_formated = ucwords($current_region);
?>

<!-- c-breadcrumbs (view-region-single) starts here -->
<section class="c-breadcrumbs">
  <ol class="c-breadcrumbs__list">
    <li class="c-breadcrumbs__list-item">
      <a title="Go home" href="<?php echo get_home_url(); ?>" class="home">
        <span>Home</span>
      </a>
    </li> 
    <li class="c-breadcrumbs__list-item c-breadcrumbs__list-item--separated">
      <a href="/regional-pages/">
        <span>Regions</span>
      </a>
    </li>
    <li class="c-breadcrumbs__list-item c-breadcrumbs__list-item--separated">
      <a href="/regional-pages/<?php echo sanitize_text_field($current_region_url_formated); ?>">
        <span><?php echo sanitize_text_field($current_region_name_formated); ?></span>
      </a>
    </li>
    <li class="c-breadcrumbs__list-item c-breadcrumbs__list-item--separated">
      <span><?php the_title(); ?></span>
    </li>
  </ol>    
</section>
<!-- c-breadcrumbs (view-region-single) ends here -->
