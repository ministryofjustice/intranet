<?php
$post_id    = get_the_id();
$post_type  = get_post_type($post_id);
$terms      = get_the_terms($post_id, 'agency');
$term_names = is_array($terms) ? array_map(fn($term) => $term->name, $terms) : null;
?>
<!-- c-last-updated starts here -->
<section class="c-last-updated">

  <?php
  // Remove last reviewed from blog posts as it is not a content item that needs to be reviewed 
  if (! is_singular('post')) : ?>
    <p><span class="c-share-post__meta__date">Last reviewed: <?= the_modified_date('j F Y') ?></span></p>
  <?php endif; ?>

  <p>
    <span class="c-share-post__meta__date">Content tagged as: <?= join(', ', $term_names) ?></span>
  </p>
</section>
<!-- c-last-updated ends here -->
