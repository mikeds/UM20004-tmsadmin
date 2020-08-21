<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Top_up_otc extends Admin_Controller {
	private
		$_account_data = "";

	public function after_init() {
		$this->set_scripts_and_styles();
		$this->load->model('admin/transactions_model', 'transactions');

		$this->validate_account_data();
	}

	private function validate_account_data() {
		$account_data = $this->get_account_data();

		if (isset($account_data['status'])) {
			$status = $account_data['status'];

			if ($status) {
				$this->_account_data = $account_data;
				return;
			}
		}

		redirect(base_url() . "logout");
	}

	public function index($page = 1) {
		$admin_bridge_id = $this->_account_data['results']['oauth_bridge_id'];

		$select = array(
			array(
				'transaction_number as id',
				'IF(transaction_status = 1, "APPROVED", IF(transaction_date_expiration < "'. $this->_today .'" OR transaction_status = 2, "CANCELLED", "PENDING")) as transaction_status',
				'transaction_requested_by',
				'UCASE(transaction_number) as transaction_number',
				'FORMAT(transaction_amount, 2) as amount',
				'FORMAT(transaction_fees, 2) as fees',
				'DATE_FORMAT(transaction_date_created, "%a, %b. %e. %Y %r") as transaction_date_created',
				'DATE_FORMAT(transaction_date_approved, "%a, %b. %e. %Y %r") as transaction_date_approved',
				'DATE_FORMAT(transaction_date_expiration, "%a, %b. %e. %Y %r") as transaction_date_expiration',
			)
		);

		$where = array(
			'transaction_requested_to' 	=> $admin_bridge_id,
			'transaction_type_id'		=> 6, // OTC Top Up
			'tms_admin_id'				=> $this->_tms_admin
		);

		$inner_joints = array(
			array(
				'table_name' 	=> 'tms_admins',
				'condition'		=> 'tms_admins.oauth_bridge_id = transactions.transaction_requested_to'
			)
		);

		$total_rows = $this->transactions->get_count(
			$where,
			array(),
			$inner_joints
		);
		$offset = $this->get_pagination_offset($page, $this->_limit, $total_rows);
	    $results = $this->transactions->get_data($select, $where, array(), $inner_joints, array('filter'=>'transaction_number', 'sort'=>'DESC'), $offset, $this->_limit);
		$results = $this->get_transaction_info($results);

		$this->_data['listing'] = $this->table_listing('', $results, $total_rows, $offset, $this->_limit, array(), 3, false, '', '');
		$this->_data['title']  = "TOP UP (OTC) - Listing";
		$this->set_template("transactions/list", $this->_data);
	}

}
