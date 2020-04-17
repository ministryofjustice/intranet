<?php
/**
 *  Modifying comments futher.
 */
// Sanitizes comments being entered into WP.
add_filter('preprocess_comment', 'sanitize_submitted_comment');

function sanitize_submitted_comment($commentdata)
{
    $commentdata['comment_content'] = wp_filter_post_kses($commentdata['comment_content']);
    return $commentdata;
}

add_filter('get_comment_date', 'convert_to_time_ago', 10, 3);

function convert_to_time_ago($date, $d, $comment)
{
    return human_time_diff(get_comment_time('U'), current_time('timestamp')) . ' ago';
}

add_filter('comment_form_default_fields', 'remove_comment_website_field');

function remove_comment_website_field($fields)
{
    unset($fields['url']);
    return $fields;
}

add_filter('comment_form_defaults', 'remove_must_be_logged_in');

function remove_must_be_logged_in($fields)
{
    $fields['must_log_in'] = sprintf(
        __(
            '<p class="must-log-in">
    You must <a href="%s">Register</a> or
    <a href="%s">Login</a> to post a comment.</p>'
        ),
        wp_registration_url(),
        wp_login_url(apply_filters('the_permalink', get_permalink()))
    );
    return $fields;
}

// Allow Login Only from gov email addresses
function is_valid_email_domain($login, $email, $errors)
{
    $valid = false; // sets default validation to false
    foreach ($GLOBALS['valid_domains'] as $d) {
        $d_length = strlen($d);
        $current_email_domain = strtolower(substr($email, -($d_length), $d_length));
        if ($current_email_domain == strtolower($d)) {
            $valid = true;
            break;
        }
    }
    // Return error message for invalid domains
    if ($valid === false) {
        $errors->add('domain_whitelist_error', __('<strong>ERROR</strong>: Login is only allowed from .gov emails. If you think you are seeing this in error, please contact the Intranet Team.'));
    }
}

add_action('register_post', 'is_valid_email_domain', 10, 3);

function format_comment($comment, $args, $depth)
{

    $post_type = get_post_type();

    $GLOBALS['comment'] = $comment; ?>

    <li <?php comment_class(); ?> id="comment-<?php comment_ID() ?>">
    <div class="comment-body" id="comment-<?php comment_ID() ?>">
    <div class="comment-author vcard">
        <cite class="fn">
            <span class="author"><?php printf(__('%s'), get_comment_author_link()) ?></span>
            <span class="dash">—</span>
            <span class="date"><?php printf(__('%1$s'), get_comment_date(), get_comment_time()) ?></span>
        </cite>
        <?php comment_text(); ?>

        <?php if ($post_type != 'condolences') { ?>
            <div class="reply">
                <?php
                $replyorlogin = '<p class="must-log-in"><a href="' . wp_login_url() . '">Login</a> or <a href="' . wp_registration_url() . '">Register</a> to post a comment.</p>'
                ?>


                <?php

                comment_reply_link(
                    array_merge(
                        $args,
                        array(
                            'depth' => $depth,
                            'max_depth' => $args['max_depth'],
                        )
                    )
                );
                ?>
            </div>
            <div class="comment-block">
                <?php echo do_shortcode('[likebutton]'); ?>
            </div>
        <?php } ?>

    </div>

    <?php
}

function inject_url_cookies_into_header()
{
    if (isset($_POST['task']) && $_POST['task'] == 'register') {
        global $wp;
        $current_url = "//" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '#respond';
        setcookie('referral_url', $current_url, time() + (86400 * 30), "/");
    }
}

add_action('init', 'inject_url_cookies_into_header');

function my_login_redirect($redirect_to, $request, $user)
{
    if (!isset($_COOKIE['referral_url'])) {
        return $redirect_to;
    } else {
        return $_COOKIE['referral_url'];
    }
}

add_filter('login_redirect', 'my_login_redirect', 10, 3);

function unset_cookies_after_login()
{
    if (!isset($_COOKIE['referral_url'])) {
    } else {
        setcookie('referral_url', '', time() - 60 * 60 * 24 * 90, '/', '', 0, 0);
        unset($_COOKIE['referral_url']);
    }
}

add_filter('wp_login', 'unset_cookies_after_login', 10, 3);

function is_gov_email($email)
{
    $parts = explode('@', $email);
    $domain = $parts[1];

    return in_array($domain, $GLOBALS['valid_domains']);
}

function format_comment_closed($comment, $args, $depth)
{
    $GLOBALS['comment'] = $comment; ?>
<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">

    <div id="div-comment-8741" class="comment-body">
    <div class="comment-author vcard">
        <cite class="fn">
            <span class="author"><?php printf(__('%s'), get_comment_author_link()) ?></span>
            <span class="dash">—</span>
            <span class="date"><?php printf(__('%1$s'), get_comment_date(), get_comment_time()) ?></span>
        </cite>
        <?php comment_text(); ?>
    </div>
    <div class="comment-block">
        <?php echo do_shortcode('[likebutton]'); ?>
    </div>
    <?php
}
