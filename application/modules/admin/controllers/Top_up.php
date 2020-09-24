<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Top_up extends Admin_Controller {
	private
		$_admin_account_data = NULL;
		
	public function after_init() {
		$this->set_scripts_and_styles();

		$this->load->model("admin/transactions_model", "transactions");

		$this->_admin_account_data = $this->get_account_data();
	}

	private function get_wallet_balance() {
        $account_results            = $this->_admin_account_data['results'];
        $admin_oauth_bridge_id		= $account_results['admin_oauth_bridge_id'];
        $account_oauth_bridge_id    = $account_results['oauth_bridge_id'];
        $wallet_address             = $account_results['wallet_address'];

		$wallet_data 				= $this->get_wallet_data($wallet_address);
		$wallet_data_results    	= $wallet_data['results'];
		$balance 					= $wallet_data_results['balance'];
		return $balance;
	}

	public function index($page = 1) {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];

		$this->_data['notification'] 	= $this->session->flashdata('notification');

		$actions = array(
			'update'
		);

		$select = array(
			'transaction_id as id',
			'IF(transaction_otp_status = 1, "Confirmed", "Unconfirmed") as "OTP Status"',
			'transaction_id as "TX ID"',
			'transaction_sender_ref_id as "Ref ID"',
			'CONCAT(merchant_fname, " ", merchant_mname, " ", merchant_lname) as "Merchant"',
			'FORMAT(transaction_amount, 2) as "Amount"',
			'FORMAT(transaction_fee, 2) as "Fee"',
			'FORMAT(transaction_total_amount, 2) as "Total Amount"',
			'transaction_date_created as "Date Created"',
			'transaction_date_expiration as "Date Expiration"',
		);

		$where = array(
			'oauth_bridge_parent_id' 		=> $admin_oauth_bridge_id,
			'transaction_status'			=> 0,
			'transaction_date_expiration >='=> $this->_today,
			'transaction_type_group_id'		=> 1 // all top-up
		);

		$inner_joints = array(
			array(
				'table_name' 	=> 'oauth_bridges',
				'condition'		=> 'oauth_bridges.oauth_bridge_id = transactions.transaction_created_by'
			),
			array(
				'table_name' 	=> 'transaction_types',
				'condition'		=> 'transaction_types.transaction_type_id = transactions.transaction_type_id'
			),
			array(
				'table_name' 	=> 'merchant_accounts',
				'condition'		=> 'merchant_accounts.oauth_bridge_id = transactions.transaction_requested_by'
			),
			array(
				'table_name' 	=> 'merchants',
				'condition'		=> 'merchants.merchant_number = merchant_accounts.merchant_number'
			)
		);

		$total_rows = $this->transactions->get_count(
			$where,
			array(),
			$inner_joints
		);
		$offset = $this->get_pagination_offset($page, $this->_limit, $total_rows);
	    $results = $this->transactions->get_data($select, $where, array(), $inner_joints, array('filter'=>'transaction_date_created', 'sort'=>'DESC'), $offset, $this->_limit);

		$this->_data['listing'] = $this->table_listing('', $results, $total_rows, $offset, $this->_limit, $actions, 2);
		$this->_data['title']  = "Top-up Request";
		$this->set_template("top_up/list", $this->_data);
	}

	public function update($id) {
        $account_results            = $this->_admin_account_data['results'];
        $admin_oauth_bridge_id		= $account_results['admin_oauth_bridge_id'];
        $account_oauth_bridge_id    = $account_results['oauth_bridge_id'];

		$this->_data['form_url']		= base_url() . "top-up/update/{$id}";
		$this->_data['notification'] 	= $this->session->flashdata('notification');

		$where = array(
			'transaction_id'				=> $id,
			'oauth_bridge_parent_id' 		=> $admin_oauth_bridge_id,
			'transaction_status'			=> 0,
			'transaction_date_expiration >='=> $this->_today,
			'transaction_type_group_id'		=> 1 // all top-up
		);

		$inner_joints = array(
			array(
				'table_name' 	=> 'oauth_bridges',
				'condition'		=> 'oauth_bridges.oauth_bridge_id = transactions.transaction_created_by'
			),
			array(
				'table_name' 	=> 'transaction_types',
				'condition'		=> 'transaction_types.transaction_type_id = transactions.transaction_type_id'
			),
			array(
				'table_name' 	=> 'merchant_accounts',
				'condition'		=> 'merchant_accounts.oauth_bridge_id = transactions.transaction_requested_by'
			),
			array(
				'table_name' 	=> 'merchants',
				'condition'		=> 'merchants.merchant_number = merchant_accounts.merchant_number'
			)
		);

		$row = $this->transactions->get_datum(
			'',
			$where,
			array(),
			$inner_joints,
			array(
				'*',
				'merchants.oauth_bridge_id as merchant_oauth_bridge_id'
			)
		)->row();

		if ($row == "") {
			redirect(base_url() . "top-up");
		}

		$this->_data['post'] = array(
			'transaction-id' => $row->transaction_id
		);

		$transaction_id 			= $row->transaction_id;
		$merchant_oauth_bridge_id 	= $row->merchant_oauth_bridge_id;

		if ($_POST) {
			if ($this->form_validation->run('validate')) {
				$password = $this->input->post("password");

				$account_row = $this->accounts->get_datum(
                    '',
                    array(
                        'oauth_bridge_id'   => $account_oauth_bridge_id,
                        'account_password'  => hash("sha256", $password)
                    )
                )->row();

                if ($account_row == "") {
                    $this->session->set_flashdata('notification', $this->generate_notification('warning', 'Incorrect Password!'));
                    redirect($this->_data['form_url']);
				}
				
				$amount			= 0;
				$fee			= 0;
				$total_amount 	= 0;

				$amount = $row->transaction_amount;
				$fee 	= $row->transaction_fee;

				$sender_amount		= $amount;
				$receiver_amount 	= $amount;
				$fee_amount			= $fee;

				$sender_total_amount 	= 0 - $sender_amount; // make it negative
				$receiver_total_amount	= $receiver_amount;

				$sender_wallet_address		= $account_results['wallet_address'];
				$receiver_wallet_address	= $this->get_wallet_address($merchant_oauth_bridge_id);
				
				$sender_new_balances = $this->update_wallet($sender_wallet_address, $sender_total_amount);
				if ($sender_new_balances) {
					// record to ledger
					$this->new_ledger_datum(
						"top_up_otc_debit", 
						$transaction_id, 
						$receiver_wallet_address, 
						$sender_wallet_address,
						$sender_new_balances
					);
				}

				$receiver_new_balances = $this->update_wallet($receiver_wallet_address, $receiver_total_amount);
				if ($receiver_new_balances) {
					// record to ledger
					$this->new_ledger_datum(
						"top_up_otc_credit", 
						$transaction_id, 
						$sender_wallet_address, 
						$receiver_wallet_address,
						$receiver_new_balances
					);
				}

				$this->transactions->update(
					$transaction_id,
					array(
						'transaction_status' 		=> 1,
						'transaction_date_approved'	=> $this->_today
					)
				);

				$this->session->set_flashdata('notification', $this->generate_notification('success', 'Successfully Approved!'));
				redirect(base_url() . "top-up");
			}
		}

		$this->_data['title']  = "Top-up Approval";
		$this->set_template("top_up/form", $this->_data);
	}
}
