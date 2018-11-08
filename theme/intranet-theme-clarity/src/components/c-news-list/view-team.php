<?php
use MOJ\Intranet\Teams;

$oTeam = new Teams();

// Set the number of news stories you want to show.
$number = 8;

$team_news_posts = $oTeam->team_news_api( $number );

if ($team_news_posts !== 0) {

  if ( $team_news_posts )
  {
    echo '<div class="c-news-list">';

          foreach ( $team_news_posts as $team_news_post ) {
              get_component('c-article-item', $team_news_post, 'show_date');
          }

    echo '</div>';
  }

} 
