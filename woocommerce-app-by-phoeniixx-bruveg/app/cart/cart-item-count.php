<?php
 
require( realpath(__DIR__ . '/../wp-load.php'));

include_once('./autoload.php');

global $woocommerce;

//WC()->cart->init();

echo json_encode(WC()->cart->get_cart_contents_count());
?>