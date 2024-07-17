<?php
/***
 *
 * Delete User - delete a user account (confirmed by email)
 * Also see inc/comments.php
 */

?>
<section class="c-delete-user">
    <?php

    if (!empty($_GET['key'])) {
        $key = stripslashes($_GET['key']);
        $login = stripslashes($_GET['login']);
        $check_result = check_password_reset_key($key, $login);

        if (is_wp_error($check_result)) { ?>
            <h1 class="o-title o-title--page">Delete your comment history</h1>

            <p>Link Expired. This link is no longer valid.</p>
            <p>If you still wish to delete your comment history you will need to
                <a href="<?= get_permalink() ?>">ask again</a>
            </p>

            <?php
        } else {
            $user_id = $check_result->ID;

            if (is_numeric($user_id)) {
                $query = new WP_Comment_Query;
                $comments = $query->query(array(
                    'user_id' => $user_id,
                    'type' => 'comment',
                    'fields' => 'ids',
                    'status' => 'any',
                ));

                foreach ($comments as $comment) {
                    wp_delete_comment($comment, true);
                }

                require_once(ABSPATH . 'wp-admin/includes/user.php');
                wp_delete_user($user_id);
            }

            ?>

            <h1 class="o-title o-title--page">Your MoJ Intranet comments have been deleted</h1>

            <p>You will need to register your email address again to make comments.</p>
        <?php
        }
    } else {
        if (is_user_logged_in()) {
            global $current_user;
            wp_get_current_user();

            if (!empty($_POST['btndelete'])) {
                $delete_link = get_bloginfo('url') . '/delete-account?key=' . get_password_reset_key(
                        $current_user
                    ) . '&login=' . rawurlencode($current_user->user_login);

                add_filter('intranet_mail_templates', function ($templates) use ($delete_link, $current_user) {
                    $template = $templates['email']['comment-deletion'];
                    $template['personalisation']['name'] = $current_user->display_name;
                    $template['personalisation']['delete_link'] = $delete_link;
                    return $template;
                }, 10, 1);

                wp_mail($current_user->user_email, 'default', 'default');

                ?>
                <h1 class="o-title o-title--page">We have sent you a confirmation email</h1>
                <p>Check your email inbox and click the link in the email to delete your MoJ Intranet comments.</p>
                <?php
            } else {
                ?>
                <h1 class="o-title o-title--page">Delete your comment history</h1>
                <p>We will send an email to confirm you want to delete comments on the MoJ Intranet associated with the
                    following details:</p>

                <p>Commenting as: <?= $current_user->display_name ?></p>
                <p>Email Address: <?= $current_user->user_email ?></p>

                <form method="post" action="?#respond">
                    <button type="submit" name="btndelete" class="button" value="1">Continue</button>
                </form>

                <?php
            }
        } else { ?>
            <h1 class="o-title o-title--page">Delete your comment history</h1>
            <p class="must-log-in">For security you have to be logged in to delete comments. Please <a
                        href="<?= wp_login_url(get_permalink()) ?>">Login</a></p>';
            <?php
        }
    }
    ?>
</section>
