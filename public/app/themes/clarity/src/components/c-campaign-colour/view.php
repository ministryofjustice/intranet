<!-- c-campaign-colour starts here -->
<?php
$campaign_colour = sanitize_hex_color(get_field('dw_campaign_colour')) ?: '#3c3c3b';
$excerpt         = get_field('dw_excerpt');
?>

<style>
h1,
.page-template-page_campaign_content .template-container h2,
.page-template-page_campaign_content .template-container h3,
.page-template-page_campaign_content .template-container h4,
.page-template-page_campaign_content .template-container h5,
.page-template-page_campaign_content .template-container h6 {
  color: <?= $campaign_colour ?>;
}

.campaign-banner {
  margin-top: 16px;
}

.page-template-page_campaign_content .template-container blockquote {
  border-left-color: <?= $campaign_colour ?>;
}

.page-template-page_campaign_content .template-container hr {
  display: inline-block;
  width: 100%;
  margin: 10px 0 0;
  border: 1px solid <?= $campaign_colour ?>;
}

.page-template-page_campaign_content .example {
  border-left-color: <?= $campaign_colour ?>;
}
</style>
<!-- c-campaign-colour ends here -->
