<?php if (!defined('ABSPATH')) die();

class User_model extends MVC_model {
  /** creates a new user
   * @param {Array} $data User data
   * @return {Integer} The ID of the newly created user
   */
  public function create($data) {
    return wp_insert_user($data);
  }

  /** updates a user
   * @param {Integer} $user_id User ID
   * @param {Array} $data User data
   * @return {Boolean} True on successs, False on error
   */
  public function update($user_id, $data) {
    unset($data['ID']);
    $result = $this->wpdb->update($this->wpdb->users, $data, array('ID' => $user_id));
    return $result !== false;
  }

  public function set_activation_key($user_id) {
    $user = get_userdata($user_id);

    $key = $this->generate_activation_key($user->user_login);

    //update user account
    $data = array(
      'user_activation_key' => $key['hashed']
    );
    $where = array(
      'user_login' => $user->user_login
    );
    $this->wpdb->update($this->wpdb->users, $data, $where);

    return $key;
  }

  public function generate_activation_key($user_login) {
    $key = wp_generate_password(20, false);
    do_action('retrieve_password_key', $user_login, $key);

    if(!class_exists('PasswordHash')) {
      require_once ABSPATH . WPINC . '/class-phpass.php';
    }

    $wp_hasher = new PasswordHash(8, true);

    return array(
      'value' => $key,
      'hashed' => time() . ':' . $wp_hasher->HashPassword($key)
    );
  }

  public function get_activation_email_content($user_id, $reset = false) {
    $user = get_userdata($user_id);

    //send email to user
    $data = array(
      'name' => $user->first_name,
      'activation_url' => network_site_url("/password/set/?key=".$key['value']."&login=" . rawurlencode($user->user_login), 'login')
    );
    $message = $this->view('email/password', $data, true);

    return array(
      'subject' => $subject,
      'message' => $message
    );
  }

  public function get_user_id_by_display_name($display_name) {
    if(!$user = $this->wpdb->get_row($this->wpdb->prepare(
      "SELECT `ID` FROM $this->wpdb->users WHERE `display_name` = %s", $display_name
    )))
    return false;

    return $user->ID;
  }
}
