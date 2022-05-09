<?php
use MOJ\Intranet\Agency;

/**
 * Template for displaying the header search form
 *
 * @package WordPress
 * @subpackage Clarity
 * @since 1.0
 * @version 1.0
 */
 $oAgency = new Agency();
 $activeAgency = $oAgency->getCurrentAgency();
?>
<section class="c-search-bar">
  <div class="u-wrapper">
    <div class="l-half-section">
      <form role="search" method="GET" action="/" id="searchform" class="u-wrapper newclass">
       <div class="c-search-bar__container">
         <label for="s" class="u-visually-hidden">Search intranet</label>
         <input type="search" name="s" placeholder="Search <?php echo $activeAgency["abbreviation"]; ?>" id="s" value="<?php echo get_search_query(); ?>" />
         <button type="submit" class="u-icon u-icon--search" aria-label="Search"><span>Search</span></button>
       </div>
      </form>
    </div>
    <div class="l-half-section">
        <?php get_template_part('src/components/c-external-services/view') ?>
    </div>
  </div>

</section>
