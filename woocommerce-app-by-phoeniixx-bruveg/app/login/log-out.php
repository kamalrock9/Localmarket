<?php  if ( ! defined( 'ABSPATH' ) ) exit;

 if(is_user_logged_in()) {
	wp_logout();
}