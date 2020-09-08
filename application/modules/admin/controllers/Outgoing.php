<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Outgoing extends Admin_Controller {

	public function after_init() {
		$this->set_scripts_and_styles();
		
		$this->load->model("admin/ledger_data_model", "ledger");

		$this->_admin_account_data = $this->get_account_data();
	}

	public function index() {
		$account_results            = $this->_admin_account_data['results'];
        $admin_oauth_bridge_id		= $account_results['admin_oauth_bridge_id'];
        $account_oauth_bridge_id    = $account_results['oauth_bridge_id'];
		$wallet_address             = $account_results['wallet_address'];
		
		$select = array(
			'*'
		);

		$where = array(
			'ledger_from_wallet_address'	=> $wallet_address
		);

		$inner_joints = array(
			array(
				'table_name'	=> 'transactions',
				'condition'		=> 'transactions.transaction_id = ledger_data.tx_id'
			)
		);

		$data = $this->ledger->get_data(
			$select,
			$where,
			array(),
			$inner_joints
		);

		$outgoin_data = $this->filter_ledger($data);

		$this->_data['listing'] = $this->table_listing('', $outgoin_data);
		$this->_data['title']  = "Outgoing";
		$this->set_template("outgoing/list", $this->_data);
	}
}
