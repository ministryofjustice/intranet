<?php

// listing page thumbnail sizes, e.g. home page
add_image_size( "newshead", get_option('large_size_w'), get_option('large_size_h'), true );
// Guidance & support head image
add_image_size( "gandshead", 225, 0, true);