<?php 

require( realpath(__DIR__ . '/../wp-load.php'));

	//global $woocommerce;

	echo get_woocommerce_currency_symbol(get_woocommerce_currency());


?>
