<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Client_accounts extends Admin_Controller {
	private
		$_admin_account_data = NULL;

	public function after_init() {
		$this->set_scripts_and_styles();
		$this->load->model('admin/client_accounts_model', 'client_accounts');
		$this->load->model('admin/oauth_bridges_model', 'bridges');

		$this->_admin_account_data = $this->get_account_data();
	}

	public function index($page = 1) {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];

		// $this->_data['add_label']= "New Account";
		// $this->_data['add_url']	 = base_url() . "merchant-accounts/new";

		$actions = array(
			// 'update'
		);

		$select = array(
			'account_number as id',
			'account_number as "Account No."',
			'account_fname as "Account First Name"',
			'account_mname as "Account Middle Name"',
			'account_lname as "Account Last Name"',
			'account_date_added as "Date Added"',
			'account_status as "Status"',
		);

		$where = array(
			'oauth_bridge_parent_id'	=> $admin_oauth_bridge_id
		);

		$inner_joints = array(
			array(
				'table_name' 	=> 'oauth_bridges',
				'condition'		=> 'oauth_bridges.oauth_bridge_id = client_accounts.oauth_bridge_id'
			)
		);

		$total_rows = $this->client_accounts->get_count(
			$where,
			array(),
			$inner_joints
		);
		$offset = $this->get_pagination_offset($page, $this->_limit, $total_rows);
	    $results = $this->client_accounts->get_data($select, $where, array(), $inner_joints, array('filter'=>'account_date_added', 'sort'=>'DESC'), $offset, $this->_limit);

		$this->_data['listing'] = $this->table_listing('', $results, $total_rows, $offset, $this->_limit, $actions, 2);
		$this->_data['title']  = "Client Accounts";
		$this->set_template("client_accounts/list", $this->_data);
	}
}
