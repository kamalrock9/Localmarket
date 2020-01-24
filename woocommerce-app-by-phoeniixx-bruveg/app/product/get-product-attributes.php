<?php 
global $wpdb;
$attribute_taxonomies = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name != '' ORDER BY attribute_id ASC;" );
set_transient( 'wc_attribute_taxonomies', $attribute_taxonomies );
$attribute_taxonomies = array_filter( $attribute_taxonomies  ) ;
$result=array();
foreach( $attribute_taxonomies as $key=>$value){
	$data = array(
		'id'           => (int) $value->attribute_id,
		'name'         => $value->attribute_label,
		'slug'         => wc_attribute_taxonomy_name( $value->attribute_name ),
		'type'         => $value->attribute_type,
		//'order_by'     => $value->attribute_orderby,
		'has_archives' => (bool) $value->attribute_public,
	);
	$_nameParam = wc_attribute_taxonomy_name( $value->attribute_name );
	$product = $_GET['product'];
	if($_GET['product']){
		$products=explode(",",$_GET['product']);
		$options=array();
		foreach($products as $product){
			$_productAttrTerm = get_the_terms($product,$_nameParam);
			foreach( $_productAttrTerm as $k=>$v){
				if(in_array((int) $v->term_id, array_column($options, "id"))){
					continue;
				}
				//$menu_order = get_woocommerce_term_meta( $item->term_id, 'order_' . $this->taxonomy );
        		$options[] = array(
            		'id'          => (int) $v->term_id,
            		'name'        => $v->name,
           	 		'slug'        => $v->slug,
            		'description' => $v->description,
            		//'menu_order'  => (int) $menu_order,
					'count'       => (int) $v->count,
					'checked'	  =>false
        		);
			}
		}
		if(count($options)>0){
			$data["options"]=$options;
			$result[]=$data;
		}
	}else if($_GET['show_all']){
		$params = array('hide_empty' => $_GET['hide_empty']);
		$_productAttrTerm= get_terms($_nameParam,$params);
		$options=array();
		foreach( $_productAttrTerm as $k=>$v){
			//$menu_order = get_woocommerce_term_meta( $item->term_id, 'order_' . $this->taxonomy );
        	$options[$k] = array(
            	'id'          => (int) $v->term_id,
            	'name'        => $v->name,
            	'slug'        => $v->slug,
            	'description' => $v->description,
            	//'menu_order'  => (int) $menu_order,
				'count'       => (int) $v->count,
				'checked'	  =>false
        	);
		}
		if(count($options)>0){
			$data["options"]=$options;
			$result[]=$data;
		}
	}
}
$response=$result;  
?>