<?php 

/*CART PRODUCT FUNCTION **/
$all_cart_data = array();
$data = array();

$country  =   isset( $_GET['country'] ) ? $_GET['country'] : 'IN'  ;
$state    =   isset( $_GET['state'] ) ? $_GET['state'] : ''  ;
$postcode =   isset( $_GET['postcode'])?$_GET['postcode'] : '';
$city     =   isset( $_GET['city'] ) ? $_GET['city'] : ''  ;

function cart_product_data($postcode){
	
	$i=0;
	if(count(WC()->cart->get_cart())>0){
		foreach (  WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

			$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			
			$product_id = $cart_item['product_id'];
			
			$pro=new WC_Product($product_id);
			
			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {

				$variation_detail = woocommerce_get_formatted_variation( $cart_item['variation'] );

				$all_cart_data[$i]['name'] = $_product->get_name();

				$image_size = apply_filters( 'single_product_archive_thumbnail_size', 'shop_catalog' );

				$all_cart_data[$i]['image'] = get_the_post_thumbnail_url( $product_id,$image_size);

				$all_cart_data[$i]['cart_item_key'] = $cart_item['key'];

				$all_cart_data[$i]['varitions'] =$variation_detail;

				$all_cart_data[$i]['price'] = WC()->cart->get_product_price( $_product );

				$all_cart_data[$i]['pincode_delivery'] = pincode_verify($postcode,$product_id,$cart_item['quantity']);

				$all_cart_data[$i]['product_desc'] = $pro->post->post_excerpt;

				$all_cart_data[$i]['shipping_class'] = $cart_item['data']->get_shipping_class_id();
				
				if($cart_item['data']->get_shipping_class_id() != 0){
				
					$all_cart_data_shipping['shipping_class'][] = $cart_item['data']->get_shipping_class_id();
					
				}
				$all_cart_data[$i]['sold_ind'] = $_product->is_sold_individually()? 'true':'false';
				
					if($_product->get_manage_stock() == 'parent'){
						
						$all_cart_data[$i]['manage_stock'] = $pro->get_manage_stock();
						
						$all_cart_data[$i]['stock_quanity'] = $pro->get_stock_quantity();
					
					}else{
						
						$all_cart_data[$i]['manage_stock'] = $_product->get_manage_stock();
						
						$all_cart_data[$i]['stock_quanity'] = $_product->get_stock_quantity();
					
					}
				
				$all_cart_data[$i]['quantity'] = $cart_item['quantity'];
			
				$all_cart_data[$i]['subtotal'] = WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] );
				
				$i++;
				
			}
			
		}
	}else{
		$all_cart_data = array();
	}
	
	return $all_cart_data;
}

function coupon_data(){

	$all_coupon = WC()->cart->get_coupon_discount_totals();

	$all_coupon_array = array();

	foreach($all_coupon as $key=>$value){
		$all_coupon_array[]=array('code'=>$key,'discount'=>wc_price($value));
	}

	return $all_coupon_array;
}

function discount_coupon_total(){
	
	$discount_excl_tax_total = WC()->cart->get_cart_discount_total();
	$discount_tax_total = WC()->cart->get_cart_discount_tax_total();
	$discount_total = $discount_excl_tax_total + $discount_tax_total;

	return wc_price($discount_total);
}

$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );

if(isset($_REQUEST['shipping_method']) && $_REQUEST['shipping_method']){
	$chosen_shipping_methods[0] = $_REQUEST['shipping_method'];
}
WC()->session->set( 'chosen_shipping_methods', $chosen_shipping_methods );

$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );


	if ( $postcode && ! WC_Validation::is_postcode( $postcode, $country ) ) {
	throw new Exception( __( 'Please enter a valid postcode / ZIP.', 'woocommerce' ) );
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

	if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) :

		$packages = WC()->shipping->get_packages();
		
		$packages[0]['destination']['country']= $country;
		$packages[0]['destination']['state']= $state;
		$packages[0]['destination']['postcode']= $postcode;
		$packages[0]['destination']['city']= $city;
		
		$shipping_method = array();
		
		foreach ( $packages as $ikey => $package ) {
			
			$chosen_method = isset( WC()->session->chosen_shipping_methods[ $ikey ] ) ? WC()->session->chosen_shipping_methods[ $ikey] : '';
			
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
					$data['chosen_shipping_method']= $chosen_method;
					foreach ( $available_methods as $method ) :
					
						$data['shipping_method'][$s]['id'] =  $method->id;
						$data['shipping_method'][$s]['method_id'] = sanitize_title( $method->id );
						$data['shipping_method'][$s]['shipping_method_name'] = $method->get_label();
						
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
						$data['shipping_method'][$s]['shipping_method_price'] = strip_tags($cost);
						$data['shipping_method'][$s]['shipping_method_price_without_symbol'] = strip_tags(str_replace(get_woocommerce_currency_symbol(),"",$cost));
						
						
						$s++;							
					endforeach;
				
				elseif ( 1 === count( $available_methods ) ) : 
				
					$method = current( $available_methods );
					//printf( '%3$s <input type="hidden" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d" value="%2$s" class="shipping_method" />', $index, esc_attr( $method->id ), wc_cart_totals_shipping_method_label( $method ) );
					//do_action( 'woocommerce_after_shipping_rate', $method, $index );
				elseif ( ! is_cart() ) :
				echo wpautop( __( 'Enter your full address to see shipping costs.', 'woocommerce' ) ); 
				endif;
		}
			//  if ( $show_package_details ) :
			// 	echo '<p class="woocommerce-shipping-contents"><small>' . esc_html( $package_details ) . '</small></p>'; 
			//  endif; 

			 if ( ! empty( $show_shipping_calculator ) ) : 
				woocommerce_shipping_calculator(); 
			endif; 
		
		}
		endif;
		
	$rate_table = array();

	$shipping_methods = WC()->shipping->get_shipping_methods();
	
	foreach($shipping_methods as $shipping_method){
		
	//  print_r($shipping_method);
	//  die(); 
	// 	$shipping_method->init();
		$shipping_method = array();
		if(!empty($shipping_method))



		{
			foreach($shipping_method  as $key => $val){
				
		
				$rate_table[$key] = $val->label; 
			}
		}
	
		
	} 

	$rate_table[WC()->session->get( 'chosen_shipping_methods' )[0]]=(isset($rate_table[WC()->session->get( 'chosen_shipping_methods' )[0]])?$rate_table[WC()->session->get( 'chosen_shipping_methods' )[0]]:'');
