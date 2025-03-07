<!-- c-breadcrumbs (view-blog) starts here -->
<section class="c-breadcrumbs">
  <ol class="c-breadcrumbs__list">
    <li class="c-breadcrumbs__list-item">
      <a title="Go to intranet home." href="<?= get_home_url() ?>" class="home">
        <span>Home</span>
      </a>
    </li> 
    <li class="c-breadcrumbs__list-item c-breadcrumbs__list-item--separated">
      <a href="/blog/">
        <span>Blog</span>
      </a>
    </li>
    <li class="c-breadcrumbs__list-item c-breadcrumbs__list-item--separated">    
      <span><?php the_title(); ?></span>
    </li>  
  </ol>
</section>
<!-- c-breadcrumbs (view-blog) ends here -->
