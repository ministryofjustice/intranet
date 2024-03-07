<?php
/*
* The data here is populated via ACF. JS builds the tabs and hides/displays the content accordingly.
* You can add as many tabs as you want, however, testing it seemed 3 tabs were really the limit with out the design looking a bit odd.
*/
if (have_rows('guidance_tabs')) :
    while (have_rows('guidance_tabs')) :
        the_row();

        $tab_count = count(get_field('guidance_tabs'));
        $tab_title = get_sub_field('tab_title');
        $sections  = get_sub_field('sections');

        if (isset($tab_count)) :
            if ($tab_count > 1) :
                 echo '<section class="c-tabbed-content js-tabbed-content c-rich-text-block"  data-tab-title="' . $tab_title . '">';
            else :
                echo '<section class="c-rich-text-block">';
            endif;

            if (get_field('guidance_tabs')) :
                while (the_repeater_field('sections')) :
                    echo '<h2>';
                    the_sub_field('section_title');
                    echo '</h2>';
                    echo apply_filters('acf_the_content', get_sub_field('section_content'));
                endwhile;
            endif;

            if (get_field('guidance_tabs')) :
                while (the_repeater_field('links')) :
                    $link_type = get_sub_field('link_type');
                    if ($link_type == 'heading') {
                        echo '<h2>';
                        the_sub_field('link_title');
                        echo '</h2>';
                    } else {
                        echo '<p><a href="' . get_sub_field('link_url') . '">';
                        the_sub_field('link_title');
                        echo '</a></p>';
                    }
                endwhile;
            endif;

            echo '</section>';
        endif; // if tab_count is set
    endwhile;
endif;
