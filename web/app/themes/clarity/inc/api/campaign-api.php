<?php

use MOJ\Intranet\Agency;
use MOJ\Intranet\Authors;

function get_campaign_api($campaign_id, $post_type): void
{
    $agency = new Agency();
    $authors = new Authors();
    $activeAgency = $agency->getCurrentAgency();

    // normalise posts
    $single_post_type = $post_type === 'posts' ? 'post' : $post_type;

    $use_author_headshot = get_field('use_author_headshot') ?: false;
    if ($post_type === 'posts') {
        $use_author_headshot = true; // always in posts
    }

    $posts = get_posts([
        'post_type' => $single_post_type,
        'numberposts' => get_field('number_of_items') ?: '6',
        'tax_query' => [
            'relation' => 'AND',
            [
                [
                    'taxonomy' => 'agency',
                    'field' => 'term_id',
                    'terms' => $activeAgency['wp_tag_id']
                ],
                [
                    'taxonomy' => 'campaign_category',
                    'field' => 'term_id',
                    'terms' => $campaign_id
                ]
            ]
        ]
    ]);

    $output = '';
    $data_type = '<div class="data-type" data-type="'.$post_type.'"></div>';

    if (!empty($posts)) {
        $output .= '<div class="campaign-container">';
        $output .= '<h2 class="o-title o-title--section">'.ucfirst($post_type).'</h2>' . $data_type;

        foreach ($posts as $post) {
            $link = get_the_permalink($post->ID);
            $featured_img_url = wp_get_attachment_url(get_post_thumbnail_id($post->ID));
            $author = $authors->getAuthorInfo($post->ID)[0] ?? false;

            $output .= '<article class="c-article-item js-article-item" data-type="'.$post_type.'">';

            // image
            $output .= '<a href="' . $link . '" class="thumbnail">';
            if ($featured_img_url && !$use_author_headshot) {
                $output .= '<img src="' . $featured_img_url . '" alt="">';
            } elseif ($author !== false) {
                $alt = $author['thumbnail_alt_text'] ?? 'Image of ' . $author['name'];
                $output .= '<img src="' . $author['thumbnail_url'] . '" alt="' . $alt . '" title="' . $author['name'] . '">';
            }
            $output .= '</a>';
            //\ end image

            // content
            $output .= '<div class="content">
                <h1>
                    <a href="' . $link . '">' . $post->post_title . '</a>
                </h1>
                <div class="meta">
                    <span class="c-article-item__dateline">
                        ' . get_gmt_from_date($post->post_date_gmt, 'j M Y') . '
                    </span>
                </div>
                <div class="c-article-excerpt">
                    <p>' . $post->post_excerpt . '</p>
                </div>
            </div>';
            //\ end content

            $output .= '</article>';
        }

        $output .= '</div>';
    } else {
        $output .= $data_type;
    }

    echo $output;
}
