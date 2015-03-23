=== Relevanssi Premium - A Better Search ===
Contributors: msaari
Donate link: http://www.relevanssi.com/
Tags: search, relevance, better search
Requires at least: 3.3
Tested up to: 4.2-alpha
Stable tag: 1.13.3

Relevanssi Premium replaces the default search with a partial-match search that sorts results by relevance. It also indexes comments and shortcode content.

== Description ==

Relevanssi replaces the standard WordPress search with a better search engine, with lots of features
and configurable options. You'll get better results, better presentation of results - your users
will thank you.

= Key features =
* Search results sorted in the order of relevance, not by date.
* Fuzzy matching: match partial words, if complete words don't match.
* Find documents matching either just one search term (OR query) or require all words to appear (AND query).
* Search for phrases with quotes, for example "search phrase".
* Create custom excerpts that show where the hit was made, with the search terms highlighted.
* Highlight search terms in the documents when user clicks through search results.
* Search comments, tags, categories and custom fields.

= Advanced features =
* Adjust the weighting for titles, tags and comments.
* Log queries, show most popular queries and recent queries with no hits.
* Restrict searches to categories and tags using a hidden variable or plugin settings.
* Index custom post types and custom taxonomies.
* Index the contents of shortcodes.
* Google-style "Did you mean?" suggestions based on successful user searches.
* Automatic support for [WPML multi-language plugin](http://wpml.org/).
* Automatic support for [s2member membership plugin](http://www.s2member.com/).
* Advanced filtering to help hacking the search results the way you want.
* Search result throttling to improve performance on large databases.
* Disable indexing of post content and post titles with a simple filter hook.

= Premium features (only in Relevanssi Premium) =
* Search result throttling to improve performance on large databases.
* Improved spelling correction in "Did you mean?" suggestions.
* WordPress Multisite support.
* Indexing and searching user profiles.
* Weights for post types, including custom post types.
* Limit searches with custom fields.
* Index internal links for the target document (sort of what Google does).
* Search using multiple taxonomies at the same time.

Relevanssi is available in two versions, regular and Premium. Regular Relevanssi is and will remain
free to download and use. Relevanssi Premium comes with a cost, but will get all the new features.
Standard Relevanssi will be updated to fix bugs, but new features will mostly appear in Premium.
Also, support for standard Relevanssi depends very much on my mood and available time. Premium
pricing includes support.

= Relevanssi in Facebook =
You can find [Relevanssi in Facebook](http://www.facebook.com/relevanssi).
Become a fan to follow the development of the plugin, I'll post updates on bugs, new features and
new versions to the Facebook page.

= Other search plugins =
Relevanssi owes a lot to [wpSearch](http://wordpress.org/extend/plugins/wpsearch/) by Kenny
Katzgrau. Relevanssi was built to replace wpSearch, when it started to fail.

Search Unleashed is a popular search plugin, but it hasn't been updated since 2010. Relevanssi
is in active development and does what Search Unleashed does.



== Installation ==

1. Extract all files from the ZIP file, and then upload the plugin's folder to /wp-content/plugins/.
1. If your blog is in English, skip to the next step. If your blog is in other language, rename the file *stopwords* in the plugin directory as something else or remove it. If there is *stopwords.yourlanguage*, rename it to *stopwords*.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Go to the plugin settings and build the index following the instructions there.

To update your installation, simply overwrite the old files with the new, activate the new
version and if the new version has changes in the indexing, rebuild the index.

= Note on updates =
If it seems the plugin doesn't work after an update, the first thing to try is deactivating and
reactivating the plugin. If there are changes in the database structure, those changes do not happen
without a deactivation, for some reason.

= Changes to templates =
None necessary! Relevanssi uses the standard search form and doesn't usually need any changes in
the search results template.

If the search does not bring any results, your theme probably has a query_posts() call in the
search results template. That throws Relevanssi off. For more information, see [The most
important Relevanssi debugging trick](http://www.relevanssi.com/knowledge-base/query_posts/).

= How to index =
Check the options to make sure they're to your liking, then click "Save indexing options and
build the index". If everything's fine, you'll see the Relevanssi options screen again with a 
message "Indexing successful!"

If something fails, usually the result is a blank screen. The most common problem is a timeout:
server ran out of time while indexing. The solution to that is simple: just return to Relevanssi
screen (do not just try to reload the blank page) and click "Continue indexing". Indexing will
continue. Most databases will get indexed in just few clicks of "Continue indexing". You can
follow the process in the "State of the Index": if the amount of documents is growing, the 
indexing is moving along.

If the indexing gets stuck, something's wrong. I've had trouble with some plugins, for example
Flowplayer video player stopped indexing. I had to disable the plugin, index and then activate
the plugin again. Try disabling plugins, especially those that use shortcodes, to see if that
helps. Relevanssi shows the highest post ID in the index - start troubleshooting from the post
or page with the next highest ID. Server error logs may be useful, too.

= Using custom search results =
If you want to use the custom search results, make sure your search results template uses `the_excerpt()`
to display the entries, because the plugin creates the custom snippet by replacing the post excerpt.

If you're using a plugin that affects excerpts (like Advanced Excerpt), you may run into some
problems. For those cases, I've included the function `relevanssi_the_excerpt()`, which you can
use instead of `the_excerpt()`. It prints out the excerpt, but doesn't apply `wp_trim_excerpt()`
filters (it does apply `the_content()`, `the_excerpt()`, and `get_the_excerpt()` filters).

To avoid trouble, use the function like this:

`<?php if (function_exists('relevanssi_the_excerpt')) { relevanssi_the_excerpt(); }; ?>`

See Frequently Asked Questions for more instructions on what you can do with
Relevanssi.

= The advanced hacker option =
If you're doing something unusual with your search and Relevanssi doesn't work, try
using `relevanssi_do_query()`. See [Knowledge Base](http://www.relevanssi.com/knowledge-base/relevanssi_do_query/).

= Uninstalling =
To uninstall the plugin remove the plugin using the normal WordPress plugin management tools
(from the Plugins page, first Deactivate, then Delete). If you remove the plugin files manually,
the database tables and options will remain.

= Combining with other plugins =
Relevanssi doesn't work with plugins that rely on standard WP search. Those plugins want to
access the MySQL queries, for example. That won't do with Relevanssi. [Search Light](http://wordpress.org/extend/plugins/search-light/),
for example, won't work with Relevanssi.

Some plugins cause problems when indexing documents. These are generally plugins that use shortcodes
to do something somewhat complicated. One such plugin is [MapPress Easy Google Maps](http://wordpress.org/extend/plugins/mappress-google-maps-for-wordpress/).
When indexing, you'll get a white screen. To fix the problem, disable either the offending plugin 
or shortcode expansion in Relevanssi while indexing. After indexing, you can activate the plugin
again.

== Frequently Asked Questions ==

= Where is the Relevanssi search box widget? =
There is no Relevanssi search box widget.

Just use the standard search box.

= Where are the user search logs? =
See the top of the admin menu. There's 'User searches'. There. If the logs are empty, please note
showing the results needs at least MySQL 5.

= Displaying the number of search results found =

The typical solution to showing the number of search results found does not work with Relevanssi.
However, there's a solution that's much easier: the number of search results is stored in a
variable within $wp_query. Just add the following code to your search results template:

`<?php echo 'Relevanssi found ' . $wp_query->found_posts . ' hits'; ?>`

= Advanced search result filtering =

If you want to add extra filters to the search results, you can add them using a hook.
Relevanssi searches for results in the _relevanssi table, where terms and post_ids are listed.
The various filtering methods work by listing either allowed or forbidden post ids in the
query WHERE clause. Using the `relevanssi_where` hook you can add your own restrictions to
the WHERE clause.

These restrictions must be in the general format of 
` AND doc IN (' . {a list of post ids, which could be a subquery} . ')`

For more details, see where the filter is applied in the `relevanssi_search()` function. This
is stricly an advanced hacker option for those people who're used to using filters and MySQL
WHERE clauses and it is possible to break the search results completely by doing something wrong
here.

There's another filter hook, `relevanssi_hits_filter`, which lets you modify the hits directly.
The filter passes an array, where index 0 gives the list of hits in the form of an array of 
post objects and index 1 has the search query as a string. The filter expects you to return an
array containing the array of post objects in index 0 (`return array($your_processed_hit_array)`).

= Direct access to query engine =
Relevanssi can't be used in any situation, because it checks the presence of search with
the `is_search()` function. This causes some unfortunate limitations and reduces the general usability
of the plugin.

You can now access the query engine directly. There's a new function `relevanssi_do_query()`,
which can be used to do search queries just about anywhere. The function takes a WP_Query object
as a parameter, so you need to store all the search parameters in the object (for example, put the
search terms in `$your_query_object->query_vars['s']`). Then just pass the WP_Query object to
Relevanssi with `relevanssi_do_query($your_wp_query_object);`.

Relevanssi will process the query and insert the found posts as `$your_query_object->posts`. The
query object is passed as reference and modified directly, so there's no return value. The posts
array will contain all results that are found.

= Sorting search results =
If you want something else than relevancy ranking, you can use orderby and order parameters. Orderby
accepts $post variable attributes and order can be "asc" or "desc". The most relevant attributes
here are most likely "post_date" and "comment_count".

If you want to give your users the ability to sort search results by date, you can just add a link
to http://www.yourblogdomain.com/?s=search-term&orderby=post_date&order=desc to your search result
page.

Order by relevance is either orderby=relevance or no orderby parameter at all.

= Filtering results by date =
You can specify date limits on searches with `by_date` search parameter. You can use it your
search result page like this: http://www.yourblogdomain.com/?s=search-term&by_date=1d to offer
your visitor the ability to restrict their search to certain time limit (see
[RAPLIQ](http://www.rapliq.org/) for a working example).

The date range is always back from the current date and time. Possible units are hour (h), day (d),
week (w), month (m) and year (y). So, to see only posts from past week, you could use by_date=7d
or by_date=1w.

Using wrong letters for units or impossible date ranges will lead to either defaulting to date
or no results at all, depending on case.

Thanks to Charles St-Pierre for the idea.

= Displaying the relevance score =
Relevanssi stores the relevance score it uses to sort results in the $post variable. Just add
something like

`echo $post->relevance_score`

to your search results template inside a PHP code block to display the relevance score.

= Did you mean? suggestions =
To use Google-style "did you mean?" suggestions, first enable search query logging. The
suggestions are based on logged queries, so without good base of logged queries, the
suggestions will be odd and not very useful.

To use the suggestions, add the following line to your search result template, preferably
before the have_posts() check:

`<?php if (function_exists('relevanssi_didyoumean')) { relevanssi_didyoumean(get_search_query(), "<p>Did you mean: ", "?</p>", 5); }?>`

The first parameter passes the search term, the second is the text before the result,
the third is the text after the result and the number is the amount of search results
necessary to not show suggestions. With the default value of 5, suggestions are not
shown if the search returns more than 5 hits.

= Search shortcode =
Relevanssi also adds a shortcode to help making links to search results. That way users
can easily find more information about a given subject from your blog. The syntax is
simple:

`[search]John Doe[/search]`

This will make the text John Doe a link to search results for John Doe. In case you
want to link to some other search term than the anchor text (necessary in languages
like Finnish), you can use:

`[search term="John Doe"]Mr. John Doe[/search]`

Now the search will be for John Doe, but the anchor says Mr. John Doe.

One more parameter: setting `[search phrase="on"]` will wrap the search term in
quotation marks, making it a phrase. This can be useful in some cases.

= Restricting searches to categories and tags =
Relevanssi supports the hidden input field `cat` to restrict searches to certain categories (or
tags, since those are pretty much the same). Just add a hidden input field named `cat` in your
search form and list the desired category or tag IDs in the `value` field - positive numbers
include those categories and tags, negative numbers exclude them.

This input field can only take one category or tag id (a restriction caused by WordPress, not
Relevanssi). If you need more, use `cats` and use a comma-separated list of category IDs.

You can also set the restriction from general plugin settings (and then override it in individual
search forms with the special field). This works with custom taxonomies as well, just replace `cat`
with the name of your taxonomy.

If you want to restrict the search to categories using a dropdown box on the search form, use
a code like this:

`<form method="get" action="<?php bloginfo('url'); ?>">
	<div><label class="screen-reader-text" for="s">Search</label>
	<input type="text" value="<?php the_search_query(); ?>" name="s" id="s" />
<?php
	wp_dropdown_categories(array('show_option_all' => 'All categories'));
?>
	<input type="submit" id="searchsubmit" value="Search" />
	</div>
</form>`

This produces a search form with a dropdown box for categories. Do note that this code won't work
when placed in a Text widget: either place it directly in the template or use a PHP widget plugin
to get a widget that can execute PHP code.

= Restricting searches with taxonomies =

You can use taxonomies to restrict search results to posts and pages tagged with a certain 
taxonomy term. If you have a custom taxonomy of "People" and want to search entries tagged
"John" in this taxonomy, just use `?s=keyword&people=John` in the URL. You should be able to use
an input field in the search form to do this, as well - just name the input field with the name
of the taxonomy you want to use.

It's also possible to do a dropdown for custom taxonomies, using the same function. Just adjust
the arguments like this:

`wp_dropdown_categories(array('show_option_all' => 'All people', 'name' => 'people', 'taxonomy' => 'people'));`

This would do a dropdown box for the "People" taxonomy. The 'name' must be the keyword used in
the URL, while 'taxonomy' has the name of the taxonomy.

= Automatic indexing =
Relevanssi indexes changes in documents as soon as they happen. However, changes in shortcoded
content won't be registered automatically. If you use lots of shortcodes and dynamic content, you
may want to add extra indexing. Here's how to do it:

`if (!wp_next_scheduled('relevanssi_build_index')) {
	wp_schedule_event( time(), 'daily', 'relevanssi_build_index' );
}`

Add the code above in your theme functions.php file so it gets executed. This will cause
WordPress to build the index once a day. This is an untested and unsupported feature that may
cause trouble and corrupt index if your database is large, so use at your own risk. This was
presented at [forum](http://wordpress.org/support/topic/plugin-relevanssi-a-better-search-relevanssi-chron-indexing?replies=2).

= Highlighting terms =
Relevanssi search term highlighting can be used outside search results. You can access the search
term highlighting function directly. This can be used for example to highlight search terms in
structured search result data that comes from custom fields and isn't normally highlighted by
Relevanssi.

Just pass the content you want highlighted through `relevanssi_highlight_terms()` function. The
content to highlight is the first parameter, the search query the second. The content with
highlights is then returned by the function. Use it like this:

`if (function_exists('relevanssi_highlight_terms')) {
    echo relevanssi_highlight_terms($content, get_search_query());
}
else { echo $content; }`

= Multisite searching =
To search multiple blogs in the same WordPress network, use the `searchblogs` argument. You can
add a hidden input field, for example. List the desired blog ids as the value. For example, 
searchblogs=1,2,3 would search blogs 1, 2, and 3. 

The features are very limited in the multiblog search, none of the advanced filtering works, and
there'll probably be fairly serious performance issues if searching common words from multiple
blogs.

= What is tf * idf weighing? =

It's the basic weighing scheme used in information retrieval. Tf stands for *term frequency*
while idf is *inverted document frequency*. Term frequency is simply the number of times the term
appears in a document, while document frequency is the number of documents in the database where
the term appears.

Thus, the weight of the word for a document increases the more often it appears in the document and
the less often it appears in other documents.

= What are stop words? =

Each document database is full of useless words. All the little words that appear in just about
every document are completely useless for information retrieval purposes. Basically, their
inverted document frequency is really low, so they never have much power in matching. Also,
removing those words helps to make the index smaller and searching faster.

== Known issues and To-do's ==
* Known issue: The most common cause of blank screens when indexing is the lack of the mbstring extension. Make sure it's installed.
* Known issue: In general, multiple Loops on the search page may cause surprising results. Please make sure the actual search results are the first loop.
* Known issue: Relevanssi doesn't necessarily play nice with plugins that modify the excerpt. If you're having problems, try using relevanssi_the_excerpt() instead of the_excerpt().
* Known issue: When a tag is removed, Relevanssi index isn't updated until the post is indexed again.

== Thanks ==
* Cristian Damm for tag indexing, comment indexing, post/page exclusion and general helpfulness.
* Marcus Dalgren for UTF-8 fixing.
* Warren Tape.
* Mohib Ebrahim for relentless bug hunting.
* John Blackbourn for amazing internal link feature and other fixes.

== Changelog ==

= 1.13.3 =
* Removes one "Undefined variable" error.
* New filter: `relevanssi_display_common_words` can be used to disable the "25 most common words" listing on the settings page, if it's too heavy to load.
* Eliminated problems where using the `relevanssi_do_not_index` filter caused error messages.
* Relevanssi was sanitizing taxonomy titles too aggressively. That is now toned down a bit.
* If Relevanssi creates an empty excerpt for a post and there's a user-set excerpt for the post, that excerpt is used.
* The `relevanssi_post_title_before_tokenize` filter now has a second parameter that contains the post object.
* No ellipsis is added to the post excerpt, if the post excerpt shows the whole post.
* Relevanssi now supports `post_parent`, `post_parent__in` and `post_parent__not_in`, though you have to set them in `relevanssi_modify_wp_query` filter for them to work.

= 1.13.2 =
* Fixed a bug that caused the results to change depending of the order of words in a multi-word search query.
* Added `product_categories` and `recent_products` from WooCommerce to the list of blocked shortcodes.
* There are improvements in excerpt-building and highlighting, especially when fuzzy search is enabled.
* Fixed a possible (if quite unlikely) XSS vulnerability.
* Improved search performance (thanks to MikeNGarrett).
* Sometimes highlights in documents make the document content disappear. I don't know why, but I've added a fix that should make the content visible (without the highlights) if a problem appears.

= 1.13.1 =
* Fixed a bug with numeric taxonomy terms.
* Fixed a bug in user search.
* API keys are now completely hidden on the Relevanssi settings page.
* `relevanssi_do_not_index` hook is moved a bit, so that when updating posts, posts that are not indexed because of the hook are now removed from the index.
* Pinning didn't work if the post wasn't otherwise found with the pinning term. Now pinning terms are also indexed to ensure that the posts can be found with them.

= 1.13 =
* New feature: You can now pin posts on particular search terms.
* New feature: Synonyms can now be defined in indexing, allowing them to be used with AND searches. (Thanks to Christoph Daum.)
* Relevanssi handles taxonomy terms in search better. The change requires a reindexing.
* Fix in indexing: Relevanssi will now bypass the global $post when indexing. This should help with problems with the Cookie Law Info plugin, for example.
* Tax query relation setting didn't work properly. It is now fixed.
* Word-based excerpt building sometimes created too short excerpts. That is now fixed.
* Synonyms are now highlighted.
* Phrase matching had issues where searching for a too common phrase crashed the search. That has been fixed.
* LIKE operator didn't work properly in meta_queries.
* API key field in settings is now a password field so clients and users can't see the API key.
* Relevanssi created lots of unnecessary post_meta rows and didn't clean up properly afterwards. Now unnecessary rows are not created, and everything is cleaned up properly.
* Problems with Avatar Upload plugin are fixed.
* Offset errors with mb_stripos() shouldn't happen anymore.
* Relevanssi tables are now added to `wpmu_drop_tables` to ensure neat cleanup with WPMU.
* A small problem in taxonomy search MySQL fixed, also a small problem with AND operator in tax_queries.
* Did you mean function now handles umlauted alphabet (ä, ö, ü and å).
* Fixed a bug with WP_Date_Queries. Thanks to Charles St-Pierre.
* New filter: `relevanssi_post_to_index` lets you access the post object before the post is indexed.
* New filter: `relevanssi_orderby` lets you modify the $orderby value before Relevanssi sorts posts.
* New filter: `relevanssi_order` lets you modify the $order value before Relevanssi sorts posts.
* New filter: `relevanssi_post_title_before_tokenize` lets you modify post titles before indexing.
* New filter: `relevanssi_private_cap` lets you adjust the capability setting for private posts in custom post types.

= 1.12.1 =
* Fixed a bug where excluding posts would cause the search to fail.
* WPML searches showed each result twice. That's fixed.
* Increased plugin safety against hackers.
* There was a bug in `relevanssi_comment_content_to_index` filter.
* Some people had problems with the log entry timestamps. Fixed that.
* New filter: `relevanssi_prevent_default_request` gives you more control over where Relevanssi prevents the default query from running.
* New filter: `relevanssi_private_cap` lets you set the correct capability for finding private posts in custom post types.
* The option to exclude categories and tags from search only worked for categories, not tags. Tags have been separated to a different option.

= 1.12 =
* Relevanssi now automatically treats 'ß' as 'ss'. If your site has 'ß' in text, reindexing the database is a good idea.
* Query variable `post_status` is now supported.
* Improvements to excerpts: excerpts with phrases work much better now, and the excerpt creation logic has been improved: the excerpts are now better. The process takes a bit more time, though.
* Allowing HTML tags in excerpts could lead to those tags being left open. Relevanssi will now try to close open HTML tags in excerpts.
* Allowed tags were not controlled in comments. They are now.
* Highlighting in documents didn't always work; it should be more reliable now.
* Non-integer values are removed from `post__in` and `post__not_in` before processing them.
* Query variables `p` and `page_id` are now supported.
* Relevanssi now understands `date_query` variables as well.
* The original post excerpt is stored in $post->original_excerpt.
* Taxonomy search works better with term id parameters (for example from `wp_category_dropdown`).
* Errors about $wpdb->prepare() missing an argument removed.
* New functions: `relevanssi_the_title()` and `relevanssi_get_the_title()` can be used to display highlighted titles in search results.
* The old title highlighting method has been disabled, because it caused highlights in wrong places. Now the highlighted title is stored in $post->highlighted_post_title, take it from there or use the Relevanssi title functions to display it.
* Polylang and WPML support was adjusted to perform better in edge cases.
* Indexing is faster, thanks to some improved code from Tom Novelli.
* MySQL injection attack vulnerability removed.
* The cache feature is now removed. Relevanssi should automatically drop the cache tables.
* New filter: `relevanssi_indexing_data` lets you modify the data before it's indexed.
* Fix for a bug that sometimes caused multisite hits to come from the wrong site.

= 1.11 =
* Fixed a bug in the TablePress support.
* Titles are put through the_title filter before indexing.
* relevanssi_related() function had a bug.
* New filter: `relevanssi_join` can be used to join tables in the Relevanssi search MySQL queries. Thanks to Ninos Ego.
* New filter: `relevanssi_tax_term_additional_content` can be used to add any content to taxonomy terms before indexing.
* New filter: `relevanssi_post_content` can be used to modify post content before any Relevanssi processing.
* New filter: `relevanssi_post_content_before_tokenize` can be used to modify post content just before it's tokenized.
* New filter: `relevanssi_indexing_values` can be used to modify what Relevanssi stores in the index.
* New filter: `relevanssi_default_meta_query_relation` can be used to change the default meta query relation (default value is "AND").
* When using a meta_query, `relation` can be set to OR now.
* Phrases are now matched to excerpts.
* Number of queries Relevanssi generates is much, much lower.
* New filter: `relevanssi_didyoumean_url` lets you modify the URL generated by the did you mean feature.
* Better set of Russian stopwords. 
* Relevanssi now highlights search query synonyms as well in documents.

= 1.10.14 =
* Fix to make Relevanssi compatible with WordPress 3.7.
* Fixed a mistyped database table name.
* Relevanssi disables responsive-flipbook shortcode in indexing; it was causing problems.
* Fixed a problem with an author dropdown with no author selected.

= 1.10.13 =
* New filter: `relevanssi_comment_content_to_index` lets you modify comment content before it's indexed by Relevanssi (to index comment meta, for example).
* Facetious support: if post_type is set to -1, Relevanssi will not hang up on it.
* Numerical search terms work better now.
* Relevanssi now handles WordPress-created tax_queries better.
* Support for Polylang broke the support for WPML. That is now fixed.
* Two deprecated $wpdb->escape() were still left; they're gone now.
* Shortcode `layerslider` was causing problems with Relevanssi; Relevanssi now disables it before building excerpts.
* Relevanssi won't break BBPress search anymore.
* Multisite searches had some issues.

= 1.10.12 =
* Excerpt-building had issues, which are now fixed.
* Punctuation removal now replaces &nbsp; with a space.
* "starrater" short code from GD Star Rating is now disabled in indexing.
* Punctuation removal now replaces invisible spaces with a normal space.
* Division by zero error caused by 0 in posts_per_page is now prevented, and -1 value for posts_per_page handled better.
* Relevanssi doesn't apply `get_the_excerpt` filters to excerpts it builds any more.
* New filter: `relevanssi_excerpt` lets you modify the excerpts Relevanssi creates.
* Relevanssi now suspends WP post cache while indexing, making indexing a lot more efficient. Thanks to Julien Mession for this one.
* Deprecated function errors in 3.6 removed.
* When search included user profiles or taxonomy terms, Relevanssi would generate lots of MySQL errors. Not anymore.
* New filter: `relevanssi_valid_status` lets you modify the post statuses Relevanssi indexes.
* New filter: `relevanssi_index_taxonomies_args` lets you modify the arguments passed to get_terms() when indexing taxonomies (for example to set 'hide_empty' to false).
* Searching by taxonomy ID could confuse two taxonomies with the same term_id. The search is now checking the taxonomy as well to see it's correct. 
* Basic support for Polylang plugin.
* Russian and Italian stopwords are now included, thanks to Flector and Valerio Vendrame.
* Small fix in the way user meta fields are handled.

= 1.10.11 =
* Previous upgrade broke AND operator in searches. Fixed that.

= 1.10.10 =
* REBUILD THE INDEX AFTER THIS UPDATE.
* Prevented error messages relating to creation of post objects from users and taxonomies.
* Fixed MySQL errors from empty meta queries.
* Removed WP complaint about badly formed $wpdb->prepare() statement.
* Sort order (orderby and order variables) are now read from query variables instead of global variables.
* Relevanssi will not choke on bad values of orderby anymore.
* Limit searches is improved: when using AND search it is less likely to miss results.
* Phrase recognition read the whole post content (which it didn't need) from database, causing memory issues in some cases. Fixed that.
* Fuzzy searches are now a lot more efficient; they were a huge resource hog before.
* Fixed a possible MySQL injection attack.

= 1.10.9.1 =
* OR fallback didn't actually fall back to OR, but instead got stuck in an endless loop of AND searches.
* Relevanssi was being called twice when a post was saved, on `save_post` and `wp_insert_post`. I removed the hook on `save_post`.

= 1.10.9 =
* Fixed the auto-update problem in 1.10.8 asking to update after update was done.
* Meta queries didn't work without a key; now they work with just meta_value or meta_value_num.
* Modified the way the highlights work; now highlighting words with apostrophes should produce more meaningful results.

= 1.10.8 =
* Major indexing problems caused by shortcodes changing the post ID during the indexing of posts are now fixed.
* Meta queries had problems with meta_value being set to null.
* Relevanssi now supports category__and. By default this sets include_children to false.
* When querying by slug, the term taxonomy is also taken into consideration, fixing problems when same slug appears in different taxonomies.
* Author search didn't work.
* Fixed an error message caused by all-number synonyms starting with zero, like 02.
* New action hook: `relevanssi_pre_indexing_query` can be used to "SET OPTION SQL_BIG_SELECTS=1" if needed.
* Synonyms are now case-insensitive.
* Highlighting should not highlight anything between & and ; or in <style> or <script> tags, thus solving some of the problems related to highlights. Reports of how well this works are welcome.
* On-post highlighting now only highlights content in the loop, so menu texts and other off-the-loop stuff should not get highlights anymore.
* Multiple taxonomy term search broke when there were empty entries in the search. Fixed that.
* New filter: `relevanssi_default_tax_query_relation` can be used to change the default tax query relation from OR to AND.
* New filter: `relevanssi_bots_to_not_log` makes it possible to block bots from logs. The format matches what other plugins, ie. WP-Useronline, use for bot blocking, so you can share block lists.
* New filter: `relevanssi_admin_search_ok` gives you more control when Relevanssi overrides the default WP search in admin, useful for fixing P2P_Box AJAX search.
* New filter: `relevanssi_term_add_data` lets you add data to taxonomy terms before they are indexed.
* Fixed undefined variable errors when doing an OR fallback.
* Ordering search results by title or date in admin search works now.
* Unsuccessful searches are now ordered by count, like the successful queries are.

= 1.10.7 =
* Removes the nasty error message on Relevanssi settings page when nothing was checked for "Choose taxonomies to index". I also added some additional instruction about the two very similar taxonomy indexing features.
* Fixed some problems with Did you mean? feature: number searches work better and exact matches don't cause the basic version of the feature to activate any more.
* Fixed a bug that could cause an error message about array_unique() function.
* $match->tag now contains the number of tag hits.

= 1.10.6 =
* Tags in breakdowns always showed 0, even though tags were indexed and searched correctly. That's now fixed.
* Checkboxes to set taxonomy term indexing now actually work.
* Disabling shortcodes didn't work. Now it does.

= 1.10.5.1 =
* Fixed a bug caused by an invisible character.

= 1.10.5 =
* Support for WP Table Reloaded and TablePress. Tables created with these plugins will now be expanded and the content indexed by Relevanssi.
* Relevanssi now adds spaces between tags when creating excerpts to make neater excerpts from tables and other similar situations.
* Relevanssi now indexes unattached attachments, if you choose to index attachments.
* Fixed some cases where AND search fails when the search terms include stopwords.
* Fixed a bug in indexing user profiles and taxonomy terms.
* Fixed the problems with Twenty Ten and Twenty Eleven themes better.
* Relevanssi now adds relevance score to posts before passing them to relevanssi_hits_filter. You can find it in $post->relevance_score.
* New filter: `relevanssi_index_comments_exclude` can be used to exclude comments from indexing. The filter gets the post ID as a parameter, so you can prevent comments of particular posts being indexed, yet index those posts.
* You can now choose the taxonomies to index from a checkbox list.
* New Premium feature: you can disable particular shortcodes from the shortcode expansion.

= 1.10.4 =
* AND search did not work in all cases.
* Posts couldn't be found by category name. Fixed that.

= 1.10.3 =
* Exclude category option was broken. Fixed that.
* Searching for a non-existing category ID caused an error. Fixed that.
* Occasional blank screens of death occurred when multibyte string operations weren't installed. That should not happen anymore.
* Fallback to OR search was a bit broken.
* New users are automatically indexed, that didn't work before.

= 1.10.2 =
* Small fix to prevent database errors.
* Small fix to prevent disappearing excerpts.

= 1.10.1 =
* Fixes a small database problem that was causing error messages.
* Fixes a problem with Twenty Ten and Twenty Eleven themes that caused doubled "Continue Reading" links.
* Small touches here and there.

= 1.10 =
* Made some changes to how user profiles and taxonomy terms are handled. As a result, there should be less warning notices. For user profiles, you can now find the user id in $post->user_id and for taxonomies, the term id is $post->term_id.
* Deleting users and taxonomy terms caused problems. Fixed that.
* Fixed a notice about undefined variable on plugin update pages.
* Small bug fixes on search to remove warning notices.
* New filter: `relevanssi_index_custom_fields` can be used to modify the list of custom fields to index.
* Deleting menus caused a warning. That is now fixed.
* Relevanssi has an option to disable IP logging (which is actually illegal in some countries). Thanks to Stefan Eufinger.
* Searching in subcategories worked sometimes, but not always. Thanks to Faebu.
* The "Limit searches" option didn't work too well in the case of strong weightings, as it didn't take note of any weights. Now it works better.
* Added a note about disabling custom excerpts when they are not needed - they can slow down the search quite a bit.
* New filter: `relevanssi_options_capability` can be used to modify the capability required to see the options page (default is `manage_options`).
* External search highlighting from Google doesn't work anymore, because Google doesn't pass the search term in referrer fields. Fixed the highlighting for Yahoo searches.
* Fixed the way IDF is calculated to account some extreme cases with small databases.
* New filter: `relevanssi_index_custom_fields` gives added control over which custom fields are indexed.
* Fixed filter: `relevanssi_pre_excerpt_content` wasn't working properly.
* Relevanssi now supports tax_query, for most part. You can query multiple taxonomies, use relation AND and OR, use operators AND, IN and NOT IN and choose include_children (which defaults to true). Old `taxonomy` and `term` still work, but I recommend using tax_query for the level of control it offers.
* Relevanssi now works better with category restrictions. The extra `cats` query variable is no longer necessary, Relevanssi can now read multiple categories from `cat`. You can also use `category__and`, `category__in` and `category__not_in`.
* Same goes with tags: `tags` is now longer necessary. Relevanssi has full support for `tag`, `tag_id`, `tag__and`, `tag__in`, `tag__not_in`, `tag_slug__and`, `tag_slug__in` and `tag_slug__not_in`. For `tag`, both `term1+term2` and `term1,term2` is supported.
* Relevanssi now supports `author_name` and negative values for `author`.
* Relevanssi now supports `offset` query variable.
* Relevanssi now supports meta_query. You can use all comparisons (also EXISTS and NOT EXISTS, even if you don't have WP 3.5). You can also use the older `meta_key` and `meta_value` query variables, including all the comparisons. I have not tested all possible meta_query constructions, so bug reports of things that don't work as expected are welcome.
* New index on the database makes some database operations faster.
* New filter: `relevanssi_user_add_data` lets you add extra data to user profiles before indexing them.
* Removed a bug that prevents one-character words from being indexed in titles, despite the minimum word length setting.
* Removed a warning when searching for nothing.
* Fixes a warning about $wpdb->prepare() caused by a change in WordPress 3.5.

= 1.9.2.1 =
* Auto-update is now actually fixed.

= 1.9.2 =
* Auto-update was broken in 1.9. It should work now.
* Added functions `relevanssi_the_tags()` and `relevanssi_get_the_tags()` which can be used to print out a highlighted tag list in search results pages.
* Fixed a bug that caused Relevanssi not to index posts in some cases.

= 1.9.1 =
* Fixed a major bug that caused the searches to fail when "Limit searches" was enabled, but "Limit" was not defined.
* Added "-es" to the list of suffixes stripped by the stemmer. If you use the stemmer, you need to reindex the database.
* Modified `relevanssi_remove_punct()` to replace curly apostrophes and quotes with spaces instead of removing them, to make the index more consistent (regular apostrophes were replaced with spaces). Reindexing the database is a good idea.
* Fixed some misleading text on the options page.

= 1.9 =
* The default function on `relevanssi_post_ok` filter is now set to priority 9, instead of 10, so that user functions happen after the default function by default.
* You can now use the plus operator for Boolean AND in OR queries. Any search term prefixed with + must appear in search results.
* Fixed warnings for undefined variables.
* Relevanssi won't prevent media library searches anymore.
* Search terms are no longer highlighted in titles on post pages. That caused too many problems.
* You can now choose to allow HTML tags in excerpts.
* Jetpack Contact Form shortcode caused problems when indexing. Relevanssi will now simply remove the shortcode before indexing.
* Phrases are now also recognized in drafts and attachments.
* Fixed an error message caused by searching for numbers.
* You can now set `post_types` to 'any'.
* Role-Scoper users: in order to make Relevanssi work with Role-Scoper, replace the Relevanssi helper file in Role-Scoper with [this file](http://www.relevanssi.com/relevanssi-helper-front_rs.txt).
* Removed an error message about set_time_limit() under safe_mode.
* Fixed errors caused by / characters in highlighting.
* Added an alert when user hasn't selected any post types to index (and default values).
* Custom field setting 'visible' works now.
* Relevanssi won't mess media library searches any more.
* Search terms are no longer highlighted in titles on post pages. That caused too many problems.
* New filter: `relevanssi_didyoumean_query` let's you modify the query for Did you mean? queries
* New filter: `relevanssi_user_searches_capability` lets you modify the minimum capability required to see the User searches page.
* When filtering results with taxonomy=a|b&term=a|b syntax, you can now use more terms per taxonomy, like this: taxonomy=a|b&term=a,b|c,d.

= 1.8.2.1 =
* A small fix to make Role-Scoper integration work better.

= 1.8.2 =
* Fixed a critical bug in 1.8.1.

= 1.8.1 =
* "Uncheck this if you use non-ASCII characters" option didn't work.
* Relevanssi showed incorrect number of posts on results pages.
* Fixed a small bug in indexing user profiles.
* I improved the way Relevanssi and Role-Scoper work together.

= 1.8 =
* Searching for pages in admin didn't work properly. Fixed that.
* Searches where posts_per_page was set to -1 didn't work well. They should work now.
* New filter 'relevanssi_content_to_index' let's user add whatever content they wish to posts before they are indexed.
* The 'relevanssi_post_ok' hook didn't work well with multiple functions attached. It now has two parameters: first one is the $post_ok value to change and second is the post ID. Make sure you specify two parameters in add_filter() call.
* Fuzzy search didn't always activate when it should, if all found posts are private posts that can't be shown to user.
* The default punctuation remover will now replace apostrophes with spaces instead of removing them. To see this in effect, you need to reindex database.
* Relevanssi will now disable the default WordPress search when Relevanssi is running. (Thanks to John Blackbourn)
* You can now set the "Custom fields to index" to "all" to index all custom fields and "visible" to index all visible custom fields (but not the ones with names starting with an underscore).
* Auto-update should work better now.
* Tab characters in excerpts are handled better now.
* Relevanssi search logs will now store user ID's and IP addresses for each query.
* You can now use user logins as well as numeric ID's to stop user from being logged.
* New query variable "operator" will let you adjust the search operator to AND or OR.
* New collation rules to MySQL databases will make sure that word pairs like "pode" and "pôde" will not be considered duplicates in the stopword database.
* Relevanssi will now automatically choose the correct stopword list based on WPLANG setting.
* Attachments are now handled better. I'd still like to hear any complaints about attachments.
* You can now use the minus operator for Boolean NOT. "dog -cat" will return all posts with the word "dog" but not the word "cat". This does not work combined with phrases.
* "Exclude post from index" metabox now appears on edit pages for all post types, not just post and page.
* Relevanssi now updates index for posts added with wp_update_post() function. (Thanks to Simon Blackbourn)

= 1.7.9 =
* A small optimization attempt broke searches in admin. Fixed that.

= 1.7.8 =
* Relevanssi indexed user profiles on update, whether the option was checked or not.
* Relevanssi tried to index taxonomy terms on update, but couldn't, because there was another bug that prevented it.
* In some cases stripping tags would cause words to be joined. Tags are now replaced with spaces to make sure that doesn't happen.
* Fixed problems with undefined variables.
* Sometimes text would have non-typical space characters left in it, causing trouble. Relevanssi can now remove those spaces.
* Relevanssi had some problems with WP-Footnotes plugin, fixed that.
* New filter 'relevanssi_modify_wp_query_filter' lets you modify $wp_query before it is passed to Relevanssi.

= 1.7.7 =
* Fixed a major bug that can make indexing fail when the user has manually chosen to hide posts from the index.
* Removed default values from text columns in the database.
* Relevanssi will now index pending and future posts. These posts are only shown in the admin search.
* Using multiple taxonomies in search will now use OR logic between term within the same taxonomy and AND logic between different taxonomies. Thanks to Jonathan Liuti.
* Added a shortcode `noindex` that can be used to prevent parts of posts being indexed. In order to use the shortcode, you must enable expanding shortcodes in indexing.

= 1.7.6 =
* New filter `relevanssi_results` added. This filter will process an array with (post->ID => document weight) pairs.
* Fixed a mistake in the FAQ: correct post date parameter is `post_date`, not `date`.
* When continuing indexing, Relevanssi now tells if there's more to index. (Thanks to mrose17.)
* Private and draft posts were deleted from the index when they were edited. This bug has been fixed. (Thanks to comprock.)
* Improved WPML support.
* The `relevanssi_index_doc()` function has a new parameter that allows you to bypass global $post and force the function to index the document given as a parameter (see 1.7.6 release notes at Relevanssi.com for more details).

= 1.7.5 =
* Drafts are now indexed and shown in the admin search.
* A first test version of English stemmer (or suffix stripper) is available. Enable it with `add_filter('relevanssi_stemmer', 'relevanssi_simple_english_stemmer');`.

= 1.7.4 =
* Fixed a bug related that caused AND queries containing short search terms to fall back to OR searches.
* The 'relevanssi_match' filter now gets the IDF as an additional parameter to make recalculating weight easier.
* Added a very nice related searches feature by John Blackbourn.

= 1.7.3 =
* Cache truncation was never actually scheduled.
* Index wasn't updated properly when post status was switched from public to private.
* Made the relevanssi_hide_post custom field invisible.
* Added an option to hide the Relevanssi post controls metabox on edit pages.
* Fixed a bug that prevents search hit highlighting in multiple blog searches.
* Added support for 'order' and 'orderby' in multiple blog searches.
* Added nonces to various forms to improve plugin security.
* Added support for 'author' query variable.
* Added support for searches without a search term.

= 1.7.2 =
* Fixed another bug that was causing error notices.

= 1.7.1 =
* Fixed a bug that caused errors when indexing, if MySQL column setting was empty.

= 1.7 =
* Relevanssi now stores more data about custom fields and custom taxonomies, allowing more fine-tuned control of results.
* There was a bug in custom field indexing that caused all custom field terms get a term frequency of 1.
* There was a bug in custom taxonomy indexing, effects of which are uncertain. Probably nothing major.
* The 'tag' (and 'tags') query variable now accepts tag names as well as tag IDs. For category names, you can use 'category_name'.
* Relevanssi can now index user-specified MySQL columns from the wp_posts table.
* It's now possible to adjust weights for all taxonomies, not just categories and tags.
* It's now possible to give a weight bonus for recent posts.

= 1.6.2.1 =
* Fixed a nasty bug that prevented indexing the database. If you installed 1.6.2 and ran into the problem, update and check the correct post types to index.

= 1.6.2 =
* Somebody had problems with the log table ID field going over MEDIUMINT limit. I changed the ID field to BIGINT.
* There were serious problems with custom post type names that include 'e_' in them. That's now fixed.

= 1.6.1 =
* Fixed small bugs in the Did you mean -feature. (Thanks to John Blackbourn.)
* Fixed the tf*idf weighing a bit in order to increase the effect of the idf component. This should improve results of OR searches in particular by giving more weight to rarer terms.
* Fixed the WPML filter when working with multisite environment. (Thanks to Richard Vencu.)
* Fixed (for real) a bug that created bad suggestion URLs with WPML. (Thanks to John Blackbourn.)
* Fixed s2member support for s2member versions 110912 and above. (Thanks to Jason Caldwell.)

= 1.6 =
* Fixed a bug that removed 'à' from search terms.
* Fixed error notices about undefined $wpdb.
* Fixed errors about deprecated ereg_replace.
* Old post type indexing settings are now imported.
* Fixed uninstall to better clean up after Relevanssi is uninstalled.
* Fixed a bug that created bad suggestion URLs with WPML. (Thanks to John Blackbourn)
* Improved s2member support.
* Removed error notices that popped up when quick editing a post.
* Relevanssi can now index drafts for admin search.
* New filter `relevanssi_show_matches` can be used to modify the text that shows where the hits are made.
* New filter `relevanssi_user_index_ok` lets you control which users are indexed and which are not.

= 1.5.13.beta =
* Support for s2member membership plugin. Search won't show posts that the current user isn't allowed to see.
* New filter `relevanssi_post_ok` can be used to add support for other membership plugins.
* Better way to choose which post types are indexed.
* Post meta fields that contain arrays are now indexed properly, expanding all the arrays.

= 1.5.12.beta =
* If a custom field limitation is set and no matches are found, no results are returned.
* New filter `relevanssi_fuzzy_query`. This can be used to change the way fuzzy matches are made.
* There's a meta box on post and page edit pages that you can use to exclude posts and pages from search.
* User profiles couldn't be found, unless "respect exclude_from_search" was disabled. I've fixed that.
* OR fallback search had a bug. Fixed that.
* Custom field searches support phrases. Thanks to davidn.de.
* Fixed a bug that caused problems when paging search results.
* `get_the_excerpt` filters weren't triggered on excerpt creation. `the_excerpt` is not used, as it will add unnecessary HTML code to the excerpts.

= 1.5.11.beta =
* New filter `relevanssi_do_not_index`. Filter is passed a post id and if it returns `true`, the post will not be indexed.
* New query variable: use `tag` or `tags` to filter results by tag. Both take comma-separated lists of tag ids (not tag slugs or names) and filter results by them (it's an OR, not AND operation).
* New filter `relevanssi_ellipsis`. Use this if you want to change the '...' appended to excerpts.
* Relevanssi-created excerpts are now passed through `the_excerpt` and `get_the_excerpt` filters.
* Attachments (with post status inherit) couldn't be found in search. Now they can.
* Amount of SQL queries made in indexing has been reduced a lot. Less memory should be required. I'd appreciate any reports of changes in the database re-indexing performance.

= 1.5.10.beta =
* Removed some unnecessary filter calls.
* the_content filters didn't have any effect on excerpts, now they work as they should.
* Taxonomy term search didn't work properly.
* I've moved the "strip invisibles" function after shortcode expansion in indexing and excerpt creation, so objects, embeds and styles created by shortcodes are stripped. Let me know if this causes any problems.
* Multibyte string functions are not required anymore, Relevanssi will work without, but will cause problems if you try to handle multibyte strings without multibyte functions. (Thanks to John Blackbourn.)
* Couple of functions Relevanssi uses weren't namespaced properly. They are now. (Thanks to John Blackbourn.)
* When $post is being indexed, `$post->indexing_content` is set to `true`. This can be useful for plugin developers and all sorts of custom hacks. (Thanks to John Blackbourn.)
* User search log now displays the total number of searches. (Thanks to Simon Blackbourn.)
* Database now has an index on document ID, which should make indexing faster.
* If you upgrade from 1.5.8 or earlier, emptying the database manually is not necessary.
* The plugin can now be upgraded automatically. The required API key can be found on Relevanssi.com in the sidebar after you log in.

= 1.5.9 =
* Fixed a MySQL error that was triggered by a media upload.
* Minimum word length to index wasn't enforced properly.
* Fixed a bug that caused an error when quick editing a post.
* Improved the handling of punctuation.
* Added an indexing option to manage thousands separators and large numbers better.
* The database is changed. The change requires reindexing and emptying the database before activating the plugin. Either truncate the database from phpMyAdmin or similar tool or use the "Delete plugin options" (but remember to back up your options and stopwords first!).
* Adjusted the default throttle to 300 posts from 500 posts.

= 1.5.8 =
* Added a new hook `relevanssi_excerpt_content`; see [Knowledge Base](http://www.relevanssi.com/category/knowledge-base/) for details.
* Improved the indexing procedure to prevent MySQL errors from appearing and to streamline the process.

= 1.5.7 =
* 1.5.6 was broken, this is a quick fix release.

= 1.5.6 =
* Added default values to the database columns, this could cause some problems.
* Indexing could cause problems, because Relevanssi changed the contents of global $post. That's fixed now.
* There's an option to choose the default order of search results, by relevance or by date.
* Indexing settings have a new option to only index certain post types.

= 1.5.5 =
* Added two new filters: `relevanssi_index_titles` and `relevanssi_index_content`. Add a function that returns `false` to the filters to disable indexing titles and post content respectively.
* Google Adsense caused double hits to the user search logs. That's now fixed thanks to Justin Klein.

= 1.5.4 =
* It's now possible to remove matches from the results with the external filter `relevanssi_match`. If the weight is set to 0, the match will be removed.
* Multisite installations had problems - installing plugin on a single site in network didn't work. John Blackbourn found and fixed the bug, thanks!

= 1.5.3 =
* User search log is available to user with `edit_post` capabilities (editor role). There's also an option to remove Relevanssi branding from the user search logs. Thanks to John Blackbourn.
* A proper database collation is now set. Thanks to John Blackbourn.
* UI looks better. Thanks to John Blackbourn.
* Small fixes: spelling corrector uses now correct multibyte string operators, unnecessary taxonomy queries are prevented. Thanks to John Blackbourn.
* You can now export and import settings. Thanks to ThreeWP Ajax Search for showing me a good (easy) way to do this.

= 1.5.2 =
* A German translation is included, thanks to David Decker.
* A get_term() call was missing a second parameter and throwing errors occasionally. Fixed that.
* Fixed a bug that caused Cyrillic searches in the log to get corrupted.
* Punctuation removal filter was actually missing from the code. Oops. Fixed that now.

= 1.5.1 =
* The result caching system didn't work properly. It works now.
* Limiting results with custom field key and value didn't work properly: it matched the value to the whole field. Now it matches the value to any part of the custom field. That should make more sense. 

= 1.5 =
* Taxonomy pages (tags, categories, custom taxonomies) can now be indexed and searched.
* Short search terms don't crash the search anymore.
* There are fixes to the user search as well, including a new option to index additional fields.
* Relevanssi now uses search result caching system that greatly reduces the number of database calls made.
* Punctuation removal function is now triggered with a filter call and can thus be replaced.

= 1.4.5 =
* New filter: `relevanssi_match` allows you to weight search results.
* Similar to `cats` vs `cat`, you can now use `post_types` to restrict the search to multiple post types.
* Multisite search supports post type restriction.

= 1.4.4 =
* Changed the way search results are paginated. This makes adjusting the number of search results shown much easier.

= 1.4.3 =
* Fixed the Did you mean -feature.
* WordPress didn't support searching for multiple categories with the `cat` query variable. There's now new `cats` which can take multiple categories.

= 1.4.2 =
* Multisite search had bugs. It's working now.
* Stopwords are not highlighted anymore. Now this feature actually works.

= 1.4.1 =
* Textdomain was incorrect.
* The new database structure broke the throttle and the spelling correction. These are now fixed.

= 1.4 =
* New database structure, which probably reduces the database size and makes clever stuff possible.
* The throttle option had no effect, throttle was always enabled. Now the option works. You can now also either replace the throttle function with your own (through 'relevanssi_query_filter') or modify it if necessary ('relevanssi_throttle').
* Highlights didn't work properly with non-ASCII alphabets. Now there's an option to make them work.
* Title highlight option now affects external search term highlights as well.
* Stopwords are not highlighted anymore.
* Fixed a small mistake that caused error notices.
* Custom post types, particularly those created by More Types plugin, were causing problems.

= 1.3.2 =
* Expired cache data is now automatically removed from the database every day. There's also an option to clear the caches.
* A nasty database bug has been fixed (thanks to John Blackbourn for spotting this).
* Fixed bugs on option page.

= 1.3.1 =
* Fixed the multiple taxonomy search logic to AND instead of OR.
* Some small security fixes.

= 1.3 =
* Bug fix: when choosing mark highlighting, the option screen showed wrong option.
* Category restrictions now include subcategories as well to mirror WordPress default behaviour.
* Internal links can be now indexed for the source, target or both source and target.
* It's now possible to limit searches by custom fields.
* It's now possible to use more than one taxonomy at the same time.

= 1.2 =
* Relevanssi can now highlight search terms from incoming queries.
* Spelling correction in Did you mean searches didn't work.
* Some shortcode plugins (Catablog, for example) were having trouble; fixed that.

= 1.1.2 =
* The plugin didn't update databases correctly, causing problems.

= 1.1.1 =
* Very small fix that improves plugin compatibility with Relevanssi when using shortcodes.

= 1.1 =
* Multisite WordPress support. See FAQ for instructions on how to search multiple blogs.
* Improved the fallback to fuzzy search if no hits are found with regular search.
* AND searches sometimes failed to work properly, causing unnecessary fallback to OR search. Fixed.
* When using WPML, it's now possible to choose if the searches are limited to current language.
* Adding stopwords from the list of 25 common words didn't work. It works now.
* The instructions to add a category dropdown to search form weren't quite correct. They are now.
* It's now possible to assign weights for post types.
* User profiles can be indexed and searched.

= 1.0 =
* First published version, matches Relevanssi 2.7.3.

== Upgrade notice ==

= 1.13.3 =
* Bug fixes and couple of small new features.

= 1.13.2 =
* Small bug fixes and improved performance.

= 1.13.1 =
* Fix for user search and other bug fixes.

= 1.13 =
* Many bug fixes and several new features.

= 1.12.1 =
* Bug fixes and security updates.

= 1.12 =
* Lots of new features, bug fixes and a stop to a MySQL injection attack vulnerability.

= 1.11 =
* New filters, better search efficiency, new features, small bug fixes.

= 1.10.14 =
* WordPress 3.7 compatibility, couple of minor bug fixes.

= 1.10.13 =
Small bug fixes, better BBPress compatibility, broken Polylang support fixed.