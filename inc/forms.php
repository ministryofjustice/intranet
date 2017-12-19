<?php

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
        $form = [
            'name'      => $_POST['fbf_name'],
            'email'     => $_POST['fbf_email'],
            'message'   => $_POST['fbf_message'],
            'agency'    => $_POST['agency'],
        ];
        $to = 'intranet@justice.gsi.gov.uk';
        $subject = 'Feedback Form';
        $message  = "Name: " . $form['name'] ."\n";
        $message .= "Email: " . $form['email'] ."\n";
        $message .= "Message: " . $form['message'] ."\n";
        $message .= "Client info:\n";
        $message .= "Page URL: " . get_permalink() . "\n";
        $message .= "Agency: " . $form['agency'] . "\n";
        $message .= "Referrer: " . $_SERVER['HTTP_REFERER'] ."\n";
        $message .= "User agent: " . $_SERVER['HTTP_USER_AGENT'] . "\n";
        $headers = "Content-Type: text/html; charset=UTF-8\n";
        $headers .= "From: Feedback form Intranet\n";

        wp_mail($to, $subject, $message, $headers);
        $feedback_email = $form['email'];
        $feedback_subject = 'Page feedback - MoJ Intranet [T'. get_the_date('Ymdgi').']';
        $feedback = "Thank you for contacting us. \n";
        $feedback .= "Your feedback matters – it helps us find out what we need to improve so that we can offer you a better intranet experience. \n";
        $feedback .= "Your query has been logged and we will deal with it as soon as possible.";
        wp_mail($feedback_email, $feedback_subject, $feedback);
    }
}
