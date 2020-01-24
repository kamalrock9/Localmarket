<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// require( realpath(__DIR__ . '/../wp-load.php'));

global $woocommerce;

WC()->cart->empty_cart();
wc_empty_cart();
WC()->session->set( 'cart', null );
WC()->session->set( 'cart_totals', null );
WC()->session->set( 'applied_coupons', null );
WC()->session->set( 'coupon_discount_totals', null );
WC()->session->set( 'coupon_discount_tax_totals', null );
WC()->session->set( 'removed_cart_contents', null );
WC()->session->set( 'order_awaiting_payment', null );

// do_action('woocommerce_cart_is_empty');

if(empty(WC()->cart->get_cart())){
	echo "1";
}

die();