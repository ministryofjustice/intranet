<?php
use MOJ\Intranet\Teams;

$oTeam = new Teams();

// Set the number of blog posts
$number = 6;

$team_blog_posts = $oTeam->team_blog_api( $number );

if ( $team_blog_posts && $team_blog_posts !== 0 ) {

		echo '<div class="c-blog-feed">';
		echo '<h1 class="o-title o-title--section">Blog</h1>';

		foreach ( $team_blog_posts as $team_blog_post ) {
			include locate_template( 'src/components/c-article-item/view-team.php', $team_blog_post, 'blogs' );
		}

		echo '</div>';
}
