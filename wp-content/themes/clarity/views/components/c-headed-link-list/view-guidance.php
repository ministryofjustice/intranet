<?php
use MOJ\Intranet\GuidanceAndForms;

$oGuidance = new GuidanceAndForms();
$guidance_pages = $oGuidance->get_guidance_and_forms_pages();

if ( !empty( $guidance_pages ) ) {

foreach ( $guidance_pages as $page ): ?>

    <!-- c-headed-link-list-guidance-and-forms starts here -->
    <section class="c-headed-link-list c-headed-link-list--guidance">
      <h1><a href="<?php echo get_permalink( $page->ID ); ?>"><?php echo $page->post_title; ?></a></h1>
    </section>
    <!-- c-headed-link-list-guidance-and-forms ends here -->

<?php endforeach;

} else { ?>

  <!-- c-headed-link-list-guidance-and-forms starts here -->
  <section class="c-headed-link-list c-headed-link-list--guidance">
    <p>There are currently no child pages available to list. Please edit the pages you want listed here as childen to this parent page.</p>
  </section>
  <!-- c-headed-link-list-guidance-and-forms ends here -->

<?php
}
?>
