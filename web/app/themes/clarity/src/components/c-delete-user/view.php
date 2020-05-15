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
            <p>If you still wish to delete your comment history you will need to <a
                        href="<?php echo get_permalink(); ?>">ask again</a></p>

            <?php
        } else {

            $user_id = $check_result->ID;

            if(is_numeric($user_id)) {
                require_once(ABSPATH.'wp-admin/includes/user.php');
                wp_delete_user($user_id);
            }

            ?>

            <h1 class="o-title o-title--page">Your MoJ Intranet comments have been deleted</h1>

            <p>You will need to register your email address again to make comments.</p>
        <?php } ?>
        <?php

    } else {
        if (is_user_logged_in()) {
            global $current_user;
            wp_get_current_user();

            if (!empty($_POST['btndelete'])) {

                $to = $current_user->user_email;

                $delete_key = get_password_reset_key($current_user);
                $btn_link = '<a style="display:inline-block;padding:8px 15px 5px;background-color:#00823b;color:#ffffff;font-size:19px;font-family:Arial,sans-serif;line-height:25px;text-decoration:none;vertical-align:top" href="' . get_bloginfo('url') . '/delete-account?key= . ' . $delete_key . '&login=' . rawurlencode($current_user->user_login) . '">I want to delete all of my MOJ Intranet comments</a>';

                $subject = 'Delete your MoJ Intranet comments';
                $body =
                    '<div style="background-color:black">
						<p style="color:#fff">
						<img src="' . get_stylesheet_directory_uri() . '/dist/images/moj_logo_email.png" alt="Ministry of Justice" height="36px" style="padding:20px 40px" class="CToWUd">
						</p>
					</div>
					<p style="padding:5px 0;font-size:19px;font-family:Arial,sans-serif">Hello,</p>
					<p style="padding:5px 0;font-size:19px;font-family:Arial,sans-serif">Click the link to delete all MoJ Intranet comments associated with your name and email address:</p>' .
                    $btn_link .
                    '<br/>
					<p style="padding:5px 0;font-size:19px;font-family:Arial,sans-serif"><strong>Any problems?</strong></p>
					<p style="padding:5px 0;font-size:19px;font-family:Arial,sans-serif">If this link has expired, you’ll need to ask again to delete your comment history. If you don’t want to delete your MoJ Intranet comments, ignore this email.</p>
					<p style="padding:25px 0 5px;font-size:16px;font-family:Arial,sans-serif;color:#6f777b">This email is generated automatically. Do not reply.</p>
					<div style="background-color:#dee0e2">
						<p style="padding:20px;font-size:16px;font-family:Arial,sans-serif">
							If you\'re unsure an email is from the MoJ, forward it to <a href="mailto:phishing@digital.justice.gov.uk" target="_blank">phishing@digital.justice.gov.<wbr>uk</a>.
						</p>
          			</div>';
                $headers = array('Content-Type: text/html; charset=UTF-8');

                wp_mail($to, $subject, $body, $headers);

                ?>
                <h1 class="o-title o-title--page">We have sent you a confirmation email</h1>
                <p>Check your email inbox and click the link in the email to delete your MoJ Intranet comments.</p>
                <?php

            } else {
                ?>
                <h1 class="o-title o-title--page">Delete your comment history</h1>
                <p>We will send an email to confirm you want to delete comments on the MoJ Intranet associated with the following details:</p>

                <p>Commenting as: <?php echo $current_user->display_name; ?></p>
                <p>Email Address: <?php echo $current_user->user_email; ?></p>

                <form method="post" action="?#respond">
                    <button type="submit" name="btndelete" class="button" value="1">Continue</button>
                </form>

                <?php
            }
        } else { ?>
            <h1 class="o-title o-title--page">Delete your comment history</h1>
            <p class="must-log-in">For security you have to be logged in to delete comments. Please <a
                        href="<?php echo wp_login_url(get_permalink()); ?>">Login</a></p>';
            <?php
        }
    }
    ?>
</section>
