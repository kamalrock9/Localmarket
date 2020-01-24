<?php 
include_once('cart-function.php');

WC()->cart->init();

if(isset($_GET['cart_item_key']) && $_GET['cart_item_key']){
	
	$cart_items_key = isset($_GET['cart_item_key'])?$_GET['cart_item_key']:''; 

	WC()->cart->remove_cart_item( $cart_items_key );
	
}

$all_cart_data = cart_product_data($postcode);

$data['discount_total'] = discount_coupon_total();

$data['cart_subtotal'] = WC()->cart->get_cart_subtotal();

$data['taxes'] = wc_price( WC()->cart->get_taxes_total());

$data['total'] =  WC()->cart->get_total();

$data['cart_data'] = $all_cart_data;

$data['coupon'] = coupon_data();

include_once('shipping-setting.php');
$data['message'] = $shipping;
?>