<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/* include("../wp-config.php");

require( realpath(__DIR__ . '/../wp-load.php'));

include_once('./autoload.php'); */

global $wpdb, $wp_hasher;
	
	$user_id = isset($_REQUEST['email'])?email_exists( $_REQUEST['email']):false;
	
	if($user_id == false) {
		$responce=array( 'code' => 0,'message'=>'User is not exist.');
	}
	
	$user = get_userdata( $user_id );
	
	$user_login=isset($user->user_login)?$user->user_login :'';
	
	
	// Generate something random for a password reset key.
	$key = wp_generate_password( 20, false );

	/** This action is documented in wp-login.php */
	// do_action( 'retrieve_password_key', $user->user_login, $key );

	// Now insert the key, hashed, into the DB.
	if ( empty( $wp_hasher ) ) {
		require_once ABSPATH . WPINC . '/class-phpass.php';
		$wp_hasher = new PasswordHash( 8, true );
	}
	$hashed = time() . ':' . $wp_hasher->HashPassword( $key );
	$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user_login) );

	$message = __("Someone requested that the password be reset for the following account:") . "\r\n\r\n";
	$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
	$message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n";
	$message = __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n";
	$message .= __('To reset your password, visit the following address:') . "\r\n\r\n";
	$message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . ">\r\n\r\n";
	
	if(!empty($user->user_email)){
		$success = wp_mail($user->user_email, sprintf(__('Password Reset for [%s]'), $user_login), $message);
	}else{
		$success=false;
	}
	
	
	
	if($success == 1) {
		
		$responce=array( 'code' => 1,'message'=>'Your password reset link send to your email.');
		
		// echo json_encode();
		
	}else{
		
		$responce=array( 'code' => 0);
		
	}
?>