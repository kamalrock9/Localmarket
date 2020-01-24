<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/* include("../wp-config.php");

require( realpath(__DIR__ . '/../wp-load.php'));

include_once('./autoload.php'); */

global $wpdb;

 $user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:'';

$current_user       = get_user_by( 'id', $user_id);

$user_pass=isset($current_user->user_pass)?$current_user->user_pass:"";

$ID=isset($current_user->ID)?$current_user->ID:"";

// , $current_user->ID 

$pass_cur            =  isset($_REQUEST['password_current'])?$_REQUEST['password_current']:'';

$pass1                 =isset($_REQUEST['password_1'])?$_REQUEST['password_1']:'';

$pass2                = isset($_REQUEST['password_2'])?$_REQUEST['password_2']:'';
$pass_check=wp_check_password( $pass_cur, $user_pass, $ID );


if ( ! empty( $pass_cur ) && empty( $pass1 ) && empty( $pass2 ) ) {
	
	// echo json_encode(array( 'code' => 0,'message'=>'Please fill out all password fields.'));
	$responce=array( 'code' => 0,'message'=>'Please fill out all password fields.');
	$save_pass = false;
	return ;
} elseif ( ! empty( $pass1 ) && empty( $pass_cur ) ) {
	// echo json_encode(array( 'code' => 0,'message'=>'Please enter your current password.'));
	$responce=array( 'code' => 0,'message'=>'Please enter your current password.');
	$save_pass = false;
	// return ;
} elseif ( ! empty( $pass1 ) && empty( $pass2 ) ) {
	// echo json_encode(array( 'code' => 0,'message'=>'Please re-enter your password.'));
	$responce=array( 'code' => 0,'message'=>'Please re-enter your password.');
	$save_pass = false;
	// return ;
} elseif ( ( ! empty( $pass1 ) || ! empty( $pass2 ) ) && $pass1 !== $pass2 ) {
	// echo json_encode(array( 'code' => 0,'message'=>'New passwords do not match.'));
	$responce=array( 'code' => 0,'message'=>'New passwords do not match.');
	$save_pass = false;
	// return ;
} /* elseif ( ! empty( $pass1 ) && ! wp_check_password( $pass_cur, $user_pass, $ID ) ) {
	// echo json_encode();
	$responce=array( 'code' => 0,'message'=>'Your current password is incorrect.');

	$save_pass = false;
	// return ;
} */

// do_action( 'password_reset', $user, $new_pass );

if(wp_check_password( $pass_cur, $user_pass, $ID )===true){
	
	wp_set_password( $pass1, $ID );

	set_reset_password_cookie();

	if(is_object($current_user) && !empty($current_user)){
		
		wp_password_change_notification( $current_user );
		
	}


	$responce=array( 'code' => 1,'message'=>'Your password has been changed successfully.');
}else{
	
	$responce=array( 'code' => 0,'message'=>'Incorrect password !!!');
	
}



// echo json_encode(array( 'code' => 1,'message'=>'Your password has been changed successfully.'));

function set_reset_password_cookie( $value = '' ) {
	
	$rp_cookie = 'wp-resetpass-' . COOKIEHASH;
	$rp_path   = isset( $_SERVER['REQUEST_URI'] ) ? current( explode( '?', wc_clean( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) ) : ''; // WPCS: input var ok.

	if ( $value ) {
		setcookie( $rp_cookie, $value, 0, $rp_path, COOKIE_DOMAIN, is_ssl(), true );
	} else {
		setcookie( $rp_cookie, ' ', time() - YEAR_IN_SECONDS, $rp_path, COOKIE_DOMAIN, is_ssl(), true );
	}
}
?>