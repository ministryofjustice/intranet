<?php
/*
 * //LEGACY: Post information being sent here is dependant on the old MoJ theme= id= name.
 * This needs to be looked at in the future.
 */
use MOJ\Intranet\Agency;
$oAgency = new Agency();
$activeAgency = $oAgency->getCurrentAgency();
?>
<section class="c-search-bar">
  <form role="search" name="search-form" action="<?php echo site_url('search')?>" id="sf" class="u-wrapper" method="post">
    <div class="c-search-bar__container">
      <label for="sf_search" class="u-visually-hidden">Search <?php echo $activeAgency['abbreviation']; ?> Intranet</label>
      <input type="text" name="s" id="s" value="<?=htmlentities(urldecode(get_query_var('search-string')))?>" placeholder="Search <?php echo $activeAgency['abbreviation']; ?> Intranet">
      <button type="submit" class="u-icon u-icon--search" aria-label="Go"><span>Go</span></button>
    </div>
  </form>
</section>
