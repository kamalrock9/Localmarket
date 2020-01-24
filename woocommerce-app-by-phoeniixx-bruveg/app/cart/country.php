<?php 

require( realpath(__DIR__ . '/../wp-load.php'));

$countries_obj   = new WC_Countries();

$countries   = $countries_obj->get_allowed_countries();

echo json_encode( $countries);