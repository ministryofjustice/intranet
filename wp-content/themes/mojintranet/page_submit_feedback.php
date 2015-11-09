<?php if (!defined('ABSPATH')) die();

/**
 * Template name: Submit feedback
*/

class Page_submit_feedback extends MVC_controller {
  private $email = 'newintranet@digital.justice.gov.uk';
  private $alt_email = 'intranet@justice.gsi.gov.uk';
  private static $nl = "\r\n";

  function main() {
    $tag = $_POST['tag'];
    $subject = $_POST['subject'];
    $dest_email = ($tag == 'search-results') ? $this->alt_email . ';' . $this->email : $this->email;

    $this->username = $_POST['username'];
    $this->user_email = $_POST['email'];
    $this->url = $_POST['url'];
    $this->user_agent = $_POST['user_agent'];
    $this->description = $_POST['description'];
    $this->resolution = $_POST['resolution'];
    $this->referrer = $_POST['referrer'];

    mail($dest_email, $subject, $this->_get_message(), $this->_get_headers());

    $this->_output_json();
  }

  private function _get_headers() {
    $headers = array();

    $headers[] = 'From: Intranet <feedback@intranet.justice.gov.uk>';
    $headers[] = 'Reply-to: ' . $this->username . ' <' . $this->user_email . '>';

    return implode(self::$nl, $headers) . self::$nl;
  }

  private function _get_message() {
    $message = array();

    $message[] = '' . $this->username . ' <' . $this->user_email . '> says:';
    $message[] = self::$nl;
    $message[] = $this->description;
    $message[] = self::$nl;
    $message[] = str_repeat('-', 71);
    $message[] = 'Client info:';
    $message[] = 'Page URL: ' . $this->url;
    $message[] = 'Referrer: ' . $this->referrer;
    $message[] = 'User agent: ' . $this->user_agent;
    $message[] = 'Screen resolution: ' . $this->resolution;
    $message[] = str_repeat('-', 71);

    return implode(self::$nl, $message);
  }

  private function _output_json($data = array()) {
    header('Content-Type: application/json');
    echo json_encode(array('status' => 'ok'));
  }
}
