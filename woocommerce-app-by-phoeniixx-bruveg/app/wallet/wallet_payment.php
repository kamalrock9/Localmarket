<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$woo_wallet_class = new Woo_Gateway_Wallet_payment();
wp_set_current_user($_GET['CUST_ID'] );
 $order_data_response = $woo_wallet_class->process_payment($_GET['ORDER_ID']);
print_r($order_data_response);
?>
