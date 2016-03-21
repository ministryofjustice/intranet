<?php
/**
 * Custom registration page
 */
class Register extends MVC_controller {
  function __construct() {
    parent::__construct();

    $this->model('user');
    $this->model('bad_words');
  }

  function main() {
    if(is_user_logged_in()) wp_redirect('/'); // Redirect to home if logged in

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
      $val = new Validation();

      $email = trim($_POST['email']);
      $first_name = $_POST['first_name'];
      $display_name = $_POST['display_name'];

      $val->is_filled('first_name', 'first name', 'Please enter first name');
      $val->is_filled('surname', 'surname', 'Please enter surname');
      $is_email_filled = $val->is_filled('email', 'email', 'Please enter email');
      $is_reenter_email_filled = $val->is_filled('reenter_email', 're-enter email', 'Please re-enter email');
      $is_display_name_filled = $val->is_filled('display_name', 'display name', 'Please enter display name');

      if($is_email_filled && $is_reenter_email_filled) {
        if($val->is_valid_email('email', 'email', 'Please enter valid email')) {
          if(email_exists($email)) {
            $val->error('email', 'email', 'This email address is already in use');
          }
        }

        if(!$this->model->user->is_gov_email($email)) {
          $val->error('email', 'email', 'You need to use an MoJ email address');
        }
      }

      if($is_display_name_filled) {
        if($this->model->user->get_user_id_by_display_name($display_name) !== false) {
          $val->error('display_name', 'display name', 'This display name is already in use');
        }
        elseif($this->model->bad_words->has_bad_words($display_name)) {
          $val->error('display_name', 'display name', 'This display name contains banned word(s)');
        }
      }

      if(!$val->has_errors()) {
        $user_id = $this->model->user->create(array(
          'user_login' => $email,
          'user_email' => $email,
          'first_name' => $first_name,
          'last_name' => $_POST['surname'],
          'display_name' => $display_name
        ));

        $key = $this->model->user->set_activation_key($user_id);

        //send email to user
        $data = array(
          'name' => $first_name,
          'activation_url' => network_site_url("/password/set/?key=".$key."&login=" . rawurlencode($email), 'login')
        );

        $message = $this->view('email/activate_account', $data, true);

        html_mail($email, 'MoJ Intranet - Activate account', $message);
      }

      $this->output_json(array(
        'success' => !$val->has_errors(),
        'validation' => $val->get_errors()
      ));
    }
    else {
      if(have_posts()) the_post();
      $this->view('layouts/default', $this->get_data());
    }
  }

  private function get_data() {
    return array(
      'page' => 'pages/register/main',
      'template_class' => 'user-register',
      'cache_timeout' => 0 /* no cache */,
      'no_breadcrumbs' => true,
      'page_data' => array(
      )
    );
  }
}
