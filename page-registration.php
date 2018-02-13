<?php  
/* 
Template Name: Register 
*/  
use MOJ\Intranet\Agency;
/* 
Template Name: Register 
*/
if (!defined('ABSPATH')) {
    die();
}

get_header();?>   

<?php 
	$err = '';
	$success = '';

	global $wpdb, $PasswordHash, $current_user, $user_ID;

	if(isset($_POST['task']) && $_POST['task'] == 'register' ) {


		$pwd1 = esc_sql(trim($_POST['pwd1']));
		$pwd2 = esc_sql(trim($_POST['pwd2']));
		$first_name = esc_sql(trim($_POST['first_name']));
		$email = esc_sql(trim($_POST['email']));
		$username = esc_sql(trim($_POST['username']));

		if( $email == "" || $pwd1 == "" || $pwd2 == "" || $username == "" || $first_name == "" ) {
			$err = 'Please don\'t leave the required fields.';
		} else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$err = 'Invalid email address.';
		} else if(email_exists($email) ) {
			$err = 'Email already exist.';
		} else if($pwd1 <> $pwd2 ){
			$err = 'Password do not match.';
		} else {

			$create_user = wp_insert_user( array (
                'first_name' => apply_filters('pre_user_name', $first_name), 'user_pass' => apply_filters('pre_user_user_pass', $pwd1), 'user_login' => apply_filters('pre_user_user_login', $username), 'user_email' => apply_filters('pre_user_user_email', $email), 
                'role' => 'subscriber' ) );
			if( is_wp_error($create_user) ) {
				$err = 'Error on user creation.';
			} else {
                do_action('user_register', $create_user);
                global $wp;
                $current_url = home_url( $wp->request );
                $to = $email;
                
				$user = get_user_by( 'email', $email );
				$user_id_number = $user->ID;
				echo 'user id is = ' . $user_id_number;
                $rp_key = get_password_reset_key( $user );
                $user_login = $user->user_login;
				$rp_link = '<a href="' . network_site_url("wp-login.php?action=rp&key=$rp_key&login=" . rawurlencode($user_login), 'login') . '&redirect_to='.get_bloginfo('url').'">' . network_site_url("wp-login.php?action=rp&key=$rp_key&login=" . rawurlencode($user_login), 'login') . '</a>';

                $subject = 'You are now registrated on our site MoJ Intranet:';
                $body = 'Click here '. $rp_link;
                $headers = array('Content-Type: text/html; charset=UTF-8');
                
                wp_mail( $to, $subject, $body, $headers );

				$success = 'You\'re successfully register';
				
			}

		}

	}
	?>

        <!--display error/success message-->
	<div id="message">
		<?php
			if(! empty($err) ) :
				echo '<p class="error">'.$err.'';
			endif;
		?>

		<?php
			if(! empty($success) ) :
				echo '<p class="error">'.$success.'';
			endif;
		?>
	</div>

	<form method="post">
		<h3>Don't have an account?<br /> Create one now.</h3>
		<p><label>First Name</label></p>
		<p><input type="text" value="" name="first_name" id="first_name" /></p>
		<p><label>Email</label></p>
		<p><input type="email" value="" name="email" id="email" /></p>
		<p><label>Username</label></p>
		<p><input type="text" value="" name="username" id="username" /></p>
		<p><label>Password</label></p>
		<p><input type="password" value="" name="pwd1" id="pwd1" /></p>
		<p><label>Password again</label></p>
		<p><input type="password" value="" name="pwd2" id="pwd2" /></p>
		<div class="alignleft"><p><?php if($success != "") { echo $success; } ?> <?php if($err != "") { echo $err; } ?></p></div>
		<button type="submit" name="btnregister" class="button" >Submit</button>
		<input type="hidden" name="task" value="register" />
	</form>

</div>

<?php get_footer(); ?>