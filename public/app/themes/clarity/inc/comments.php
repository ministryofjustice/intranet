<?php
/**
 *  Modifying comments futher.
 */
// Sanitize comments being entered into WP.
add_filter('preprocess_comment', 'sanitize_submitted_comment');

function sanitize_submitted_comment($comment_data)
{
    $comment_data['comment_content'] = wp_filter_post_kses($comment_data['comment_content']);
    return $comment_data;
}

add_filter('get_comment_date', 'convert_to_time_ago', 10, 3);

function convert_to_time_ago($date, $d, $comment): string
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
        __('<p class="must-log-in">You must <a href="%s">Register</a> or <a href="%s">Login</a> to post a comment.</p>'),
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
    $GLOBALS['comment'] = $comment;
    $options = get_option('maintenance_options', [
        'maintenance_mode_status' => 0,
        'maintenance_mode_message' => '',
    ]);
    $maintenance_mode = $options['maintenance_mode_status'] ?? false;
    ?>
    

    <li <?= comment_class() ?> id="comment-<?= comment_ID() ?>">
    <div class="comment-body" id="comment-<?= comment_ID() ?>">
    <div class="comment-author vcard">
        <cite class="fn">
            <span class="author"><?php printf(__('%s'), get_comment_author_link()) ?></span>
            <span class="dash">—</span>
            <span class="date"><?php printf(__('%1$s'), get_comment_date(), get_comment_time()) ?></span>
        </cite>
        <?php comment_text();

        if ($post_type != 'condolences' && !$maintenance_mode) { ?>
            <div class="reply">
                <?php
                $args['depth'] = $depth;
                comment_reply_link($args);
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
        $current_url = "//" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '#respond';

        $options = [
            'expires' => time() + (86400 * 30),
            'path' => "/",
            'domain' => "",
            'httponly' => true,
            'secure' => is_ssl()
        ];

        setcookie('referral_url', $current_url, $options);
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
    if (isset($_COOKIE['referral_url'])) {
        $options = [
            'expires' => time() - 60 * 60 * 24 * 90,
            'path' => "/",
            'domain' => "",
            'httponly' => false
        ];

        setcookie('referral_url', '', $options);
        unset($_COOKIE['referral_url']);
    }
}

add_filter('wp_login', 'unset_cookies_after_login', 10, 3);

function is_gov_email($email): bool
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
