<?php
use MOJ\Intranet\Teams;

$oTeam = new Teams();

// Set the number of news stories you want to show.
$number = 8;

$team_news_posts = $oTeam->team_news_api( $number );

if ( $team_news_posts !== 0 ) {

	if ( $team_news_posts ) {
		echo '<h1 class="o-title o-title--section">News</h1>';
		echo '<div class="c-news-list">';

		foreach ( $team_news_posts as $team_news_post ) {
			?>
			<article class="c-article-item teamlist">
			  <div class="content">
				<h1>
				  <a href="<?php echo esc_url( $team_news_post['link'] ); ?>"><?php echo esc_attr( $team_news_post['title']['rendered'] ); ?></a>
				</h1>
			  </div>
			</article>

			<?php
		}

		echo '</div>';
	}
}
