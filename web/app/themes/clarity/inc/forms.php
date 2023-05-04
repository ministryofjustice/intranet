<?php

use MOJ\Intranet\Agency;

if (!defined('ABSPATH')) {
    die();
}

/***
 *
 * Feedback Form
 * Two action occurs here.
 * This is the feedback form used everywhere. footer.php
 * - Mail to intranet@justice.gsi.gov.uk which captures the name,email,message,agency & client info
 * - Confirmation mail to the user
 *
 ***/
add_action('wp_head', 'feedback_form');

function feedback_form()
{
    if (isset($_POST['submit'])) {
        $oAgency = new Agency();
        $activeAgency = $oAgency->getCurrentAgency();
        $agency = $activeAgency['shortcode'];

        switch ($agency) {
            case 'hmcts':
                $agency_name = 'HM Courts & Tribunals Service - ';
                $agency_mail = 'hmcts.communications@justice.gov.uk';
                break;
            case 'opg':
                $agency_name = 'Office of the Public Guardian - ';
                $agency_mail = 'communications@publicguardian.gov.uk';
                break;
            case 'laa':
                $agency_name = 'Legal Aid Agency - ';
                $agency_mail = 'communicationsdepartment@justice.gov.uk';
                break;
            case 'cica':
                $agency_name = 'Criminal Injuries Compensation Authority - ';
                $agency_mail = 'internal.comms@cica.gov.uk';
                break;
            default:
                $agency_name = '';
                $agency_mail = 'intranet@justice.gov.uk';
        }

        $submit_data = [
            'date' => current_time('d-m-Y g:i'),
            'name' => sanitize_text_field($_POST['fbf_name']),
            'email_creator' => sanitize_text_field($_POST['fbf_email']),
            'message' => sanitize_text_field($_POST['fbf_message']),
            'agency' => sanitize_text_field($_POST['fbf_agency']),
            'page_url' => get_permalink(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT']
        ];

        // send feedback to communications team
        add_filter('intranet_mail_templates', function ($templates) use ($submit_data) {
            $template = $templates['email']['wrong-with-this-page'];
            $template['personalisation'] = $submit_data;
            return $template;
        }, 11, 1);

        wp_mail($agency_mail, 'no-value-here', 'no-value-here');

        // new filter for confirmation email
        add_filter('intranet_mail_templates', function ($templates) use ($submit_data, $agency_name) {
            $template = $templates['email']['wrong-with-this-page-confirmation'];
            $template['personalisation']['date'] = $submit_data['date'];
            $template['personalisation']['name'] = $submit_data['name'];
            $template['personalisation']['agency_name'] = $agency_name;
            return $template;
        }, 10, 1);

        wp_mail($submit_data['email_creator'], 'no-value-here', 'no-value-here');
    }
}
