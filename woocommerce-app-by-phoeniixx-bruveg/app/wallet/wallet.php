<?php
/**
 * The Template for displaying wallet recharge form
 *
 * This template can be overridden by copying it to yourtheme/woo-wallet/wc-endpoint-wallet.php.
 *
 * HOWEVER, on occasion we will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @author 	Subrata Mal
 * @version     1.1.8
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

global $wp;
//do_action('woo_wallet_before_my_wallet_content');
$user_id = isset($_GET['uid'])?$_GET['uid']:'';
$user_wallet['balance'] = woo_wallet()->wallet->get_wallet_balance($user_id);
$transactions = get_wallet_transactions(array('user_id' => $user_id));

if (!empty($transactions)) { 
	$i=0;
		foreach ($transactions as $transaction) :
	
			$user_wallet['transaction'][$i]['details'][] = $transaction->details;

			$user_wallet['transaction'][$i]['date'][] = wc_string_to_datetime($transaction->date)->date_i18n(wc_date_format());
			$user_wallet['transaction'][$i]['amount'][] = ($transaction->type == 'credit' ? '+' : '-' ). get_woocommerce_currency_symbol(). $transaction->amount;
			$user_wallet['transaction'][$i]['type'][] = $transaction->type;
					$i++;
		endforeach;
	
} else {
	$user_wallet['transaction'] = 'No transactions found';
}