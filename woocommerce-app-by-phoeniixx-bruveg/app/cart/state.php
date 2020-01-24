<?php 

require( realpath(__DIR__ . '/../wp-load.php'));

$countries_obj   = new WC_Countries();

$state   = $countries_obj->get_states($_GET["country_code"]);

echo json_encode( $state);