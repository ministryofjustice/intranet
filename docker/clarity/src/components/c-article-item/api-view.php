<article class="c-article-item js-article-item">
    <h1>
        <a href="<?php echo $post->link ?>"><?php echo $post->title->rendered?></a>
    </h1>
    <div class="meta">
        <span class="c-article-item__dateline"><?php echo get_the_time('j M Y', $id);?> by <?php echo $authors[0]['name'];?></span>
    </div>
    <div class="c-article-exceprt">
        <p><?php the_excerpt();?></p>
    </div>
    <span class="c-article-item__byline"><?php echo $authors[0]['name'];?></span>
</article>