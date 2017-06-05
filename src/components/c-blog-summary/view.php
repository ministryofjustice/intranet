<?php
use MOJ\Intranet\Posts;

$oPosts = new Posts();

//Todo: Pass it as part of $data from the container
$options = array (
    'page' => 1,
    'per_page' => 5,
);

$postsList = $oPosts->getPosts($options, true);

if (!empty($postsList)) {
?>
    <section class="c-blog-summary">
        <h1 class="o-title o-title--section">Blog</h1>
        <?php foreach ($postsList['results'] as $result) {
            get_component('c-article-item', $result, 'blog');
        } ?>
    </section>
<?php
}
?>

