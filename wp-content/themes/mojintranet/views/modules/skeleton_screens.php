<?php if (!defined('ABSPATH')) die(); ?>

<?php
/**
 *
 */
?>

<script data-name="skeleton-screen-standard" type="text/x-partial-template">
  <li class="skeleton-screen">
    <article class="skeleton-screen">
      <div class="skeleton-img-box">
        <img class="skeleton-img shimmer" src="<?=get_template_directory_uri()?>/assets/images/skeleton-image.gif" alt="skeleton-placeholder" />
      </div>
      <div class="skeleton-content-box">
        <p>
          <span class="skeleton-copy skeleton-copy-long shimmer"></span>
          <span class="skeleton-copy skeleton-copy-medium shimmer"></span>
        </p>
        <p>
          <span class="skeleton-copy skeleton-copy-long shimmer"></span>
        </p>
        <p>
          <span class="skeleton-copy skeleton-copy-short shimmer"></span>
        </p>
      </div>
    </article>
  </li>
</script>

<script data-name="skeleton-screen-featured" type="text/x-partial-template">
  <li class="skeleton-screen">
    <article class="skeleton-screen">
      <img class="skeleton-img-full shimmer" src="<?=get_template_directory_uri()?>/assets/images/skeleton-image.gif" alt="skeleton-placeholder" />
      <div class="skeleton-content-box">
        <p>
          <span class="skeleton-copy skeleton-copy-long shimmer"></span>
          <span class="skeleton-copy skeleton-copy-medium shimmer"></span>
        </p>
        <p>
          <span class="skeleton-copy skeleton-copy-long shimmer"></span>
          <span class="skeleton-copy skeleton-copy-long shimmer"></span>
          <span class="skeleton-copy skeleton-copy-short shimmer"></span>
        </p>
        <p>
          <span class="skeleton-copy skeleton-copy-short shimmer"></span>
        </p>
      </div>
    </article>
  </li>
</script>

<script data-name="skeleton-screen-one-liner" type="text/x-partial-template">
  <li class="skeleton-screen">
    <article class="skeleton-screen">
      <div class="skeleton-content-box">
        <p>
          <span class="skeleton-copy skeleton-copy-medium shimmer" data-size="20:80"></span>
        </p>
      </div>
    </article>
  </li>
</script>
