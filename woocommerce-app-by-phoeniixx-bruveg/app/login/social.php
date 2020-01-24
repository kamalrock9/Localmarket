<?php

 if ( ! defined( 'ABSPATH' ) ) exit;

$user_data =  json_decode(file_get_contents('php://input'),true);

if($user_data['mode']=='facebook'){
	
	$profile_name = $user_data['name'];

	$profile_email = $user_data['email'];
	
	$first_name = $user_data['first_name'];
	
	$last_name = $user_data['last_name'];
	
}else if($user_data['mode']=='google'){
	
	$profile_name = $user_data['displayName'];

	$profile_email = $user_data['email'];
	
	$first_name = $user_data['givenName'];
	
	$last_name = $user_data['familyName'];
	
}

	if( !email_exists($profile_email) && username_exists( $profile_name )){
				
		$profile_id=rand(1000, 9999);
		
		$profile_name=$profile_name.$profile_id;
		
	}

	$user_id = username_exists( $profile_name );
			
	if ( !$user_id and email_exists($profile_email) == false ) {
		
		$random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
		
		$user_idd = wp_create_user( $profile_name, $random_password, $profile_email );
		
		wp_update_user( array( 'ID' => $user_idd, 'first_name' => $first_name,'last_name'=>$last_name ) );
	}


	if(isset($user_idd)) 
	{
		$user1 = get_user_by('id',$user_idd);

		wp_set_current_user( $user1->ID);

		wp_set_auth_cookie( $user1->ID );

		$user_info = get_userdata($user1->ID);

		do_action( 'wp_login', $user1->user_login,$user_info );

		$userdata = !empty($user1->ID)?format_customer_response($user1, $_REQUEST):'';
		update_user_meta( $user1->ID, 'refer_code_applied',"0");
		update_user_meta( $user1->ID, 'referal_code_users',array());
		$social_data = array("code"=>"1","details"=>$userdata,"refer_earn"=>true);		
		
	}else{
		
		$user1 = get_user_by( 'email', $profile_email);
		
		wp_set_current_user( $user1->ID );

		wp_set_auth_cookie( $user1->ID );
		
		$user_info = get_userdata($user1->ID);

		do_action( 'wp_login', $user1->user_login,$user_info );
		
		$userdata = !empty($user1->ID)?format_customer_response($user1, $_REQUEST):'';
		
		$social_data = array("code"=>"1","details"=>$userdata);
		
	}

?>