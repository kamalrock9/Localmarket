<?php
function phoen_woo_app_format_product_response($args){ //WP_Query args needed to be passed
	$controller=new WC_REST_Products_V2_Controller();
	$_products = new WP_Query($args);
	$list=array();
	if($_products->post_count >0){
		foreach($_products->posts as  $value){
			$product= wc_get_product( $value->ID );
			if($product){
				$attributes=array();
				foreach ( $product->get_attributes() as $attribute ) {
					$attr= array(
						'name'      => wc_attribute_label( $attribute['name'] ),
						'slug'      => str_replace(" ","-",strtolower($attribute['name'])),
						'position'  => (int) $attribute['position'],
						'visible'   => (bool) $attribute['is_visible'],
						'variation' => (bool) $attribute['is_variation'],
						//'options'   => wc_get_product_terms($value->ID, $attribute['name'])
					);
					if ( isset( $attribute['is_taxonomy'] ) && $attribute['is_taxonomy'] ) {		
						$attr['options']= wc_get_product_terms( $value->ID,$attribute['name'],array('fields'=>'all'));
					} elseif ( isset( $attribute['value'] ) ) {
						$attr['options']= array_map( 'trim', explode( '|', $attribute['value'] ) );
					}else{
						$attr['options']=array();
					}
					$attributes[]=$attr;
				}
				$data=$controller->prepare_object_for_response( $product, $_REQUEST );
				$product=$controller->prepare_response_for_collection( $data );
				$product['attributes']=$attributes;
				if ( empty( $product['images'] ) ) {
					$product['images'][] = array(
					'id'                => 0,
					'date_created'      => wc_rest_prepare_date_response( current_time( 'mysql' ), false ), // Default to now.
					'date_created_gmt'  => wc_rest_prepare_date_response( current_time( 'timestamp', true ) ), // Default to now.
					'date_modified'     => wc_rest_prepare_date_response( current_time( 'mysql' ), false ),
					'date_modified_gmt' => wc_rest_prepare_date_response( current_time( 'timestamp', true ) ),
					'src'               => wc_placeholder_img_src(),
					'name'              => __( 'Placeholder', 'woocommerce' ),
					'alt'               => __( 'Placeholder', 'woocommerce' ),
					'position'          => 0,
					);
				}
				//print_r($formatted_product);die();
				$list[]=$product;	
			}
		}
	}
	return $list;
}
?>