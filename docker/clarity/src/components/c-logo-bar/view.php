<?php 
  use MOJ\Intranet\Agency;

  $oAgency = new Agency();
  $activeAgency = $oAgency->getCurrentAgency();

?>

<section class="c-logo-bar">
  <div class="u-wrapper">
    <a href="/" rel="home">
      <img src="<?php echo get_assets_folder() ?>/images/moj_logo.png" alt="<?php echo $activeAgency['label']; ?> Logo">
      <span class="agency-title"><?php echo $activeAgency['label']; ?></span>
    </a>
    <a href="/agency-switcher" class="c-logo-bar__switch">Switch to other intranet</a>
  </div>
</section>
