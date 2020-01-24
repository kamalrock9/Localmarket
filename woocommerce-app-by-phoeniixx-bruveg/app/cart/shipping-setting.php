<?php

// Get Free Shipping Methods for Rest of the World Zone & populate array $min_amounts
 
$default_zone = new WC_Shipping_Zone(0);

$default_methods = $default_zone->get_shipping_methods();

foreach( $default_methods as $key => $value ) {
    if ( $value->id === "free_shipping" ) {
      if ( $value->min_amount > 0 ) $min_amounts[] = $value->min_amount;
    }
}
 
// Get Free Shipping Methods for all other ZONES & populate array $min_amounts
 
$delivery_zones = WC_Shipping_Zones::get_zones();
 
foreach ( $delivery_zones as $key => $delivery_zone ) {
  foreach ( $delivery_zone['shipping_methods'] as $key => $value ) {
    if ( $value->id === "free_shipping" ) {
    if ( $value->min_amount > 0 ) $min_amounts[] = $value->min_amount;
    }
  }
}
 
// Find lowest min_amount
 
if ( is_array($min_amounts) ) {
 
$min_amount = min($min_amounts);
 
// Get Cart Subtotal inc. Tax excl. Shipping
 
$current = WC()->cart->subtotal;
 
// If Subtotal < Min Amount Echo Notice
// and add "Continue Shopping" button
$shipping = '';
if ( $current < $min_amount ) {


$shipping = esc_html__('Get free shipping if you order ', 'woocommerce' ) . wc_price( $min_amount - $current ) . esc_html__(' more!', 'woocommerce' );


        }
 
    }
   // print_r($response);
 
?>