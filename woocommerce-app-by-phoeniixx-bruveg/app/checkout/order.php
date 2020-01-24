<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

$product_item_key_data =  json_decode(file_get_contents('php://input'),true);

$cart = WC()->cart;

function phoen_add_cod_charge(){
		global $table_prefix, $wpdb;
		$chosen_gateway =  isset( $_REQUEST['payment_method'] ) ? $_REQUEST['payment_method'] : WC()->session->chosen_payment_method;	
		$table_pin_codes = $table_prefix."check_pincode_pro";
		$uid=$_REQUEST['user_id'];
		$postcode =  isset($uid)? get_user_meta( $uid, 'shipping_postcode', true ):'';
		//echo "postcode ".$postcode;die();
		foreach(WC()->cart->cart_contents as $key => $value){
			$product_id = $value['product_id'];
			$quantity = $value['quantity'];
			$phen_pincodes_list = get_post_meta( $product_id, 'phen_pincode_list',true );
			$safe_zipcode='';
			$pincode='';
			$safe_zipcode = $postcode;
			$pincode = substr($safe_zipcode, 0, 3);

			$query_maine='';
			if(!empty($safe_zipcode)){				
				if((is_array($phen_pincodes_list) && count($phen_pincodes_list)==0)|| empty($phen_pincodes_list)){				
					$count = $wpdb->get_var( $wpdb->prepare( "select COUNT(*) from $table_pin_codes where `pincode` = %s ", $safe_zipcode ) );						
					$like = false;				
					if( $count == 0  ){						
						$count_query = "SELECT * FROM `$table_pin_codes` where pincode LIKE '".$wpdb->esc_like($pincode)."%'";							
						$count_array = $wpdb->get_results($count_query);					
						$count=count($count_array);					
						$like = true;				
					}				
					if( $count !== 0 ){
						if( $like ){						
							$query_maine = "SELECT * FROM `$table_pin_codes` where pincode LIKE '".$pincode."%'";
						
						}
					else{
						
						$query_maine = "SELECT * FROM `$table_pin_codes` where pincode='$safe_zipcode'";						
					}
				}
			
				$ftc_ary = $wpdb->get_results($query_maine);	
				if(isset($ftc_ary) && is_array($ftc_ary)){					
					$quantity_into=isset($ftc_ary[0]->quantity_into)?$ftc_ary[0]->quantity_into:0;				
					$cod_charge=isset($ftc_ary[0]->cod_charge)?$ftc_ary[0]->cod_charge:0;
					if($quantity_into==1){						
						$total_cod_charge[]=$cod_charge*$quantity;						
					}else{						
						$total_cod_charge[]=$cod_charge;						
					}
				}					
			}else{
				$phen_pincode_list = isset($phen_pincodes_list[0])?$phen_pincodes_list[0]:$phen_pincodes_list;
				if (isset($safe_zipcode) && is_array($phen_pincode_list) && array_key_exists( $wpdb->esc_like($safe_zipcode),$phen_pincode_list ) ){
					$cod_charge = $phen_pincode_list[$safe_zipcode][9];					
					$quantity_into = $phen_pincode_list[$safe_zipcode][10];					
					if($quantity_into==1){					
						$total_cod_charge[]=$cod_charge*$quantity;						
					}else{						
						$total_cod_charge[]=$cod_charge;						
					}	
				}
				elseif(isset($pincode) && is_array($phen_pincode_list) && array_key_exists(  $pincode,$phen_pincode_list ) ){	
					$cod_charge = $phen_pincode_list[$safe_zipcode][9];					
					$quantity_into = $phen_pincode_list[$safe_zipcode][10];					
					if($quantity_into==1){					
						$total_cod_charge[]=$cod_charge*$quantity;						
					}else{						
						$total_cod_charge[]=$cod_charge;						
					}
				}				
			}
		}
	}
	$cod_charges='';
	if(isset($total_cod_charge) && is_array($total_cod_charge)){		
		$cod_charges=array_sum($total_cod_charge);	
	}
	
	$enable_cod = get_option('enable_cod');
	if ( $chosen_gateway == 'cod' && $cod_charges!='' && $enable_cod==1) { //test with cod method 
		$cod_fee = array(
			'id' => '_via_cod_charges',
			'name' => __( 'COD charge' ),
			'amount' => (float) 1 * $cod_charges,
			'taxable' => false,
			'tax_class' => '',
		);
		wc()->cart->fees_api()->add_fee( $cod_fee);
	}
}

function phoen_get_woowallet_cart_total() {
	$cart_total = 0;
	if ( is_array( wc()->cart->cart_contents) && sizeof( wc()->cart->cart_contents) > 0 ) {
		$cart_total = wc()->cart->get_subtotal( 'edit' ) + wc()->cart->get_taxes_total() + wc()->cart->get_shipping_total( 'edit' ) - wc()->cart->get_discount_total();
	}
	return apply_filters( 'woowallet_cart_total', $cart_total );
}



function phoenwoo_wallet_add_partial_payment_fee() {
	
	$parial_payment_amount = apply_filters( 'woo_wallet_partial_payment_amount', woo_wallet()->wallet->get_wallet_balance( $_REQUEST['user_id'], 'edit' ) );
	
	$fee = array(
		'id' => '_via_wallet_partial_payment',
		'name' => __( 'Via wallet' ),
		'amount' => (float) -1 * $parial_payment_amount,
		'taxable' => false,
		'tax_class' => '',
	);
	
	$cart_total = phoen_get_woowallet_cart_total();
	
	if ( 'on' === woo_wallet()->settings_api->get_option( 'is_auto_deduct_for_partial_payment', '_wallet_settings_general' ) && $cart_total > apply_filters( 'woo_wallet_partial_payment_amount', woo_wallet()->wallet->get_wallet_balance( $_GET['user_id'], 'edit' ) )) {
		
		wc()->cart->fees_api()->add_fee( $fee);
	
	} else {
		
		if($_GET['pay_via_wallet']==true){
			$fee = array(
				'id' => '_via_wallet_partial_payment',
				'name' => __( 'Via wallet' ),
				'amount' => (float) -1 * $parial_payment_amount,
				'taxable' => false,
				'tax_class' => '',
			);
			wc()->cart->fees_api()->add_fee( $fee);
		}

		$all_fees = wc()->cart->fees_api()->get_fees();
		if ( isset( $all_fees['_via_partial_payment_wallet'] ) ) {
			unset( $all_fees['_via_partial_payment_wallet'] );
			wc()->cart->fees_api()->set_fees( $all_fees);
		}
	}
}


$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );

if(isset($_REQUEST['shipping_method']) && $_REQUEST['shipping_method'] && $_REQUEST['shipping_method']=='undefined'){
	
	$chosen_shipping_methods[0] = $_REQUEST['shipping_method'];
	
	WC()->session->set( 'chosen_shipping_methods', $chosen_shipping_methods );
	
}
if (in_array( 'woo-wallet/woo-wallet.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ))) && !is_wallet_rechargeable_cart()) {
	add_action( 'woocommerce_cart_calculate_fees', 'phoenwoo_wallet_add_partial_payment_fee',10,1);
}
add_action( 'woocommerce_cart_calculate_fees', 'phoen_add_cod_charge');
WC()->cart->calculate_totals();



$checkout = WC()->checkout();

$payment_method=$_REQUEST['payment_method'];

$data = [
    'payment_method' => $payment_method,
	'set_paid' => false
];

$order_id = $checkout->create_order($data);

$order = wc_get_order($order_id);
//$order->set_customer_id($user_id);
foreach ($order->get_items() as $item_id => $item_obj) {
	
	$key = 'Delivery schedule';
	
	$product_id_array=$item_obj->get_data();
	
	$product_id=	$product_id_array['product_id'];
	
	$delivery_date=isset($product_item_key_data['pincode_meta'][$product_id])?$product_item_key_data['pincode_meta'][$product_id]:'';
	
	woocommerce_add_order_item_meta($item_id, $key, $delivery_date);

} 

update_post_meta($order_id, '_phoen_order_app', '1');

$user_id= isset($_GET['user_id'])?$_GET['user_id']:'';
	
if($user_id !=''){
	
	$user_data = get_user_by('id',$user_id);
	$custumer_details=format_customer_response($user_data, $_REQUEST);
	//print_r("test");
	//die();	
	$order->set_address( $custumer_details['billing'], 'billing' );
	$order->set_address( $custumer_details['shipping'], 'shipping' );
	$order->set_customer_id( $user_id );
	if($payment_method=='cod'){
		
		$order->update_status('processing');
		
	}else if($payment_method=='bacs' || $payment_method=='cheque'){
		
		$order->update_status('on-hold');

	}
}
$order->calculate_totals(); 
$controller=new WC_REST_Orders_V2_Controller();

if ( version_compare( WC()->version, '3.7', '<' ) ) {
	$formatted_order_data= $controller->prepare_item_for_response($order, $_REQUEST);
} else {
	$formatted_order_data= $controller->prepare_object_for_response($order, $_REQUEST);
}

$response=$controller->prepare_response_for_collection( $formatted_order_data );
?>