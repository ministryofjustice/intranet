<?php if (!defined('ABSPATH')) die(); ?>

<?php
/** Skeleton screen partials
 *
 * Description
 * #############################################################################
 * Each template element below represents one skeleton screen partial.
 * Skeleton screens will be automatically added to elements that have a data-skeleton-screen-count attribute.
 * Here's the full list of attributes to configure the skeleton screens for an element:
 * - data-skeleton-screen-count {Integer as string}
 *     If set, skeleton screens will be used for this element
 *     The value represents how many skeleton screens will be inserted
 * - data-skeleton-screen-type {String}
 *     Name of a skeleton screen to use. Must be a valid skeleton screen defined in this file
 * - data-skeleton-screen-classes {String}
 *     CSS classes that will be added to the top level element of the skeleton screen
 *     Useful if the skeleton screen has to use grid settings
 *
 * Note: Remember to make sure that the partial's top element will work in a given context, e.g.
 * some partial's top elements are <li> elements so they are intended to be used with list elements.
 *
 *
 * Creating skeleton screens
 * #############################################################################
 * Skeleton screens must be defined as partials using the following format:
 *   <script data-name="skeleton-screen" data-type="{unique name-of-the-screen}" type="text/x-partial-template"></script>
 *
 * It might be useful to ge familiar with the _skeleton_screen.scss file to see what is supported out of the box
 *
 * You can add a placeholder text by using:
 *   <span class="skeleton-copy"></span>
 *
 * If you want the shimmer effect, add "shimmer" class:
 *    <span class="skeleton-copy shimmer"></span>
 *
 * You can use a special property "data-size" to define the width of the element:
 *    <span class="skeleton-copy shimmer" data-size="50"></span>
 *    This will make the copy 50% wide
 *
 * You can also specify a range from which a random width will be selected:
 *    <span class="skeleton-copy shimmer" data-size="20:80"></span>
 *    This will make the copy between 20% and 80% wide
 *
 * You can add a placeholder image by using:
 *   <img class="skeleton-img" src="<?=get_template_directory_uri()?>/assets/images/skeleton-image.gif" alt="skeleton-placeholder" />
 *   This will add an image in a 3:2 ratio (like all thumbnails used on the site)
 *   You can also add the shimmer effect to it just like for placeholder text
 *   and you can customise its width by using the data-size property.
 *
 *
 * Initialising skeleton screens on an element on the page:
 * #############################################################################
 * <ul class="my-list"
 *    data-skeleton-screen-count="5"
 *    data-skeleton-screen-type="standard"></ul>
 *
 *    The above example will add 5 "standard" skeleton screens to the my-list element.
 *
 * Remember to remove the skeleton screens once your content is loaded.
 * Use the following JS code to achieve this:
 *    App.ins.SkeletonScreens.remove($myList);
 *
 */
?>

<?php
/** Standard partial
 * Used for elements similar to news list, posts list etc.
 */
?>
<script data-name="skeleton-screen" data-type="standard" type="text/x-partial-template">
  <li class="skeleton-screen">
    <article class="skeleton-screen">
      <div class="skeleton-img-box">
        <img class="skeleton-img shimmer" src="<?=get_template_directory_uri()?>/assets/images/skeleton-image.gif" alt="skeleton-placeholder" />
      </div>
      <div class="skeleton-content-box">
        <p>
          <span class="skeleton-copy shimmer" data-size="100"></span>
          <span class="skeleton-copy shimmer" data-size="20:80"></span>
        </p>
        <p>
          <span class="skeleton-copy shimmer" data-size="30"></span>
        </p>
        <p>
          <span class="skeleton-copy shimmer" data-size="30"></span>
        </p>
      </div>
    </article>
  </li>
</script>

<script data-name="skeleton-screen" data-type="featured" type="text/x-partial-template">
  <li class="skeleton-screen">
    <article class="skeleton-screen">
      <img class="skeleton-img-full shimmer" src="<?=get_template_directory_uri()?>/assets/images/skeleton-image.gif" alt="skeleton-placeholder" />
      <div class="skeleton-content-box">
        <p>
          <span class="skeleton-copy shimmer" data-size="100"></span>
          <span class="skeleton-copy shimmer" data-size="20:80"></span>
        </p>
        <p>
          <span class="skeleton-copy shimmer" data-size="100"></span>
          <span class="skeleton-copy shimmer" data-size="100"></span>
          <span class="skeleton-copy shimmer" data-size="20:80"></span>
        </p>
        <p>
          <span class="skeleton-copy skeleton-copy shimmer" data-size="30"></span>
        </p>
      </div>
    </article>
  </li>
</script>

<script data-name="skeleton-screen" data-type="one-liner" type="text/x-partial-template">
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

<script data-name="skeleton-screen" data-type="app" type="text/x-partial-template">
  <li class="skeleton-screen app">
    <article class="skeleton-screen">
      <div class="skeleton-img-box">
        <img class="skeleton-img shimmer" src="<?=get_template_directory_uri()?>/assets/images/skeleton-image.gif" alt="skeleton-placeholder" />
      </div>
      <div class="skeleton-content-box">
        <p>
          <span class="skeleton-copy skeleton-copy-medium shimmer" data-size="50:90"></span>
        </p>
      </div>
    </article>
  </li>
</script>
