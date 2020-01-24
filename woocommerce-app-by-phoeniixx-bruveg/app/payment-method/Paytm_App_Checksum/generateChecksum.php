<?php
header("Pragma: no-cache");
header("Cache-Control: no-cache");
header("Expires: 0");
// following files need to be included
require_once("lib/config_paytm.php");
//require_once("lib/encdec_paytm.php");
$checkSum = "";

// below code snippet is mandatory, so that no one can use your checksumgeneration url for other purpose .
$findme   = 'REFUND';
$findmepipe = '|';

$paramList = array();

$paramList["MID"] = '';
$paramList["ORDER_ID"] = '';
$paramList["CUST_ID"] = '';
$paramList["INDUSTRY_TYPE_ID"] = '';
$paramList["CHANNEL_ID"] = '';
$paramList["TXN_AMOUNT"] = '';
$paramList["WEBSITE"] = '';

foreach($_POST as $key=>$value)
{  
  $pos = strpos($value, $findme);
  $pospipe = strpos($value, $findmepipe);
  if ($pos === false || $pospipe === false) 
    {
        $paramList[$key] = $value;
    }
}

//Here checksum string will return by getChecksumFromArray() function.
$checkSum = getChecksumFromArray($paramList,PAYTM_MERCHANT_KEY);
$response = array("CHECKSUMHASH" => $checkSum,"ORDER_ID" => $_POST["ORDER_ID"], "payt_STATUS" => "1");
?>
