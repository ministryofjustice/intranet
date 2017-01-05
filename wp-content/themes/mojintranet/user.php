<?php
/**
 * User Auth Class
 */
class User extends MVC_controller {
  function __construct($param_string, $post_id) {
    parent::__construct($param_string, $post_id);

    $this->model('user');
    $this->model('bad_words');
  }

  function request() {
    if(is_user_logged_in()) wp_redirect('/'); // Redirect to home if logged in

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
      $val = new Validation();

      $email = trim($_POST['email']);
      $display_name = $_POST['display_name'];

      $is_email_filled = $val->is_filled('email', 'email', 'Please enter email');
      $is_display_name_filled = $val->is_filled('display_name', 'display name', 'Please enter display name');

      if($is_email_filled) {
        $val->is_valid_email('email', 'email', 'Please enter valid email');

        if(!$this->model->user->is_gov_email($email)) {
          $val->error('email', 'email', 'You need to use an MoJ email address');
        }
      }

      if($is_display_name_filled) {
        if($this->model->bad_words->has_bad_words($display_name)) {
          $val->error('display_name', 'display name', 'This display name contains banned word(s)');
        }
      }

      if(!$val->has_errors()) {

        if(!email_exists($email)) {
          //TO DO set password here
          $user_id = $this->model->user->create(array(
              'user_login' => $email,
              'user_email' => $email,
              'display_name' => $display_name
          ));
        }
        else {
          //temp user displayname
          $user = get_user_by('email', $email);
          $user_id = $user->ID;
          $this->model->user->update_meta($user_id, 'dw_temp_display_name', $display_name);
          
        }

        $key = $this->model->user->set_activation_key($user_id);

        //send email to user
        $data = array(
          'name' => $display_name,
          'activation_url' => network_site_url("/user/auth/?key=".$key."&login=" . rawurlencode($email), 'login')
        );

        $message = $this->view('email/activate_account', $data, true);

        html_mail($email, 'Activate your MoJ Intranet account', $message);
      }

      $this->output_json(array(
        'success' => !$val->has_errors(),
        'validation' => $val->get_errors()
      ));
    }
  }

  function auth() {

    if(!$this->_is_expired()) {

      //TO DO Check key
      wp_signon(array(
          'user_login' => $user->data->user_login,
          'user_password' => $password
      ));
    }
  }

  private function _is_expired() {
    $email = $_GET['login'];
    $user = get_user_by('email', $email);
    $key = $_GET['key'];
    $user = check_password_reset_key($key, $user->data->user_login);

    return is_array($user->errors);
  }
}
