<?php
// Mail Functions
use Alphagov\Notifications\Client as Client;
use Alphagov\Notifications\Exception\ApiException;

if (defined('SMTP_HOST') && SMTP_HOST !== "") {
    add_filter('wp_mail_from', fn() => 'intranet-support@digital.justice.gov.uk');
    add_filter('wp_mail_from_name', fn() => 'Intranet support');
}

//remove site name from email subject
add_filter('wp_mail', function ($attrs) {
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
    $attrs['subject'] = str_replace("[" . $blogname . "] -", "", $attrs['subject']);
    $attrs['subject'] = str_replace("[" . $blogname . "]", "", $attrs['subject']);
    $attrs['subject'] = trim($attrs['subject']);
    return $attrs;
}, 2, 1);


/**
 * Set up the default filter (example)
 * Use a filter like this to modify email or SMS content
 *
 * Call it just before you send an email using wp_mail()
 */
add_filter('intranet_mail_settings', function ($templates, $attrs) {
    // default
    $default = $templates['email']['default'];

    // personalisation
    $default['personalisation']['subject'] = $attrs['subject'];
    $default['personalisation']['message'] = $attrs['message'];

    return $default;
}, 2, 2);


/**
 *  Redirect mail to Gov.UK Notify
 */
add_filter('pre_wp_mail', function ($null, $mail) {
    // Don't short-circuit if the password doesn't look right
    $maybe_api_key = env('SMTP_PASSWORD');
    preg_match_all('/[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-[a-f0-9]{4}\-[a-f0-9]{12}/', $maybe_api_key, $matches);
    if (count_chars($maybe_api_key) < 73 && count($matches[0]) < 2) {
        // hand back to wp_mail()
        return null;
    }

    // Set up Gov Notify client
    $client = new Client([
        'apiKey' => $maybe_api_key,
        'httpClient' => new \Http\Adapter\Guzzle7\Client
    ]);

    $templates = require 'mail-templates.php';

    /**
     * Filters the Intranet mail settings, in the form of Gov Notify args
     *
     * @param array $settings
     */
    $settings = apply_filters('intranet_mail_settings', $templates, $mail);

    $to = $mail['to'];
    $message = $mail['message'];
    $subject = $mail['subject'];
    $headers = $mail['headers'];
    $attachments = $mail['attachments'];

    if (isset($mail['to'])) {
        $to = $mail['to'];
    }

    if (!is_array($to)) {
        $to = explode(',', $to);
    }

    $mail_data = compact('to', 'subject', 'message', 'headers', 'attachments', 'settings');

    if (empty($settings)) {
        do_action('wp_mail_failed', new WP_Error('wp_mail_failed', "Gov Notify: No settings were found.", $mail_data));
    }

    foreach ($to as $recipient) {
        // Send!
        try {
            $id = $settings['id'];
            $placeholders = $settings['personalisation'] ?? [];
            $ref = $settings['reference'] ?? '';
            $reply_id = $settings['reply_to_id'] ?? null;

            /**
             * Supports SMS, plus email delivery
             * * * * * * * * * * * * * * * * * * * * */
            $response = (strpos($recipient, "@") > 0)
                ? $client->sendEmail($recipient, $id, $placeholders, $ref, $reply_id)
                : $client->sendSms($recipient, $id, $placeholders, $ref, $reply_id);

            $mail_data['gov_notify_success'] = $response;
            do_action('wp_mail_succeeded', $mail_data);
        }
        catch (ApiException $ex) {
            $mail_data['gov_notify_exception_code'] = $ex->getCode();
            do_action('wp_mail_failed', new WP_Error('wp_mail_failed', $ex->getMessage(), $mail_data));
        }
    }

    return true;
}, 8, 2);
