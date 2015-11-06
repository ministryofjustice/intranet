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
    $email = ($tag == 'search-results') ? $this->alt_email . ';' . $this->email : $this->email;
    $message = $this->compose();

    mail($email, $subject, implode(self::$nl, $message));

    $this->_output_json();
  }

  private function compose() {
    $message = array();

    $url = $_POST['url'];
    $user_agent = $_POST['user_agent'];
    $description = $_POST['description'];
    $resolution = $_POST['resolution'];
    $username = $_POST['username'];
    $email = $_POST['email'];

    $message[] = 'User: ' . $username . ' (' . $email . ')';
    $message[] = self::$nl;
    $message[] = $description;
    $message[] = self::$nl;
    $message[] = str_repeat('-', 71);
    $message[] = 'Client info:';
    $message[] = 'Page URL: '.$url;
    $message[] = 'User agent: '.$user_agent;
    $message[] = 'Screen resolution: '.$resolution;
    $message[] = str_repeat('-', 71);

    return $message;
  }

  private function _output_json($data = array()) {
    header('Content-Type: application/json');
    echo json_encode(array('status' => 'ok'));
  }
}
