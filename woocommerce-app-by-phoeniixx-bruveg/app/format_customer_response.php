<?php
function format_customer_response($user, $request){
	$controller=new WC_REST_Customers_V2_Controller();
	$formatted_user_data= $controller->prepare_item_for_response($user, $request);
	return $controller->prepare_response_for_collection( $formatted_user_data );
}
?>