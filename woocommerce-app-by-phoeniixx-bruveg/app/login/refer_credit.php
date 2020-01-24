<?php  if ( ! defined( 'ABSPATH' ) ) exit;
ob_start();

global $wpdb;

$user_id = isset($_POST['user_id']) ? sanitize_text_field($_POST['user_id']):'';
$refer_code =	isset($_POST['refer_code']) ? sanitize_text_field($_POST['refer_code']):'';

if(get_user_meta( $user_id, 'refer_code_applied',true)=="1"){
	
	$refercredit_data = array('status'=>0, "message"=> sprintf( __( 'Refer code already used.', 'phoen-woo-app' )));
	return ;
	
}

$form_data = is_array(get_option("phoen_app_refer_earn_layout_setting"))?get_option("phoen_app_refer_earn_layout_setting"):array();

if($form_data['refer_earn_on']!=true){
	$refercredit_data = array('status'=>0, "message"=> sprintf( __( 'Refer Earn is not Enabled.', 'phoen-woo-app' )));
	return ;
}

$users = get_users( array( 'fields' => array( 'ID' ) ) );
$referrer_id = '';
foreach($users as $user){
	$earn_code = get_user_meta ( $user->ID,'phoen_app_refer_earn_code',true);
	if($earn_code && $earn_code == $refer_code ){
		$referrer_id = $user->ID;
	}
}

if($referrer_id==''){
	$refercredit_data = array('status'=>0, "message"=> sprintf( __( 'Refer code is not valid.', 'phoen-woo-app' )));
	return ;
}

$referal_code_users = get_user_meta( $referrer_id, 'referal_code_users',true); 

if(empty($referal_code_users)){
	update_user_meta( $referrer_id, 'referal_code_users',array()); 
	$referal_code_users = get_user_meta( $referrer_id, 'referal_code_users',true);
}

if(in_array($user_id,$referal_code_users)) {	
	$refercredit_data = array('status'=>0, "message"=> sprintf( __( 'Refer code is not allowed.', 'phoen-woo-app' )));
	return ;
}

if(count($referal_code_users)<$form_data['refer_earn_uses'] || $form_data['refer_earn_uses']==0){
	
	$refer_earner_amt = $form_data['refer_earner_amt'];
	$refer_referrer_amt = $form_data['refer_referrer_amt'];
	woo_wallet()->wallet->credit( $user_id, $refer_earner_amt, sanitize_textarea_field('Reward amount from referal code') );
	woo_wallet()->wallet->credit( $referrer_id, $refer_referrer_amt, sanitize_textarea_field('Reward amount from referal code') );
	array_push($referal_code_users,$user_id);
	
	update_user_meta( $referrer_id, 'referal_code_users',$referal_code_users); 
	
	update_user_meta( $user_id, 'refer_code_applied',"1");
	
	$refercredit_data = array('status'=>1, "message"=> sprintf( __( 'Refer code applied successfully.', 'phoen-woo-app' )));
	return ;
}else{
	$refercredit_data = array('status'=>0, "message"=> sprintf( __( 'Refer code uses limit reached.', 'phoen-woo-app' )));
	return ;
}