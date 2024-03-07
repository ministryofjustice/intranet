<!-- c-register starts here -->
<?php

if (!defined('ABSPATH')) {
    die();
}

$err = $err_name = $err_email = '';
$success = false;
$first_name = '';
$email = '';

$c_register_post_array = $_POST;

global $wpdb, $PasswordHash, $current_user, $user_ID;

if (isset($c_register_post_array['task']) && $c_register_post_array['task'] == 'register') {
    $first_name = esc_sql(trim($c_register_post_array['first_name']));
    $email = trim($c_register_post_array['email']);
    $username = $email;

    /**
     * Test the given name
     */
    if ($first_name == '') {
        $err_name = 'Enter a screen name';
    }

    /**
     * Test the given email
     */
    switch (true) {
        case $email == '':
            $err_email = 'Enter an email address';
            break;
        case !filter_var($email, FILTER_VALIDATE_EMAIL):
            $err_email = 'Enter a valid email address';
            break;
        case email_exists($email):
            $err_email = 'This email already exists';
            break;
        case !is_gov_email($email):
            $err_email = 'Enter an MoJ email address';
            break;
        default:
            $err_email = '';
    }

    if ($err_name == '' && $err_email == '') {
        $create_user = wp_insert_user([
                'first_name' => apply_filters('pre_user_name', $first_name),
                'user_pass' => apply_filters('pre_user_user_pass', wp_generate_password()),
                'user_login' => apply_filters('pre_user_user_login', $username),
                'user_email' => apply_filters('pre_user_user_email', $email),
                'role' => 'subscriber'
        ]);

        if (is_wp_error($create_user)) {
            $err = '<p class="error-message">There was an error creating your account.</p>';
        } else {
            do_action('user_register', $create_user);

            $user = get_user_by('email', $email);
            $rp_key = get_password_reset_key($user);
            $user_name = $user->display_name;
            $rp_link = network_site_url(
                "wp-login.php?action=rp&key=$rp_key&login=" . rawurlencode($user->user_login),
                'login'
            );

            add_filter('intranet_mail_templates', function ($templates) use ($rp_link, $user_name) {
                $template = $templates['email']['comment-registration'];
                $template['personalisation']['name'] = $user_name;
                $template['personalisation']['reply_link'] = $rp_link;
                return $template;
            }, 10, 1);

            wp_mail($email, 'default', 'default');

            $success = true;
        }
    } else {
        if ($err_name != '') {
            $err .= "<p><a href='#name-error' class='error-message'>$err_name</a></p>";
        }

        if ($err_email != '') {
            $err .= "<p><a href='#email-error' class='error-message'>$err_email</a></p>";
        }
    }
}

?>
<div class="c-register">

    <!--display error/success message-->

    <?php
    if (!empty($err)) :
        echo '<div id="message" class="error" aria-labelledby="error-summary-title" role="alert">' .
                '<h2 id="error-summary-title" class="error-title">There is a problem</h2>' .
                    $err .
             '</div>';
    endif;
    ?>

    <?php
    if ($success) :
        ?>
        <div id="message" class="success">
            <p><strong>Now check your email</strong></p>
            <p>We're sending an email to <?php echo $email; ?>. This can take up to 5 minutes.</p>

            <p>Open the email and click on the link. This will take you to the reset password page, where you would need
                to
                finish the registration.</p>

            <p><strong>Any problems?</strong></p>
            <p>The email will be from <a href="mailto:intranet-support@digital.justice.gov.uk"
                                         target="_blank">intranet-support@digital.justice.gov.uk</a>.
            <p>

            <p>If you can’t find it, check your junk folder then add the address to your safe list.
            </p>

            <p>Do not reply to the email.</p>
        </div>
        <?php
    endif;
    ?>

    <p>Fill in your details. We’ll then send you a link back to this page so you can start commenting.</p>

    <form method="post" action="?#respond" novalidate>
        <div <?php if ($err_name != '') {
            echo 'class="error-state"';
             } ?>>
            <p class="label-paragraph"><label for="first_name">Screen name (will appear on screen)</label></p>
            <?php if (trim($err_name) != '') { ?>
                <p id="name-error" class="error-message">
                    <span class="govuk-visually-hidden">Error:</span> <?php echo $err_name; ?>
                </p>
            <?php } ?>
            <p><input type="text" value="<?php echo $first_name; ?>" name="first_name" id="first_name"/></p>
        </div>
        <div <?php if ($err_email != '') {
            echo 'class="error-state"';
             } ?>>
            <p class="label-paragraph"><label for="email">Email address (will not be shown with your comment)</label>
            </p>
            <?php if ($err_email != '') { ?>
                <p id="email-error" class="error-message">
                    <span class="govuk-visually-hidden">Error:</span> <?php echo $err_email; ?>
                </p>
            <?php } ?>
            <p><input type="email" value="<?php echo $email; ?>" name="email" id="email"/></p>
        </div>
        <button type="submit" name="btnregister" class="button">Register</button>
        <input type="hidden" name="task" value="register"/>

    </form>
</div>
<!-- c-register ends here -->
