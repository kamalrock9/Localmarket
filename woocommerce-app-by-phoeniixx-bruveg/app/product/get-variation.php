<?php if ( ! defined( 'ABSPATH' ) ) exit;

$data = json_decode(file_get_contents('php://input'), true);

/* $data=array ( 'product_id' => 144,
	'attributes' => array ( 1 => 'Blue')); */
	
	$attributes=array();
	
	$variation_id='';
	
	if(is_array($data) && !empty($data['attributes'])){
		
		foreach($data['attributes'] as $key=>$values){
			$k='attribute_'.$key;
			$attributes[$k]=$values;			
		}
	}
	
	$variation_id=find_matching_product_variation_id($data['product_id'], $attributes);
	
if(!empty($variation_id) && isset($data['product_id']) && !empty($data['product_id'])){
	$_variation=wc_get_product($variation_id);
	$price_html=$_variation->get_price_html();
	$controller=new WC_REST_Product_Variations_V2_Controller();
	$data=$controller->prepare_object_for_response( $_variation, $_REQUEST );
	$response=$controller->prepare_response_for_collection( $data );
	$response['price_html']=$price_html;
	
	/*$finel_array = $woocommerce->get('products/'.$data['product_id'].'/variations/'.$variation_id.'');
	$variation_product=wc_get_product($variation_id);
	$price_html=$variation_product->get_price_html();
	$finel_array['price_html']=$price_html;
	unset($finel_array['_links']);*/
	
	
}else{
	
	$response=array('error'=>true);
	
}

function find_matching_product_variation_id($product_id, $attributes)
{
	//print_r($product_id,);
    return (new \WC_Product_Data_Store_CPT())->find_matching_product_variation(
        new \WC_Product($product_id),
        $attributes
    );
}

/*function phoen_product_variation( $product, $attributes ) {

		foreach( $attributes as $key => $value ) {
			if( strpos( $key, 'attribute_' ) === 0 ) {
				continue;
			}

			unset( $attributes[ $key ] );
			$attributes[ sprintf( 'attribute_%s', $key ) ] = $value;
		}

		if( class_exists('WC_Data_Store') ) {

			$data_store = WC_Data_Store::load( 'product' );
			return $data_store->find_matching_product_variation( $product, $attributes );

		} else {

			return $product->get_matching_variation( $attributes );

		}

}*/