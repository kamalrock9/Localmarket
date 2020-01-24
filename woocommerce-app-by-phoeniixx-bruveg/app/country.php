<?php 
$phoen_app_country = array();
global $woocommerce;
$countries_obj   = new WC_Countries();
$phoen_app_country['countries']   = $countries_obj->get_allowed_countries();
$default_country = $countries_obj->get_base_country();
$phoen_app_country['county_states'] = $countries_obj->get_allowed_country_states();

?>