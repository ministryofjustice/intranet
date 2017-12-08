<?php $component_path = 'src/components/'; ?>
<header class="c-header-container" role="banner">
  <?php
    get_template_part($component_path.'c-logo-bar/view');
    get_template_part($component_path.'c-search-bar/view');
    get_template_part($component_path.'c-main-nav-bar/view');
  ?>
  <!--[if lte IE 9]>
  <div class="u-message u-message--warning">
    You are using a very old browser that may impact your web browsing experience. It is recommended you switch to use Firefox or upgrade to a modern version of Internet Explorer.
  </div>
  <![endif]-->

  <!--[if IE 6]>
  <div class="u-message u-message--error">
    Internet Explorer 6 is not supported and you are likely to experience web page errors. Your web browsing experience will be greatly improved by updating to a modern browser.
  </div>
  <![endif]-->
</header>
