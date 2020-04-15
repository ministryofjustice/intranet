<?php

?>
<section class="c-delete-user">
    <?php

    if (!empty($_GET['key'])) {
        $key = stripslashes($_GET['key']);
        $login = stripslashes($_GET['login']);
        $check_result = check_password_reset_key($key, $login);

        if (is_wp_error($check_result)) { ?>
            <h2>Link Expired</h2>

            <p>This link is no longer valid.</p>
            <p>If you still wish to delete your account you will need to make another <a
                        href="<?php echo get_permalink(); ?>"> account deletion request</a></p>

            <?php
        } else {

            $user_id = $check_result->ID;

            //wp_delete_user($user_id)

            ?>

            <h2>Account Deleted</h2>

            <p>Your account has been deleted</p>
        <?php } ?>
        <?php

    } else {
        if (is_user_logged_in()) {
            global $current_user;
            wp_get_current_user();

            if (!empty($_POST['btndelete'])) {

                $to = $current_user->user_email;

                $delete_key = get_password_reset_key($current_user);
                $btn_link = '<a style="display:inline-block;padding:8px 15px 5px;background-color:#00823b;color:#ffffff;font-size:19px;font-family:Arial,sans-serif;line-height:25px;text-decoration:none;vertical-align:top" href="' . get_bloginfo('url') . '/delete-account?key= . ' . $delete_key . '&login=' . rawurlencode($current_user->user_login) . '"> Delete Account</a>';

                $subject = 'Deletion of MoJ Intranet Account';
                $body =
                    '<div style="background-color:black">
						<p style="color:#fff">
						<img src="' . get_stylesheet_directory_uri() . '/dist/images/moj_logo_email.png" alt="Ministry of Justice" height="36px" style="padding:20px 40px" class="CToWUd">
						</p>
					</div>
					<p style="padding:5px 0;font-size:19px;font-family:Arial,sans-serif">Hello,</p>
					<p style="padding:5px 0;font-size:19px;font-family:Arial,sans-serif">To confirm the deletion of your MoJ Intranet account please click the link below</p>' .
                    $btn_link .
                    '<br/>
					<p style="padding:5px 0;font-size:19px;font-family:Arial,sans-serif"><strong>Any problems?</strong></p>
					<p style="padding:5px 0;font-size:19px;font-family:Arial,sans-serif">If this link has expired, you’ll need to request to delete your account again to get another link. If you don’t want to delete your account, ignore this email.</p>
					<p style="padding:25px 0 5px;font-size:16px;font-family:Arial,sans-serif;color:#6f777b">This email is generated automatically. Do not reply.</p>
					<div style="background-color:#dee0e2">
						<p style="padding:20px;font-size:16px;font-family:Arial,sans-serif">
							If you\'re unsure an email is from the MoJ, forward it to <a href="mailto:phishing@digital.justice.gov.uk" target="_blank">phishing@digital.justice.gov.<wbr>uk</a>.
						</p>
          			</div>';
                $headers = array('Content-Type: text/html; charset=UTF-8');

                wp_mail($to, $subject, $body, $headers);

                ?>
                <p>An email has been sent to your registered email address to confirm the deletion of your account.
                    Please click the link in this email to delete your account.</p>
                <?php

            } else {
                ?>

                <p>This will delete your account and all comments associated to your account. An email will be sent to
                    your
                    registered email address to confirm this deletion.</p>

                <p>You are currently logged in as:</p>
                <p>Screen name: <?php echo $current_user->display_name; ?></p>
                <p>Email Address: <?php echo $current_user->user_email; ?></p>

                <h2>Confirm Deletion</h2>

                <p>To confirm deletion please click the button below</p>

                <form method="post" action="?#respond">
                    <button type="submit" name="btndelete" class="button" value="1">Delete Account</button>
                </form>

                <?php
            }
        } else { ?>
            <p class="must-log-in">For security you have to be logged in to delete your account. Please <a
                        href="' . wp_login_url(get_permalink()) . '">Login</a></p>';
            <?php
        }
    }
    ?>
</section>
