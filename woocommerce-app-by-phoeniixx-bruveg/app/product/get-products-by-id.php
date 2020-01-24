<?php
	$include=explode(",",$data['include']);
	$controller=new WC_REST_Products_V2_Controller();
	$response=array();

    if(isset($_GET['id'])){
		
		$product=wc_get_product($_GET['id']);
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
				
		}
	$response=$product;
	}else if(count($include)>0){
		foreach($include as $value){
			$product= wc_get_product( $value );
			if($product){
				$attributes=array();
				foreach ( $product->get_attributes() as $attribute ) {
					$attr= array(
						'name'      => wc_attribute_label( $attribute['name'] ),
						'slug'      => $attribute['name'],
						'position'  => (int) $attribute['position'],
						'visible'   => (bool) $attribute['is_visible'],
						'variation' => (bool) $attribute['is_variation'],
						//'options'   => wc_get_product_terms($value->ID, $attribute['name'])
					);
					if ( isset( $attribute['is_taxonomy'] ) && $attribute['is_taxonomy'] ) {		
						$attr['options']= wc_get_product_terms( $value, $attribute['name'], array( 'fields' => 'all' ) );						
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
				$response[]=$product;	
			}
		}
	}
?>