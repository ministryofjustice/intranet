<?php if (!defined('ABSPATH')) die(); ?>

<style>
.template-campaign-content .template-container .editable h2 strong,
.template-campaign-content .template-container .editable h3 strong {
  color: <?=$campaign_colour?>;
}

.template-campaign-content .template-container .editable hr {
  display: inline-block;
  width: 100%;
  margin: 10px 0 0;
  border: 1px solid <?=$campaign_colour?>;
}

.main-content .editable .example {
  border-left-color: <?=$campaign_colour?>;
}
</style>
