<!-- c-dynamic-style starts here -->
<?php
    $campaign_colour = get_field('dw_campaign_colour');
?>
<style>
.page-template-page_campaign .l-main .c-rich-text-block h2 strong,
.page-template-page_campaign .l-main .c-rich-text-block h3 strong,
.page-template-page_campaign .l-main .c-rich-text-block h4 strong,
.page-template-page_campaign .l-main .c-rich-text-block h5 strong,
.page-template-page_campaign .l-main .c-rich-text-block h6 strong {
  color: <?=$campaign_colour?>;
}

.page-template-page_campaign .l-main .c-rich-text-block hr {
  display: inline-block;
  width: 100%;
  margin: 10px 0 0;
  border: 1px solid <?=$campaign_colour?>;
}

.l-main .c-rich-text-block .example {
  border-left-color: <?=$campaign_colour?>;
}
</style>
<!-- c-dynamic-style ends here -->