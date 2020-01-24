<?php  if ( ! defined( 'ABSPATH' ) ) exit;

$user_data =  json_decode(file_get_contents('php://input'),true);

global $wpdb;

$login_email = sanitize_email($user_data['email']);

$login_password =  sanitize_text_field($user_data['password']);

$user = wp_authenticate($login_email, $login_password);


if (!is_wp_error($user)) {
	 
	if(wp_check_password( $login_password, $user->user_pass))
	{
		
		wp_set_current_user( $user->ID );

		wp_set_auth_cookie($user->ID);
		
		$userdata= format_customer_response($user, $_REQUEST);
		
		$login_report = array("code"=>"1","details"=>$userdata);		
		
	}
	else
	{
		$login_report = array("code"=>"0", "message"=>"Password is incorrect !!");
	}
	
}else{
	$login_report = array("code"=>"0", "message"=>"Incorrect username/password !!");
}
?>