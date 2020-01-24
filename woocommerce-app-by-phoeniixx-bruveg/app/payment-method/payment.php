<?php 

if ( ! defined( 'ABSPATH' ) ) exit;
session_start();
$order_id = $_GET['ORDER_ID'];

$customer_id = $_GET['CUST_ID'];	

//wp_set_current_user($customer_id);
//wp_clear_auth_cookie();
session_start();
//$sesion_var = new WC_Session_Handler();
//$sesion_var->delete_session($customer_id);
$user = get_user_by( 'id', $customer_id ); 
if( $user ) {
    wp_set_current_user( $customer_id, $user->user_login );
    wp_set_auth_cookie( $customer_id );
    do_action( 'wp_login', $user->user_login,$user );
}

if($_GET['payment_method']=='razorpay'){

	Class Phoen_Razor_pay extends WC_Razorpay{
		
		public function __construct($order_id){
					
			parent::__construct(true);
			
			WC()->session->set('razorpay_wc_order_id', $order_id);
			
		}
		
		function check_razorpay_response()
        {
            global $woocommerce;

            $orderId = $woocommerce->session->get('razorpay_wc_order_id');
           
        }
		
	}

	$woo_WC_Razorpay = new Phoen_Razor_pay($order_id);
	//$woocommerce->session->set('razorpay_wc_order_id', $order_id);
	$order_data_response = $woo_WC_Razorpay->process_payment($order_id);
	
	if ( isset( $order_data_response['result'] ) && 'success' === $order_data_response['result'] ) {
      $order_data_response = apply_filters( 'woocommerce_payment_successful_result', $order_data_response, $order_id );

      if ( ! is_ajax() ) {
        wp_safe_redirect( $order_data_response['redirect'] );
        exit;
      }
	  wp_send_json( $order_data_response );
    }
	
}else if($_GET['payment_method']=='paytm'){

	$woo_WC_paytm = new WC_paytm();

	$order_data_response = $woo_WC_paytm->process_payment($order_id);
	
	if ( isset( $order_data_response['result'] ) && 'success' === $order_data_response['result'] ) {
      $order_data_response = apply_filters( 'woocommerce_payment_successful_result', $order_data_response, $order_id );

		if ( ! is_ajax() ) {
			wp_redirect( $order_data_response['redirect'] );
			exit;
		}
    }
}else if($_GET['payment_method']=='stripe'){

	$woo_WC_stripe = new stripe();

	$order_data_response = $woo_WC_stripe->process_payment($order_id);
	
	if ( isset( $order_data_response['result'] ) && 'success' === $order_data_response['result'] ) {
      $order_data_response = apply_filters( 'woocommerce_payment_successful_result', $order_data_response, $order_id );

		if ( ! is_ajax() ) {
			wp_redirect( $order_data_response['redirect'] );
			exit;
		}
    }
}else if($_GET['payment_method']=='paypal'){

	$paypal = new WC_Gateway_Paypal();

	$order_data_response = $paypal->process_payment($order_id);
	
	if ( isset( $order_data_response['result'] ) && 'success' === $order_data_response['result'] ) {
      $order_data_response = apply_filters( 'woocommerce_payment_successful_result', $order_data_response, $order_id );

		if ( ! is_ajax() ) {
			wp_redirect( $order_data_response['redirect'] );
			exit;
		}
    }
}else if($_GET['payment_method']=='pumcp'){

	$pumcp = new WC_Pumcp();

	$order_data_response = $pumcp->process_payment($order_id);
	
	if ( isset( $order_data_response['result'] ) && 'success' === $order_data_response['result'] ) {
      $order_data_response = apply_filters( 'woocommerce_payment_successful_result', $order_data_response, $order_id );

		if ( ! is_ajax() ) {
			wp_redirect( $order_data_response['redirect'] );
			exit;
		}
    }
}else if($_GET['payment_method']=='instamojo'){
	
	class payment_instamojo extends WP_Gateway_Instamojo{
		
		public $order_id;
		public  function __construct($order_id){
			parent::__construct();
			$this->order_id = $order_id;
			
		}
		
		public function get_order_total(){
			$order = new WC_Order( $this->order_id );
			return $order->get_total();

		}
		
	}

	$Instamojo = new payment_instamojo($order_id);
	//$Instamojo = new WP_Gateway_Instamojo($order_id);

	$order_data_response = $Instamojo->process_payment($order_id);
	
	if ( isset( $order_data_response['result'] ) && 'success' === $order_data_response['result'] ) {
      $order_data_response = apply_filters( 'woocommerce_payment_successful_result', $order_data_response, $order_id );

		if ( ! is_ajax() ) {
			wp_redirect( $order_data_response['redirect'] );
			exit;
		}
    }
}
?>