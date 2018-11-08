<header class="c-header-container" role="banner">

  <?php
    get_template_part('src/components/c-logo-bar/view');

    if (!is_search()):
      get_template_part('src/components/c-search-bar/view');
    endif;

    get_template_part('src/components/c-main-nav-bar/view');
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
