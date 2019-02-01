<?php
use MOJ\Intranet\News;

$oNews = new News();

$options = [
	'page'     => 1,
	'per_page' => 6,
];

$post_id    = get_the_ID();
$latestNews = $oNews->getNews( $options, true );

if ( ! empty( $latestNews ) ) : ?>
	<div class="c-news-list-aside">
		<?php
		foreach ( $latestNews['results'] as $postItem ) {

			// Avoid story duplication of current page in lefthand list
			if ( $postItem['id'] === $post_id ) {
				$postItem = '';
			} else {

				if ( ! empty( $postItem['thumbnail_url'] ) ) :

					echo '<div class="c-news-list-aside__wrapper">';
					echo '<div class="c-news-list-aside__img">';

					echo '<a href="';
					echo esc_url( $postItem['url'] );
					echo '">';
					echo '<img src="';
					echo esc_url( $postItem['thumbnail_url'] );
					echo '" alt="';
					echo esc_attr( $postItem['thumbnail_alt_text'] );
					echo '">';
					echo '</a>';
					echo '</div>';

				endif;

				echo '<div class="c-news-list-aside__text">';
				echo '<h1><a href="';
				echo esc_url( $postItem['url'] );
				echo '">';
				echo esc_attr( $postItem['title'] );
				echo '</a></h1>';

				echo '<div class="">';
				echo '<span class="">';
				echo get_the_time( 'j M Y', $postItem['id'] );
				echo '</span>';
				echo '</div></div>';

				echo '</div>';

			}
		}
		?>

		<div class="c-news-list-aside__related"><a href="/newspage">See all news</a></div>

	</div>

	<?php
else :
	echo 'There are currently no news stories';
endif;
