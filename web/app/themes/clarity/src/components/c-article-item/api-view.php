<article class="c-article-item js-article-item">
    <h1>
        <a href="<?= $post->link ?>"><?= $post->title->rendered ?></a>
    </h1>
    <div class="meta">
        <span class="c-article-item__dateline"><?php echo get_the_time('j M Y', $id); ?> by <?php echo $authors[0]['name']; ?></span>
    </div>
    <div class="c-article-excerpt">
        <p><?php the_excerpt(); ?></p>
    </div>
    <span class="c-article-item__byline"><?= $authors[0]['name'] ?></span>
</article>
