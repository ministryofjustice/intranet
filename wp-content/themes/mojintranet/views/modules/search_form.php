<form role="search" class="search-form" name="search-form" action="<?=site_url()?>/search" method="post">
  <input class="keywords-field" type="text" placeholder="Search MoJ Intranet" name="s" id="s" value="<?=htmlentities(urldecode(get_query_var('search-string')))?>" title="Search">
  <!--[if IE 6 ]><img class="search-btn cta" id="search-btn" src="<?=get_template_directory_uri()?>/assets/images/search_icon_ie.png" alt="Search"/><![endif]-->
  <!--[if IE 7 ]><img class="search-btn cta" id="search-btn" src="<?=get_template_directory_uri()?>/assets/images/search_icon_ie.png" alt="Search"/><![endif]-->
  <!--[if (gt IE 7)|!(IE)]><!--><img class="search-btn cta" id="search-btn" src="<?=get_template_directory_uri()?>/assets/images/search_icon_x2.png" alt="Search"/><!--<![endif]-->
</form>
