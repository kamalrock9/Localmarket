<?php 


    global $wpdb;
    
    # Get ALL related products prices related to a specific product category
    $results = $wpdb->get_col( "
        SELECT pm.meta_value
        FROM {$wpdb->prefix}term_relationships as tr
        INNER JOIN {$wpdb->prefix}term_taxonomy as tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        INNER JOIN {$wpdb->prefix}terms as t ON tr.term_taxonomy_id = t.term_id
        INNER JOIN {$wpdb->prefix}postmeta as pm ON tr.object_id = pm.post_id
        WHERE tt.taxonomy LIKE 'product_cat'
        AND pm.meta_key = '_price' 
    ");

	//print_r($results);
	//removing blank values
	$results=array_filter($results);
	
    // Sorting prices numerically
    sort($results, SORT_NUMERIC);
	
    // Get the min and max prices
    $min = (int)current($results);
    $max = (int)end($results);
    $min_with_symbol=wc_price($min);
    $max_with_symbol=wc_price($max);
    
    // Format the price range after the title
    $price_range = array(
        'min'=>$min,
        'max'=>$max,
        'min_with_symbol'=>$min_with_symbol,
        'max_with_symbol'=>$max_with_symbol
    );

?>