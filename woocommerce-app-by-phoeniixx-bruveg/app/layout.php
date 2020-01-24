<?php
$response=get_option("phoen_app_layout_setting");
foreach($response['banner'] as $key=>$value){

	if($value['type'] ==  'category'){
		$term = get_term_by( 'id', $value['banner_category'], 'product_cat' );
		$response['banner'][$key]['name']=$term->name;
		$response['banner'][$key]['id']=$value['banner_category'];
		
	}else{
		$term = get_term_by( 'id', $value['top_banner_brands'], 'brands' );
		$response['banner'][$key]['name']=$term->name;
		$response['banner'][$key]['id']=$value['top_banner_brands'];
		
	}
	unset($response['banner'][$key]['banner_category']);
	unset($response['banner'][$key]['top_banner_brands']);

}
unset($response['top_brand_seller']);
	//unset($response['banner_category'][$key]['top_banner_brands']);
//  foreach($response['banner'] as $key =>$value){
// 	 if($value)

//  }
//unset($response['center_banner']);
	
if($response['top_seller']){
    $per_page=$response['tspnumber'];
    $query_args = array(
		'posts_per_page' => $per_page,
		'offset'         => 0,
		'no_found_rows'  => 1,
		'post_status'    => 'publish',
		'post_type'      => 'product',
		'meta_key'       => 'total_sales',
		'orderby' => array(
							'meta_value_num' => 'DESC',
							'post_date'      => 'DESC',
						),
    );
    $response['top_seller'] = phoen_woo_app_format_product_response($query_args);
}

foreach($response['center_banner'] as $key=>$value){
	if($value['type'] ==  'category'){
		$term = get_term_by( 'id', $value['center_banner_category'], 'product_cat' );
		$response['center_banner'][$key]['name']=$term->name;
		$response['center_banner'][$key]['id']=$value['center_banner_category'];
	}else{
		$term = get_term_by( 'id', $value['center_banner_brands'], 'brands' );
		$response['center_banner'][$key]['name']=$term->name;
		$response['center_banner'][$key]['id']=$value['center_banner_brands'];
		
	}
	unset($response['center_banner'][$key]['center_banner_category']);
	unset($response['center_banner'][$key]['center_banner_brands']);

}	
unset($response['brand_seller']);
	
if($response['featured_products']){
    $per_page=$response['fpnumber'];
	$query_args = array(
    'post_type'      => 'product',
	'offset'         => 0,
    'posts_per_page' => $per_page,
	'post_status'    => 'publish',
    'tax_query'     => array(
					   array(
					   'taxonomy' => 'product_visibility',
					   'field'    => 'name',
					   'terms'    => 'featured',
					   'operator' => 'IN'
					   		)
					      )
					   );
	$response['featured_products']=phoen_woo_app_format_product_response($query_args);
}

if($response['sale_products']){
    $per_page=$response['salepnumber'];
	$query_args = array(
    'post_type'      => 'product',
	'offset'         => 0,
    'posts_per_page' => $per_page,
	'post_status'    => 'publish',
    'meta_query'     => array(
						'relation' => 'OR',
						array( // Simple products type
							'key'           => '_sale_price',
							'value'         => 0,
							'compare'       => '>',
							'type'          => 'numeric'
						),
						array( // Variable products type
							'key'           => '_min_variation_sale_price',
							'value'         => 0,
							'compare'       => '>',
							'type'          => 'numeric'
						)
					)
	);
	$response['sale_products'] = phoen_woo_app_format_product_response($query_args);
}

if($response['top_rated_products']){
    $per_page=$response['top_rated_pnumber'];
    $query_args = array(
		'posts_per_page' =>$per_page ,
        'offset'         => 0,
        'no_found_rows'  => 1,
        'post_status'    => 'publish',
        'post_type'      => 'product',
        'meta_key'       => '_wc_average_rating',
        'orderby'        =>array(
                                'meta_value_num' => 'DESC',
                                'ID'             => 'ASC',
                            ),
        );
	$response['top_rated_products'] = phoen_woo_app_format_product_response($query_args);
}
	
$response['categories']=array();
for($i=0;$i<count($response['front_page_category']);$i++){
	$id=$response['front_page_category'][$i];
	$term = get_term_by( 'id', $id, 'product_cat' );
	$response['categories'][$i]['name']=$term->name;
	$response['categories'][$i]['id']=$id;

	$image_id = get_woocommerce_term_meta( $id, 'thumbnail_id' );
	if($image_id){
		$response['categories'][$i]['image']=wp_get_attachment_url( $image_id );
	}
}
unset($response['front_page_category']);

$response['brand_category_grid']=array();
foreach($response['bran_and_category'] as $values){
	$data=array();
	$data['title']=$values['bran_cate'];
	foreach($values['brand_category_banner'] as $k=>$v){
		
	$term = get_term( $v );
	$data['brand_category_banner'][$k]['id']=$v;
	$data['brand_category_banner'][$k]['name']=$term->name;

	if($term->taxonomy=='brands'){
		$data['brand_category_banner'][$k]['type']='brand';
		$image_id = get_woocommerce_term_meta( $v, 'category-image-id' );
		if($image_id){
			$data['brand_category_banner'][$k]['image']=wp_get_attachment_url( $image_id );
		}
	}else{
		$data['brand_category_banner'][$k]['type']='category';
		$image_id = get_woocommerce_term_meta( $v, 'thumbnail_id' );
		if($image_id){
			$data['brand_category_banner'][$k]['image']=wp_get_attachment_url( $image_id );
		}

		}
	}
	$response['brand_category_grid'][]=$data;
}
unset($response['bran_and_category']);
	
?>