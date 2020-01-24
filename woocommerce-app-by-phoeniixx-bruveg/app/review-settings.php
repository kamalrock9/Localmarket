<?php
$response['enable_reviews']=wc_string_to_bool(get_option( 'woocommerce_enable_reviews'));
$response['review_rating_verification_label']=wc_string_to_bool (get_option( 'woocommerce_review_rating_verification_label'));
$response['review_rating_verification_required']=wc_string_to_bool (get_option( 'woocommerce_review_rating_verification_required'));
$response['enable_review_rating']=wc_string_to_bool (get_option( 'woocommerce_enable_review_rating'));
$response['review_rating_required']=wc_string_to_bool (get_option( 'woocommerce_review_rating_required'));
$response['user_bought_product']=wc_customer_bought_product($data['email'],$data['user_id'],$data['product_id']);
?>