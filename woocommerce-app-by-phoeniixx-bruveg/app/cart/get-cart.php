<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

	include_once('cart-function.php');
	include_once('shipping-setting.php');
	
	if(isset($_GET['clear'])){
		
		WC()->cart->get_cart_from_session(); // Load users session
		WC()->cart->empty_cart(true); // Empty the cart
		WC()->session->set('cart', array()); // Empty the session cart data
		
	}
	
	/*Cart product data*/
	$all_cart_data = cart_product_data($postcode);
	/*End cart product*/
	
	$data['discount_total'] = discount_coupon_total();
	
	$data['cart_subtotal'] = WC()->cart->get_cart_subtotal();

	$data['coupon'] = coupon_data();
		
	$data['taxes'] = wc_price( WC()->cart->get_taxes_total());

	$data['total'] =  WC()->cart->get_total();

	$data['cart_data'] = $all_cart_data;

	$data['message'] = $shipping;
