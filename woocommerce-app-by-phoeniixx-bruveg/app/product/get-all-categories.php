<?php
//global $wp_version;
if(version_compare(get_bloginfo('version'),'4.5.0', '>=') ){
   $args = array(
    'taxonomy'   => "product_cat",
    'number'     => $number,
    'orderby'    => $orderby,
    'order'      => $order,
    'hide_empty' => $hide_empty,
    'include'    => $ids
	);
	if(isset($_GET['parent'])){
		$args['parent']=$_GET['parent'];
	}
	$product_categories = get_terms($args);
}
else{
   $args = array(
    'number'     => $number,
    'orderby'    => $orderby,
    'order'      => $order,
    'hide_empty' => $hide_empty,
    'include'    => $ids
	);
	if(isset($_GET['parent'])){
		$args['parent']=$_GET['parent'];
	}
	$product_categories = get_terms( 'product_cat', $args );
}
$result=array();
foreach( $product_categories as $key=>$value){
	array_push($result, phoen_woo_app_prepare_item_for_response($value));
}

function phoen_woo_app_prepare_item_for_response( $item ) {
        // Get category display type.
        $display_type = get_woocommerce_term_meta( $item->term_id, 'display_type' );

        // Get category order.
        //$menu_order = get_woocommerce_term_meta( $item->term_id, 'order' );

        $data = array(
            'id'          => (int) $item->term_id,
            'name'        => $item->name,
            'slug'        => $item->slug,
            'parent'      => (int) $item->parent,
            'description' => $item->description,
            'display'     => $display_type ? $display_type : 'default',
            'image'       => null,
            //'menu_order'  => (int) $menu_order,
            'count'       => (int) $item->count,
        );

        // Get category image.
        $image_id = get_woocommerce_term_meta( $item->term_id, 'thumbnail_id' );
        if ( $image_id ) {
            $attachment = get_post( $image_id );

            $data['image'] = array(
                'id'                => (int) $image_id,
                //'date_created'      => wc_rest_prepare_date_response( $attachment->post_date ),
                //'date_created_gmt'  => wc_rest_prepare_date_response( $attachment->post_date_gmt ),
                //'date_modified'     => wc_rest_prepare_date_response( $attachment->post_modified ),
                //'date_modified_gmt' => wc_rest_prepare_date_response( $attachment->post_modified_gmt ),
                'src'               => wp_get_attachment_url( $image_id ),
                'title'             => get_the_title( $attachment ),
                //'alt'               => get_post_meta( $image_id, '_wp_attachment_image_alt', true ),
            );
        }
		return $data;
    }
$response=$result;
?>