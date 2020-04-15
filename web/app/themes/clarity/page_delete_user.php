<?php
/***
 *
 * Template name: Delete User
 *
 */

get_header();
?>

    <div id="maincontent" class="u-wrapper l-main l-reverse-order t-default">
        <div class="l-primary" role="main">
            <h1 class="o-title o-title--page"><?php the_title(); ?></h1>
            <?php get_template_part('src/components/c-delete-user/view'); ?>
        </div>

    </div>


<?php
get_footer();
