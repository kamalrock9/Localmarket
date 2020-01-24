<?php
//Change the value of PAYTM_MERCHANT_KEY constant with details received from Paytm.
$settings=get_option('woocommerce_paytm_settings',null);
if($settings!=null){
    define('PAYTM_MERCHANT_KEY', $settings['secret_key']);
} 
?>
