<?php 
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

global $wp;

global $table_prefix, $wpdb,$woocommerce;

global $data;

WC()->cart->init();

//include_once(plugins_url().'woo-wallet\includes\class-woo-wallet-frontend.php');

$data = json_decode(file_get_contents('php://input'), true);

$woo_wallet = new Woo_Wallet_Frontend();

//WC()->cart->init();

function is_valid_wallet_recharge_amount_cus($amount = 0) {
	$response = array('is_valid' => true);
	$min_topup_amount = woo_wallet()->settings_api->get_option('min_topup_amount', '_wallet_settings_general', 0);
	$max_topup_amount = woo_wallet()->settings_api->get_option('max_topup_amount', '_wallet_settings_general', 0);
	//if (isset($data['woo_wallet_topup']) ) {
		if ($min_topup_amount && $amount < $min_topup_amount) {
			$response = array(
				'is_valid' => false,
				'message' => sprintf(__('The minimum amount needed for wallet top up is %s', 'woo-wallet'), wc_price($min_topup_amount))
			);
		}
		if ($max_topup_amount && $amount > $max_topup_amount) {
			$response = array(
				'is_valid' => false,
				'message' => sprintf(__('Wallet top up amount should be less than %s', 'woo-wallet'), wc_price($max_topup_amount))
			);
		}
		if ($min_topup_amount && $max_topup_amount && ($amount < $min_topup_amount || $amount > $max_topup_amount)) {
			$response = array(
				'is_valid' => false,
				'message' => sprintf(__('Wallet top up amount should be between %s and %s', 'woo-wallet'), wc_price($min_topup_amount), wc_price($max_topup_amount))
			);
		}
	/* } else {
		$response = array(
			'is_valid' => false,
			'message' => __('Cheatin&#8217; huh?', 'woo-wallet')
		);
	} */
	return apply_filters('woo_wallet_is_valid_wallet_recharge_amount', $response, $amount);
}
add_action('woocommerce_before_calculate_totals', 'woo_wallet_set_recharge_product_price');
if (isset($data['woo_add_to_wallet'])) {
	
	if (isset($data['woo_wallet_balance_to_add']) && !empty($data['woo_wallet_balance_to_add'])) {
		
		$is_valid = is_valid_wallet_recharge_amount_cus($data['woo_wallet_balance_to_add']);
		
		if ($is_valid['is_valid']) {
			
			add_filter('woocommerce_add_cart_item_data', 'add_woo_wallet_product_price_to_cart_item_data_cust', 10, 2);
			
			$product = wc_get_product(get_option('_woo_wallet_recharge_product'));
			
			//print_r($product);
			
			if ($product) {
				
				WC()->cart->empty_cart();
				
				WC()->cart->add_to_cart($product->get_id());
				
				return $wallet_data = array("code"=>"1", "message"=> sprintf( __( ' %s has been added to your cart.', 'woocommerce' ), $product->get_name()));
				
			}
		} else {
			$wallet_data = wc_add_notice($is_valid['message'], 'error');
		}
	}
	
}
add_action( 'woocommerce_before_calculate_totals',  'woo_wallet_set_recharge_product_price' );

function woo_wallet_set_recharge_product_price( $cart) {
	
	$product = wc_get_product(get_option('_woo_wallet_recharge_product'));
	if ( !$product && empty( $cart->cart_contents) ) {
		return;
	}
	
	foreach ( $cart->cart_contents as $key => $value ) {
	
		if ( isset( $value['recharge_amount'] ) && $value['recharge_amount'] && $product->get_id() == $value['product_id'] ) {
			$value['data']->set_price( $value['recharge_amount'] );
		}
	}
}

function add_woo_wallet_product_price_to_cart_item_data_cust($cart_item_data, $product_id) {
	global $data;
	$product = wc_get_product($product_id);
	
	if (isset($data['woo_wallet_balance_to_add']) && $product) {
		$recharge_amount = round($data['woo_wallet_balance_to_add'], 2);
		$cart_item_data['recharge_amount'] = $recharge_amount;
	}
	
	return $cart_item_data;
}

?>