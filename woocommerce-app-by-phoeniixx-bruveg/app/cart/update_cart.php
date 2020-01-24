<?php 
 if ( ! defined( 'ABSPATH' ) ) exit;

 include_once('cart-function.php');

$all_cart_data = array();

if(isset($_GET['cart_item_key']) && isset($_GET['quantity'])){
	
	$cart_items_key = isset($_GET['cart_item_key'])?$_GET['cart_item_key']:'';
	
	WC()->cart->set_quantity( $cart_items_key , $_GET['quantity'] );
}

$all_cart_data = cart_product_data($postcode);

$data['discount_total'] = discount_coupon_total();

$data['cart_subtotal'] = WC()->cart->get_cart_subtotal();

$data['coupon'] = coupon_data();

$data['taxes'] = wc_price( WC()->cart->get_taxes_total());

$data['total'] =  WC()->cart->get_total();

$data['cart_data'] = $all_cart_data;

include_once('shipping-setting.php');
$data['message'] = $shipping;

?>