<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dragonpay extends Public_Controller {
	public function after_init() {
		$this->set_scripts_and_styles();
	}

	public function index() {
		$base_url 		= "https://gw.dragonpay.ph/Pay.aspx";
		$base_url_test	= "https://test.dragonpay.ph/Pay.aspx";

		$tmp_amount		= isset($_GET['amount']) ? $_GET['amount'] : "150.00";

		if (is_numeric($tmp_amount)) {
			$tmp_amount = number_format($tmp_amount, 2, '.', '');
		}
		
		$merchant_id	= "BAMBUPAYSOLUTIONS";
		$tx_id 			= isset($_GET['txid']) ? $_GET['txid'] : "txbp" . strtotime($this->_today);
		$amount			= $tmp_amount;
		$ccy			= "PHP";
		$email_address	= isset($_GET['email']) ? urldecode($_GET['email'] ) : "marknel.pineda23@gmail.com";
		$description 	= "test";
		$secret_key		= "z9QqpzhMuKcaQzE";

		$digest = sha1("{$merchant_id}:{$tx_id}:{$amount}:{$ccy}:{$description}:{$email_address}:{$secret_key}");

		$redirect = "{$base_url_test}?" . "merchantid=" . $merchant_id . "&txnid=" . $tx_id . "&amount=" . $amount . "&ccy=" . $ccy . "&description=" . urlencode($description) . "&email=" . urlencode($email_address) . "&digest=" . $digest;
	
		echo "<a href='{$redirect}' target='_blank'>click here!</a>";
	}
}

