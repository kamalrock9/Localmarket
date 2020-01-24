<?php  if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
	$i=0;
	
	$order_review_data = array();
	$chosen_gateway =  isset( $_GET['chosen_payment_method'] ) ? $_GET['chosen_payment_method'] : WC()->session->chosen_payment_method;
	$postcode =   isset( $_GET['postcode'])?$_GET['postcode'] : '';
	
	function phoen_get_woowallet_cart_total() {
        $cart_total = 0;
        if ( is_array( wc()->cart->cart_contents) && sizeof( wc()->cart->cart_contents) > 0 ) {
            $cart_total = wc()->cart->get_subtotal( 'edit' ) + wc()->cart->get_taxes_total() + wc()->cart->get_shipping_total( 'edit' ) - wc()->cart->get_discount_total();
        }
        return apply_filters( 'woowallet_cart_total', $cart_total );
    }
	
	
	
	
	
	if (in_array( 'woo-wallet/woo-wallet.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ))) && !is_wallet_rechargeable_cart()) {
		$current_wallet_amount = apply_filters( 'woo_wallet_partial_payment_amount', woo_wallet()->wallet->get_wallet_balance($_GET['user_id'], 'edit' ) );
		add_action( 'woocommerce_cart_calculate_fees', 'phoenwoo_wallet_add_partial_payment_fee',10,1);
		
		if ( $current_wallet_amount > 0 ) {
			
			$rest_amount = phoen_get_woowallet_cart_total() - $current_wallet_amount;
			
			$cart_total = phoen_get_woowallet_cart_total();
			
			if ( 'on' === woo_wallet()->settings_api->get_option( 'is_auto_deduct_for_partial_payment', '_wallet_settings_general' ) && $cart_total > apply_filters( 'woo_wallet_partial_payment_amount', woo_wallet()->wallet->get_wallet_balance( $_GET['user_id'], 'edit' ) )) {
				
				$order_review_data['wallet_message'] = sprintf( __( '%s will be debited from your wallet and remaining amount will be paid through other payment method', 'woo-wallet' ), wc_price( $current_wallet_amount ), wc_price( $rest_amount ) );
				
			}else{
				
				if($cart_total > apply_filters( 'woo_wallet_partial_payment_amount', woo_wallet()->wallet->get_wallet_balance( $_GET['user_id'], 'edit' ) )){
					
					$order_review_data['pay_via_wallet'] = (isset($_GET['pay_via_wallet'])?$_GET['pay_via_wallet']:false);
					
				}
				
			}
			
		}
	
	}
	function phoenwoo_wallet_add_partial_payment_fee() {
		$parial_payment_amount = apply_filters( 'woo_wallet_partial_payment_amount', woo_wallet()->wallet->get_wallet_balance( $_GET['user_id'], 'edit' ) );
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
	
	function phoen_wc_cart_totals_fee_html( $fee ) {
		$cart_totals_fee_html = WC()->cart->display_prices_including_tax() ? wc_price( $fee->total + $fee->tax ) : wc_price( $fee->total );

		return apply_filters( 'woocommerce_cart_totals_fee_html', $cart_totals_fee_html, $fee );
	}
	function phoen_add_cod_charge(){
		global $table_prefix, $wpdb;
		$chosen_gateway =  isset( $_GET['chosen_payment_method'] ) ? $_GET['chosen_payment_method'] : WC()->session->chosen_payment_method;	
		$table_pin_codes = $table_prefix."check_pincode_pro";
		$postcode =   isset( $_GET['postcode'])?$_GET['postcode'] : '';
	 
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
				
				// echo '1';die();
				
				$phen_pincode_list = isset($phen_pincodes_list[0])?$phen_pincodes_list[0]:$phen_pincodes_list;
					
				if (isset($safe_zipcode) && is_array($phen_pincode_list) && array_key_exists( $wpdb->esc_like($safe_zipcode),$phen_pincode_list ) )
				{
					$cod_charge = $phen_pincode_list[$safe_zipcode][9];
					
					$quantity_into = $phen_pincode_list[$safe_zipcode][10];
					
					if($quantity_into==1){
					
						$total_cod_charge[]=$cod_charge*$quantity;
						
					}else{
						
						$total_cod_charge[]=$cod_charge;
						
					}
					
					
				}
				elseif(isset($pincode) && is_array($phen_pincode_list) && array_key_exists(  $pincode,$phen_pincode_list ) )
				{
					
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
	if ( in_array( 'woocommerce-pincode-check-pro-unl-num/woocommerce-pincode-check.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		add_action( 'woocommerce_cart_calculate_fees', 'phoen_add_cod_charge');
	}
	WC()->cart->calculate_totals();

	$order_review_data['chosen_gateway']=isset($chosen_gateway)?$chosen_gateway:false;
		
	$discount_total = 0;
	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
		$product_id = $cart_item['product_id'];
		if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
			$order_review_data['product'][$i]['product_id'] = $product_id;
			$order_review_data['product'][$i]['pincode_delivery'] = pincode_verify($postcode,$product_id,$cart_item['quantity']);
			$order_review_data['product'][$i]['variation_id'] = $cart_item['variation_id'];
			$order_review_data['product'][$i]['product_name']=  apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
			$order_review_data['product'][$i]['product_qty'] =   apply_filters( 'woocommerce_checkout_cart_item_quantity', $cart_item['quantity']  , $cart_item, $cart_item_key ); 
			wc_get_formatted_cart_item_data( $cart_item );
				
			$order_review_data['product'][$i]['product_total'] = strip_tags(apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ));
			
			if ( $_product->is_on_sale() ) {
				$regular_price = $_product->get_regular_price();
				$sale_price = $_product->get_sale_price();
				$discount = ($regular_price - $sale_price) * $cart_item['quantity'];
				$discount_total += $discount;
			}
			
		}
		$i++;
	}
	$order_review_data['total_sale_saving']=wc_price($discount_total);
	
	$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );

	$country  =  isset( $_GET['country'] ) ? $_GET['country'] : 'IN'  ;
	$state    = isset( $_GET['state'] ) ? $_GET['state'] : ''  ;
	
	$city     = isset( $_GET['city'] ) ? $_GET['city'] : ''  ;

	if ( $postcode && ! WC_Validation::is_postcode( $postcode, $country ) ) {
		$order_review_data['err']= __( 'Please enter a valid postcode / ZIP.', 'woocommerce' );
	//throw new Exception( __( 'Please enter a valid postcode / ZIP.', 'woocommerce' ) );
	//return;
	} elseif ( $postcode ) {
	$postcode = wc_format_postcode( $postcode, $country );
	}
	if ( $country ) {
		WC()->customer->set_location( $country, $state, $postcode, $city );
		WC()->customer->set_shipping_location( $country, $state, $postcode, $city );
	} else {
		WC()->customer->set_billing_address_to_base();
		WC()->customer->set_shipping_address_to_base();
	}


	WC()->customer->set_calculated_shipping( true );
	WC()->customer->save();

	WC()->cart->calculate_shipping();

	WC()->cart->calculate_totals();

	$discount_excl_tax_total = WC()->cart->get_cart_discount_total();
	$discount_tax_total = WC()->cart->get_cart_discount_tax_total();
	$coupon_discount = $discount_excl_tax_total + $discount_tax_total;
	$order_review_data['coupon_discount']=wc_price($coupon_discount);
	$discount_total += $coupon_discount;
	$order_review_data['discount_total']=wc_price($discount_total);

		$order_review_data['cart_subtotal'] = strip_tags(WC()->cart->get_cart_subtotal());
		
		$order_review_data['cart_discount_coupon'] = array();
		
		$all_coupon = WC()->cart->get_coupon_discount_totals();

		$all_coupon_array = array();

		foreach($all_coupon as $key=>$value){
			$all_coupon_array[]=array('code'=>$key,'discount'=>wc_price($value));
		}

		$order_review_data['cart_discount_coupon']=$all_coupon_array;
	
	if(isset($_REQUEST['shipping_method']) && $_REQUEST['shipping_method']){
		$chosen_shipping_methods[0] = $_REQUEST['shipping_method'];
		WC()->session->set( 'chosen_shipping_methods', $chosen_shipping_methods );
		WC()->cart->calculate_totals();
	}
	
	 if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) :

		$packages = WC()->shipping->get_packages();
		
		$packages[0]['destination']['country']= $country;
		$packages[0]['destination']['state']= $state;
		$packages[0]['destination']['postcode']= $postcode;
		$packages[0]['destination']['city']= $city;
		
		$shipping_method = array();
		
		foreach ( $packages as $i => $package ) {
			
			$chosen_method = isset( WC()->session->chosen_shipping_methods[ $i ] ) ? WC()->session->chosen_shipping_methods[ $i ] : '';
			//$chosen_method = str_replace(':','',$chosen_method);
			$product_names = array();

			if ( sizeof( $packages ) > 1 ) {
				foreach ( $package['contents'] as $item_id => $values ) {
					$product_names[ $item_id ] = $values['data']->get_name() . ' &times;' . $values['quantity'];
				}
				$product_names = apply_filters( 'woocommerce_shipping_package_details_array', $product_names, $package );
			}
			$available_methods = $package['rates'];
		if(!empty($available_methods)){
			 if ( 0 < count( $available_methods ) ) :
					$s=0;
					$order_review_data['chosen_shipping_method']= $chosen_method;
					foreach ( $available_methods as $method ) :
					
						$order_review_data['shipping_method'][$s]['id'] =  $method->id;
						$order_review_data['shipping_method'][$s]['method_id'] = sanitize_title( $method->id );
						$order_review_data['shipping_method'][$s]['shipping_method_name'] = $method->get_label();
						
						$cost=0;
						$another_cost;
						 if ( $method->cost > 0 ) {
							if ( WC()->cart->display_prices_including_tax() ) {
								$another_cost = $method->cost + $method->get_shipping_tax();
								$cost = wc_price( $method->cost + $method->get_shipping_tax() );
								if ( $method->get_shipping_tax() > 0 && ! wc_prices_include_tax() ) {
									$cost .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
								}
							} else {
								$another_cost = $method->cost;
								$cost = wc_price( $method->cost );
								if ( $method->get_shipping_tax() > 0 && wc_prices_include_tax() ) {
									$cost .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
								}
							}
						}
						$cost = !empty($cost)?$cost:wc_price(0.00);
						$order_review_data['shipping_method'][$s]['shipping_method_price'] = strip_tags($cost);
						$order_review_data['shipping_method'][$s]['shipping_method_price_without_symbol'] = strip_tags(str_replace(get_woocommerce_currency_symbol(),"",$cost));
						
						
						$s++;							
					endforeach;
				
				elseif ( 1 === count( $available_methods ) ) : 
				
					$method = current( $available_methods );
					//printf( '%3$s <input type="hidden" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d" value="%2$s" class="shipping_method" />', $index, esc_attr( $method->id ), wc_cart_totals_shipping_method_label( $method ) );
					//do_action( 'woocommerce_after_shipping_rate', $method, $index );
				elseif ( ! is_cart() ) :
				//echo wpautop( __( 'Enter your full address to see shipping costs.', 'woocommerce' ) ); 
				endif;
		}
			 if ( $show_package_details ) :
				// echo '<p class="woocommerce-shipping-contents"><small>' . esc_html( $package_details ) . '</small></p>'; 
			 endif; 

			 if ( ! empty( $show_shipping_calculator ) ) : 
				woocommerce_shipping_calculator(); 
			endif; 
		
		}
		if(!is_array($order_review_data['shipping_method']) || count($order_review_data['shipping_method'])==0){
			$order_review_data['shipping_required_msg']=sprintf(__('There are no shipping methods available. Please ensure that your address has been entered correctly, or contact us if you need any help.'));
		}
		endif; 
		//$all_fees = wc()->cart->fees_api()->get_fees();
		//$order_review_data['cart_get_fees'] = array();
		foreach ( wc()->cart->fees_api()->get_fees() as $fee ) : 
			//echo phoen_wc_cart_totals_fee_html( $fee );
			
			$order_review_data['cart_fees'][$fee->name] = phoen_wc_cart_totals_fee_html( $fee );
			
		endforeach;
		//echo json_encode($order_review_data['cart_get_fees']);
		//$order_review_data['cart_tax_total'] = array();
		if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : 
			 if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : 
				 foreach ( WC()->cart->get_tax_totals() as $code => $tax ) :
					
					$order_review_data['cart_tax_total'][$tax->label] =  $tax->formatted_amount ;
					
				endforeach;
			else :
				$order_review_data['cart_tax_total'][WC()->countries->tax_or_vat()] = wc_price( WC()->cart->get_taxes_total());				
			 endif;
		endif;
		
		$value = WC()->cart->get_total();
		$value2 = WC()->cart->get_total();
		
		 if ( wc_tax_enabled() && WC()->cart->display_prices_including_tax() ) {
        $tax_string_array = array();
        $cart_tax_totals  = WC()->cart->get_tax_totals();

        if ( get_option( 'woocommerce_tax_total_display' ) == 'itemized' ) {
            foreach ( $cart_tax_totals as $code => $tax ) {
                $tax_string_array[] = sprintf( '%s %s', $tax->formatted_amount, $tax->label );
            }
        } elseif ( ! empty( $cart_tax_totals ) ) {
            $tax_string_array[] = sprintf( '%s %s', wc_price( WC()->cart->get_taxes_total( true, true ) ), WC()->countries->tax_or_vat() );
        }

        if ( ! empty( $tax_string_array ) ) {
            $taxable_address = WC()->customer->get_taxable_address();
            $estimated_text  = WC()->customer->is_customer_outside_base() && ! WC()->customer->has_calculated_shipping()
                ? sprintf( ' ' . __( 'estimated for %s', 'woocommerce' ), WC()->countries->estimated_for_prefix( $taxable_address[0] ) . WC()->countries->countries[ $taxable_address[0] ] )
                : '';
            $value .= '<small class="includes_tax">' . sprintf( __( '(includes %s)', 'woocommerce' ), implode( ', ', $tax_string_array ) . $estimated_text ) . '</small>';
			
        }
    }
		
		//do_action( 'woocommerce_review_order_before_order_total' ); 
		$order_review_data['cart_order_total']= strip_tags($value);
		
		$order_review_data['cart_order_total_without_symbol'] =  strip_tags(str_replace(get_woocommerce_currency_symbol(),"",$value2));

		
$available_gateways = WC()->payment_gateways->payment_gateways();

if (in_array( 'woo-wallet/woo-wallet.php', apply_filters( 'active_plugins', get_option( 'active_plugins' )))) {
	if(is_wallet_rechargeable_cart()){
	foreach ($available_gateways as $gateway_id => $gateway) {
		if (woo_wallet()->settings_api->get_option($gateway_id, '_wallet_settings_general', 'on') != 'on' || $gateway_id == 'wallet') {
			unset($available_gateways[$gateway_id]);
		}
	}
	}

	//echo "test"; die();

$cart_total = get_woowallet_cart_total();
if ( 'on' === woo_wallet()->settings_api->get_option( 'is_auto_deduct_for_partial_payment', '_wallet_settings_general' ) && $cart_total > apply_filters( 'woo_wallet_partial_payment_amount', woo_wallet()->wallet->get_wallet_balance( $_GET['user_id'], 'edit' ) )) {
	unset($available_gateways['wallet']);
}else if($cart_total > apply_filters( 'woo_wallet_partial_payment_amount', woo_wallet()->wallet->get_wallet_balance( $_GET['user_id'], 'edit' ))){
	unset($available_gateways['wallet']);
}
}

//$available_gateways = WC()->payment_gateways->get_available_payment_gateways();

if ( WC()->cart->needs_payment() ) : 
	if ( ! empty( $available_gateways ) ) {
		$p=0;
		foreach ( $available_gateways as $gateway ) {
			
			if( $gateway->enabled == 'yes' ) {
				
				if($gateway->id == 'wallet'){
					$user_wallet = apply_filters( 'woo_wallet_partial_payment_amount', woo_wallet()->wallet->get_wallet_balance( $_GET['user_id'], 'edit' ));
					$order_review_data['payment_gateway'][$p]['gateway_title'] = $gateway->get_title()." | ".$user_wallet;	
				}else{
					$order_review_data['payment_gateway'][$p]['gateway_title'] = $gateway->get_title();
				}
				
				$order_review_data['payment_gateway'][$p]['gateway_id'] = $gateway->id;
				$order_review_data['payment_gateway'][$p]['gateway_order_button_text'] = esc_attr( $gateway->order_button_text );
				$order_review_data['payment_gateway'][$p]['gateway_chosen'] = $gateway->chosen;
				$order_review_data['payment_gateway'][$p]['gateway_icon'] = $gateway->get_icon();
			
				if ( $gateway->has_fields() || $gateway->get_description() ) : 
				
					$order_review_data['payment_gateway'][$p]['gateway_description'] =	esc_html($gateway->get_description());
				
				endif;
				$p++;
			}
			
		}
	} else {
		$order_review_data['payment_gateway']['error'] =  apply_filters( 'woocommerce_no_available_payment_methods_message', WC()->customer->get_billing_country() ? esc_html__( 'Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' ) : esc_html__( 'Please fill in your details above to see available payment methods.', 'woocommerce' ) ); // @codingStandardsIgnoreLine----------------
	}
		
endif;
