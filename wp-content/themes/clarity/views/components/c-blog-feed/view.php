<?php
$blogpage_link = 14013;

use MOJ\Intranet\Posts;

$oPosts = new Posts();
$options = [
    'page' => 1,
    'per_page' => 5,
];

$postsList = $oPosts->getPosts($options, true);
if (!empty($postsList)) {
    ?>
    <section class="c-blog-feed">
        <h1 class="o-title o-title--section">Blog</h1>
        <div>
          <?php foreach ($postsList['results'] as $result) {
        get_component('c-article-item', $result, 'blog');
    } ?>
        </div>
        <a href="<?php the_permalink( $blogpage_link ) ?>" class="o-see-all-link">See all blogs</a>
        <br/>
    </section>
<?php
}
?>
