<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$user_id = $_REQUEST['user_id'];

$refer_earn_code = get_user_meta($user_id,'phoen_app_refer_earn_code',true);

$refer_earn_data = get_option("phoen_app_refer_earn_layout_setting",true);

if($refer_earn_code){
	
	$message = $refer_earn_data['refer_earn_msg'];
	$amount_referrer = $refer_earn_data['refer_referrer_amt'];
	$amount_earner = $refer_earn_data['refer_earner_amt'];
	$message= str_replace("{referralcode}","$refer_earn_code",$message);
	$referearn_data['message'] = str_replace("{referralamount}","$amount_earner",$message);
	
	$referearn_data['refer_earn_code']= $refer_earn_code;
	$referearn_data['amount_earner'] = wc_price($amount_earner);
	$referearn_data['amount_referrer'] = wc_price($amount_referrer);
	$referearn_data['refer_earn_uses'] = $refer_earn_data['refer_earn_uses'];
	$referearn_data['banner'] = $refer_earn_data['refer_earn_banner_url'];
}else{
	
	$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    
	$charactersLength = strlen($characters);
    
	$randomString = '';
    
	for ($i = 0; $i < 6; $i++) {
		$refer_earn_code .= $characters[rand(0, $charactersLength - 1)];
    }
	
	update_user_meta($user_id,'phoen_app_refer_earn_code',$refer_earn_code);
	
	$referearn_data['refer_earn_code']= $refer_earn_code;

	$message = $refer_earn_data['refer_earn_msg'];
	$amount_referrer = $refer_earn_data['refer_referrer_amt'];
	$amount_earner = $refer_earn_data['refer_earner_amt'];
	$message= str_replace("{referralcode}","$refer_earn_code",$message);
	$referearn_data['message'] = str_replace("{referralamount}","$amount_earner",$message);
	
	$referearn_data['refer_earn_code']= $refer_earn_code;
	$referearn_data['amount_earner'] = wc_price($amount_earner);
	$referearn_data['amount_referrer'] = wc_price($amount_referrer);
	$referearn_data['refer_earn_uses'] = $refer_earn_data['refer_earn_uses'];
	$referearn_data['banner'] = $refer_earn_data['refer_earn_banner_url'];

}