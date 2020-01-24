<?php if ( ! defined( 'ABSPATH' ) ) exit;

	$query_args;
	
	$product_data =  json_decode(file_get_contents('php://input'),true);
	
	if($_GET['sort'] == "default"){

		$query_args = array(
			'posts_per_page' => $per_page,
			'offset'		 => $offset,			
			'no_found_rows'  => 1,
			'post_status'    => 'publish',
			'post_type'      => 'product',
			'meta_key'       => '',
			'orderby' => 'menu_order title',
			'order'=>'ASC',
			);
			
		
	}
	else if($_GET['sort'] == "popularity"){
			
		$query_args = array(
			'posts_per_page' => $per_page,
			'offset'         => $offset,
			'no_found_rows'  => 1,
			'post_status'    => 'publish',
			'post_type'      => 'product',
			'meta_key'       => 'total_sales',
			'orderby' => array(
									'meta_value_num' => 'DESC',
									'post_date'             => 'DESC',
							),			
		);
		
	}
	else if($_GET['sort'] == "rating"){
		
		$query_args = array(
			'posts_per_page' =>$per_page ,
			'offset'=>$offset,
			'no_found_rows'  => 1,
			'post_status'    => 'publish',
			'post_type'      => 'product',
			'meta_key'       => '_wc_average_rating',
			'orderby'        =>array(
										'meta_value_num' => 'DESC',
										'ID'             => 'ASC',
									),	
		);
	}
	else if($_GET['sort'] == "date"){
		$query_args = array(
			'posts_per_page' => $per_page,
			'offset'=>$offset,
			'no_found_rows'  => 1,
			'post_status'    => 'publish',
			'post_type'      => 'product',
			'meta_key'       => 'total_sales',
			'orderby'        => 'date ID',
			'order'          => 'desc',
			
		);
	}
	 
	else if($_GET['sort'] === "price_desc" OR $_GET['sort'] === "price_asc"){
		
		if($_GET['sort'] === "price_desc"){
			$order="DESC";
		}else{
			$order="ASC";
		}
		
		$query_args = array(
			'posts_per_page' => $per_page,
			'offset'=>$offset,			
			'post_status'    => 'publish',
			'post_type'      => 'product',
			'meta_key'       => '_price',
			'orderby' =>	"meta_value_num ID",
			'order'=>( 'DESC' === $order ) ? 'DESC' : 'ASC',
			
		);
	}
	
	if(isset($_GET['max_price']) || isset($_GET['min_price'])){
		
		include_once('product-price-range.php');
	
		$price_min=(isset($_GET['min_price'])&&!empty($_GET['min_price']))?$_GET['min_price']:$price_range['min'];
		
		$price_max=(isset($_GET['max_price'])&&!empty($_GET['max_price']))?$_GET['max_price']:$price_range ['max'];
		
		$query_args['meta_query'][] = array(
											array(
												'key' => '_price',
												'value' => array($price_min,$price_max),
												'compare' => 'BETWEEN',
												'type' => 'NUMERIC'
											)
										);	
	}
	
	if(isset($_GET['on_sale']) && $_GET['on_sale']){
		$query_args['meta_query'][] = array(
											array(
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
	}
	if(isset($_GET['featured']) && $_GET['featured']){
		$query_args['tax_query'][] = array(
											array(
												'taxonomy' => 'product_visibility',
												'field'    => 'name',
												'terms'    => 'featured',
												'operator' => 'IN'
											)
										);	
	}
	
	if($search != '' || $search != null){
		$query_args['s'] = $search;
	}
	if($category_id!='' || $category_id != null){
			
		$query_args['tax_query'] []= array(
											'taxonomy'      => 'product_cat',
											'field'         => 'term_id',
											'terms'         => $category_id,
											'operator'      => 'IN'
										  );
														
	}
	
	if(isset($product_data) && !empty($product_data)){
		
		foreach($product_data as $key =>$values){
			
			$query_args['tax_query'] []=  array(
				'taxonomy'        => $key,
				'field'           => 'slug',
				'terms'           =>  $values,
				'operator'        => 'IN',
			 );
		}
	}

	$response=phoen_woo_app_format_product_response($query_args);
?>