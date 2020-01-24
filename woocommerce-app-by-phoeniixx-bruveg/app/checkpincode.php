<?php if ( ! defined( 'ABSPATH' ) ) exit;

global $table_prefix, $wpdb,$woocommerce;

$data = json_decode(file_get_contents('php://input'), true);

$pincode=$data['pincode']; 

$product_id=$data['product_id']; 
	
$responce=pincode_verify($pincode,$product_id,false);
	