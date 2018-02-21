<!-- c-register starts here -->
<?php   

if (!defined('ABSPATH')) {
    die();
}

	$err = '';
	$success = '';

	global $wpdb, $PasswordHash, $current_user, $user_ID;

	if(isset($_POST['task']) && $_POST['task'] == 'register' ) {

		$pwd1 = wp_generate_password();;
		$first_name = esc_sql(trim($_POST['first_name']));
		$email = esc_sql(trim($_POST['email']));
		$username = $email;

		if( $email == "" || $first_name == "" ) {
			$err = 'Please don\'t leave the required fields.';
		} elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$err = 'Invalid email address.';
		} elseif(email_exists($email) ) {
			$err = 'Email already exist.';
		} elseif(!is_gov_email($email)){
			$err = 'You need to use an MoJ email address';
		} else {

			$create_user = wp_insert_user( array (
                'first_name' => apply_filters('pre_user_name', $first_name), 'user_pass' => apply_filters('pre_user_user_pass', $pwd1), 'user_login' => apply_filters('pre_user_user_login', $username), 'user_email' => apply_filters('pre_user_user_email', $email), 
                'role' => 'subscriber' ) );
			if( is_wp_error($create_user) ) {
				$err = 'Error on user creation.';
			} else {
                do_action('user_register', $create_user);
				
                $to = $email;
                
				$user = get_user_by( 'email', $email );
				$user_id_number = $user->ID;
                $rp_key = get_password_reset_key( $user );
                $user_login = $user->user_login;
				$rp_link = '<a style="display:inline-block;padding:8px 15px 5px;background-color:#00823b;color:#ffffff;font-size:19px;font-family:Arial,sans-serif;line-height:25px;text-decoration:none;vertical-align:top" href="' . network_site_url("wp-login.php?action=rp&key=$rp_key&login=" . rawurlencode($user_login), 'login') .'"> Reset Password </a>';

                $subject = 'You are now registrated on our site MoJ Intranet:';
				$body = 
					'<div style="background-color:black">
						<p style="color:#fff">
						<img src="https://peoplefinder.service.gov.uk/assets/moj_logo_horizontal_36x246-90c698afdefe7275f7580065062aebc6.png" alt="Ministry of Justice" height="36px" style="padding:20px 40px" class="CToWUd">
						</p>
					</div>
					<p style="padding:5px 0;font-size:19px;font-family:Arial,sans-serif">Hello,</p>
					<p style="padding:5px 0;font-size:19px;font-family:Arial,sans-serif">To add your comments to the intranet, just click on the button below and finish the registration</p>'.
					$rp_link. 
					'<br/>
					<p style="padding:5px 0;font-size:19px;font-family:Arial,sans-serif">This will take you to the intranet reset password page where you need to set your password.</p>
					<p style="padding:5px 0;font-size:19px;font-family:Arial,sans-serif"><strong>Any problems?</strong></p>
					<p style="padding:5px 0;font-size:19px;font-family:Arial,sans-serif">If this link has expired, you’ll need to fill in your details again to get another link. If you don’t want to comment on the intranet, ignore this email.</p>
					<p style="padding:25px 0 5px;font-size:16px;font-family:Arial,sans-serif;color:#6f777b">This email is generated automatically. Do not reply.</p>
					<div style="background-color:#dee0e2">
						<p style="padding:20px;font-size:16px;font-family:Arial,sans-serif">
							If you\'re unsure an email is from the MoJ, forward it to <a href="mailto:phishing@digital.justice.gov.uk" target="_blank">phishing@digital.justice.gov.<wbr>uk</a>.
						</p>
          			</div>';
				$headers = array('Content-Type: text/html; charset=UTF-8');
                
				wp_mail( $to, $subject, $body, $headers );

				$success = 'You\'re successfully register';
				
			}

		}

	}

?>
<div class="c-register">
    
    <!--display error/success message-->
	
		<?php
			if(! empty($err) ) :
				echo '<div id="message" class="error">';
				echo '<p>'.$err.'</p>';
				echo '</div>';
			endif;
		?>

		<?php
			if(! empty($success) ) :
				?>
				<div id="message" class="success">
					<p><strong>Now check your email</strong></p>
					<p>We're sending an email to <?php echo $email; ?>. This can take up to 5 minutes.</p>

					<p>Open the email and click on the link. This will take you to the reset password page, where you would need to finish the registration.</p>

					<p><strong>Any problems?</strong></p>
					<p>The email will be from <a href="mailto:intranet-support@digital.justice.gov.uk" target="_blank">intranet-support@digital.justice.gov.uk</a>.<p>

					<p[>If you can’t find it, check your junk folder then add the address to your safe list.</p>

					<p>Do not reply to the email.</p>
				</div>
				<?php 
			endif;
		?>

	<p>Fill in your details. We’ll then send you a link back to this page so you can start commenting.</p>

	<form method="post">
		<p><label>Screen name (will appear on screen)</label></p>
		<p><input type="text" value="" name="first_name" id="first_name" /></p>
		<p><label>Email address (will not be shown with your comment)</label></p>
		<p><input type="email" value="" name="email" id="email" /></p>
		<button type="submit" name="btnregister" class="button" >Register</button>
		<input type="hidden" name="task" value="register" />
	</form>
</div>
<!-- c-register ends here -->