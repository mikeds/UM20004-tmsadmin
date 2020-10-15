<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CMS_Controller class
 * Base controller ?
 *
 * @author Marknel Pineda
 */
class Crawler_Controller extends Global_Controller {
	protected
		$_today = "";

	protected
		$_base_controller = "public",
		$_base_session = "session",
		$_data = array(); // shared data with child controller

	/**
	 * Constructor
	 */
	public function __construct() {
		// Initialize all configs, helpers, libraries from parent
		parent::__construct();
		date_default_timezone_set("Asia/Manila");
		$this->_today = date("Y-m-d H:i:s");

		$this->validate_access();
		$this->after_init();
	}

	public function get_wallet_address($bridge_id) {
		$this->load->model('admin/wallet_addresses_model', 'wallet_addresses');

		$row = $this->wallet_addresses->get_datum(
			'',
			array(
				'oauth_bridge_id' => $bridge_id
			)
		)->row();

		if ($row == "") {
			return "";
		}

		return $row->wallet_address;
	}

	public function decrypt_wallet_balance($encrypted_balance) {
		return openssl_decrypt($encrypted_balance, $this->_ssl_method, getenv("BPKEY"));
	}

	public function encrypt_wallet_balance($balance) {
		return openssl_encrypt($balance, $this->_ssl_method, getenv("BPKEY"));
	}

	public function update_wallet($wallet_address, $amount) {
		$this->load->model("admin/wallet_addresses_model", "wallet_addresses");

		$row = $this->wallet_addresses->get_datum(
			'',
			array(
				'wallet_address'	=> $wallet_address
			)
		)->row();

		if ($row == "") {
			return false;
		}

		$wallet_balance         = $this->decrypt_wallet_balance($row->wallet_balance);

		$old_balance            = $wallet_balance;
		$encryted_old_balance   = $this->encrypt_wallet_balance($old_balance);

		$new_balance            = $old_balance + $amount;
		$encryted_new_balance   = $this->encrypt_wallet_balance($new_balance);

		$wallet_data = array(
			'wallet_balance'                => $encryted_new_balance,
			'wallet_address_date_updated'   => $this->_today
		);

		// update wallet balances
		$this->wallet_addresses->update(
			$wallet_address,
			$wallet_data
		);

		return array(
			'old_balance'	=> $old_balance,
			'new_balance'	=> $new_balance,
			'amount'		=> $amount
		);
	}

	public function generate_code($data, $hash = "sha256") {
		$json = json_encode($data);
		return hash_hmac($hash, $json, getenv("SYSKEY"));
	}

	public function new_ledger_datum($description = "", $transaction_id, $from_wallet_address, $to_wallet_address, $balances) {
		$this->load->model("admin/ledger_data_model", "ledger");
		$this->load->model("admin/wallet_addresses_model", "wallet_addresses");

		$to_oauth_bridge_id 	= getenv("SYSADD");
		$from_oauth_bridge_id 	= getenv("SYSADD");


		$from_row = $this->wallet_addresses->get_datum(
			'',
			array(
				'wallet_address' => $from_wallet_address
			)
		)->row();

		if ($from_row != "") {
			$from_oauth_bridge_id 	= $from_row->oauth_bridge_id;
		}

		$to_row = $this->wallet_addresses->get_datum(
			'',
			array(
				'wallet_address' => $to_wallet_address
			)
		)->row();

		if ($to_row != "") {
			$to_oauth_bridge_id 	= $to_row->oauth_bridge_id;
		}

		$old_balance = $balances['old_balance'];
		$new_balance = $balances['new_balance'];
		$amount		 = $balances['amount'];

		$ledger_type = 0; // unknown

		if ($amount < 0) {
			$ledger_type = 1; // debit
		} else if ($amount >= 0) {
			$ledger_type = 2; // credit
		}

		// add new ledger data
		$ledger_data = array(
			'tx_id'                         => $transaction_id,
			'ledger_datum_type'				=> $ledger_type,
			'ledger_datum_bridge_id'		=> $to_oauth_bridge_id,
			'ledger_datum_desc'             => $description,
			'ledger_from_wallet_address'    => $from_wallet_address,
			'ledger_to_wallet_address'      => $to_wallet_address,
			'ledger_from_oauth_bridge_id'   => $from_oauth_bridge_id,
			'ledger_to_oauth_bridge_id'     => $to_oauth_bridge_id,
			'ledger_datum_old_balance'      => $old_balance,
			'ledger_datum_new_balance'      => $new_balance,
			'ledger_datum_amount'           => $amount,
			'ledger_datum_date_added'       => $this->_today
		);

		$ledger_datum_id = $this->generate_code(
			$ledger_data,
			"crc32"
		);

		$ledger_data = array_merge(
			$ledger_data,
			array(
				'ledger_datum_id'   => $ledger_datum_id,
			)
		);

		$ledger_datum_checking_data = $this->generate_code($ledger_data);

		$this->ledger->insert(
			array_merge(
				$ledger_data,
				array(
					'ledger_datum_checking_data' => $ledger_datum_checking_data
				)
			)
		);
	}

	public function validate_access() {
		if (isset($_SERVER['HTTP_X_KEY'])) {
			$key = $_SERVER['HTTP_X_KEY'];
			if ($key == getenv("SYSKEY")) {
				return;
			}
		}

		// invalid access
		$this->output->set_status_header(401);
		die();
	}
}
