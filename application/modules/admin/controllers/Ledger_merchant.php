<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ledger_merchant extends Admin_Controller {
	private
		$_admin_account_data = NULL;
		
	public function after_init() {
		$this->set_scripts_and_styles();
		
		$this->load->model("admin/ledger_data_model", "ledger");
		$this->load->model("admin/merchants_model", "merchants");
		$this->load->model("admin/merchant_accounts_model", "merchant_accounts");

		$this->_admin_account_data = $this->get_account_data();
	}

	public function index() {
		$this->_data['form_url']		= base_url() . "ledger-merchant";
		$this->_data['notification'] 	= $this->session->flashdata('notification');

		$redirect_url					= base_url() . "ledger-merchant/search";

		$merchants = $this->get_merchants();

		$this->_data['merchants'] = $this->generate_selection(
			"merchant", 
			$merchants, 
			"", 
			"id", 
			"name", 
			false,
			"Select Merchant"
		);

		if ($_POST) {
			if ($this->form_validation->run('validate')) {
				$merchant_number = $this->input->post("merchant");

				$query = "/{$merchant_number}";

				$redirect_url .= $query;

				redirect($redirect_url);
			}
		}

		$this->_data['title']  = "Ledger - Search Merchant";
		$this->set_template("ledger_merchant/form", $this->_data);
	}

	
	public function search($merchant_number) {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];

		$page = isset($_GET['page']) ? (is_numeric($_GET['page']) ? $_GET['page'] : 1 ) : 1;

		$row = $this->merchants->get_datum(
			'',
			array(
				'merchant_number'			=> $merchant_number,
				'oauth_bridge_parent_id'	=> $admin_oauth_bridge_id
			),
			array(),
			array(
				array(
					'table_name'	=> 'oauth_bridges',
					'condition'		=> 'oauth_bridges.oauth_bridge_id = merchants.oauth_bridge_id'
				)
			)
		)->row();
		
		if ($row == "") {
			redirect(base_url() . "ledger-merchant");
		}

		$merchant_name = trim("{$row->merchant_fname} {$row->merchant_mname} {$row->merchant_lname}");

		$merchant_oauth_bridge_id = $row->oauth_bridge_id;
		
		$where = array(
			'ledger_datum_bridge_id'	=> $merchant_oauth_bridge_id
		);

        $select = array(
			'transaction_id as "TX ID"',
			'transaction_sender_ref_id as "Sender Ref ID"',
			'transaction_type_name as "TX Type"',
			'transaction_requested_by as "Requested By"',
			'FORMAT(transaction_amount, 2) as "TX Amount"',
			'FORMAT(transaction_fee, 2) as "Fee"',
			'FORMAT(ledger_datum_old_balance, 2) as "Old Balance"',
			'FORMAT(ledger_datum_amount, 2) as "Debit/Credit Amount"',
			'FORMAT(ledger_datum_new_balance, 2) as "New Balance"',
			'ledger_datum_date_added as "Date Added"'
		);

		$inner_joints = array(
			array(
				'table_name'	=> 'transactions',
				'condition'		=> 'transactions.transaction_id = ledger_data.tx_id'
			),
			array(
				'table_name'	=> 'transaction_types',
				'condition'		=> 'transaction_types.transaction_type_id = transactions.transaction_type_id'
			)
		);

		$data = $this->ledger->get_data(
			$select,
			$where,
			array(),
			$inner_joints,
			array(
				'filter'	=> 'ledger_datum_date_added',
				'sort'		=> 'ASC'
            ),
            0,
            $this->_limit
		);
		
		$total_rows = $this->ledger->get_count(
			$where,
			array(),
			$inner_joints
		);
		$offset = $this->get_pagination_offset($page, $this->_limit, $total_rows);
		
		$ledger_data = $this->filter_ledger($data);

		$this->_data['listing'] = $this->table_listing('', $ledger_data, $total_rows, $offset, $this->_limit, array(), 4);
		$this->_data['title']  = "Merchant Ledger ({$merchant_name})";
		$this->set_template("ledger_merchant/list", $this->_data);
	}
}
