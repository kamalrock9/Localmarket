<?php
if($data['search']){
	$response=array();
	$cat_args = array(
		'orderby'    => $orderby,
		'order'      => $order,
		'hide_empty' => $hide_empty,
		'search'     => $data['search'],
		'number'     => $data['per_page']
	);
	$_categories = get_terms( 'product_cat', $cat_args );
	foreach( $_categories as $key=>$value){
		$response['categories'][] = array(
            'id'          => (int) $value->term_id,
            'name'        => $value->name,
            'slug'        => $value->slug,
            'parent'      => (int) $value->parent,
            'description' => $value->description,
            'count'       => (int) $value->count
        );
	}
	
	$query_args = array(
			'posts_per_page' => $data['per_page'],
			'offset'		 => 0,
			'no_found_rows'  => 1,
			'post_status'    => 'publish',
			'post_type'      => 'product',
			'meta_key'       => 'total_sales',
			'orderby'        => 'date ID',
			'order'          => 'desc',
			's'				 => $data['search']
		);
	
	$response['products']=phoen_woo_app_format_product_response($query_args);

}else{
	$response=array();
}
?>