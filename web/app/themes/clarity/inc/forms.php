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
  $oAgency = new Agency();
  $activeAgency = $oAgency->getCurrentAgency();
  $agency = $activeAgency['shortcode'];
  $agencymail = '';

  if ($agency === 'hmcts') {
    $agencymail = 'INTRANET.ENQUIRIES2@Justice.gov.uk';
  }
  elseif ($agency === 'opg') {
    $agencymail = 'communications@publicguardian.gov.uk';
  }
  elseif ($agency === 'laa') {
    $agencymail = 'CommunicationsDepartment@Justice.gov.uk';
  }
  elseif ($agency === 'cica') {
    $agencymail = 'Internal.Comms@cica.gov.uk';
  }
  else {
    $agencymail = 'intranet@justice.gov.uk';
  }

  // this function here styles the emails.
  add_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );

  if (isset($_POST['submit'])) {
    $to = $agencymail;
    $subject = 'Feedback Form ['. current_time('d-m-Y g:i').']';
    $headers = array('Content-Type: text/html; charset=UTF-8', 'From: Intranet Feedback Form');
    $message  = "Name: " . $_POST['fbf_name'] ."\n";
    $message .= "Email: " . $_POST['fbf_email'] ."\n";
    $message .= "Message: " . $_POST['fbf_message'] ."\n\n";
    $message .= "Client info\n";
    $message .= "Page URL: " . get_permalink() . "\n";
    $message .= "Agency: " . $_POST['fbf_agency'] . "\n";
    $message .= "Referrer: " . $_SERVER['HTTP_REFERER'] ."\n";
    $message .= "User agent: " . $_SERVER['HTTP_USER_AGENT'] . "\n";

    wp_mail($to, $subject, $message, $headers);

    $feedback_email = $_POST['fbf_name'];
    $feedback_subject = 'Page feedback - MoJ Intranet [T'. get_the_date('Ymdgi').']';
    $feedback = "Thank you for contacting us. \n";
    $feedback .= "Your feedback matters – it helps us find out what we need to improve so that we can offer you a better intranet experience. \n";
    $feedback .= "Your query has been logged and we will deal with it as soon as possible.";
    wp_mail($feedback_email, $feedback_subject, $feedback);
  }

  // Reset content-type to avoid conflicts -- https://core.trac.wordpress.org/ticket/23578
  remove_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );

  // this function here styles the emails.
  function wpdocs_set_html_mail_content_type() {
    return 'text/html';
  }

}
