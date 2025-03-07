<?php
// Display post types beside search result title
$post_id   = get_the_id();
$post_type = get_post_type($post_id);

// Changes post type name 'post' so the name shows up as a 'blog'
$post_type_blog_filter = str_replace('post', 'blog', $post_type);
$post_type_blog_filter_display = ucwords($post_type_blog_filter);

if ($post_type_blog_filter === 'note-from-antonia') {
    list($note, $from, $antonia) = explode('-', $post_type_blog_filter);
    $post_type_blog_filter_display = ucfirst($note) . ' ' . $from . ' ' . ucfirst($antonia);
}

$terms = get_the_terms($post_id, 'agency');
$term_names = is_array($terms) ? array_map(fn($term) => $term->name, $terms) : [];
?>
<!-- c-search-result-item starts here -->
<section class="c-search-result-item">
  <h1 class="o-title o-title--subtitle">
      <a href="<?= get_permalink($post_id) ?>"><?php the_title(); ?></a>
      <span class="c-search-result-item__meta__itemtype">| <?= esc_attr_e($post_type_blog_filter_display) ?></span>
  </h1>
  <div class="c-search-result-item__meta">
    <span class="c-search-result-item__meta__date"><?= get_the_date('j F Y') . ', ' ?>
    <?= join(', ', $term_names) ?>
    </span>
  </div>
  <div class="c-search-result-item__description">
    <?php the_excerpt(); ?>
  </div>
</section>
<!-- c-search-result-item ends here -->
