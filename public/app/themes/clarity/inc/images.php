<?php
/**
 * Setting image attributes (sizes, restrictions etc) used throughout the Clarity theme.
 *
 * @link https://developer.wordpress.org/reference/functions/add_image_size/
 *
 * @package WordPress
 * @subpackage Clarity
 * @since 2018
 */

// Default image size editors can choose for images on posts and pages. (3:2 aspect ratio)
add_image_size('intranet-large', 650, 433, true);
add_image_size('intranet-small', 280, 182, true);

// Used on homepage blog listing and any where guest author appears as thumb (1:1 aspect ratio)
add_image_size('user-thumb', 128, 128, true);

// Homepage feature item image (3:2 aspect ratio)
add_image_size('feature-thumbnail', 325, 217, true);

// Used on the homepage by the need-to-know widget (image carousel)
add_image_size('need-to-know', 768, 384, true);

// Right hand list images on single.php pages such as news (3:2 aspect ratio).
add_image_size('list-thumbnail', 100, 67, true);

// Large square feature image
add_image_size('square-feature', 320, 320, true);
