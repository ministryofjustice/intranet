<form role="search" class="search-form" name="search-form" action="<?=site_url()?>/search" method="post">
  <input class="keywords-field" type="text" placeholder="Search" name="s" id="s" value="<?=htmlentities(urldecode(get_query_var('search-string')))?>" title="Search">
  <input class="search-btn cta" id="search-btn" type="submit" value="Search" />
</form>
