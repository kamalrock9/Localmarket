<?php
/*
Plugin Name: Woocommerce app by phoeniixx

Plugin URI: http://www.phoeniixx.com

Description: This Plugin will convert your woocommerce store into app.

Version: 1.0.0

Text Domain: phoen-woo-app

Domain Path: /i18n/languages/

Author: phoeniixx

Author URI: http://www.phoeniixx.com

** WC requires at least: 2.6.0

** WC tested up to: 3.4.4c

*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) 
{	

	add_action('admin_head', 'phoe_app_script_main');

	define("woo_app_plugin_dir_url", esc_url( plugin_dir_url( __FILE__ ) ) );
	
	function phoe_app_script_main(){
		
		wp_enqueue_media();
		
		wp_enqueue_style( 'wp-color-picker');
		
		wp_enqueue_style( 'jquery-ui-accordion');
		
        wp_enqueue_script( 'wp-color-picker');
		
		wp_enqueue_script( 'jquery-ui-accordion');
		
		wp_enqueue_script("jquery-effects-core");
		
		wp_enqueue_style( 'custom-style-datetimepicker', woo_app_plugin_dir_url . '/assets/css/admin_jquery_css_backend.css'); 

		wp_enqueue_script( 'phoen-app-script-select2', woo_app_plugin_dir_url . '/assets/js/select2.min.js'); 

		wp_enqueue_style( 'phoen-app-style-select2', woo_app_plugin_dir_url . '/assets/css/select2.min.css'); 

		wp_enqueue_style( 'phoen-app-style-switchButton', woo_app_plugin_dir_url . '/assets/css/jquery.switchButton.css'); 

		wp_enqueue_script( 'phoen-app-jquery-switchButton', woo_app_plugin_dir_url . '/assets/js/jquery.switchButton.js'); 
		
	}
	
	add_action('admin_menu', 'phoe_app_menu',99);
	
	function phoe_app_menu() {
			
		$app = __('Woocommerce App','phoen-woo-app');
		
		add_submenu_page( 'woocommerce', 'phoeniixx_woo_app', $app,'manage_options', 'phoeniixx_woo_app',  'phoen_app_backend_func' );

	}

/* 	add_action( 'woocommerce_payment_complete', 'so_payment_complete' );
	
	function so_payment_complete( $order_id ){
		$data = get_post_meta( $order_id, '_phoen_order_app', true );
		if($data==1){
			
		}
	} */
	
	register_activation_hook(__FILE__, 'phoen_app_plugin_activation');
	
	function phoen_app_plugin_activation(){
		
		$phoen_app_styling_setting=get_option("phoen_app_styling_setting");

		if(empty($phoen_app_styling_setting))	{
			
				$final_array=array(
					"secondary_text_color"=>"#757575",
					"primary_text_color"=>"#212121",
					"accent_color"=>"#f28529",
					"primary_color_text"=>"#FFFFFF",
					"primary_color_light"=>"#F8BBD0",
					"primary_color_dark"=>"#1b202a",
					"primary_color"=>"#1f2528"
				);
				
				update_option("phoen_app_styling_setting",$final_array);
				
		}

		$phoen_app_layout_setting=get_option("phoen_app_layout_setting");

		if(empty($phoen_app_layout_setting))	{
			
				$final_array=array(
					"top_seller"=>true,
					"tspnumber"=>6,
					"featured_products"=>true,
					"fpnumber"=>6,
					"sale_products"=>true,
					"salepnumber"=>6,
					"top_rated_products"=>true,
					"top_rated_pnumber"=>6,
					"front_page_category"=>array()
				);
				
				update_option("phoen_app_layout_setting",$final_array);
				
		}
		$phoen_app_refer_earn_layout_setting=get_option("phoen_app_refer_earn_layout_setting");
		if(empty($phoen_app_refer_earn_layout_setting))	{
			
			$final_array=array(
			"refer_earn_on"=>true,
			"refer_earn_msg"=>"Use my referral code {referralcode} and get Wallet credit of {referralamount}. Download Wooapp and sign up using my code from below link.",
			"refer_referrer_amt"=>10,
			'refer_earner_amt'=>10,
			"refer_earn_uses"=>5,
			"refer_earn_banner_url"=>true
			
			);
				
			update_option("phoen_app_refer_earn_layout_setting",$final_array);
				
		}

		$phoen_term_condition_setting=get_option('phoen_term_condition_setting');
		if(empty($phoen_term_condition_setting)){

			$final_array=array(

				'term_condition'=>'',
				'enable_term_condition'=>false
			);

			update_option('phoen_term_condition_setting', $final_array);
		}
		
	}
	
	add_action('wp_head','phoen_app_enqueue_scripts');
	
	function phoen_app_enqueue_scripts(){
		
		wp_enqueue_style( 'phoen_app_complete_css', plugin_dir_url(__FILE__).'assets/css/style.css'); 
		
	}
	
	function phoen_getClosest($search, $arr) {
			
			$end_arr=end($arr);
			
		   $closest = null;
		   
		   if(isset($arr) && is_array($arr)){
			   
			    foreach ($arr as $key => $item) {
					
					$num_val=$key-1;
					
				  if($key!==0 && $search <= $item && $search > $arr["$num_val"]){
					  
					 $closest = $item;
					 
				  }elseif($arr[0] > $search){
					  
					  $closest=$arr[0];
				  }else{
					
					  if($search > $end_arr){
						  
						  $closest=$end_arr;
						  
					  }
					  if($search==$arr["$key"]){
						  
						   $closest = $item;
						   
					  }
					  
				  }
			   }
		   }
		  
		   return $closest;
		}
	
	add_action( 'rest_api_init', 'phoen_woo_app_template');
	
	function phoen_woo_app_template(){
		
		if ( version_compare( WC_VERSION, '3.6.0', '>=' ) && WC()->is_rest_api_request() == 'wc/' ) {
			require_once( WC_ABSPATH . 'includes/wc-cart-functions.php' );
			require_once( WC_ABSPATH . 'includes/wc-notice-functions.php' );
 			if ( null === WC()->session ) {
				$session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );
 				// Prefix session class with global namespace if not already namespaced
				if ( false === strpos( $session_class, '\\' ) ) {
					$session_class = '\\' . $session_class;
				}
 				WC()->session = new $session_class();
				WC()->session->init();
			}
 			/**
			 * For logged in customers, pull data from their account rather than the 
			 * session which may contain incomplete data.
			 */
			if ( null === WC()->customer ) {
				if ( is_user_logged_in() ) {
					WC()->customer = new WC_Customer( get_current_user_id() );
				} else {
					WC()->customer = new WC_Customer( get_current_user_id(), true );
				}
			}
 			// Load Cart.
			if ( null === WC()->cart ) {
				WC()->cart = new WC_Cart();
			}
		}
		
		/*---------------- Cart API List Start -------------------*/
		
		register_rest_route( 'wc/v2','/cart', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoen_woo_app_get_cart' ,
			'args'     => array(
				'thumb' => array(
					'default' => null
				),
			),
			
		));
		
		register_rest_route( 'wc/v2','/product/thumbnail', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoen_woo_app_product_thumbnail' ,
			'args'     => array(
				'thumb' => array(
					'default' => null
				),
			),
			
		));
		register_rest_route( 'wc/v2','/cart/add', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoen_woo_app_add_to_cart' ,
			'args'     => array(
				'thumb' => array(
					'default' => null
				),
			),
			
		));
		
		register_rest_route( 'wc/v2','/cart/update', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoen_woo_app_update_cart' ,
			'args'     => array(
				'thumb' => array(
					'default' => null
				),
			),
			
		));
		
		register_rest_route( 'wc/v2','/cart/remove', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoen_woo_app_remove_cart' ,
			'args'     => array(
				'thumb' => array(
					'default' => null
				),
			),
			
		));
		
		register_rest_route( 'wc/v2','/cart/clear', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoen_woo_app_clear_cart' ,
			'args'     => array(
				'thumb' => array(
					'default' => null
				),
			),
			
		));
		
		register_rest_route( 'wc/v2','/cart/totals', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoen_woo_app_get_totals' ,
			'args'     => array(
				'thumb' => array(
					'default' => null
				),
			),
			
		));
		
		register_rest_route( 'wc/v2','/cart/coupon', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoen_woo_app_apply_coupon' ,
			'args'     => array(
				'thumb' => array(
					'default' => null
				),
			),
			
		));
		
		register_rest_route( 'wc/v2','/cart/remove-coupon', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoen_woo_app_remove_coupon' ,
			'args'     => array(
				'thumb' => array(
					'default' => null
				),
			),
			
		));
		
		register_rest_route( 'wc/v2','/cart/item-count', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoen_woo_app_cart_item_count' ,
			'args'     => array(
				'thumb' => array(
					'default' => null
				),
			),
			
		));
		
		/*---------------- Cart API List End -------------------*/
		
		/*---------------- Checkout API List Start -------------------*/
		
		register_rest_route( 'wc/v2','checkout/review-order', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoen_woo_app_review_order' ,
			'args'     => array(
				'thumb' => array(
					'default' => null
				),
			),
			
		));
		
		register_rest_route( 'wc/v2','checkout/new-order', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoen_woo_app_new_order' ,
			'args'     => array(
				'thumb' => array(
					'default' => null
				),
			),
			
		));
		register_rest_route( 'wc/v2','checkout/paytm-checksum', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoen_woo_app_checkout_paytm_checksum' ,
			'args'     => array(
				'thumb' => array(
					'default' => null
				),
			),
			
		));
		
		/*---------------- Checkout API List End -------------------*/

		/*---------------- Product API List Start -------------------*/
		
		register_rest_route( 'wc/v2','products/get-variation', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoen_woo_app_get_variation' ,
			'args'     => array(
				'thumb' => array(
					'default' => null
				),
			),
			
		));
		
		register_rest_route( 'wc/v2','custom-products', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoen_woo_app_products' ,
			'args'     => array(
				'thumb' => array(
					'default' => null
				),
			),
			
		));

		register_rest_route( 'wc/v2','products/custom-attributes', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoen_woo_app_custom_attributes' ,
			'args'     => array(
				'thumb' => array(
					'default' => null
				),
			),
			
		));
		register_rest_route( 'wc/v2','products/all-categories', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoen_woo_app_all_categories' ,
			'args'     => array(
				'thumb' => array(
					'default' => null
				),
			),
			
		));
		
		register_rest_route( 'wc/v2','products/all-brands', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoen_woo_app_all_brands' ,
			'args'     => array(
				'thumb' => array(
					'default' => null
				),
			),
			
		));
		
		register_rest_route( 'wc/v2','products/price-range', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoen_woo_app_price_range' ,
			'args'     => array()
		));
		register_rest_route( 'wc/v2','custom-search', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoen_woo_app_custom_search' ,
			'args'     => array(
				'search'=>array(
					'default'=>null
				)
			)
		));
		register_rest_route( 'wc/v2','get-product-by-url', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoen_woo_app_get_product_by_url' ,
			'args'     => array(
				'url'=>array(
					'default'=>null
				)
			)
		));
		register_rest_route( 'wc/v2','get-products-by-id', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoen_woo_app_get_products_by_id' ,
			'args'     => array(
				'include'=>array(
					'default'=>null
				)
			)
		));
		/*---------------- Product API List End -------------------*/
		
		register_rest_route( 'wc/v2','app-settings', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoen_woo_app_app_settings' ,
			'args'     => array(
				'thumb' => array(
					'default' => null
				),
			),
			
		));
		
		register_rest_route( 'wc/v2','faq-settings', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoen_woo_app_faq_settings' ,
			'args'     => array(
				'thumb' => array(
					'default' => null
				),
			),
			
		));
		
		
		/*---------------- Login API List Start -------------------*/
		
		register_rest_route( 'wc/v2','login', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoen_woo_app_login' ,
			'args'     => array(
				'thumb' => array(
					'default' => null
				),
			),
			
		));
		
		register_rest_route( 'wc/v2','logout', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoen_woo_app_log_out' ,
			'args'     => array(
				'thumb' => array(
					'default' => null
				),
			),
			
		));
		
		register_rest_route( 'wc/v2','register', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoen_woo_app_register' ,
			'args'     => array(
				'thumb' => array(
					'default' => null
				),
			),
			
		));
		register_rest_route( 'wc/v2','forget-password', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoen_woo_app_forget_password' ,
			'args'     => array(
				'thumb' => array(
					'default' => null
				),
			),
			
		));
		register_rest_route( 'wc/v2','change-password', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoen_woo_app_change_password' ,
			'args'     => array(
				'thumb' => array(
					'default' => null
				),
			),
			
		));
		register_rest_route( 'wc/v2','social-login', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoen_woo_app_social_login' ,
			'args'     => array(
				'thumb' => array(
					'default' => null
				),
			),
			
		));
		
		/*---------------- Login API List End -------------------*/
		/*---------------- pincode API List Start -------------------*/
		
		
		if ( in_array( 'woocommerce-pincode-check-pro-unl-num/woocommerce-pincode-check.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) 
		{	
			register_rest_route( 'wc/v2','/checkpincode', array(
				'methods'  =>WP_REST_Server::ALLMETHODS,
				'callback' => 'phoen_woo_app_checkpincode' ,
				'args'     => array(
					'thumb' => array(
						'default' => null
					),
				),
				
			));
		
		}
	
		register_rest_route( 'wc/v2','/payment', array(
				'methods'  =>WP_REST_Server::ALLMETHODS,
				'callback' => 'phoen_woo_app_payment' ,
				'args'     => array(
					'thumb' => array(
						'default' => null
					),
				), 
				
			));
	
		if ( in_array( 'woo-wallet/woo-wallet.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) 
		{	
			register_rest_route( 'wc/v2','/wallet', array(
				'methods'  =>WP_REST_Server::ALLMETHODS,
				'callback' => 'phoen_woo_app_wallet' ,
				'args'     => array(
					'thumb' => array(
						'default' => null
					),
				), 
				
			));
			register_rest_route( 'wc/v2','/wallet/payment', array(
				'methods'  =>WP_REST_Server::ALLMETHODS,
				'callback' => 'phoen_woo_app_wallet_payment' ,
				'args'     => array(
					'thumb' => array(
						'default' => null
					),
				), 
				
			));
			register_rest_route( 'wc/v2','/wallet/add', array(
				'methods'  =>WP_REST_Server::ALLMETHODS,
				'callback' => 'phoen_woo_app_wallet_add' ,
				'args'     => array(
					'thumb' => array(
						'default' => null
					),
				), 
				
			));
		
		}
			
			
		/*---------------- pincode API List End -------------------*/
		/*Refer And Earn*/
		register_rest_route( 'wc/v2','/refer', array(
				'methods'  =>WP_REST_Server::ALLMETHODS,
				'callback' => 'phoen_woo_app_refer_earn' ,
				'args'     => array(
					'thumb' => array(
						'default' => null
					),
				), 
				
			));
			register_rest_route( 'wc/v2','/referapply', array(
				'methods'  =>WP_REST_Server::ALLMETHODS,
				'callback' => 'phoen_woo_app_refer_apply' ,
				'args'     => array(
					'thumb' => array(
						'default' => null
					),
				), 
				
			));
		/*---------------- Refer Earn End -------------------*/	

		/*---------------- Layout Api List -------------------*/

		register_rest_route( 'wc/v2','/layout', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoen_woo_app_layout',
			'args'     => array(
				'thumb' => array(
					'default' => null
				),
			),
		));

		/*---------------- Layout API List End -------------------*/

		/*---------------- Terms and Conditions Api List -------------------*/

		register_rest_route( 'wc/v2','/terms', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoen_woo_app_terms',
			'args'     => array(
				'thumb' => array(
					'default' => null
				),
			),
		));

		/*---------------- Terms and Conditions API List End -------------------*/
		
		/*------------------ Review Api List ---------------------*/
		register_rest_route( 'wc/v2','/product/review-settings', array(
			'methods'  =>WP_REST_Server::ALLMETHODS,
			'callback' => 'phoen_woo_app_review_settings' ,
			'args'     => array(
				'email' => array(
					'default' => null
				),
				'user_id' => array(
					'default' => null
				),
				'product_id' => array(
					'default' => null
				)
			)
		
		));
		/*---------------- Review Api List End -------------------*/


		/*---------------- shipping Api List -------------------*/

				register_rest_route( 'wc/v2','/shipping-setting', array(
					'methods'  =>WP_REST_Server::ALLMETHODS,
					'callback' => 'phoen_woo_app_shipping_setting',
					'args'     => array(
						'thumb' => array(
							'default' => null
						),
					),
				));
		
		/*---------------- shipping Api List -------------------*/
	}

	function phoen_woo_app_shipping_setting(){
		include_once(plugin_dir_path(__FILE__).'app/shipping-setting.php');
		return new WP_REST_Response( $response, 200 );
	}
	function phoen_woo_app_review_settings($data=array()){
	include_once(plugin_dir_path(__FILE__).'app/review-settings.php');
	return new WP_REST_Response( $response, 200 );
	}
function phoen_woo_app_refer_earn(){
	include_once(plugin_dir_path(__FILE__).'app/referearn/main.php');
	return new WP_REST_Response( $referearn_data, 200 );
}
function phoen_woo_app_refer_apply(){
	include_once(plugin_dir_path(__FILE__).'app/login/refer_credit.php');
	return new WP_REST_Response( $refercredit_data, 200 );
}
function phoen_woo_app_social_login(){
	
	include_once(plugin_dir_path(__FILE__).'app/format_customer_response.php');
	include_once(plugin_dir_path(__FILE__).'app/login/social.php');
	return new WP_REST_Response( $social_data, 200 );
}
	
function phoen_woo_app_payment(){
	
	include_once(plugin_dir_path(__FILE__).'app/payment-method/payment.php');

}
	
	function phoen_woo_app_wallet_add(){
		include_once(plugin_dir_path(__FILE__).'app/wallet/wallet_add.php');
		return new WP_REST_Response( $wallet_data, 200 );
	}	
	
	function phoen_woo_app_product_thumbnail(){
		
		$product_id=$_GET['id'];
		
		$image_size = apply_filters( 'single_product_archive_thumbnail_size', 'shop_catalog' );
				
		$tumbnail['src'] = get_the_post_thumbnail_url( $product_id,$image_size);
		
		return new WP_REST_Response( $tumbnail, 200 );
		
	}
	
	function phoen_woo_app_custom_attributes(){
		
		include_once(plugin_dir_path(__FILE__).'app/product/get-product-attributes.php');
		
		return new WP_REST_Response( $response, 200 );
		
	}
	function phoen_woo_app_all_categories(){
		
		include_once(plugin_dir_path(__FILE__).'app/product/get-all-categories.php');
		
		return new WP_REST_Response( $response, 200 );
		
	}
	
	function phoen_woo_app_all_brands(){
		
		include_once(plugin_dir_path(__FILE__).'app/product/get-all-brands.php');
		
		return new WP_REST_Response( $response, 200 );
		
	}
	
	function phoen_woo_app_price_range(){
		
		include_once(plugin_dir_path(__FILE__).'app/product/product-price-range.php');
		
		return new WP_REST_Response( $price_range, 200 );
		
	}
	
	function phoen_woo_app_custom_search($data=array()){
		
		include_once(plugin_dir_path(__FILE__).'app/product/format-product-response.php');
		include_once(plugin_dir_path(__FILE__).'app/product/search.php');
		return new WP_REST_Response( $response, 200 );
	}
	
	function phoen_woo_app_get_product_by_url($data = array()){
		
		include_once(plugin_dir_path(__FILE__).'app/product/get-product-by-url.php');
		
		return new WP_REST_Response( $response, 200 );
	}
	
	function phoen_woo_app_get_products_by_id($data = array()){
		include_once(plugin_dir_path(__FILE__).'app/product/get-products-by-id.php');
		
		return new WP_REST_Response( $response, 200 );
	}
	
	function phoen_woo_app_wallet_payment(){
		
		include_once(plugin_dir_path(__FILE__).'app/autoload.php');	
		
		include_once(plugin_dir_path(__FILE__).'app/wallet/wallet_payment.php');
		
		return new WP_REST_Response( $response, 200 );
	}
	
	function phoen_woo_app_wallet(){
		
		include_once(plugin_dir_path(__FILE__).'app/wallet/wallet.php');
		
		return new WP_REST_Response( $user_wallet, 200 );
		
	}
	
	function pincode_verify($pincode,$product_id,$quantity){
		
		global $table_prefix, $wpdb,$woocommerce;
		
		$safe_zipcode = $pincode;

		$qry22 = $wpdb->get_results("SELECT * FROM `".$table_prefix."pincode_setting_pro` ORDER BY `id` ASC  limit 1",ARRAY_A);

		$responce['delivery']=true;

		if( $pincode != '' )
		{
			
			// $phen_pincodes_list = get_post_meta( $product_id, 'phen_pincode_list');
				
			$star_pincode = substr($pincode, 0, 3).'*';
			
			$phen_pincodes_list = get_post_meta( $product_id, 'phen_pincode_list' );
						
			$phen_pincode_list = isset($phen_pincodes_list[0])?$phen_pincodes_list[0]:'';
			
			if(isset($phen_pincodes_list) && empty($phen_pincodes_list[0])){
				
				$phen_pincodes_list='';
				
			}
			
			if($phen_pincodes_list=='' || count($phen_pincodes_list) == 0 )
			{ 
				
					$safe_zipcode = $pincode;
				
					$pincode = substr($pincode, 0, 3);
					
					$show_d_d_on_pro =  get_option( 'woo_pin_check_show_d_d_on_pro' );
					
					$table_pin_codes = $table_prefix."check_pincode_pro";
								
					
					if($safe_zipcode)
					{
						
						$count = $wpdb->get_var( $wpdb->prepare( "select COUNT(*) from $table_pin_codes where `pincode` = %s ", $safe_zipcode ) );
						
						$like = false;
						
						 // 'count:'.$count;
						
						if( $count == 0  )
						{
							
							$ppook = "SELECT * FROM `$table_pin_codes` where pincode LIKE '".$wpdb->esc_like($pincode)."%'";
							
							$ftc_ary = $wpdb->get_results($ppook);
							
							$tem_pin=$ftc_ary[0]->pincode;
							
							 $count=count($ftc_ary);
							
							$like = true;
							
							
						}
						
						if( $count == 0 || (isset($tem_pin) && strpos($tem_pin,'*')===false))
						{
							$responce['delivery']=false;
						 
						}
						else
						{
							if( $like )
							{
								
								$query = "SELECT * FROM `$table_pin_codes` where pincode LIKE '".$wpdb->esc_like($pincode)."%'";
								
							}
							else
							{
								
								$query = "SELECT * FROM `$table_pin_codes` where pincode='$safe_zipcode'"; 
								
							}
							
							
							
							$ftc_ary = $wpdb->get_results($query);
						
							$dod = $ftc_ary[0]->dod;
							
							$dod_name = $ftc_ary[0]->dod_name;
							
							$state = $ftc_ary[0]->state;
							
							$city = $ftc_ary[0]->city;
							
							$deliver_by = $ftc_ary[0]->deliver_by;
							 
							if($deliver_by=="day"){
								
								if($dod >= 1)
								{
									
									for($i=1; $i<=$dod; $i++)
									{
											$dd = date("D", strtotime("+ $i day"));
											
											if($qry22[0]['s_s'] == 0)
											{			
										
												if($dd == 'Sat')
												{	
											
													$dod++;	
												}
												
											}
											
											if($qry22[0]['s_s1'] == 0)
											{
												
												if($dd == 'Sun')
												{	
											
													$dod++;	
												}
												
											}
											
									}
									
									$delivery_date = date("D, jS M", strtotime("+ $dod day"));
									
								}else{
									
									 $delivery_date = date("D, jS M");
									 
								}
							}elseif($deliver_by=="time_picker"){
								
								$time_hrs=$ftc_ary[0]->time_hrs;
								
								$time_minuts=$ftc_ary[0]->time_minuts;
								
								 $start = current_time('Y-m-d H:i');
								 
								$delivery_date=date("d-m-Y H:i", strtotime("+$time_hrs hours +$time_minuts minutes",strtotime($start)));
									
							}elseif($deliver_by=="quantity"){
								 
								 $delivery_days=isset($ftc_ary[0]->deliver_day) ? $ftc_ary[0]->deliver_day:'';	

								$delivery_quantity=isset($ftc_ary[0]->deliver_quantity) ? $ftc_ary[0]->deliver_quantity:'';
								 
								 if($quantity !==false){
									 					
											$delivery_quantity_array=explode(",",$delivery_quantity);
											
											 $delivery_days_array=explode(",",$delivery_days);

											$min_array=array_combine($delivery_quantity_array,$delivery_days_array);
											
											$delvery_day= phoen_getClosest($quantity,$delivery_quantity_array);
										
											$min_array_min=$min_array[$delvery_day];
											
											if($min_array_min==1){
												
												$day_days="Day";
												
											}else{
												
												$day_days="Days";
												
											}
											
											$delivery_date=$min_array_min." $day_days";
																															 
								 }else{
									 
									 $delivery_date="Quantity Based";
									 
								 }
								 
							}
							else
							{
								
								
								$dod_name = $ftc_ary[0]->dod_name;
								
								if($dod_name!='')
								{										
									 $delivery_date =  date('D, jS M', strtotime("next $dod_name"));
								
								}else{
									
									$delivery_date = '';
								}
								
							}
							
							
							if($ftc_ary[0]->cod == 'no')
							{
								
								
								if($show_d_d_on_pro == 1)
								{
									$responce['delivery_date']=  $delivery_date;
								}
								else
								{
									
									if($dod!='0')
									{
										$responce['delivery_date']=  $ftc_ary[0]->dod." days";
									}else{
										$dod_name = $ftc_ary[0]->dod_name;
										
										$responce['delivery_date']=   date('D, jS M', strtotime("next $dod_name"));
									}
									
								}
								
								
								$responce['cod_message']= $qry22[0]['cod_msg2'];
							
								$responce['state']= $state;
								
								$responce['city']= $city;
											
							}
							elseif($ftc_ary[0]->cod == 'yes')
							{
								
								if($show_d_d_on_pro == 1)
								{
									$responce['delivery_date']=  $delivery_date;
								}
								else
								{
									if($dod!='0')
									{
										$responce['delivery_date']=  $ftc_ary[0]->dod." days";
										
									}else{
										
										if($dod!='0')
										{
											$responce['delivery_date']=  $ftc_ary[0]->dod." days";
										}else{
											
											$dod_name = $ftc_ary[0]->dod_name;
										
											$responce['delivery_date']=   date('D, jS M', strtotime("next $dod_name"));
										}
									}
								}
								
								$responce['cod_message']= $qry22[0]['cod_msg1'];
							
								$responce['state']= $state;
								
								$responce['city']= $city;
								
							}
							
							
							$customer = new WC_Customer();
					
							$customer->set_shipping_postcode($safe_zipcode);
								
							$user_ID = get_current_user_id();
							
							if(isset($user_ID) && $user_ID != 0) {
								
								update_user_meta($user_ID, 'shipping_postcode', $safe_zipcode); //for setting shipping postcode
								
							}
							
						}
						// 
					}
					else
					{
						
						$responce['delivery']=false;
						
					}	
										
			}
			else
			{
			
				$phen_pincode_list = $phen_pincodes_list[0];
				$clear_min=false;
				
				if($state_based_pincode==1 && $phen_pincode_list[$pincode][2]==$state_min){
					$clear_min=true;
				}elseif($state_based_pincode!=1){
					$clear_min=true;
				}
				
				if ($clear_min==true &&  array_key_exists( $wpdb->esc_like($pincode),$phen_pincode_list ) )
				{
					
					$safe_zipcode = $pincode;
				
					$dod = $phen_pincode_list[$safe_zipcode][3];
					
					$dod_name = $phen_pincode_list[$safe_zipcode][5];
					
					$state = $phen_pincode_list[$safe_zipcode][2];
					
					$city = $phen_pincode_list[$safe_zipcode][1];
					
					$deliver_by = $phen_pincode_list[$safe_zipcode][6];
					
					$time_hrs = $phen_pincode_list[$safe_zipcode][7];
					
					$time_minuts = $phen_pincode_list[$safe_zipcode][8];
					
					$show_d_d_on_pro =  get_option( 'woo_pin_check_show_d_d_on_pro' );
					
					
					if($deliver_by=="day"){
						
						if($dod >= 1)
						{
							
							for($i=1; $i<=$dod; $i++)
							{
									$dd = date("D", strtotime("+ $i day"));
									
									if($qry22[0]['s_s'] == 0)
									{			
								
										if($dd == 'Sat')
										{	
									
											$dod++;	
										}
										
									}
									
									if($qry22[0]['s_s1'] == 0)
									{
										
										if($dd == 'Sun')
										{	
									
											$dod++;	
										}
										
									}
									
							}
							
							$delivery_date = date("D, jS M", strtotime("+ $dod day"));
							
						}else{
							
							 $delivery_date = date("D, jS M");
							 
						}
					}elseif($deliver_by=="time_picker"){
						
						 $start = current_time('H:i');
						 
						$delivery_date=date("d-m-Y H:i", strtotime("+$time_hrs hours +$time_minuts minutes",strtotime($start)));
							
					}elseif($deliver_by=="quantity"){
						
						$delivery_quantity=isset($finel_array[12])?$finel_array[12]:'';	
				
						$delivery_days=isset($finel_array[13])?$finel_array[13]:'';	
						
						 if($quantity !==false){
									 					
									$delivery_quantity_array=explode(",",$delivery_quantity);
									
									 $delivery_days_array=explode(",",$delivery_days);

									$min_array=array_combine($delivery_quantity_array,$delivery_days_array);
									
									$delvery_day= phoen_getClosest($quantity,$delivery_quantity_array);
								
									$min_array_min=$min_array[$delvery_day];
									
									if($min_array_min==1){
										
										$day_days="Day";
										
									}else{
										
										$day_days="Days";
										
									}
									
									$delivery_date=$min_array_min." $day_days";
																													 
						 }else{
							 
							 $delivery_date="Quantity Based";
							 
						 }
						 							
					}
					else
					{
						
						
						if($dod_name!='')
						{										
							 $delivery_date =  date('D, jS M', strtotime("next $dod_name"));
						
						}else{
							
							$delivery_date = '';
						}
						
					}
					
					if($phen_pincode_list[$safe_zipcode][4] == 'no')
					{
						
						if($show_d_d_on_pro == 1)
						{
							$responce['delivery_date']=  $delivery_date;
						}
						else
						{
						
							if($dod!='0')
							{
								$responce['delivery_date']=  $phen_pincode_list[$safe_zipcode][3]." days";
								
							}else{
								
								$responce['delivery_date']=  date('D, jS M', strtotime("next $dod_name"));
							}
							
						}
						$responce['cod_message']= $qry22[0]['cod_msg2'];
							
						$responce['state']= $state;
						
						$responce['city']= $city;
									
					}
					elseif($phen_pincode_list[$safe_zipcode][4] == 'yes')
					{
						
						if($show_d_d_on_pro == 1)
						{
							$responce['delivery_date']=  $delivery_date;
						}
						else
						{
							if($dod!='0')
							{
								$responce['delivery_date']=  $phen_pincode_list[$safe_zipcode][3]." days";
								
							}else{
								
								$responce['delivery_date']= date('D, jS M', strtotime("next $dod_name"));
							}
						}
						$responce['cod_message']= $qry22[0]['cod_msg1'];
							
						$responce['state']= $state;
						
						$responce['city']= $city;
					}
				
					
				}
				elseif( array_key_exists(  $star_pincode,$phen_pincode_list ) )
				{
					
					
					$safe_zipcode = $pincode;
				
					$dod = $phen_pincode_list[$star_pincode][3];
					
					$dod_name = $phen_pincode_list[$star_pincode][5];
					
					$state = $phen_pincode_list[$star_pincode][2];
					
					$city = $phen_pincode_list[$star_pincode][1];
					
					$deliver_by = $phen_pincode_list[$star_pincode][6];
					
					$time_hrs = $phen_pincode_list[$safe_zipcode][7];
					
					$time_minuts = $phen_pincode_list[$safe_zipcode][8];
					
					$show_d_d_on_pro =  get_option( 'woo_pin_check_show_d_d_on_pro' );
					
					if($deliver_by=="day"){
						
						if($dod >= 1)
						{
							
							for($i=1; $i<=$dod; $i++)
							{
									$dd = date("D", strtotime("+ $i day"));
									
									if($qry22[0]['s_s'] == 0)
									{			
								
										if($dd == 'Sat')
										{	
									
											$dod++;	
										}
										
									}
									
									if($qry22[0]['s_s1'] == 0)
									{
										
										if($dd == 'Sun')
										{	
									
											$dod++;	
										}
										
									}
									
							}
							
							$delivery_date = date("D, jS M", strtotime("+ $dod day"));
							
						}else{
							
							 $delivery_date = date("D, jS M");
							 
						}
					}elseif($deliver_by=="time_picker"){
						
						 $start = current_time('H:i');
						 
						$delivery_date=date("d-m-Y H:i", strtotime("+$time_hrs hours +$time_minuts minutes",strtotime($start)));
							
					}elseif($deliver_by=="quantity"){
						 
						$delivery_quantity=isset($finel_array[12])?$finel_array[12]:'';	
				
						$delivery_days=isset($finel_array[13])?$finel_array[13]:'';	
						
						 if($quantity !==false){
									 					
									$delivery_quantity_array=explode(",",$delivery_quantity);
									
									 $delivery_days_array=explode(",",$delivery_days);

									$min_array=array_combine($delivery_quantity_array,$delivery_days_array);
									
									$delvery_day= phoen_getClosest($quantity,$delivery_quantity_array);
								
									$min_array_min=$min_array[$delvery_day];
									
									if($min_array_min==1){
										
										$day_days="Day";
										
									}else{
										
										$day_days="Days";
										
									}
									
									$delivery_date=$min_array_min." $day_days";
																													 
						 }else{
							 
							 $delivery_date="Quantity Based";
							 
						 }
							
					}
					else
					{
						
						
						if($dod_name!='')
						{
							$delivery_date =  date('D, jS M', strtotime("next $dod_name"));
						
						}else{
							
							$delivery_date = '';
						}
						
					}
					
					
					if($phen_pincode_list[$star_pincode][4] == 'no')
					{	
						
						if($show_d_d_on_pro == 1)
						{
							$responce['delivery_date']=  $delivery_date;
						}
						else
						{
						
							if($dod!='0')
							{
								$responce['delivery_date']=  $phen_pincode_list[$star_pincode][3]." days";
								
							}else{
								
								$responce['delivery_date']=   date('D, jS M', strtotime("next $dod_name"));
							}
							
							
						}
						
						$responce['cod_message']= $qry22[0]['cod_msg2'];
							
						$responce['state']= $state;
						
						$responce['city']= $city;
						
									
					}
					elseif($phen_pincode_list[$star_pincode][4] == 'yes')
					{
						
						if($show_d_d_on_pro == 1)
						{
							$responce['delivery_date']=  $delivery_date;
						}
						else
						{
							if($dod!='0')
							{
								
								$responce['delivery_date']= $phen_pincode_list[$star_pincode][3]." days";
								
							}else{
								
								 $delivery_date =  date('D, jS M', strtotime("next $dod_name"));
								
								$responce['delivery_date']= $delivery_date;
							}
						}
						
						$responce['cod_message']= $qry22[0]['cod_msg1'];
							
						$responce['state']= $state;
						
						$responce['city']= $city;
						
					}
					
				}
				else
				{
					
					$responce['delivery']=false;
					
				}
				
			}
			
		}
		
		return $responce;
		
	}
	
	function phoen_woo_app_checkout_paytm_checksum(){
		include_once(plugin_dir_path(__FILE__).'app/payment-method/Paytm_App_Checksum/generateChecksum.php');		
		
		return new WP_REST_Response( $response, 200 );
	}
	
	function phoen_woo_app_new_order(){
		
		include_once(plugin_dir_path(__FILE__).'app/format_customer_response.php');
		
		include_once(plugin_dir_path(__FILE__).'app/checkout/order.php');		
		
		return new WP_REST_Response( $response, 200 );
		
	}
	
	function phoen_woo_app_checkpincode(){
		
		include_once(plugin_dir_path(__FILE__).'app/checkpincode.php');		
		
		return new WP_REST_Response( $responce, 200 );
		
	}
	
	function phoen_woo_app_app_settings(){
		
		include_once(plugin_dir_path(__FILE__).'app/autoload.php');		
		
		include_once(plugin_dir_path(__FILE__).'app/country.php');
		
		include_once(plugin_dir_path(__FILE__).'app/product/product-price-range.php');
		
		$phoen_app_styling_setting=get_option("phoen_app_styling_setting");
		
		$phoen_authenticate_setting = get_option("phoen_authenticate_setting");
		
		unset($phoen_authenticate_setting['consumer_key']);
		unset($phoen_authenticate_setting['consumer_secret']);
		
		$phoen_app_styling_setting+=$phoen_authenticate_setting;
		
		$phoen_app_referearn_setting=get_option("phoen_app_refer_earn_layout_setting",true);
		$phoen_app_styling_setting['referearn']= $phoen_app_referearn_setting['refer_earn_on']; 
			
		if ( in_array( 'woo-wallet/woo-wallet.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			$phoen_app_styling_setting['wallet_active']=true;
		}else{
			$phoen_app_styling_setting['wallet_active']=false;
		}
		
		if ( in_array( 'woocommerce-pincode-check-pro-unl-num/woocommerce-pincode-check.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) 
		{
			$phoen_app_styling_setting['pincode_active']=true;
			
		}else{
			$phoen_app_styling_setting['pincode_active']=false;
		}

		$phoen_app_styling_setting+=$phoen_app_country;
		$phoen_app_styling_setting['price']=$price_range;

		$enable_term_condition=get_option('phoen_term_condition_setting');
		$phoen_app_styling_setting['enable_term_condition']=$enable_term_condition['enable_term_condition'];
		$phoen_app_styling_setting['enable_faq']=get_option('pheon_woo_app_enable_faq',false);

		// ------------------- banner_category --------------------//
		/*$banner=array();
		 foreach($phoen_app_styling_setting['banner'] as $key=>$value){
			$term = get_term_by( 'id', $value['banner_category'], 'product_cat' );
			$phoen_app_styling_setting['banner'][$key]['name']=$term->name;
			$phoen_app_styling_setting['banner'][$key]['id']=$value['banner_category'];
			unset($phoen_app_styling_setting['banner'][$key]['banner_category']);
		 }*/
		
		return new WP_REST_Response( $phoen_app_styling_setting, 200 );
	}
	
	
	function phoen_woo_app_faq_settings(){
		
		include_once(plugin_dir_path(__FILE__).'app/autoload.php');		
		
		$faq_setting= is_array(get_option("pheon_woo_app_faq_setting"))?get_option("pheon_woo_app_faq_setting"):array();
		
		return new WP_REST_Response( $faq_setting, 200 );		
	}
	
	function phoen_woo_app_products(){
		
		//include_once(plugin_dir_path(__FILE__).'app/autoload.php');		
		
		include_once(plugin_dir_path(__FILE__).'app/product/format-product-response.php');
		
		include_once(plugin_dir_path(__FILE__).'app/product/products.php');
		
		return new WP_REST_Response( $response, 200 );
	}
	
	function phoen_woo_app_get_variation(){
		
		//include_once(plugin_dir_path(__FILE__).'app/autoload.php');		
		
		include_once(plugin_dir_path(__FILE__).'app/product/get-variation.php');
		
		return new WP_REST_Response( $response, 200 );
	}
	
	function phoen_woo_app_forget_password(){	
		
		include_once(plugin_dir_path(__FILE__).'app/login/forget-password.php');
		
		return new WP_REST_Response( $responce, 200 );
	}
	function phoen_woo_app_change_password(){	
		
		include_once(plugin_dir_path(__FILE__).'app/login/change-password.php');
		
		return new WP_REST_Response( $responce, 200 );
	}
	
	function phoen_woo_app_register(){	
		
		include_once(plugin_dir_path(__FILE__).'app/login/register.php');
		
		return new WP_REST_Response( $error_report, 200 );
	}
	
	function phoen_woo_app_log_out(){	
		
		include_once(plugin_dir_path(__FILE__).'app/login/log-out.php');
		
		return new WP_REST_Response( true, 200 );
	}
	
	function phoen_woo_app_login(){
		
		include_once(plugin_dir_path(__FILE__).'app/format_customer_response.php');
		
		include_once(plugin_dir_path(__FILE__).'app/login/login.php');
			
		return new WP_REST_Response( $login_report, 200 );

	}
	
	function phoen_woo_app_review_order(){
		
		include_once(plugin_dir_path(__FILE__).'app/autoload.php');		
		
		include_once(plugin_dir_path(__FILE__).'app/checkout/review-order.php');
				
		return new WP_REST_Response( $order_review_data, 200 );

	}
	
	
	
	function phoen_woo_app_get_cart(){
		
		include_once(plugin_dir_path(__FILE__).'app/autoload.php');		
	
		include_once(plugin_dir_path(__FILE__).'app/cart/get-cart.php');
				
		return new WP_REST_Response( $data, 200 );

	}
	
	function phoen_woo_app_add_to_cart(){
		
		include_once(plugin_dir_path(__FILE__).'app/autoload.php');		
		
		include_once(plugin_dir_path(__FILE__).'app/cart/add-to-cart.php');
		
		return new WP_REST_Response( $was_added_to_cart, 200 );
		
	}
	
	function phoen_woo_app_clear_cart(){
		
		include_once(plugin_dir_path(__FILE__).'app/autoload.php');		
		
		include_once(plugin_dir_path(__FILE__).'app/cart/clear-cart.php');
		
	}
	
	function phoen_woo_app_update_cart(){
		
		include_once(plugin_dir_path(__FILE__).'app/autoload.php');		
		
		include_once(plugin_dir_path(__FILE__).'app/cart/update_cart.php');
		
		return new WP_REST_Response( $data, 200 );
		// print_r($data);die();

	}
	
	function phoen_woo_app_remove_cart(){
		
		include_once(plugin_dir_path(__FILE__).'app/autoload.php');		
		
		include_once(plugin_dir_path(__FILE__).'app/cart/remove_cart.php');
		
		return new WP_REST_Response( $data, 200 );
		
	}
	
	function phoen_woo_app_cart_item_count(){
		
		$count = WC()->cart->get_cart_contents_count();

		return $count;
		
	}
	
	function phoen_woo_app_apply_coupon(){
		
		include_once(plugin_dir_path(__FILE__).'app/autoload.php');		
		
		include_once(plugin_dir_path(__FILE__).'app/cart/apply_coupon.php');
		
		return new WP_REST_Response( $data, 200 );
		
	}
	
	function phoen_woo_app_remove_coupon(){
		
		include_once(plugin_dir_path(__FILE__).'app/autoload.php');		
		
		include_once(plugin_dir_path(__FILE__).'app/cart/remove_coupon.php');
		
		return new WP_REST_Response( $data, 200 );
		
	}
	
	 function phoen_woo_app_get_totals() {
		
		$totals = WC()->cart->get_totals();

		return $totals;
	}

	function phoen_woo_app_layout(){

		//include_once(plugin_dir_path(__FILE__).'app/autoload.php');
		include_once(plugin_dir_path(__FILE__).'app/product/format-product-response.php');

		include_once(plugin_dir_path(__FILE__).'app/layout.php');

		return new WP_REST_Response( $response, 200 );
	}

	function phoen_woo_app_terms(){

		include_once(plugin_dir_path(__FILE__).'app/autoload.php');

		include_once(plugin_dir_path(__FILE__).'app/term_condition.php');

		return new WP_REST_Response( $data, 200 );
	}
	
	function phoen_app_backend_func(){ ?>
	
		<div id="profile-page" class="wrap">
			<?php
					$tab = isset($_GET['tab']) ? sanitize_text_field( $_GET['tab'] ):'';	
			?>
				<h2> <?php _e('Woocommerce App','phoen-woo-app'); ?></h2>
				
				<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
				
					<a class="nav-tab <?php if($tab == 'genral' || $tab == ''){ echo esc_attr( "nav-tab-active" ); } ?>" href="?page=phoeniixx_woo_app&amp;tab=genral"><?php echo esc_html('Setting','phoen-woo-app'); ?></a>
					<a class="nav-tab <?php if($tab == 'styling'){ echo esc_attr( "nav-tab-active" ); } ?>" href="?page=phoeniixx_woo_app&amp;tab=styling"><?php echo esc_html('Styling','phoen-woo-app'); ?></a>
					<a class="nav-tab <?php if($tab == 'layout'){ echo esc_attr( "nav-tab-active" ); } ?>" href="?page=phoeniixx_woo_app&amp;tab=layout"><?php echo esc_html('Layout','phoen-woo-app'); ?></a>
					<a class="nav-tab <?php if($tab == 'refearn'){ echo esc_attr( "nav-tab-active" ); } ?>" href="?page=phoeniixx_woo_app&amp;tab=refearn"><?php echo esc_html('Refer & Earn','phoen-woo-app'); ?></a>
					<a class="nav-tab <?php if($tab == 'term_and_condition'){ echo esc_attr( "nav-tab-active" ); } ?>" href="?page=phoeniixx_woo_app&amp;tab=term_and_condition"><?php echo esc_html('Terms & Conditions','phoen-woo-app'); ?></a>
					<a class="nav-tab <?php if($tab == 'faq'){ echo esc_attr( "nav-tab-active" ); } ?>" href="?page=phoeniixx_woo_app&amp;tab=faq"><?php echo esc_html('FAQ','phoen-woo-app'); ?></a>
					
					
				</h2>
				
			</div><?php
			
			if($tab=='' || $tab == 'genral'){
				
				require_once('includes/authenticate-setting.php');
								
			}elseif($tab == 'styling'){
				
				require_once('includes/styling-setting.php');
				
			}elseif($tab == 'faq'){
				
				require_once('includes/faq-setting.php');
				
			}
			elseif($tab == 'layout'){
				require_once('includes/layout-setting.php');
			}
			elseif($tab == 'refearn'){
				require_once('includes/refearn-setting.php');
			}
			elseif($tab == 'term_and_condition'){
				require_once('includes/term-condition-setting.php');
			}
			
	}
}