<?php
use MOJ\Intranet\GuidanceAndForms;

$oGuidance      = new GuidanceAndForms();
$guidance_pages = $oGuidance->getGuidanceAndFormsPages();

if (! empty($guidance_pages)) {
    foreach ($guidance_pages as $page) : ?>
    <!-- c-headed-link-list-guidance-and-forms starts here -->
    <section class="c-headed-link-list c-headed-link-list--guidance">
      <h2><a href="<?php echo get_permalink($page->ID); ?>"><?php echo $page->post_title; ?></a></h2>
    </section>
    <!-- c-headed-link-list-guidance-and-forms ends here -->

        <?php
    endforeach;
} else {
    ?>

  <!-- c-headed-link-list-guidance-and-forms starts here -->
  <section class="c-headed-link-list c-headed-link-list--guidance">
    <p>There are currently no child pages available to list. Please edit the pages you want listed here as childen to this parent page.</p>
  </section>
  <!-- c-headed-link-list-guidance-and-forms ends here -->

    <?php
}
?>
