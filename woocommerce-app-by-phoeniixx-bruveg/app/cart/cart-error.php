<?php 

function cart_error($product_id,$variation_id=0,$quantity=0){
	
	$cart_item_data = (array) apply_filters( 'woocommerce_add_cart_item_data', WC()->cart->get_cart(), $product_id, $variation_id = 0, $quantity );
	
	$cart_id = WC()->cart->generate_cart_id( $product_id );

	$cart_item_key = WC()->cart->find_product_in_cart( $cart_id );

	$product_data = wc_get_product($product_id );
	
	$error_report=0;

	if ( $product_data->is_sold_individually() ) {
		
		$quantity      = apply_filters( 'woocommerce_add_to_cart_sold_individually_quantity', 1, $quantity, $product_id, $variation_id, $cart_item_data );
		
		$found_in_cart = apply_filters( 'woocommerce_add_to_cart_sold_individually_found_in_cart', $cart_item_key && WC()->cart->cart_contents[ $cart_item_key ]['quantity'] > 0, $product_id, $variation_id, $cart_item_data, $cart_id );

		if ( $found_in_cart ) {
			/* translators: %s: product name */
			$error_report = array("code"=>"0", "message"=> sprintf( __( 'You cannot add another %s to your cart.', 'woocommerce' ), $product_data->get_name()));
			
		}
	}
	
	if ( ! $product_data->is_purchasable() ) {
		$error_report = array("code"=>"0", "message"=> sprintf( __( 'Sorry, &quot;%s&quot;  product cannot be purchased.', 'woocommerce' ),$product_data->get_name()));
	}

	// Stock check - only check if we're managing stock and backorders are not allowed.
	if ( ! $product_data->is_in_stock() ) {
		/* translators: %s: product name */
		
		$error_report = array("code"=>"0", "message"=> sprintf( __( 'You cannot add &quot;%s&quot; to the cart because the product is out of stock.', 'woocommerce' ), $product_data->get_name() ));
		
	}

	if ( ! $product_data->has_enough_stock( $quantity ) ) {
		/* translators: 1: product name 2: quantity in stock */
		$error_report = array("code"=>"0", "message"=> sprintf( __( 'You cannot add that amount of &quot;%1$s&quot; to the cart because there is not enough stock (%2$s remaining).', 'woocommerce' ), $product_data->get_name(), wc_format_stock_quantity_for_display( $product_data->get_stock_quantity(), $product_data ) ));
		
	}

	// Stock check - this time accounting for whats already in-cart.
	if ( $product_data->managing_stock() ) {
		$products_qty_in_cart = WC()->cart->get_cart_item_quantities();

		if ( isset( $products_qty_in_cart[ $product_data->get_stock_managed_by_id() ] ) && ! $product_data->has_enough_stock( $products_qty_in_cart[ $product_data->get_stock_managed_by_id() ] + $quantity ) ) {
			$error_report = array("code"=>"0", "message"=>			
					/* translators: 1: quantity in stock 2: current quantity */
					sprintf( __( 'You cannot add that amount to the cart &mdash; we have %1$s in stock and you already have %2$s in your cart.', 'woocommerce' ), wc_format_stock_quantity_for_display( $product_data->get_stock_quantity(), $product_data ), wc_format_stock_quantity_for_display( $products_qty_in_cart[ $product_data->get_stock_managed_by_id() ], $product_data ) )
				
			);
		}
	}
	
	return $error_report;
	
}
?>