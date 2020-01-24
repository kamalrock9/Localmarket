<?php  if ( ! defined( 'ABSPATH' ) ) exit;

include_once('cart-function.php');

wc_clear_notices();
if ( ! empty( $_REQUEST['coupon_code'] ) ) {
	
	$coupan_on = WC()->cart->add_discount( sanitize_text_field( $_REQUEST['coupon_code'] ) );
	
} else {
	$coupan_on =  false;
}

$all_cart_data = cart_product_data($postcode);

WC()->customer->set_calculated_shipping( true );

WC()->customer->save();

WC()->cart->calculate_shipping();

WC()->cart->calculate_totals();

$data['discount_total'] = discount_coupon_total();

$data['cart_subtotal'] = WC()->cart->get_cart_subtotal();

$data['taxes'] = wc_price( WC()->cart->get_taxes_total());

$data['total'] =  WC()->cart->get_total();

include_once('shipping-setting.php');
$data['message'] = $shipping;

$data['cart_data'] = $all_cart_data;

if($coupan_on===true){
	
	$data['coupon'] = coupon_data();

}else{
	$note=wc_get_notices();
	if($note && $note['error']){
		$data=array('code'=>201,'message'=>$note['error']);
	}else{
		$data=array('code'=>201,'message'=>array());
	}
}
?>