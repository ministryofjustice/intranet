<form role="search" class="search-form" name="search-form" action="<?=site_url()?>/search-results/" method="post">
  <input class="keywords-field" type="text" placeholder="Search" name="s" id="s" value="<?=urldecode(get_query_var('search-string'))?>">
  <input class="search-btn cta" type="submit" value="Search" />
</form>
