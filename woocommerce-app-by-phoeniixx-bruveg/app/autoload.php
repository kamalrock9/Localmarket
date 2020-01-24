<?php if ( ! defined( 'ABSPATH' ) ) exit;

// echo '11';die();

require_once('WooCommerce/Client.php');
require_once('WooCommerce/HttpClient/BasicAuth.php');
require_once('WooCommerce/HttpClient/HttpClient.php');
require_once('WooCommerce/HttpClient/HttpClientException.php');
require_once('WooCommerce/HttpClient/OAuth.php');
require_once('WooCommerce/HttpClient/Options.php');
require_once('WooCommerce/HttpClient/Request.php');
require_once('WooCommerce/HttpClient/Response.php');

use Automattic\WooCommerce\Client;

$getoption=get_option("phoen_authenticate_setting");

$url=site_url();

$consumer_key=isset($getoption['consumer_key'])? $getoption['consumer_key']:'';

$consumer_secret=isset($getoption['consumer_secret'])?$getoption['consumer_secret']:'';



// WC
 try{
 $woocommerce = new Client(
    $url, 
    $consumer_key, 
    $consumer_secret,
    	[
        'wp_api' => true,
        'version' => 'wc/v2',
		'query_string_auth' =>true,
		'follow_redirects'=>true
    	]
	);
}catch (HttpClientException $e) {
    $response = $e->getMessage(); // Error message. 
}


?>