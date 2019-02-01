<!-- c-campaign-colour starts here -->
<?php
$campaign_colour = get_field( 'dw_campaign_colour' );
$excerpt         = get_field( 'dw_excerpt' );
?>
<style>
.page-template-page_campaign_content .template-container h2 strong,
.page-template-page_campaign_content .template-container h3 strong,
.page-template-page_campaign_content .template-container h4 strong,
.page-template-page_campaign_content .template-container h5 strong,
.page-template-page_campaign_content .template-container h6 strong {
  color: <?php echo $campaign_colour; ?>;
}

.page-template-page_campaign_content .template-container hr {
  display: inline-block;
  width: 100%;
  margin: 10px 0 0;
  border: 1px solid <?php echo $campaign_colour; ?>;
}

.page-template-page_campaign_content .example {
  border-left-color: <?php echo $campaign_colour; ?>;
}
</style>
<!-- c-campaign-colour ends here -->
