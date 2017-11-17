<?php 
    $args = array(
        'max_num_pages' => 5,
        'posts_per_page' => 5,
        'post_type' => 'post',
        'date_query' => array(
            array(
                'year'  => 2017,
                'month' => 05,
            ),
        ),
    );
    $query = new WP_Query( $args );
?>
    <section class="c-blog-feed">
        <h1 class="o-title o-title--section">Latest</h1>
        <div>
        <?php
            if ( $query->have_posts() ) {
                while ( $query->have_posts() ) {
            
                    $query->the_post();
                    
                    ?>
                    <article class="c-article-item js-article-item">
                        <img src="<?php echo $thumbnail_url;?>" alt="<?php echo $alt_text;?>">
                        <h1><a href="<?php echo get_the_permalink($id);?>"><?php echo get_the_title($id);?></a></h1>
                        <?php
                        // If the 'show_excerpt' value has been passed to $config: Display the excerpt.
                        if ($config === 'show_excerpt') { ?>
                            <?php get_component('c-article-excerpt'); ?>
                        <?php } ?>
                        <span class="c-article-item__dateline"><?php echo get_the_time('j M Y', $id);?></span>

                        <?php
                        // If the 'blog' value has been passed to $config: Display the byline.
                        if ($config === 'blog') {
                        ?>
                            <span class="c-article-item__byline"><?php echo $authors[0]['name'];?></span>
                        <?php } ?>
                    </article>
                    <?php
                    
                        //get_component('c-article-item','blog');
                  
            
                }
            
            }
        wp_reset_postdata();
        ?>
        </div>
    </section>
         

