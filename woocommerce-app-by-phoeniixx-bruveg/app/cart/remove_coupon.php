<?php  if ( ! defined( 'ABSPATH' ) ) exit;

include_once('cart-function.php');

$coupon = isset($_GET['coupon_code'])?$_GET['coupon_code']:'';

WC()->cart->remove_coupon($coupon);

$all_cart_data = cart_product_data($postcode);

WC()->customer->set_calculated_shipping( true );

WC()->customer->save();

WC()->cart->calculate_shipping();

WC()->cart->calculate_totals();

$data['discount_total'] = discount_coupon_total();

$data['cart_subtotal'] = WC()->cart->get_cart_subtotal();

$data['coupon'] = coupon_data();

$data['taxes'] = wc_price( WC()->cart->get_taxes_total());

$data['total'] =  WC()->cart->get_total();

$data['cart_data'] = $all_cart_data;

include_once('shipping-setting.php');
$data['message'] = $shipping;

?>