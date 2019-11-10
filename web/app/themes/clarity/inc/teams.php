<?php
namespace MOJ\Intranet;

/**
 * Retrieves and returns guidance and form related data
 */

class Teams {

	private $page_meta;

	public function __construct() {
		$this->page_meta = [
			'post_id'  => get_the_ID(),
			'agency'   => get_intranet_code(),
			'home_url' => get_home_url(),
		];
	}

	 /**
	  *
	  * Team News API
	  *
	  * @param
	  * @return
	  */
	public function team_news_api( $number ) {
		$post_per_page = 'per_page=' . $number;
		$current_page  = '&page=1';
		$agency_name   = '&agency=' . $this->page_meta['agency'];

		/*
		* A temporary measure so that API calls do not get blocked by
		* changing IPs not whitelisted. All calls are within container.
		*/
		$siteurl = 'http://127.0.0.1';

		$response = wp_remote_get( $siteurl . '/wp-json/wp/v2/team-news/?' . $post_per_page . $current_page );

		if ( is_wp_error( $response ) ) :
			return;
		 endif;

		$pagetotal        = wp_remote_retrieve_header( $response, 'x-wp-totalpages' );
		$posts            = json_decode( wp_remote_retrieve_body( $response ), true );
		$response_code    = wp_remote_retrieve_response_code( $response );
		$response_message = wp_remote_retrieve_response_message( $response );

		if ( 200 == $response_code && $response_message == 'OK' ) :

			return $posts; else :

				return 0;

		 endif;
	} // END team_news_api()

	 /**
	  *
	  * Team Blog API
	  *
	  * @param
	  * @return
	  */
	public function team_blog_api( $number ) {
		$post_per_page = 'per_page=' . $number;
		$current_page  = '&page=1';
		$agency_name   = '&agency=' . $this->page_meta['agency'];

		/*
		* A temporary measure so that API calls do not get blocked by
		* changing IPs not whitelisted. All calls are within container.
		*/
		$siteurl = 'http://127.0.0.1';

		$response = wp_remote_get( $siteurl . '/wp-json/wp/v2/team-blogs/?' . $post_per_page . $current_page );

		if ( is_wp_error( $response ) ) :
			return;
		 endif;

		$pagetotal        = wp_remote_retrieve_header( $response, 'x-wp-totalpages' );
		$posts            = json_decode( wp_remote_retrieve_body( $response ), true );
		$response_code    = wp_remote_retrieve_response_code( $response );
		$response_message = wp_remote_retrieve_response_message( $response );

		if ( 200 == $response_code && $response_message == 'OK' ) :

			return $posts; else :

				return 0;

		 endif;
	} // END team_blog_api()

	 /**
	  *
	  * Team Events API
	  *
	  * @param
	  * @return
	  */
	public function team_events_api( $number ) {
		$post_per_page = 'per_page=' . $number;
		$current_page  = '&page=1';
		$orderby       = '&meta_key=event-start-date';
		$order         = '&order=desc';

		/*
		* A temporary measure so that API calls do not get blocked by
		* changing IPs not whitelisted. All calls are within container.
		*/
		$siteurl = 'http://127.0.0.1';

		$response = wp_remote_get( $siteurl . '/wp-json/wp/v2/team-events/?' . $post_per_page . $current_page . $orderby . $order );

		if ( is_wp_error( $response ) ) :
			return;
		 endif;

		$pagetotal        = wp_remote_retrieve_header( $response, 'x-wp-totalpages' );
		$posts            = json_decode( wp_remote_retrieve_body( $response ), true );
		$response_code    = wp_remote_retrieve_response_code( $response );
		$response_message = wp_remote_retrieve_response_message( $response );

		if ( 200 == $response_code && $response_message == 'OK' ) :

			return $posts; else :

				return 0;

		 endif;
	} // END team_events_api()
}
