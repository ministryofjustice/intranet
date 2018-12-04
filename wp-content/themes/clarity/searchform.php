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
  <div class="split">
      <form role="search" method="GET" action="/" id="searchform" class="u-wrapper newclass">
       <div class="c-search-bar__container">
         <label for="s" class="u-visually-hidden">Search Intranet</label>
         <input type="search" name="s" placeholder="Search <?php echo $activeAgency['abbreviation']; ?> intranet" id="s" value="<?php echo get_search_query(); ?>" />
         <button type="submit" class="u-icon u-icon--search" aria-label="Go"><span>Go</span></button>
       </div>
      </form>
  </div>
  <div class="split">
    <h1>hello marker</h1>
  </div>


</section>
