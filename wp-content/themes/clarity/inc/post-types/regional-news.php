<?php
add_action( 'init', 'define_regional_news_post_type' );

// Regional news post type
function define_regional_news_post_type() {
	register_post_type(
		'regional_news',
		[
			'labels'             => [
				'name'               => __( 'Regional News' ),
				'singular_name'      => __( 'News story' ),
				'add_new_item'       => __( 'Add New News story' ),
				'edit_item'          => __( 'Edit News story' ),
				'new_item'           => __( 'New News story' ),
				'view_item'          => __( 'View News story' ),
				'search_items'       => __( 'Search News stories' ),
				'not_found'          => __( 'No News stories found' ),
				'not_found_in_trash' => __( 'No News stories found in Trash' ),
			],
			'description'        => __( 'Contains details of regional news stories' ),
			'public'             => true,
			'publicly_queryable' => true,
			'menu_position'      => 3,
			'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt', 'author' ],
			'rewrite'            => [
				'slug'       => 'regional-news',
				'with_front' => false,
			],
			'show_in_rest'       => true,
			'rest_base'          => 'hmcts-regional-news',
			'capability_type'    => [
				'regional_new',
				'regional_news',
			],
			'capabilities'       => array(
				'publish_posts'       => 'publish_regional_news',
				'edit_posts'          => 'edit_regional_news',
				'edit_others_posts'   => 'edit_others_regional_news',
				'read_private_posts'  => 'read_private_regional_news',
				'edit_post'           => 'edit_regional_news',
				'delete_post'         => 'delete_regional_news',
				'delete_posts'        => 'delete_regional_news',
				'delete_others_posts' => 'delete_regional_news',
				'read_post'           => 'read_regional_news',
			),
			'has_archive'        => false,
			'hierarchical'       => false,
		]
	);
}
