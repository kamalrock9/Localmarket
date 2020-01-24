<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

// global $woocommerce;

include_once('cart-error.php');

WC()->cart->init();

$product_data =  json_decode(file_get_contents('php://input'),true);

function add_to_cart_phoen_simple( $product_id,$quantity ) {
	
	$passed_validation 	= apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
	$product_dataa = wc_get_product($product_id );
	
	if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity ) !== false ) {
		 
		return $success_report = array("code"=>"1", "message"=> sprintf( __( ' %s has been added to your cart.', 'woocommerce' ), $product_dataa->get_name()));
		
	}
	 
	return cart_error($product_id,0,$quantity);
}

function add_to_cart_phoen_grouped( $product_id ,$quantity_data=array()) {
	$was_added_to_cart = false;
		$added_to_cart     = array();
		$error_data = array();
		if ( ! empty( $quantity_data ) && is_array( $quantity_data ) ){
			$quantity_set = false;

			foreach ( $quantity_data as $item => $quantity ) {
				if ( $quantity <= 0 ) {
					continue;
				}
				$quantity_set = true;

				// Add to cart validation
				$passed_validation 	= apply_filters( 'woocommerce_add_to_cart_validation', true, $item, $quantity );
				
				$product_dataa = wc_get_product($item );
				
				if ( $passed_validation && WC()->cart->add_to_cart( $item, $quantity ) !== false ) {
					$was_added_to_cart = true;
					$added_to_cart[ $item ] = $quantity;
					$error_data[] = array("code"=>"1", "message"=> sprintf( __( ' %s has been added to your cart.', 'woocommerce' ), $product_dataa->get_name()));
				}else{
					$error_data[] = cart_error($item,0,$quantity);
				}
								
			}

			if ( ! $was_added_to_cart && ! $quantity_set ) {
				return $error_report = array("code"=>"0", "message"=> sprintf( __( 'Please choose the quantity of items you wish to add to your cart&hellip;', 'woocommerce' )));
				
			} elseif ( $was_added_to_cart ) {
				return $error_data;
				//return $error_report = array("code"=>"0", "message"=> $added_to_cart);
				
			}
		} elseif ( $product_id ) {
			/* Link on product archives */
			return $error_report = array("code"=>"0", "message"=> 'Please choose a product to add to your cart&hellip;');
			
		}
		return false;
}
function add_to_cart_phoen_variable( $product_id,$quantity=0,$variation_id=0,$variation= array()) {
	
	$adding_to_cart     = wc_get_product( $product_id );
	$variation_id       = ( $variation_id =='') ? '' : absint( $variation_id );
	$quantity           = ( $quantity=='' ) ? 1 : wc_stock_amount( $quantity);
	$missing_attributes = array();
	$variations         = array();
	$attributes         = $adding_to_cart->get_attributes();
	
	
	$posted_attributes=array();
	foreach($variation as $k=>$v ){
		$kq='attribute_'.$k;
		//$vq=str_replace(" ","-",strtolower($v));
		$posted_attributes[$kq]=$v;
	}

	// If no variation ID is set, attempt to get a variation ID from posted attributes.
	if ( empty( $variation_id ) ) {
		$data_store   = WC_Data_Store::load( 'product' );
		$variation_id = $data_store->find_matching_product_variation( $adding_to_cart, wp_unslash( $_POST ) );
	}
	
	// Do we have a variation ID?
	if ( empty( $variation_id ) ) {
		return array("code"=>"0", "message"=> sprintf( __( 'Please choose product options&hellip;', 'woocommerce' )));
	}
	
	// Check the data we have is valid.
	$variation_data = wc_get_product_variation_attributes( $variation_id );

	foreach ( $adding_to_cart->get_attributes() as $attribute ) {
		if ( ! $attribute['is_variation'] ) {
			continue;
		}

		// Get valid value from variation data.
		$attribute_key = 'attribute_' . sanitize_title( $attribute['name'] );
		$valid_value   = isset( $variation_data[ $attribute_key ] ) ? $variation_data[ $attribute_key ]: '';
		/**
		 * If the attribute value was posted, check if it's valid.
		 *
		 * If no attribute was posted, only error if the variation has an 'any' attribute which requires a value.
		 */
		if ( isset( $posted_attributes[ $attribute_key ] ) ) {
			$value = $posted_attributes[ $attribute_key ];
			//print_r($value);
			//print_r($attribute->get_slugs());
			// Allow if valid or show error.
			if ( $valid_value === $value ) {
				$variations[ $attribute_key ] = $value;
			} elseif ('' === $valid_value && in_array( $value, $attribute->get_slugs() )) {
				// If valid values are empty, this is an 'any' variation so get all possible values.
				$variations[ $attribute_key ] = $value;
			} else {		
				return array("code"=>"0", "message"=> sprintf( __( 'Invalid value posted for %s', 'woocommerce' ), wc_attribute_label( $attribute['name'] ) ) );
			}
		} elseif ( '' === $valid_value ) {
			$missing_attributes[] = wc_attribute_label( $attribute['name'] );
		}
	}
	
	if ( ! empty( $missing_attributes ) ) {
		return array("code"=>"0", "message"=> sprintf( _n( '%s is a required field', '%s are required fields', sizeof( $missing_attributes ), 'woocommerce' ), wc_format_list_of_items( $missing_attributes ) ) );
	}

	// Add to cart validation
	$passed_validation 	= apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations );

	if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations ) !== false ) {
		$success_report = array("code"=>"1", "message"=>"Product Added To cart" );
		return ($success_report);
	}

	return cart_error($product_id,$variation_id,$quantity);
	
}
//this function use to get attribute options
function phoen_wp_app_get_attribute_options( $product_id, $attribute ) {
	//echo("function called"); die();
	if ( isset( $attribute['is_taxonomy'] ) && $attribute['is_taxonomy'] ) {
		return wc_get_product_terms(
			$product_id, $attribute['name'], array(
				'fields' => 'names',
			)
		);
	} elseif ( isset( $attribute['value'] ) ) {
		return array_map( 'trim', explode( '|', $attribute['value'] ) );
	}
	return array();
}

	$product_id          = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $product_data['id'] ) );
	$quantity = $product_data['quantity'];
	
	$was_added_to_cart   = false;
	$adding_to_cart      = wc_get_product( $product_id );
	
	if( in_array( 'woo-wallet/woo-wallet.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ))) && is_wallet_rechargeable_cart()){
		
		$was_added_to_cart = array("code"=>"0", "message"=>"You can not add another product while your cart contains with wallet rechargeable product.");
		
	}elseif ( ! $adding_to_cart ) {
		
		$was_added_to_cart = array("code"=>"0", "message"=>"Product Not Added To cart");
		
	}else{

	$add_to_cart_handler = apply_filters( 'woocommerce_add_to_cart_handler', $adding_to_cart->get_type(), $adding_to_cart );
	
	if ( 'variable' === $add_to_cart_handler ) {
		$variation_id = isset($product_data['variation_id'])?$product_data['variation_id']:0;
		$variations=isset($product_data['variation'])?$product_data['variation']:array();
		//Variable product handling
		$was_added_to_cart = add_to_cart_phoen_variable( $product_id ,$quantity,$variation_id,$variations);
	
	} elseif ( 'grouped' === $add_to_cart_handler ) {
		//Grouped Products
		$was_added_to_cart = add_to_cart_phoen_grouped( $product_id,$quantity);
	
	} elseif ( 'external' === $add_to_cart_handler ) {
		//External Products
		$was_added_to_cart = array("code"=>"0", "message"=>"External product not added to cart");
		
	
	} else {
		//Simple Products
		$was_added_to_cart = add_to_cart_phoen_simple( $product_id ,$quantity);
	}
}