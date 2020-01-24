<?php  if ( ! defined( 'ABSPATH' ) ) exit;
ob_start();

global $wpdb;

$first_name = isset($_POST['fname']) ? sanitize_text_field($_POST['fname']):'';
$last_name =	isset($_POST['lname']) ? sanitize_text_field($_POST['lname']):'';
$reg_email =isset($_POST['email']) ? sanitize_text_field($_POST['email']):'';
$reg_password =isset($_POST['password']) ? sanitize_text_field($_POST['password']):'';

$arr_name = explode("@",$reg_email);  
						
$temp = $arr_name[0];

if( !email_exists($profile_email) && username_exists( $temp )){
			
	$profile_id=rand(1000, 9999);
	
	$temp=$temp.$profile_id;
	
}

$user = get_user_by( 'email',$reg_email );
$error_report=0;
if(is_email($reg_email))
{ 	

	if($user->user_email == $reg_email)
	{

		$error_report = array("status"=>"0", "error"=>"An account is already registered with your email address. Please login");
		// echo json_encode($error_report);
		// exit;
	}
	else
	{
		$password_generated = true;
		$userdata=array("role"=>"customer",
													
								"first_name"=>$first_name,
								
								"last_name"=>$last_name,

								"user_email"=>$reg_email,
								
								"user_login"=>$temp,
								
								"user_pass"=>$reg_password);
								
		if($user_id = wp_insert_user( $userdata ))
		{	
			wp_new_user_notification( $user_id, $reg_password );
			update_user_meta( $user_id, 'refer_code_applied',"0");
			update_user_meta( $user_id, 'referal_code_users',array());
			$error_report = array("status"=>"1","user_id"=>$user_id,"refer_earn"=>true);
												 
		}else{
			$error_report = array("status"=>"0", "error"=>"Account is not created.");
			
		}
	}
}
?>