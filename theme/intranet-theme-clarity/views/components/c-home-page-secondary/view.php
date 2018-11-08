<?php
use MOJ\Intranet\Agency;

/**
*
* This pulls in the components needed for the homepage sidebar.
* These componets are filtered on and off depending on agency via $activeAgency
* This ensures we can have a custom homepage sidebar for each agency.
*
*/
$oAgency = new Agency();
$activeAgency = $oAgency->getCurrentAgency();
$agency = $activeAgency['shortcode'];

?>
<!-- c-home-page-secondary starts here -->
<section class="c-home-page-secondary l-secondary" role="complementary">

<?php

  // MyMoJ
  get_template_part( 'src/components/c-my-moj/view' );

  get_template_part( 'src/components/c-polls/view');

  // Side banner
  get_template_part( 'src/components/c-sidebar-banner/view' );

  // MyWork
  if ( $agency == 'hmcts' ) {
    get_template_part( 'src/components/c-my-work/view');
  }

  // Quicklinks
  get_template_part( 'src/components/c-quick-links/view', 'home' );

  // Blog feed
  if ( $agency !== 'judicial-office' ) {
    get_template_part( 'src/components/c-blog-feed/view', 'home' );
  }

  // Social links
  if ( $agency !== 'cica' ) {
    get_template_part( 'src/components/c-social-links/view' );
  }
?>

</section>
<!-- c-home-page-secondary ends here -->
