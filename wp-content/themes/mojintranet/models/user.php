<?php if (!defined('ABSPATH')) die();

class User_model extends MVC_model {
  private $valid_domains = array(
    'publicguardian.gsi.gov.uk',
    'digital.justice.gov.uk',
    'legalaid.gsi.gov.uk',
    'justice.gsi.gov.uk',
    'justice.gov.uk',
    'hmcts.gsi.gov.uk',
    'noms.gsi.gov.uk'
  );

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

  /** updates a user meta
   * @param {Integer} $user_id User ID
   * @param {String} $meta_key the meta key to update
   * @param {String} $meta_value meta value
   * @return {Boolean} True on successs, False on error
   */
  public function update_meta($user_id, $meta_key, $meta_value) {
    return (bool) update_user_meta($user_id, $meta_key, $meta_value);
  }

  /** sets the activation key on user's account
   * @param {Integer} $user_id User ID
   * @return {String} The unhashed activation key
   */
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

    return $key['value'];
  }

  /** generate activation key
   * @param {String} $user_login User login
   * $return {Array} The key and the hashed version of the key
   */
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

  /** gets the copy for the activation email
   * @param {Integer} $user_id User ID
   * @return {Array} (subject => Email Subject, message => Email Message)
   */
  public function get_activation_email_content($user_id) {
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

  /** gets user ID by display name
   * @param {String} $display_name Display name
   * @return {Boolean|Integer} False is user was not found, otherwise User ID
   */
  public function get_user_id_by_display_name($display_name) {
    $sql = $this->wpdb->prepare("SELECT `ID` FROM " . $this->wpdb->users . " WHERE `display_name` = '%s'", $display_name);
    $user = $this->wpdb->get_row($sql);
    return $user ? $user->ID : false;

    return $user->ID;
  }

  public function is_gov_email($email) {
    $parts = explode('@', $email);
    $domain = $parts[1];

    return in_array($domain, $this->valid_domains);
  }

  public function get_status() {
    $data = [
      'is_logged_in' => is_user_logged_in(),
      'timestamp' => time()
    ];

    if ($data['is_logged_in']) {
      $user_data = wp_get_current_user();

      $data['name'] = $user_data->data->display_name;
    }

    return $data;
  }
}
